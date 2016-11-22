<div class="panel panel-default">
	<div class="panel-heading">
		<h2>
			<a href="">Recent contributions</a>
		</h2>
	</div>
	<div class="panel-body">
		<ol class="commit-list media-list">
			<?PHP foreach (filter_git_events(loadUrlJson("https://api.github.com/users/ferrybig/events?per_page=7", EXPIRE_HOUR)) as $event): ?>
				<li class="commit media">
					<div class="media-body">
						<h3 class="media-heading commit-name" style="font-size: 130%">
							<a href="<?= htmlentities(loadUrlJson($event->repo->url)->html_url) ?>">
								<?= htmlentities(loadUrlJson($event->repo->url)->full_name) ?>
							</a>
						</h3>
						<time datetime="<?= $event->created_at ?>"><?= $event->created_at ?></time>
						<?PHP if ($event->type == "PushEvent"): ?>
							<p class="commit-description">
								<?PHP if (count($event->payload->commits) == 1): ?>
									Pushed 1 commit to
								<?PHP else: ?>
									Pushed <?= count($event->payload->commits) ?> commits to
								<?PHP endif; ?>
								<span class="label label-<?= get_branch_style($event->payload->ref) ?>">
									<?= htmlentities(format_branch_name($event->payload->ref)) ?>
								</span>
							</p>
							<ol class="commit-sublist">
								<?PHP foreach (limit($event->payload->commits, 5) as $commit): ?>
									<?PHP $commitObj = loadUrlJson($commit->url); ?>
									<li>
										<?PHP if ($commitObj->author) : ?>
											<a href="<?= htmlentities(loadUrlJson($commit->url)->author->html_url) ?>">
												@<?= htmlentities(loadUrlJson($commit->url)->author->login) ?>
											</a>:
										<?PHP else: ?>
											<?= htmlentities($commit->author->name) ?>:
										<?PHP endif; ?>
										<a href="<?= htmlentities($commitObj->html_url) ?>">
											<?= htmlentities(explode("\n", $commit->message)[0]) ?>
										</a>
										<small>
											<?PHP if ($commitObj->stats->additions) : ?>
												<span class="label label-success" title="<?= $commitObj->stats->additions ?> insertions">
													<?= $commitObj->stats->additions ?>
													<span class="glyphicon glyphicon-import"></span>
												</span>
											<?PHP endif; ?>
											<?PHP if ($commitObj->stats->deletions) : ?>
												<span class="label label-danger" title="<?= $commitObj->stats->deletions ?> deletions">
													<?= $commitObj->stats->deletions ?>
													<span class="glyphicon glyphicon-export"></span>
												</span>
											<?PHP endif; ?>
										</small>
									</li>
								<?PHP endforeach; ?>
								<?PHP if (has_limited($event->payload->commits, 5)) : ?>
									<li>
										<?= has_limited($event->payload->commits, 5) ?> more...
									</li>
								<?PHP endif; ?>
							</ol>
						<?PHP elseif ($event->type == "PullRequestEvent"): ?>
							<p class="commit-description">
								<?PHP if ($event->payload->action == "closed" && $event->payload->pull_request->merged == true) : ?>
									Merged pull request:
								<?PHP elseif ($event->payload->action == "closed") : ?>
									Closed pull request:
								<?PHP elseif ($event->payload->action == "opened") : ?>
									Opened pull request:
								<?PHP else: ?>
									???
								<?PHP endif; ?>
								<a href="<?= htmlentities($event->payload->pull_request->html_url) ?>">
									#<?= htmlentities($event->payload->pull_request->number) ?>
									: <?= htmlentities($event->payload->pull_request->title) ?>
								</a>
							</p>
						<?PHP elseif ($event->type == "IssueCommentEvent"): ?>
							<p class="commit-description">
								@Ferrybig: Commented on: 
								<a href="<?= htmlentities($event->payload->comment->html_url) ?>">
									#<?= htmlentities($event->payload->issue->number) ?>
									: <?= htmlentities($event->payload->issue->title) ?>
								</a>
							</p>
						<?PHP elseif ($event->type == "CreateEvent"): ?>
							<p class="commit-description">
								<?PHP if ($event->payload->ref_type == "repository") : ?>
									Created repository
								<?PHP elseif ($event->payload->ref_type == "branch") : ?>
									Created branch
									<span class="label label-<?= get_branch_style("refs/heads/" . $event->payload->ref) ?>">
										<?= htmlentities($event->payload->ref) ?>
									</span>
								<?PHP elseif ($event->payload->ref_type == "tag") : ?>
									Created tag
									<span class="label label-<?= get_branch_style("refs/tags/" . $event->payload->ref) ?>">
										<?= htmlentities($event->payload->ref) ?>
									</span>
								<?PHP else: ?>
									Unknown create event
								<?PHP endif; ?>
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
	</div>
</div>