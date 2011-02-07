<?php
	require_once("ohjournal.php");
	$title = "Unlock " . (( $j->config->owner == "" ) ? "your" : TextHelper::possessive($j->config->owner)) . " journal.";
	require_once("header.php");
?>

<h2><?php echo $title; ?></h2>
	<div id="unlock">
		<form action="./" method="post">
			<input type="text" name="password" value="password" id="passwordHolder" />
			<input type="password" name="password" value="password" id="password" />
			<input type="submit" name="submit" value="unlock" />
		</form>
	</div>
<?php require_once("footer.php"); ?>
