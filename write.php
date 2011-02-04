<?php
	require("ohjournal.php");
	$title = "New Entry";
	$j->protect();
	//TODO: move to class function
	if(	isset($_POST['body']) && 
		trim($_POST['body']) != "" && 
		$j->submitEntry(date("U"), date("U"), "Submitted online with OhJournal.", $_POST['body'])){
		header("Location: ./read.php");
		die();
	}
	require("header.php");
?>

<h2><?php echo $title; ?></h2>
	<div id="new">
		<h4>It's <?php echo date(Config::$webDate); ?>. What's on your mind?</h4>
		<form action="" method="post">
			<textarea name="body" id="body"></textarea>
			<input type="submit" name="submit" value="save" />
		</form>
	</div>
<?php require("footer.php"); ?>


