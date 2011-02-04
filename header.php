<?php $j->logVisit(); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php echo ( Config::$owner == "" ) ? "" : Config::$owner . "'s "; ?>
			OhJournal
			<?php echo ( $title == "" ) ? "" : " | $title"; ?>
		</title>
		<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css' />
		<link href='style.css' rel='stylesheet' type='text/css' />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.min.js"></script>
		<script type="text/javascript" src="dynamic.js"></script>
	</head>
	<body>
		<div id="menu">
			<div id="menu_content">
				<div id="logo"><h1>OhJournal</h1></div>
				<div id="items">
					<ul>
						<?php if($j->isLoggedIn()){ ?>
						<?php if(Config::$webRead){ ?><li class="read"><a href="./read.php">read</a></li><?php } ?>
						<li class="write"><a href="./write.php">write</a></li>
						<li class="settings"><a href="./manage.php">manage</a></li>
						<li class="lock"><a href="./lock.php">lock</a></li>
						<?php } ?>
					</ul>
				</div>
				<div id="contact">developed by <a href="http://www.petersobot.com">Peter Sobot</a></div>
			</div>
			<div id="footer"></div>
		</div>
		<div id="content">

