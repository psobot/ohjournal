<?php
	$title = "Unlock your journal";
	require("ohjournal.php");
	//TODO: move to class function
	if($_POST['password'] && $j->login($_POST['password'])){
		if(Config::$webRead)header("Location: ./read.php");
		else header("Location: ./write.php");
		die();
	} else if ($j->isLoggedIn() && Config::$webRead){
		if(Config::$webRead)header("Location: ./read.php");
		else header("Location: ./write.php");
		die();
	}
	require("header.php");
?>

<h2>Unlock <?php echo ( Config::$owner == "" ) ? "your" : Config::$owner . "'s"; ?> journal</h2>
	<div id="unlock">
		<form action="./" method="post">
			<input type="text" name="password" value="password" id="passwordHolder" />
			<input type="password" name="password" value="password" id="password" />
			<input type="submit" name="submit" value="unlock" />
		</form>
	</div>
<?php require("footer.php"); ?>

