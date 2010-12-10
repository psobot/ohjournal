<?php
	
	/*
	 *	OhJournal - open-source replacement for OhLife
	 *	http://github.com/psobot/ohjournal
	 *
	 *	by Peter Sobot <hi@petersobot.com>
	 */

	date_default_timezone_set("America/Toronto");		//set your timezone here if this is incorrect
	$yourEmail	=	"psobot+ohjournal@gmail.com";			//the email you want to recieve your journal prompts to
	$saveEmail	=	"psobot+ohjournalresponse@gmail.com";	//the email you want to save your journal entries at
	$subject	=	"It's " . date("l, F n") . " - How did your day go?";
	$body		=	"Just respond to this email with your entry, and it will be saved automagically.";
	$headers	=	'From: OhJournal <ohjournal@petersobot.com>' . "\r\n" .
   					'Reply-To: ' . $saveEmail . "\r\n" .
    				'X-Mailer: PHP/' . phpversion();

	mail($yourEmail, $subject, $body, $headers);
?>
