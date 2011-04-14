<?php //$j->logVisit(); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php echo ( $j->config->owner == "" ) ? "" : $j->config->owner . "'s "; ?>
			OhJournal
			<?php echo ( $title == "" ) ? "" : " | $title"; ?>
		</title>
		<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css' />
		<link href='js/ui-lightness/jquery-ui-1.8.11.custom.css' rel='stylesheet' type='text/css' />
		<link href='style.css' rel='stylesheet' type='text/css' />
		<link href='favicon.png' rel="icon" type="image/png" />
		<link href='apple-touch-icon.png' rel="apple-touch-icon" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ohjournal.js"></script>
		<script type="text/javascript" src="js/highcharts.js"></script>
		<?php if($page == 'stats'): ?><script type="text/javascript" src="js/stats.js"></script><?php endif; ?>
	</head>
	<body>
		<div id="menu">
			<div id="menu_content">
				<div id="logo"><a href="./"><h1>OhJournal</h1></a></div>
				<div id="items">
					<ul>
						<?php if($j->isLoggedIn()){ ?>
						<?php if($j->config->webRead){ ?><li class="read"><a href="./read">read</a></li><?php } ?>
						<li class="write"><a href="./write">write</a></li>
						<li class="settings"><a href="./manage">manage</a></li>
						<?php if($j->config->webRead){ ?><li class="stats"><a href="./stats">stats</a></li><?php } ?>
						<li class="lock"><a href="./lock">lock</a></li>
						<?php } ?>
					</ul>
				</div>
				<div id="contact">developed by <a href="http://www.petersobot.com">Peter Sobot</a></div>
			</div>
			<div id="footer">
				<div id="version">
					version <?php echo $version; ?>
				</div>
			</div>
		</div>
		<div id="content">

