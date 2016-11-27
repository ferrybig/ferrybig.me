<?PHP

$config = [
	"baseurl" => "https://ferrybig.me",
	"title" => "ferrybig.me",
];

ini_set('log_errors', 1);
ini_set('display_errors', 0);
error_reporting(-1);

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
includeToFile("pages/index.php", "output/site/index.html");
includeToFile("pages/about.php", "output/site/about.html");
includeToFile("pages/contact.php", "output/site/contact.html");
includeToFile("pages/github_frame.php", "output/site/github_frame.html");
includeToFile("pages/projects_index.php", "output/site/projects/index.html");

echo "Done!\n";
