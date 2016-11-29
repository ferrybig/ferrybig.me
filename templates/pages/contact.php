<?= extend(__DIR__ . "/../modules/base.php", ['url' => "contact.html", "page" => "contact"]); ?>
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2>Contacts</h2>
			</div>
			<div class="panel-body">
				This page is unsuported at this time... Check again soon...
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
