<?php
	/*
	 *	OhJournal - open-source replacement for OhLife
	 *	http://github.com/psobot/ohjournal
	 *
	 *	by Peter Sobot <hi@petersobot.com>
	 *
	 *	This script will take in a text file and send you emails (in chronological order) of your OhLife entries.
	 *	One could call it a rudiementary "import" script.
	 *	It's retardedly inefficient, but it works.
	 *
	 ********************************************************
	 *	BE CAREFUL TO ADD THE PROPER EMAIL ADDRESS!!!!!!!!!
	 *	Otherwise someone else (possibly me) will get your entries.
	 *	As they're supposed to be somewhat private, be careful.
	 ********************************************************
	 *
	 */

	require("./ohjournal.php");
	date_default_timezone_set("America/Toronto");			//set your timezone here if this is incorrect
	$j = new Journal($j->config->dbFile);

	$f = file_get_contents("./ohlife_20101209.txt");
	$lines = explode("\n", $f);
	$lines[] = "9999-99-99";
	$entries = array();
	$buffer = array();

	foreach($lines as $line){
		if(preg_match("/(\d{4}-\d{2}-\d{2})/", $line)){
			if(count($buffer) != 0)	$entries[] = $buffer;
			$buffer = array();
		}
		if(trim($line) != "")	$buffer[] = $line;
	}

	$entries = array_reverse($entries);

	foreach($entries as $entry){
		$date = $entry[0];
		unset($entry[0]);
		$j->submitEntry(strtotime($date), strtotime($date), "Imported from OhLife text dump.", implode("\n", $entry));
	}

	$f = file_get_contents("own.txt");
	$f = preg_replace("%=\n%", "", $f);
	$entries = explode("From psobot@", $f);
	$entries = array_reverse($entries);
	foreach($entries as $entry){
		if($entry != ""){
			$entry = "From psobot@".$entry;
			$entry = $j->parseEmail(trim($entry));
			var_dump($j->submitEntry($entry[0], $entry[1], $entry[2], $entry[3]));
		}
	}

?>
