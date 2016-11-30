<?= extend(__DIR__ . "/../modules/base.php", ['url' => "projects/", "page" => "projects", "base" => "../"]); ?>
<?PHP $base = "../"; ?>
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2>Projects</h2>
			</div>
			<div class="panel-body">
				<ul class="project-list media-list">
					<?PHP usort($projects, function($a, $b) { return strnatcasecmp($a->nice_name, $b->nice_name);}); ?>
					<?PHP foreach ($projects as $project): ?>
						<?PHP if(isset($project->hidden) && $project->hidden) continue; ?>
						<li class="project media">
							<?PHP if (isset($project->icons)): ?>
								<div class="media-left">
									<a href="<?= htmlentities($project->html_url) ?>">
										<img class="media-object" src="<?= htmlentities($project->icons[0]->medium) ?>" alt="" width="128" height="128">
									</a>
								</div>
							<?PHP else: ?>
								<div class="media-left">
									<a href="<?= htmlentities("$project->slug.html") ?>">
										<img class="media-object" src="../images/missing_image.svg" alt="" width="128" height="128">
									</a>
								</div>
							<?PHP endif; ?>
							<div class="media-body">
								<h3 class="media-heading project-name" style="font-size: 130%">
									<a href="<?= htmlentities("$project->slug.html") ?>"><?= htmlentities($project->nice_name) ?></a>
								</h3>
								<?PHP if (isset($project->language)): ?>
									<p class="project-tags">
										<small>
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
										</small>
									</p>
								<?PHP endif; ?>
								<p class="project-time">
									<small>
										<time datetime="<?= htmlentities($project->created_at) ?>">
											Created at: <?= htmlentities(gmdate('Y-m-d H:i:s', strtotime($project->created_at))) ?>
										</time>
									</small>
								</p>
								<p class="project-description">
									<?= htmlentities(str_max_length($project->description, 1024)) ?>
								</p>
							</div>
						</li>
					<?PHP endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<aside class="col-md-4 hidden-print">
		<div class="row">
			<div class="col-sm-6 col-md-12">
				<?PHP include __DIR__ . "/../modules/projects.php" ?>
			</div>
			<div class="col-sm-6 col-md-12">
				<?PHP include __DIR__ . "/../modules/github.php" ?>
			</div>
		</div>
	</aside>
</div>
