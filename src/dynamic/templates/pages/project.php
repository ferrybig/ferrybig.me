<?=
extend(__DIR__ . "/../partials/base.php", [
	"url" => "projects/$project->slug.html",
	"page" => "projects",
	"base" => "../",
	"image" => $project->icons[0]->big ?? "",
	"pages" => [["projects/", "Projects"], $project->nice_name ?? $project->name]
]);
?>
<?PHP $base = "../"; ?>
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default project project-full-page">
			<div class="panel-heading">
				<h2 class="panel-title panel-title-lg" property="http://purl.org/dc/terms/title"><?= htmlentities($project->nice_name ?? $project->name) ?></h2>
			</div>
			<div class="panel-body">
				<?PHP if (isset($project->language)): ?>
					<p class="project-tags">
						<?PHP if (is_array($project->language)): ?>
							<?PHP foreach ($project->language as $language) : ?>
								<span class="label language-tag language-tag-<?= htmlentities(strtolower($language)) ?>">
									<?= htmlentities($language) ?>
								</span>
							<?PHP endforeach; ?>
						<?PHP else: ?>
							<span class="label language-tag language-tag-<?= htmlentities(strtolower($project->language)) ?>">
								<?= htmlentities($project->language) ?>
							</span>
						<?PHP endif; ?>
					</p>
				<?PHP endif; ?>
				<?PHP if ($project->html_url): ?>
					<p>
						<iframe src="<?= htmlentities("//ghbtns.com/github-btn.html?user={$project->owner->login}&repo=$project->name&type=star&count=true&size=large") ?>" frameborder="0" scrolling="0" width="160px" height="30px"></iframe>
						<iframe src="<?= htmlentities("//ghbtns.com/github-btn.html?user={$project->owner->login}&repo=$project->name&type=watch&count=true&size=large&v=2") ?>" frameborder="0" scrolling="0" width="160px" height="30px"></iframe>
						<iframe src="<?= htmlentities("//ghbtns.com/github-btn.html?user={$project->owner->login}&repo=$project->name&type=fork&count=true&size=large") ?>" frameborder="0" scrolling="0" width="158px" height="30px"></iframe>
					</p>
					<p>
						<a href="<?= htmlentities($project->html_url) ?>">
							Open <?= htmlentities($project->full_name) ?> on Github
						</a>
					</p>
				<?PHP endif; ?>
				<?PHP if ($project->homepage): ?>
					<p>
						<a href="<?= htmlentities($project->homepage) ?>" class="btn btn-sm btn-primary">
							View project
						</a>
					</p>
				<?PHP endif; ?>
				<p class="project-time">
					<?PHP if ($project->updated_at): ?>
						<small>
							<time datetime="<?= htmlentities($project->updated_at) ?>">
								Updated at: <?= htmlentities(gmdate('Y-m-d H:i:s', strtotime($project->updated_at))) ?>
							</time>
						</small>
					<?PHP endif; ?>
					<small>
						<time datetime="<?= htmlentities($project->created_at) ?>" property="http://purl.org/dc/terms/created">
							Created at: <?= htmlentities(gmdate('Y-m-d H:i:s', strtotime($project->created_at))) ?>
						</time>
					</small>
				</p>
				<div>
					<?PHP if (strlen($project->description_html) > 20) : ?>
						<?= $project->description_html /* Should be left in raw mode */ ?>
					<?PHP else: ?>
						<p>
							<em>
								No readme found for this project,
								<a href="https://github.com/<?= htmlentities($project->full_name) ?>/new/master?readme=1">
									Create one
								</a>
								if you know what this project contains
							</em>
						</p>
					<?PHP endif; ?>
				</div>
				<!--
				<h3>Download</h3>
				<ol>
					<li><a href="/repository/">v0.0.0</a></li>
					<li>4 more... </li>
				</ol>
				-->
				<?PHP if (!empty($project->icons)): ?>
					<h3>Images</h2>
						<div class="row">
							<?PHP foreach ($project->icons as $icon) : ?>
								<div class="col-sm-6 col-md-3">
									<a href="<?= htmlentities($icon->normal) ?>" class="thumbnail">
										<img src="<?= htmlentities($icon->big) ?>"
											 alt="<?= htmlentities($icon->title ?? "") ?>" width="256" height="256">
									</a>
								</div>
							<?PHP endforeach; ?>
						</div>
					<?PHP endif; ?>
					<div class="project-related">
						<h3>Related projects</h3>
						<?PHP $projects_related = $projects; ?>
						<?PHP unset($projects_related[array_search($project, $projects_related)]); ?>
						<?PHP usort($projects_related, compare_project_sort_callback($project)); ?>
						<?PHP $projects_related = limit($projects_related, 4); ?> 
						<?= include_advanced(__DIR__ . "/../partials/projects_list.php", ["projects" => $projects_related, "base" => "../"])
						?>
					</div>
			</div>
		</div>
	</div>
	<aside class="col-md-4 hidden-print">
		<div class="row">
			<div class="col-sm-6 col-md-12">
				<?PHP include __DIR__ . "/../partials/projects.php" ?>
			</div>
			<div class="col-sm-6 col-md-12">
				<?PHP include __DIR__ . "/../partials/github.php" ?>
			</div>
		</div>
	</aside>
</div>
