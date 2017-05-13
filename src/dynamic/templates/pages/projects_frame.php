<?= extend(__DIR__ . "/../partials/base.php", ["url" => "github_frame.html", "page" => null, "no_container" => true, "target" => "_parent"]); ?>
<?PHP

usort($projects, function($a, $b) {
	return $a->created_at < $b->created_at ? 1 : -1;
});
?>
<?= include_advanced(__DIR__ . "/../partials/projects_list.php", ["projects" => limit($projects, 14), "base" => ""])
?>
