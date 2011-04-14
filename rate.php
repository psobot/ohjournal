<?php
	require_once("ohjournal.php");
	$j->protect();
	//TODO: move to class function
	if(	isset($_POST['date']) && 
		isset($_POST['rating']) &&
		$j->rateEntry($_POST['date'], $_POST['rating'])	){
		die("true");
	} else die("false");
