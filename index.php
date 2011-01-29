<?php
	$title = "Unlock your journal";
	require("ohjournal.php");
	//TODO: move to class function
	if($_POST['username'] && $_POST['password'] && $j->login($_POST['username'], $_POST['password'])){
		header("Location: ./read.php");
		die();
	} else if ($j->isLoggedIn()){
		header("Location: ./read.php");
		die();
	}
	require("header.php");
?>

<h2>Unlock <?php echo ( Config::$owner == "" ) ? "your" : Config::$owner . "'s"; ?> journal</h2>
	<div id="unlock">
		<form action="./" method="post">
			<input type="text" name="username" value="username" />
			<input type="password" name="password" value="password" />
			<input type="submit" name="submit" value="unlock" />
		</form>
	</div>
<?php require("footer.php"); ?>

