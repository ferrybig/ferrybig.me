<?= extend(__DIR__ . "/../modules/base.php", ["url" => "github_frame.html", "page" => null, "no_container" => true]); ?>
<ul class="project-list media-list">
	<?PHP if(file_exists(__DIR__ . "/../config/projects.json")): ?>
		<?PHP $projects = useExpandSystem(json_decode(file_get_contents(__DIR__ . "/../config/projects.json"))); ?>
	<?PHP else: ?>
		<?PHP $projects = []; ?>
	<?PHP endif; ?>
	<?PHP usort($projects, function($a, $b){return $a->created_at < $b->created_at ? 1 : -1;}); ?>
	<?PHP foreach(limit($projects, 14) as $project): ?>
		<li class="project media">
			<?PHP if(isset($project->icons)): ?>
				<div class="media-left">
					<a href="<?= htmlentities($project->html_url) ?>">
						<img class="media-object" src="<?= htmlentities($project->icons[0]->small) ?>" alt="">
					</a>
				</div>
			<?PHP endif; ?>
			<div class="media-body">
				<h3 class="media-heading project-name" style="font-size: 130%">
					<a href="<?= htmlentities($project->html_url) ?>"><?= htmlentities($project->name) ?></a>
					<?PHP if(isset($project->language)): ?>
						<small>
							<?PHP if(is_array($project->language)): ?>
								<?PHP foreach($project->language as $language) : ?>
									<span class="label label-default">
										<?= htmlentities($project->language) ?>
									</span>
								<?PHP endforeach; ?>
							<?PHP else: ?>
								<span class="label label-default">
									<?= htmlentities($project->language) ?>
								</span>
							<?PHP endif; ?>
						</small>
					<?PHP endif; ?>
				</h3>
				<p>
					<small>
						<time datetime="<?= htmlentities($project->created_at) ?>">
							Created at: <?= htmlentities(gmdate('Y-m-d H:i:s', strtotime($project->created_at))) ?>
						</time>
					</small>
				</p>
				<p class="project-description">
					<?= htmlentities(str_max_length($project->description, 255)) ?>
				</p>
			</div>
		</li>
	<?PHP endforeach; ?>
</ul>