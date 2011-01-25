<?php
	$user = trim(shell_exec('whoami'));
	$data = file_get_contents("/var/mail/$user");
	if($data == NULL) die("Mailbox is empty.");
	$rawMessage = explode("\n\n", $data, 2);

	$header = trim($rawMessage[0]);
	$body = trim($rawMessage[1]);
	preg_match("%Date: (.+?)\n%", $header, $date);
	preg_match("%Subject: (.+?)\n%", $header, $subject);
	$receiveDate = strtotime($date[1]);

	//On Jan 24, 2011, at 8:00 PM, OhJournal wrote:
	preg_match("%On (.+?), at (.+?), OhJournal%", $body, $sendDate);
	$sendDate = strtotime($sendDate[1]." ".$sendDate[2]);
	preg_match("%^([\s\S]+?)On .{3} \d{2}, \d{4}, at %", $body, $body);
	$body = trim($body[1]);

/*	
 *	All times in GMT, should be corrected for display.
 */

	$db = new SQLite3('journal.db');
	$db->query('insert into entries values(datetime('.$sendDate.', \'unixepoch\'), datetime('.$receiveDate.', \'unixepoch\'), "'.$header.'", "'.$body.'")');
?>
