<?PHP

if(file_exists(__DIR__ . "/config/config.json")) {
	$config = json_decode(file_get_contents(__DIR__ . "/config/config.json"));
} else {
	fwrite(STDERR, "Fatal, `config.json` not found in templates/config/!\n");
	exit(1);
}

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

$pos_args = [];
for($i = 1; $i < $argc; $i++) {
	array_push($pos_args, $argv[$i]);
}
if (empty($pos_args)) {
	$pos_args = ["build"];
}

foreach ($pos_args as $action) {
	switch ($action) {
		case "build":
			fwrite(STDERR, "Starting build...\n");
			@mkdir("output");
			@mkdir("output/site");

			fwrite(STDERR, "Loading projects...\n");
			if(file_exists(__DIR__ . "/config/projects.json")):
				$projects = useExpandSystem(json_decode(file_get_contents(__DIR__ . "/config/projects.json")));
			else:
				fwrite(STDERR, "Warning, `projects.json` not found in templates/config/!\n");
				$projects = [];
			endif;

			fwrite(STDERR, "Copy static resources...\n");
			copy_dir("public_html/", "output/site");

			fwrite(STDERR, "Generating basic pages...\n");
			includeToFile("pages/index.php", "output/site/index.html");
			includeToFile("pages/about.php", "output/site/about.html");
			includeToFile("pages/contact.php", "output/site/contact.html");
			includeToFile("pages/github_frame.php", "output/site/github_frame.html");
			includeToFile("pages/projects_frame.php", "output/site/projects_frame.html", ["projects" => $projects]);
			includeToFile("pages/projects_index.php", "output/site/projects/index.html", ["projects" => $projects]);
			
			fwrite(STDERR, "Generate dynamic pages...\n");
			foreach($projects as $project) {
				includeToFile("pages/project.php", "output/site/projects/$project->slug.html", ["project" => $project]);
			}
			break;
		case "projects":
			//TODO: Change this number in the future... (or use proper paging system...)
			$projects = (array)loadUrlJson("https://api.github.com/users/ferrybig/repos?per_page=100", EXPIRE_HOUR);
			foreach($projects as $key => $project) {
				if ($project->fork) {
					unset($projects[$key]);
					continue;
				}
				$projects[$key] = json_encode(["more" => $project->url]);
			}
			echo "[\n\t".implode(",\n\t", $projects) . "\n]\n";
			break;
	}
}

fwrite(STDERR, "Done!\n");
