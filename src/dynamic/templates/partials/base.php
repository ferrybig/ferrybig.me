<!DOCTYPE html>
<html lang="en">
	<head> 
		<meta charset="utf-8"> 
		<meta content="IE=edge" http-equiv="X-UA-Compatible"> 
		<meta content="width=device-width,initial-scale=1" name="viewport"> 
		<title><?= htmlentities($real_title = (isset($title) ? $title . " - " : (isset($pages) ? end($pages) . " - " : "")) . $config->title) ?></title>
		<link href="<?= $base ?? "./" ?>css/main.css" rel="stylesheet">
		<link rel="canonical" href="<?= htmlentities($realurl = includeToSitemap($config->baseurl . ($url ?? ""))) ?>">
		<base target="<?= $target ?? "_self" ?>">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="<?= $base ?? "./" ?>js/main.js"></script>
		<!--[if lt IE 9]> <script src=https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js></script><![endif]-->
		<meta name="author" content="Fernando van Loenhout">
		<meta property="og:url" content="<?= htmlentities($realurl) ?>">
		<?PHP if (!empty($image)) : ?>
			<meta property="og:image" content="<?= htmlentities($image) ?>">
		<?PHP endif; ?>
		<?PHP if (!empty($updated_at)) : ?>
			<meta property="og:updated_time" content="<?= htmlentities($updated_at) ?>">
		<?PHP endif; ?>
		<meta property="og:title" content="<?= htmlentities($real_title) ?>">
	</head>
	<?PHP if (!isset($no_container)) : ?>
		<body class="main_body">
			<div class="page-top-wrapper hidden-print">
				<nav class="navbar navbar-default">
					<div class="container visible-sm-block visible-md-block visible-lg-block">
						<div class="page-header page-brand">
							<h1>Ferrybig.me <small>The personal website of Fernando&nbsp;van&nbsp;Loenhout</small></h1>
						</div>
					</div>
					<div class="container">
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand visible-xs-block" href="#">Ferrybig.me</a>
						</div>

						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
							<ul class="nav navbar-nav nav-tabs">
								<li class="<?= $page == "index" ? "active" : "" ?>"><a href="<?= $base ?? "./" ?>">Home</a></li>
								<li class="<?= $page == "about" ? "active" : "" ?>"><a href="<?= $base ?? "./" ?>about.html">About me <span class="sr-only">(current)</span></a></li>
								<li class="<?= $page == "projects" ? "active" : "" ?>"><a href="<?= $base ?? "./" ?>projects/">Projects</a></li>
								<li class="<?= $page == "contact" ? "active" : "" ?>"><a href="<?= $base ?? "./" ?>contact.html">Contact me</a></li>
							</ul>
						</div><!-- /.navbar-collapse -->
					</div><!-- /.container-fluid -->
				</nav>
			</div>
			<div class="container">
				<?PHP if (isset($pages)) : ?>
					<ol class="breadcrumb">
						<?PHP foreach ($pages as $breadcrumb) : ?>
							<?PHP if (is_array($breadcrumb)) : ?>
								<li><a href="<?= htmlentities(($base ?? "") . $breadcrumb[0]) ?>"><?= htmlentities($breadcrumb[1]) ?></a></li>
							<?PHP else: ?>
								<li class="active"><?= htmlentities($breadcrumb) ?></li>
							<?PHP endif; ?>
						<?PHP endforeach; ?>
					</ol>
				<?PHP endif; ?>
				<?PHP extend_body() ?>
			</div>
		</body>
	<?PHP else: ?>
		<body>
			<div class="container-fluid">
				<?PHP extend_body() ?>
			</div>
		</body>
	<?PHP endif; ?>
</html>
