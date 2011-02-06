<?php
	require("ohjournal.php");
	$raw = file_get_contents("php://stdin");
	if($stdin == "")	$raw = $j->parseMailFile(System::getMail());
	if(trim($raw) == "" || $raw == false)	return false;
	$data = $j->parseEmail($raw);
	if($j->submitEntry($data[0], $data[1], $data[2], $data[3])){
		System::deleteMail($raw);
		return true;
	}
?>
