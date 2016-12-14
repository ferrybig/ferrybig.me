<?PHP
$data_format = $type; // Required for minifier
switch($type) {
	case "xml":
		echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	break;
	case "json":
		echo '{"pages":[';
	break;
}
$size = count($sitemap);
for($i = 0; $i < $size; $i++) {
	if($i != 0) {
		switch($type) {
			case "json":
				echo ',';
			break;
		}
	}
	switch($type) {
		case "xml":
			echo '<url><loc>' . htmlentities($sitemap[$i]) . '</loc></url>';
		break;
		case "json":
			echo json_encode($sitemap[$i]);
		break;
		case "txt":
			echo $sitemap[$i] . "\r\n";
		break;
	}
}
switch($type) {
	case "xml":
		echo '</urlset>';
	break;
	case "json":
		echo ']}';
	break;
}