<ul class="project-list media-list">
	<?PHP foreach($projects as $project): ?>
		<?= include_advanced(__DIR__ . "/../modules/project_list_entry.php", 
			["project" => $project, "size" => $size ?? "medium", "base" => $base ?? "./", "project_list_header" => $project_list_header ?? "h3"]) ?>
	<?PHP endforeach; ?>
</ul>
