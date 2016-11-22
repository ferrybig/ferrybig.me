<?= extend("base.php"); ?>
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2>About me</h2>
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
							<th>Sex</th>
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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2>Recent projects</h2>
			</div>
			<div class="panel-body">
				<ul class="project-list media-list">
					<li class="project media">
						<div class="media-left">
							<a href="projects/NeuralNetwork">
								<img class="media-object" src="projects/images/NeuralNetwork/0-s.png" alt="">
							</a>
						</div>
						<div class="media-body">
							<h3 class="media-heading project-name" style="font-size: 130%">
								<a href="projects/NeuralNetwork">NeuralNetwork</a>
							</h3>
							<p class="project-description">
								A neural network implentation for java
							</p>
						</div>
					</li>
				</ul>
				<p>
					<a href="about.html" class="btn btn-primary">
						Open <em>project list</em>
					</a>
				</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<?PHP include "github_contributions.php" ?>
	</div>
</div>