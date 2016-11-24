<?PHP

$config = [
	"baseurl" => "ferrybig.me",
];

$curl = curl_init();

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_FILETIME, true);

define("EXPIRE_HOUR", 60 * 60);
define("EXPIRE_DAY", 24 * 60 * 60);
define("EXPIRE_WEEK", 7 * 24 * 60 * 60);

require "functions.php";

@mkdir("output");
@mkdir("output/site");
copy_dir("public_html/", "output/site");
includeToFile("index.php", "output/site/index.html");
includeToFile("about.php", "output/site/about.html");
