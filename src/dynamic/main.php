<?PHP

require(__DIR__ . "/bootstrap.php");

if (!file_exists("cache")) {
	mkdir("cache");
}
if (!file_exists("public")) {
	mkdir("public");
}

foreach ($pos_args as $action) {
	switch ($action) {
		case "build":
			fwrite(STDERR, "Starting build...\n");

			fwrite(STDERR, "Loading projects...\n");
			if (file_exists("config/projects.json")):
				$projects = useExpandSystem(json_decode(file_get_contents("config/projects.json")));
			else:
				fwrite(STDERR, "Warning, `projects.json` not found in templates/config/!\n");
				$projects = [];
			endif;

			fwrite(STDERR, "Generating basic pages...\n");
			includeToFile("pages/index.php", "public/index.html");
			includeToFile("pages/about.php", "public/about.html");
			includeToFile("pages/contact.php", "public/contact.html");
			includeToFile("pages/github_frame.php", "public/github_frame.html");
			includeToFile("pages/projects_frame.php", "public/projects_frame.html", ["projects" => $projects]);
			includeToFile("pages/projects_index.php", "public/projects/index.html", ["projects" => $projects]);

			fwrite(STDERR, "Checking out dynamic projects...\n");
			foreach ($projects as $project) {
				tryCheckout($project);
			}

			fwrite(STDERR, "Generate dynamic pages...\n");
			foreach ($projects as $project) {
				includeToFile("pages/project.php", "public/projects/$project->slug.html", ["project" => $project, "projects" => $projects]);
			}

			fwrite(STDERR, "Generating sitemaps...\n");
			includeToFile("pages/sitemap.php", "public/sitemap.xml", ["sitemap" => getSiteMap(), "type" => "xml"]);
			includeToFile("pages/sitemap.php", "public/sitemap.txt", ["sitemap" => getSiteMap(), "type" => "txt"]);

			break;
		case "projects":
			//TODO: Change this number in the future... (or use proper paging system...)
			$projects = (array) loadUrlJson("https://api.github.com/users/ferrybig/repos?per_page=100", EXPIRE_HOUR);
			foreach ($projects as $key => $project) {
				if ($project->fork) {
					unset($projects[$key]);
					continue;
				}
				$projects[$key] = json_encode(["more" => $project->url]);
			}
			echo "[\n\t" . implode(",\n\t", $projects) . "\n]\n";
			break;
		case "clean":
			if (is_dir("cache")) {
				$files = glob('cache/*.json', GLOB_BRACE);
				foreach ($files as $file) {
					unlink($file);
				}
			}
			echo "Dropped cache\n";
			break;
		case "gc":
			$expired = 0;
			$total = 0;
			$cleaned = 0;
			$correct = 0;
			$files = glob('cache/*.json', GLOB_BRACE);
			$time = time();
			foreach ($files as $file) {
				$json = json_decode(file_get_contents($file));
				$total++;
				if ($json->expires > $time) {
					$correct++;
				} else if ($json->expires + EXPIRE_WEEK > $time) {
					$expired++;
				} else {
					$cleaned++;
					unlink($file);
				}
			}
			echo "Total: $total\n";
			echo "Correct: $correct\n";
			echo "Expired: $expired\n";
			echo "Cleaned: $cleaned\n";
			echo "Checked cache!\n";
			break;
	}
}


if (error_get_last() == null) {
	fwrite(STDERR, "Done!\n");
} else {
	fwrite(STDERR, "Done (with errors)!\n");
	fwrite(STDERR, var_export(error_get_last(), true));
	exit(1);
}
