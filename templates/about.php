<?= extend("base.php", ['url' => "about.html", "page" => "about"]); ?>
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2>About me</h2>
			</div>
			<div class="panel-body section-about-me">
				<div class="row">
					<div class="col-md-8">
						<p>
							Hello, I'm Fernando&nbsp;van&nbsp;Loenhout, and
							I'm a backend developer. I like to program using
							object oriented languages, like Java. When I
							program, I try to keep my code clear and 
							structured, for easier changes when I revisit
							the projects.
						</p>
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
					<aside class="col-md-4">
						<div class="thumbnail">
							<img src="images/myself.jpg" alt="...">
						</div>
					</aside>
				</div>
				<h3>Online presence</h3>
				<dl class="dl-horizontal">
					<dt>StackOverflow</dt>
					<dd>
						<a href="//stackexchange.com/users/1677570">
							<img src="//stackexchange.com/users/flair/1677570.png" width="208" height="58"
								 alt="profile for Ferrybig on Stack Exchange, a network of free, community-driven Q&amp;A sites"
								 title="profile for Ferrybig on Stack Exchange, a network of free, community-driven Q&amp;A sites">
						</a>
					</dd>
					<dt>Github</dt>
					<dd>
						<a href="//github.com/ferrybig">Ferrybig</a>
					</dd>
					<dt>Twitter</dt>
					<dd>
						<a href="https://twitter.com/ferrybig3">@Ferrybig3</a>
					</dd>
				</dl>
				<h3>Timeline</h3>
				<ol class='timeline'>
					<li class='timeline-point left clear'>
						<h4>
							<a href="//www.idcollege.nl/">Vocational school: ID&nbsp;College</a>
							<small>
								<time datetime="2014-09">September 2014</time> - <time datetime="2017-07">August 2017</time>
							</small>
						</h4>
						<p>
							This was a study on the course <em>Application Developer 4</em>
						</p>
					</li>
					<li class='timeline-point right'>
						<h4>
							<a href="//autoboekjes.nl/">Stage: autoboekjes.nl</a>
							<small>
								<time datetime="2015-11">November 2015</time> - <time datetime="2016-05">May 2016</time>
							</small>
						</h4>
						<p>
							In this stage, I helped with managing
							the existing system, and helped fixing
							bugs in the existing systems.
						</p>
					</li>
					<li class="timeline-point left clear">
						<h4>
							<a href="//www.albeda.nl/">Vocational school: Albeda&nbsp;College</a>
							<small>
								<time datetime="2012-09">September 2012</time> - <time datetime="2014-07">July 2014</time>
							</small>
						</h4>
						<p>
							This was a study on the course <em>System Administrator 3</em>
						</p>
					</li>
					<li class='timeline-point right'>
						<h4>
							<a href="//www.richit.com.au/">Stage: Rich&nbsp;IT&nbsp;Solutions</a>
							<small>
								<time datetime="2014-05">May 2014</time> - <time datetime="2014-07">July 2014</time>
							</small>
						</h4>
						<p>
							In this stage, we had a project to give
							lessons to people who were inexpierenced
							with computers. This was a great
							learning expierence for me.
						</p>
					</li>
					<li class='timeline-point right'>
						<h4>
							<a href="//www.mkb-expert.nl/">Stage: MKB-Expert</a>
							<small>
								<time datetime="2013-09">September 2013</time> - <time datetime="2014-05">May 2014</time>
							</small>
						</h4>
						<p>
							In this stage, I was asked to help the
							company with support line calls (from
							companies we gave support to), and help
							those companies if there were problems.
						</p>
					</li>
					<li class="timeline-point left clear">
						<h4>
							<a href="//www.pleysier.nl/westerbeek">High school: Pleysier&nbsp;Westerbeek&nbsp;College</a>
							<small>
								<time datetime="2008-09">September 2008</time> - <time datetime="2012-07">July 2012</time>
							</small>
						</h4>
						<p>
							This was my first high school, here I got a diploma for VMBO.
						</p>
					</li>
				</ol>
			</div>
		</div>
	</div>
	<aside class="col-md-4 hidden-print">
		<div class="row">
			<div class="col-sm-6 col-md-12">
				<?PHP include "projects.php" ?>
			</div>
			<div class="col-sm-6 col-md-12">
				<?PHP include "github.php" ?>
			</div>
		</div>
	</aside>
</div>
