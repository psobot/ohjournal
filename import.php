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
	date_default_timezone_set("America/Toronto");			//set your timezone here if this is incorrect

	$f = file_get_contents("/var/www/journal.petersobot.com/ol.txt");
	//var_dump($f);
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
		$date = strtotime($entry[0]);
		unset($entry[0]);

		$email		=	"youremail+ohliferesponse@gmail.com";	//the email you want to save your journal entries at
		$subject	=	"Re: It's " . date("l, F j", $date) . " - How did your day go?";
		$body		=	implode($entry, "\n") . "\n\n\n(imported from OhLife)";
		$headers	=	'From: OhJournal Import <ohjournal@petersobot.com>' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
		mail($email, $subject, $body, $headers);
		sleep(1);	//Delay to ensure all messages make it out properly.
	}
?>
