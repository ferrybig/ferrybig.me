<!DOCTYPE html>
<html lang="en">
	<head> 
		<meta charset="utf-8"> 
		<meta content="IE=edge" http-equiv="X-UA-Compatible"> 
		<meta content="width=device-width,initial-scale=1" name="viewport"> 
		<title>ferrybig.me</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="css/custom.css" rel="stylesheet">
		<link rel="canonical" href="<?= $config['baseurl'] . $url ?>">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<!--[if lt IE 9]> <script src=https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js></script><![endif]--> 
	</head>
	<body>
		<div class="page-top-wrapper hidden-print">
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="#">Ferrybig.me</a>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li class="active"><a href="./">Home</a></li>
							<li><a href="about.html">About me <span class="sr-only">(current)</span></a></li>
							<li><a href="projects/">Projects</a></li>
							<li><a href="contact.html">Contact me</a></li>
						</ul>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
		</div>
		<div class="container">
			<div class="page-header hidden-print">
				<h1>Ferrybig.me <small>The personal website of Fernando&nbsp;van&nbsp;Loenhout</small></h1>
			</div>
			<?PHP extend_body() ?>
		</div>
	</body>
</html>
