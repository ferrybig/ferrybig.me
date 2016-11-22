<div class="panel panel-default">
	<div class="panel-heading">
		<h2>
			<a href="">Recent contributions</a>
		</h2>
	</div>
	<div class="panel-body">
		<ol class="commit-list media-list">
			<?PHP foreach(loadUrlJson("https://api.github.com/users/ferrybig/events?per_page=7", EXPIRE_HOUR) as $event): ?>
				<li class="commit media">
					<div class="media-body">
						<h3 class="media-heading commit-name" style="font-size: 130%">
							<a href="<?= htmlentities(loadUrlJson($event->repo->url)->html_url) ?>">
								<?= htmlentities(loadUrlJson($event->repo->url)->full_name) ?>
							</a>
						</h3>
						<time datetime="<?= $event->created_at ?>"><?= $event->created_at ?></time>
						<?PHP if($event->type == "PushEvent"): ?>
							<p class="commit-description">
								<?PHP if(count($event->payload->commits) == 1): ?>
									Pushed 1 commit to
								<?PHP else: ?>
									Pushed <?= count($event->payload->commits) ?> commits to
								<?PHP endif; ?>
								<span class="label label-<?= get_branch_style($event->payload->ref) ?>">
									<?= htmlentities(format_branch_name($event->payload->ref)) ?>
								</span>
							</p>
							<ol class="commit-sublist">
								<?PHP foreach(limit($event->payload->commits, 5) as $commit): ?>
									<li>
										<?PHP if(loadUrlJson($commit->url)->author) : ?>
											<a href="<?= htmlentities(loadUrlJson($commit->url)->author->html_url) ?>">
												@<?= htmlentities(loadUrlJson($commit->url)->author->login) ?>
											</a>:
										<?PHP else: ?>
											<?= htmlentities($commit->author->name) ?>:
										<?PHP endif; ?>
										<a href="<?= htmlentities(loadUrlJson($commit->url)->html_url) ?>">
											<?= htmlentities(explode("\n",$commit->message)[0]) ?>
										</a>
									</li>
								<?PHP endforeach; ?>
								<?PHP if(has_limited($event->payload->commits, 5)) : ?>
									<li>
										<?= has_limited($event->payload->commits, 5) ?> more...
									</li>
								<?PHP endif; ?>
							</ol>
						<?PHP elseif($event->type == "PullRequestEvent"): ?>
							<p class="commit-description">
							</p>
						<?PHP elseif($event->type == "IssueCommentEvent"): ?>
							<p class="commit-description">
								Commented on an issue
							</p>
						<?PHP else: ?>
							<p class="commit-description">
								Unknown action: <?= $event->type ?>
							</p>
						<?PHP endif; ?>
					</div>
				</li>
			<?PHP endforeach; ?>
		</ol>
		<p>
			<a href="//github.com/ferrybig/" class="btn btn-primary">Open on Github</a>
		</p>
	</div>
</div>