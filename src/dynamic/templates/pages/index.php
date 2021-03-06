<?= extend(__DIR__ . "/../partials/base.php", ["url" => "", "page" => "index"]); ?>
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title panel-title-lg" property="http://purl.org/dc/terms/title">About me</h2>
			</div>
			<div class="panel-body">
				<div class="thumbnail">
					<img src="images/myself.jpg" alt="...">
				</div>
				<div class="caption">
					<table class="table table-condensed">
						<tr>
							<th>Country</th>
							<td>Netherlands</td>
						</tr>
						<tr>
							<th>Birthday</th>
							<td><time datetime="1995-11-19">19 november 1995</time></td>
						</tr>
						<tr>
							<th>Gender</th>
							<td>Male</td>
						</tr>
					</table>
				</div>
				<p>
					<a href="about.html" class="btn btn-primary">
						Open <em>about me</em>
					</a>
				</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<?PHP include __DIR__ . "/../partials/projects.php" ?>
	</div>
	<div class="col-md-4">
		<?PHP include __DIR__ . "/../partials/github.php" ?>
	</div>
</div>
