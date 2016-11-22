<?PHP

$curl = curl_init();

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_FILETIME, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // The worst thing that can happen is fake data

define("EXPIRE_HOUR", 60 * 60);
define("EXPIRE_DAY", 24 * 60 * 60);
define("EXPIRE_WEEK", 7 * 24 * 60 * 60);

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
	));
	$response = curl_exec($curl);
	$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);
	$header_arr = get_headers_from_curl_response($header);
	if($response == false) {
		fwrite(STDERR, "curl: (" . curl_errno($curl) . ") " . curl_error($curl));
	}
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	fwrite(STDERR, "cache: " . $url . ": " . ($code == 304 ? "hit" : "miss"));
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
	$json->expires = time() + max($expireTime,
			isset($header_arr["x-poll-interval"]) ? $header_arr["x-poll-interval"] : 0);
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
	return substr($rawname, $split);
}

function get_branch_style($rawname) {
	if ($rawname == "refs/heads/master") {
		return "primary";
	} else if (startsWith($rawname, "refs/heads/feature")) {
		return "info";
	} else if (startsWith($rawname, "refs/heads/hotfix")) {
		return "warning";
	} else {
		return "default";
	}
}
function filter_git_events(Array $events) {
	$newArr = [];
	foreach($events as $event) {
		switch($event->type) {
			case "PushEvent":
			case "PublicEvent":
			case "PullRequestEvent":
			case "ForkEvent":
			case "ReleaseEvent":
				$newArr[] = $event;	
		}
	}
	return $newArr;
}

//var_dump(loadUrlJson("https://api.github.com/users/ferrybig/events", SECONDS_IN_A_DAY));
ob_start();
include "github_contributions.php";
file_put_contents("output/github_contributions.html", ob_get_clean());
