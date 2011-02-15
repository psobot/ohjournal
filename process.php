<?php
	require_once("ohjournal.php");
	$raw = file_get_contents("php://stdin");
	if($raw == "")	$raw = $j->parseMailFile($j->getMail());
	var_dump($raw);
	if(trim($raw) == "" || $raw == false)	return false;
	$data = $j->parseEmail($raw);
	if($j->submitEntry($data[0], $data[1], $data[2], $data[3])){
		$j->deleteMail($raw);
		return true;
	}
?>
