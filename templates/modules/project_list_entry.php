<<?=$tag ?? "li"?> class="project project-small media" resource="<?= htmlentities(($base ?? "") ."projects/$project->slug.html") ?>">
	<?PHP if(isset($project->icons)): ?>
		<div class="media-left">
			<a href="<?= htmlentities(($base ?? "") ."projects/$project->slug.html") ?>">
				<img class="media-object" src="<?= htmlentities($project->icons[0]->small) ?>" alt="" width="64" height="64">
			</a>
		</div>
	<?PHP endif; ?>
	<div class="media-body">
		<<?= $project_list_header ?? "h3" ?> class="media-heading project-name" style="font-size: 130%">
			<a href="<?= htmlentities(($base ?? "") . "projects/$project->slug.html") ?>" property="http://purl.org/dc/terms/title">
				<?= htmlentities($project->nice_name) ?>
			</a>
		</<?= $project_list_header ?? "h3" ?>>
		<?PHP if(isset($project->language)): ?>
			<p class="project-tags">
				<small>
					<?PHP if(is_array($project->language)): ?>
						<?PHP foreach($project->language as $language) : ?>
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
			<?= htmlentities(str_max_length($project->description, 255)) ?>
		</p>
	</div>
</<?=$tag ?? "li"?>>
