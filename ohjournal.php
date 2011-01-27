<?php
	require("config.php");
	class Date_Difference {
		/**
		 *	Converts a timestamp to pretty human-readable format.
		 * 
		 *	Original JavaScript Created By John Resig (jquery.com)  Copyright (c) 2008
		 *	Copyright (c) 2008 John Resig (jquery.com)
		 *	Licensed under the MIT license.
		 *	Ported to PHP >= 5.1 by Zach Leatherman (zachleat.com)
		 *
		 */
		public static function getStringResolved($date, $compareTo = NULL) { 
			if(!is_null($compareTo)) $compareTo = new DateTime($compareTo); 
			return self::getString(new DateTime($date), $compareTo); 
		} 
	
		public static function getString(DateTime $date, DateTime $compareTo = NULL) { 
			if(is_null($compareTo))	$compareTo = new DateTime('now'); 
			$diff = $compareTo->format('U') - $date->format('U'); 
			$dayDiff = floor($diff / 86400); 
	
			if(is_nan($dayDiff) || $dayDiff < 0) return ''; 
					 
			if($dayDiff == 0) { 
				if($diff < 60) return 'just now'; 
				elseif($diff < 120)	return '1 minute ago'; 
				elseif($diff < 3600) return floor($diff/60) . ' minutes ago'; 
				elseif($diff < 7200) return '1 hour ago'; 
				elseif($diff < 86400) return floor($diff/3600) . ' hours ago'; 
			} elseif($dayDiff == 1) return 'yesterday'; 
			elseif($dayDiff < 7) return $dayDiff . ' days ago'; 
			elseif($dayDiff == 7) return '1 week ago'; 
			elseif($dayDiff < (7*6)) return ceil($dayDiff/7) . ' weeks ago'; 
			elseif($dayDiff < 365) return ceil($dayDiff/(365/12)) . ' months ago'; 
			else { 
				$years = round($dayDiff/365); 
				return $years . ' year' . ($years != 1 ? 's' : '') . ' ago'; 
			} 
		} 
	}
	class Journal{
		public $db = null;

		function __construct($database){
			$this->db = new SQLite3(dirname(__FILE__).$database);
		}
		function __destruct(){
			$this->db->close();
		}
		/*
		 *	Adds a Journal entry to the DB.
		 *	Returns through from DB call. (false on error)
		 *
		 */
		public function submitEntry($sendDate, $receiveDate, $header, $body){
			$query = 'insert into '.Config::$tblEntries.' values(
										NULL,
										datetime('.$sendDate.', \'unixepoch\'), 
										datetime('.$receiveDate.', \'unixepoch\'), 
										"'.htmlentities($header).'", 
										"'.htmlentities($body).'",
										0)';
			$r = $this->db->query($query);
			if ($r == false) var_dump($query);
			return $r;
		}

		/*
		 *	Gets a random journal entry that hasn't been reflected at the user yet.
		 *	Used for peeks back at the past.
		 *	After grabbing a row, it automatically sets its "reflected" flag to 1 (true)
		 *	Returns array of row data or false on no historical rows left.
		 *
		 */
		public function getRandomEntry(){
			$entry = $this->db->querySingle("select * from ".Config::$tblEntries." where reflected = 0 order by random() limit 1", true);
			if($entry){
				$this->db->query("update ".Config::$tblEntries." set reflected = 1 where id = ".$entry['id']);
				return $entry;
			} else return false;
		}

		/*
		 *	Gets latest mail by reading user's mail file
		 *	Usually in /var/mail/username, but can be set in config
		 *	This is, by all accounts, a dirty hack
		 *	Triggering an event on postfix receive is much, much better.
		 *	This also currently assumes that only one email exists in the mail file.
		 *
		 *	Returns the raw source of the email.
		 */
		public function getMail(){
			$data = file_get_contents(Config::$mailboxes.Config::$mailUser);
			if($data == NULL || trim($data) == "") return false;
			return $data;
		}

		/*
		 *	Clears out the user's mailbox file.
		 *	Yes, you read that right.
		 *	Probably shouldn't be doing this. Could cause major problems.
		 */
		public function clearMail(){
			$f = fopen(Config::$mailboxes.Config::$mailUser, 'w');
			fwrite($f, "");
			fclose($f);
			return (trim(file_get_contents(Config::$mailboxes.Config::$mailUser)) == "");
		}


		/*
		 *	Parses raw email data for important fields.
		 *	Returns an array of (send timestamp, receive timestamp, header string, body string).
		 */		
		public function parseEmail($raw){
			$rawMessage = explode("\n\n", $raw, 2);
			$header = trim($rawMessage[0]);
			$body = trim($rawMessage[1]);

			preg_match("%Date: (.+?)\n%", $header, $date);						//Parse date
			$receiveDate = strtotime($date[1]);

			//On Jan 24, 2011, at 8:00 PM, OhJournal wrote:
			preg_match("%On (.+?), at (.+?), OhJournal%", $body, $sendDate);	//Parse send date & time from reply line
			$sendDate = strtotime($sendDate[1]." ".$sendDate[2]);

			preg_match("%^([\s\S]+?)On .{3} \d{2}, \d{4}, at %", $body, $body);	//Remove previous email from what's being saved
			$body = trim($body[1]);

			return array($sendDate, $receiveDate, $header, $body);
		}

		/*
		 *	The worker function to receive new mail into the database.
		 *
		 */
		public function addEntry(){
			$raw = $this->getMail();
			if(trim($raw) == "" || $raw == false)	return false;
			$data = $this->parseEmail($raw);
			//$this->forward(Config::$forwardAddress, $data);
			if($this->submitEntry($data[0], $data[1], $data[2], $data[3])){
				$this->clearMail();
				return true;
			}
		}

		public function sendDaily(){
			date_default_timezone_set("America/Toronto");				//set your timezone here if this is incorrect
			$subject	=	"It's " . date("l, F j") . " - How did your day go?";
			$body		=	"Just respond to this email with your entry, and it will be saved automagically.";
			$past		= 	$this->getRandomEntry();
			if($past != false)
				$body .= 	"\r\n\r\n\r\n".
							Date_Difference::getStringResolved($past['received']).
							" (on ".
							date("l, F j, Y", strtotime($past['received'])).
							") you wrote:\r\n\r\n".
							$past['entry'];

			$headers	=	'From: OhJournal <'.Config::$fromEmail.'>' . "\r\n" .
							'Reply-To: ' . Config::$serverEmail . "\r\n" .
							'X-Mailer: OhJournal from user <'.trim(shell_exec("whoami")).'> on PHP/' . phpversion();
			//var_dump(Config::$yourEmail, $subject, $body, $headers);
			return mail(Config::$yourEmail, $subject, $body, $headers);
		}
	}
	$j = new Journal(Config::$dbFile);
?>
