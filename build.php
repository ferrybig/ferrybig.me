<?PHP
$opts = [
        'http' => [
                'method' => 'GET',
                'header' => [
                        'User-Agent: Crawler for ferrybig.me'
                ]
        ]
];
$opts = stream_context_create($opts);
define("SECONDS_IN_A_DAY", 24 * 60 * 60);
function loadUrlJson($url) {
	global $opts;
	if(!isset($expireTime))//todo
		$expireTime = SECONDS_IN_A_DAY;
	$hash = hash("sha512", $url);
	if(file_exists("output/cache/$hash.json")) {
		$json = json_decode(file_get_contents("output/cache/$hash.json"));
		if($json && $json->expires > time())
			return $json->payload;
	}
	$json = new stdClass();
	$json->payload = json_decode(file_get_contents($url, false, $opts));
	$json->expires = time() + $expireTime;
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
	if($size <= $count)
		return $arr;
	return array_slice($input, 0, $count);
}
function has_limited(Array $arr, $count) {
	$size = count($arr);
	if($size <= $count)
		return 0;
	return $size - $count;
}
function format_branch_name($rawname) {
	// We need the second / here (but more slashes may follow): 
	$split = strrpos($rawname, "/", strrpos($rawname, "/"));
	return $split[count($split)];
}
function get_branch_style($rawname) {
	if($rawname == "refs/heads/master") {
		return "primary";
	} else if(startsWith($rawname, "refs/heads/feature")) {
		return "info";
	} else if(startsWith($rawname, "refs/heads/hotfix")) {
		return "warning";
	} else {
		return "default";
	}
}
//var_dump(loadUrlJson("https://api.github.com/users/ferrybig/events", SECONDS_IN_A_DAY));

include "github_contributions.php";