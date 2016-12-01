<?PHP
function get_headers_from_curl_response($response) {
	$headers = array();
	$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
	foreach (explode("\r\n", $header_text) as $i => $line) {
		if ($i === 0) {
			$headers['http_code'] = $line;
		} else {
			list ($key, $value) = explode(': ', $line);
			$headers[strToLower($key)] = $value;
		}
	}
	return $headers;
}

function loadUrlJson($url, $expireTime = EXPIRE_WEEK, $accept = "application/vnd.github.v3.full+json, application/json", $raw = false) {
	global $curl;
	$hash = hash("sha512", $url);
	$etag = "";
	if (file_exists("output/cache/$hash.json")) {
		$json = json_decode(file_get_contents("output/cache/$hash.json"));
		if ($json->expires > time()) {
			return $json->payload;
		}
		$etag = isset($json->etag) ? $json->etag : "";
	}
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'If-None-Match: ' . $etag,
		'User-Agent: Crawler for ferrybig.me',
		'Accept: '. $accept
	));
	$response = curl_exec($curl);
	$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);
	$header_arr = get_headers_from_curl_response($header);
	if ($response == false) {
		fwrite(STDERR, "curl: (" . curl_errno($curl) . ") " . curl_error($curl) . "\n");
	}
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	// TODO: remove debugging statement
	fwrite(STDERR, "cache: " . $url . ": " . ($code == 304 ? "hit" : "miss") . "\n");
	if ($code == 304) {
		$json->expires = time() + $expireTime;
		file_put_contents("output/cache/$hash.json", json_encode($json));
		return $json->payload;
	}
	if	($raw) {
		if ($code != 200 && $code != 404 && $code != 204) {
			trigger_error("Invalid code received: " . $code);
			return;
		}
	} else {
		if ($code != 200) {
			trigger_error("Invalid code received: " . $code);
			return;
		}
	}
	$json = new stdClass();
	$json->payload = $raw ? $body : json_decode($body);
	$json->expires = time() + max($expireTime, isset($header_arr["x-poll-interval"]) ? $header_arr["x-poll-interval"] : 0);
	$json->url = $url;
	if (isset($header_arr["etag"])) {
		$json->etag = $header_arr["etag"];
	}
	is_dir("output") || mkdir("output");
	is_dir("output/cache") || mkdir("output/cache");
	file_put_contents("output/cache/$hash.json", json_encode($json));
	return $json->payload;
}

// http://stackoverflow.com/a/834355/1542723
function startsWith($haystack, $needle) {
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

function limit(Array $arr, $count) {
	$size = count($arr);
	if ($size <= $count)
		return $arr;
	return array_slice($arr, 0, $count);
}

function has_limited(Array $arr, $count) {
	$size = count($arr);
	if ($size <= $count)
		return 0;
	return $size - $count;
}

function format_branch_name($rawname) {
	// We need the second / here (but more slashes may follow):
	$split = strrpos($rawname, "/", strrpos($rawname, "/"));
	return substr($rawname, $split + 1);
}

function get_branch_style($rawname) {
	if ($rawname == "refs/heads/master") {
		return "primary";
	} else if (startsWith($rawname, "refs/heads/feature")) {
		return "info";
	} else if (startsWith($rawname, "refs/heads/hotfix")) {
		return "warning";
	} else if (startsWith($rawname, "refs/tags")) {
		return "success";
	} else {
		return "default";
	}
}

function filter_git_events(Array $events) {
	$newArr = [];
	foreach ($events as $event) {
		switch ($event->type) {
			case "PushEvent":
			case "PublicEvent":
			case "PullRequestEvent":
			case "CreateEvent":
			case "ForkEvent":
			case "ReleaseEvent":
				$newArr[] = $event;
		}
	}
	return limit($newArr, 7);
}

function copy_dir($src, $dst) {
	exec("rsync -a " .  escapeshellarg($src) . " " . escapeshellarg($dst), $output, $rtn);
	fwrite(STDERR, implode("\n", $output));
	if($rtn != 0) // bad exit status
		recurse_copy($src, $dst);
}

function recurse_copy($src, $dst) {
	$dir = opendir($src);
	is_dir($dst) || mkdir($dst);
	while (false !== ( $file = readdir($dir))) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if (is_dir($src . '/' . $file)) {
				recurse_copy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

$extend_depth = 0;
$extend_array = [];

function extend($other, $passedArgs = NULL) {
	global $extend_depth, $extend_array, $config;
	$extend_depth++;
	if (is_array($passedArgs)) {
		extract($passedArgs);
	}
	ob_start();
	include $other;
	$end = ob_get_clean();
	$start = isset($extend_array[$extend_depth]) ? $extend_array[$extend_depth] : $end;
	unset($extend_array[$extend_depth]);
	$page_decorator = function ($contents, $phase) use ($start, $end) {
		if ($phase & PHP_OUTPUT_HANDLER_START) {
			$contents = $start . $contents;
		}
		if ($phase & PHP_OUTPUT_HANDLER_END) {
			$contents = $contents . $end;
		}
		return $contents;
	};
	ob_start($page_decorator, 1024 * 8);
}

function extend_body() {
	global $extend_depth, $extend_array;
	$extend_array[$extend_depth] = ob_get_contents();
	ob_clean();
}

function includeToFile($php, $to, Array $vars = []) {
	global $extend_depth;
	$file = fopen($to, "w");
	fwrite(STDERR, "Writing $php to $to\n");
	ob_start(function($contents) use ($file) {
		fwrite($file, $contents);
	});
	include_advanced($php, $vars);
	for (; $extend_depth > 0; $extend_depth--) {
		ob_end_flush();
	}
	ob_end_flush();
}

function include_advanced($php, Array $vars = []) {
	global $config;
	extract($vars);
	include $php;
}

// Based on an edit of http://stackoverflow.com/a/3022234/1542723
function slug($z){
    $z = strtolower($z);
    $z = preg_replace('/[^a-z0-9 \/_-]+/', '', $z);
    $z = str_replace(' ', '-', $z);
    $z = str_replace('/', '-', $z);
    $z = str_replace('_', '-', $z);
    return trim($z, '-');
}

function expandNode($value, $cache) {
	if(is_string($value)) {
		$value = loadUrlJson($value->more, $cache);
	} else if(isset($value->more)) {
		if(is_array($value->more)) {
			foreach($value->more as $doc) {
				$value = (object)array_merge((array)loadUrlJson($doc, $cache), (array)$value);
			}
		}
		if(is_string($value->more)) {
			$value = (object)array_merge((array)loadUrlJson($value->more, $cache), (array)$value);
		}
	}
	return $value;
}

function expandProject(StdClass $value, $cache) {
	if(isset($value->url) && !isset($value->description_html)) {
		$readme = loadUrlJson($value->url . "/readme", $cache, "application/vnd.github.v3.html", true);
		// Hacky 404 check, all 404's on github are json, and we expect html here...
		if(!startsWith($readme, "{")) {
			if(strlen(strip_tags($readme)) > strlen($value->description)) {
				$value->description_html = $readme; //Github can send us a XSS attack here
				$value->description_html = preg_replace(
					"/href=\"#([a-z -]+)\"/",
					"href=\"#user-content-\\1\"",
					$value->description_html);
				
			}
		}
	}
	if(!isset($value->description_html)) {
		$value->description_html = nl2br(htmlentities($value->description));
	}
	if(!isset($value->nice_name)) {
		$value->nice_name = $value->name;
	}
	$value->slug = slug($value->name);
	return $value;
}

function useExpandSystem(Array $orginal, $cache = EXPIRE_WEEK) {
	foreach($orginal as $key => &$value) {
		$value = expandProject(expandNode($value, $cache), $cache);
	}
	return $orginal;
}
function str_max_length($str, $length) {
	return $str; // TODO
}
function get_project_language_array($project) {
	if(empty($project->language)) {
		return [];
	}
	if(is_string($project->language)) {
		return [$project->language];
	}
	return $project->language;
}
function compare_projects($a, $b) {
	$taga = get_project_language_array($a);
	$tagb = get_project_language_array($b);
	$points = count(array_diff($taga, $tagb)) + count(array_diff($tagb, $taga));
	if($points == 0) {
		$points = levenshtein ($a->nice_name, $b->nice_name);
	} else {
		$points = $points * 200;
	}
	return $points;
}
function compare_project_sort_callback($base) {
	return function($a, $b) use ($base) {
		return compare_projects($base, $a) <=> compare_projects($base, $b);
	};
}
