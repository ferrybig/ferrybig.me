<?PHP

if (file_exists(__DIR__ . "/../../config/config.json")) {
	$config = json_decode(file_get_contents(__DIR__ . "/../../config/config.json"));
} else if (file_exists(__DIR__ . "/../../templates/config.json")) {
	mv(__DIR__ . "/../../templates/config.json", __DIR__ . "/../../config/config.json");
	mv(__DIR__ . "/../../templates/projects.json", __DIR__ . "/../../config/projects.json");
	$config = json_decode(file_get_contents(__DIR__ . "/../../config/config.json"));
} else {
	fwrite(STDERR, "Fatal, `config.json` not found in config/!\n");
	exit(1);
}

ini_set('log_errors', 1);
ini_set('display_errors', 0);
error_reporting(-1);

$curl = curl_init();

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_FILETIME, true);
// Used for github rate limiting
if (isset($config->username)) {
	curl_setopt($curl, CURLOPT_USERPWD, "$config->username:$config->password");
}

define("EXPIRE_HOUR", 60 * 60);
define("EXPIRE_DAY", 24 * 60 * 60);
define("EXPIRE_WEEK", 7 * 24 * 60 * 60);

require "functions.php";

$pos_args = [];
for ($i = 1; $i < $argc; $i++) {
	array_push($pos_args, $argv[$i]);
}
if (empty($pos_args)) {
	$pos_args = ["build"];
}

if(file_exists("templates/config/config.json")) {
	rename("templates/config/config.json", "config/config.json");
	rename("templates/config/projects.json", "config/projects.json");
}
