<?= extend(__DIR__ . "/../modules/base.php", [
	'url' => "contact.html", 
	"page" => "contact",
	"pages" => [["./", "Home"], "Contact"],
]); ?>
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title panel-title-lg">Contacts</h2>
			</div>
			<div class="panel-body">
				<p>
					If you have any questions, you could mail me on 
					<a href="mailto:mailmehere@ferrybig.me">mailmehere@ferrybig.me</a>
					(Mail is subjected to change if it attracts to many spam,
						if I reply, you will get my orginal mail address)
				</p>
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
