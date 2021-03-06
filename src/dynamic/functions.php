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
	$hash = substr(hash("sha512", $url), 0, 10);
	$etag = "";
	if (file_exists("cache/$hash.json")) {
		$json = json_decode(file_get_contents("cache/$hash.json"));
		if ($json->expires > time()) {
			return $json->payload;
		}
		$etag = isset($json->etag) ? $json->etag : "";
	}
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'If-None-Match: ' . $etag,
		'User-Agent: Crawler for ferrybig.me',
		'Accept: ' . $accept
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
		file_put_contents("cache/$hash.json", json_encode($json));
		return $json->payload;
	}
	if ($raw) {
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
	$json->expires = time() + max($expireTime, isset($header_arr["x-poll-interval"]) ? $header_arr["x-poll-interval"] : 0) + random_int(-10, 10);
	$json->url = $url;
	if (isset($header_arr["etag"])) {
		$json->etag = $header_arr["etag"];
	}
	file_put_contents("cache/$hash.json", json_encode($json));
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
	passthru("rsync -a " . escapeshellarg($src) . " " . escapeshellarg($dst), $rtn);
	if ($rtn != 0) // bad exit status
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

function rrmdir($src) {
	$dir = opendir($src);
	while (false !== ( $file = readdir($dir))) {
		if (( $file != '.' ) && ( $file != '..' )) {
			$full = $src . '/' . $file;
			if (is_dir($full)) {
				rrmdir($full);
			} else {
				unlink($full);
			}
		}
	}
	closedir($dir);
	rmdir($src);
}

$extend_depth = 0;
$extend_array = [];

function extend($other, $passedArgs = NULL) {
	global $EXPAND, $config;
	if (!$EXPAND) {
		$EXPAND = [];
		$EXPAND["start"] = "";
		$EXPAND["end"] = "";
	}
	if (is_array($passedArgs)) {
		extract($passedArgs);
	}
	ob_start();
	include $other;
	$EXPAND["end"] = ob_get_clean() . $EXPAND["end"];
}

function extend_body() {
	global $EXPAND;
	$EXPAND["start"] .= ob_get_contents();
	ob_clean();
}

function includeToFile($php, $to, Array $vars = []) {
	global $EXPAND, $config;
	if (!file_exists(dirname($to))) {
		mkdir(dirname($to));
	}
	$file = fopen($to, "w");
	fwrite(STDERR, "Writing $php to $to\n");
	ob_start();
	$data_format = "html";
	$vars["data_format"] = &$data_format;
	include_advanced("templates/" . $php, $vars);
	$data = ob_get_clean();
	if ($EXPAND) {
		$data = $EXPAND["start"] . $data . $EXPAND["end"];
	}
	$EXPAND = false;
	fwrite($file, $data);
	fclose($file);
}

function include_advanced($php, Array $vars = []) {
	global $config;
	extract($vars, EXTR_REFS);
	include $php;
}

// Based on an edit of http://stackoverflow.com/a/3022234/1542723
function slug($z) {
	$z = strtolower($z);
	$z = preg_replace('/[^a-z0-9 \/_-]+/', '', $z);
	$z = str_replace(' ', '-', $z);
	$z = str_replace('/', '-', $z);
	$z = str_replace('_', '-', $z);
	return trim($z, '-');
}

function expandNode($value, $cache) {
	if (is_string($value)) {
		$value = loadUrlJson($value->more, $cache);
	} else if (isset($value->more)) {
		if (is_array($value->more)) {
			foreach ($value->more as $doc) {
				$value = (object) array_merge((array) loadUrlJson($doc, $cache), (array) $value);
			}
		}
		if (is_string($value->more)) {
			$value = (object) array_merge((array) loadUrlJson($value->more, $cache), (array) $value);
		}
	}
	return $value;
}

function expandProject(StdClass $value, $cache) {
	if (isset($value->url) && !isset($value->description_html)) {
		$readme = loadUrlJson($value->url . "/readme", $cache, "application/vnd.github.v3.html", true);
		// Hacky 404 check, all 404's on github are json, and we expect html here...
		if (!startsWith($readme, "{")) {
			if (strlen(strip_tags($readme)) > strlen($value->description)) {
				$value->description_html = $readme; //Github can send us a XSS attack here
				$value->description_html = preg_replace(
						"/href=\"#([a-z -]+)\"/", "href=\"#user-content-\\1\"", $value->description_html);
			}
		}
	}
	if (!isset($value->description_html)) {
		$value->description_html = nl2br(htmlentities($value->description));
	}
	if (!isset($value->nice_name)) {
		$value->nice_name = $value->name;
	}
	$value->slug = slug($value->name);
	return $value;
}

function useExpandSystem(Array $orginal, $cache = EXPIRE_WEEK) {
	foreach ($orginal as $key => &$value) {
		$value = expandProject(expandNode($value, $cache), $cache);
	}
	return $orginal;
}

function str_max_length($str, $length) {
	return $str; // TODO
}

function get_project_language_array($project) {
	if (empty($project->language)) {
		return [];
	}
	if (is_string($project->language)) {
		return [$project->language];
	}
	return $project->language;
}

function compare_projects($a, $b) {
	$taga = get_project_language_array($a);
	$tagb = get_project_language_array($b);
	$points = count(array_diff($taga, $tagb)) + count(array_diff($tagb, $taga));
	$points += levenshtein($a->nice_name, $b->nice_name);
	return $points;
}

function compare_project_sort_callback($base) {
	return function($a, $b) use ($base) {
		return compare_projects($base, $a) <=> compare_projects($base, $b);
	};
}

function tryCheckout(stdClass $project) {
	if (!isset($project->checkout) || !isset($project->clone_url) || !isset($project->pushed_at) || !isset($project->slug))
		return;
	is_dir("output/checkout") || mkdir("output/checkout");
	$resultingPath = "output/checkout/" . $project->slug;
	$requiresClone = true;
	$hasupdated = false;
	if (is_dir($resultingPath)) {
		if (str_replace("+00:00", "Z", date(DATE_ATOM, filemtime($resultingPath))) > $project->pushed_at) {
			echo "No update needed for $project->slug!\n";
			$requiresClone = false;
		} else {
			$exitcode;
			passthru("git -C " . escapeshellarg($resultingPath) . " pull", $exitcode);
			if ($exitcode) {
				echo "Error updating $project->slug!\n";
				rrmdir($resultingPath);
			} else {
				$requiresClone = false;
				$hasupdated = true;
			}
		}
	}
	if ($requiresClone) {
		echo "Cloning $project->slug!\n";
		$exitcode;
		passthru("git clone " . escapeshellarg($project->clone_url) . " " . escapeshellarg($resultingPath), $exitcode);
		if ($exitcode) {
			trigger_error("Problem cloning $project->slug!", E_USER_WARNING);
		} else {
			$requiresClone = false;
			$hasupdated = true;
		}
	}
	if (!$requiresClone) {
		if (!empty($project->checkout_script)) {
			passthru($project->checkout_script);
		}
		if ($hasupdated) {
			touch($resultingPath);
			$src = realpath(realpath($resultingPath) . "/" . ($project->checkout_subdir ?? "")) . "/";
			if (!is_dir($src)) {
				trigger_error("Problem moving $src (Not a valid directory)", E_USER_WARNING);
				return;
			}
			$dst = "output/site/projects/$project->checkout";
			if (!is_dir($dst))
				mkdir($dst);
			copy_dir($src, $dst);
		}
		$project->homepage = "projects/" . str_replace("+", "%20", urlencode($project->checkout));
	}
}

$sitemap = [];

function includeToSitemap($url) {
	global $sitemap;
	$sitemap[] = $url;
	return $url;
}

function getSiteMap() {
	global $sitemap;
	return $sitemap;
}
