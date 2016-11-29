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

function loadUrlJson($url, $expireTime = EXPIRE_WEEK) {
	global $curl;
	if (!isset($expireTime)) {//todo
		$expireTime = SECONDS_IN_A_DAY;
	}
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
		'Accept: application/vnd.github.v3.full+json, application/json'
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
	if ($code != 200) {
		trigger_error("Invalid code received: " . $code);
		return;
	}
	$json = new stdClass();
	$json->payload = json_decode($body);
	$json->expires = time() + max($expireTime, isset($header_arr["x-poll-interval"]) ? $header_arr["x-poll-interval"] : 0);
	$json->url = $url;
	$json->etag = $header_arr["etag"];
	@mkdir("output");
	@mkdir("output/cache");
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
	@mkdir($dst);
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

function includeToFile($php, $to) {
	global $extend_depth, $config;
	$file = fopen($to, "w");
	fwrite(STDERR, "Writing $php to $to\n");
	ob_start(function($contents) use ($file) {
		fwrite($file, $contents);
	});
	include $php;
	for (; $extend_depth > 0; $extend_depth--) {
		ob_end_flush();
	}
	ob_end_flush();
}

function dumpToFile($function, $to) {
	
}

function useExpandSystem(Array $orginal, $cache = EXPIRE_WEEK) {
	foreach($orginal as $key => &$value) {
		if(isset($value->more)) {
			if(is_array($value->more)) {
				foreach($value->more as $doc) {
					$value = (object)array_merge((array)$value, (array)loadUrlJson($doc, $cache));
				}
			}
			if(is_string($value->more)) {
				$value = (object)array_merge((array)$value, (array)loadUrlJson($value->more, $cache));
			}
		}
	}
	return $orginal;
}
function str_max_length($str, $length) {
	return $str; // TODO
}