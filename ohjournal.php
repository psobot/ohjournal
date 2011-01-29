<?php
	session_start();
	require("config.php");
	date_default_timezone_set("America/Toronto");
	class NumberToWord {
		public static function toWords($number, $uppercase = false){
			$numbers = array(	"zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine",
				"ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "ninteen");
			if ($number > 0 && $number < 20)		$number = $numbers[$number];
			else if ($number > 20 && $number < 30)	$number = "twenty-" . $numbers[$number-20]; 
			else if ($number > 30 && $number < 40)$number = "thirty-" . $numbers[$number-30];  
			return ($uppercase ? ucwords($number) : $number);
		}
	}

	class Frontend {
	//	public $
	}

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
				if($diff < 60) return $diff.' seconds'; 
				elseif($diff < 120)	return '1 minute'; 
				elseif($diff < 3600) return NumberToWord::toWords(floor($diff/60)) . ' minutes'; 
				elseif($diff < 7200) return '1 hour'; 
				elseif($diff < 86400) return NumberToWord::toWords(floor($diff/3600)) . ' hours'; 
			} elseif($dayDiff == 1) return 'yesterday';
			elseif($dayDiff < 7) return NumberToWord::toWords($dayDiff) . ' days'; 
			elseif($dayDiff == 7) return '1 week'; 
			elseif($dayDiff < (7*6)) return "About " . NumberToWord::toWords(ceil($dayDiff/7)) . ' weeks'; 
			elseif($dayDiff < 365) return "About " . NumberToWord::toWords(ceil($dayDiff/(365/12))) . ' months'; 
			else { 
				$years =  NumberToWord::toWords(round($dayDiff/365)); 
				return $years . ' year' . ($years != 1 ? 's' : ''); 
			} 
		} 
	}
	class Journal{
		private $db = null;
		private $loggedIn = false;

		function __construct($database){
			$this->db = $this->db($database);
		}
		function __destruct(){
			$this->db()->close();
		}
		private function db($database = NULL){
			if($database == NULL) $database = Config::$dbFile;
			return new SQLite3(dirname(__FILE__)."/".$database);
		}
		public function login($username, $password){
			$query = "select count(*) as login from ".Config::$tblUser." where username = '$username' and password = '".md5($password)."'";
			$r = $this->db()->query($query);
			$a = $r->fetchArray();
			$this->loggedIn = $a[0] == true;
			return $a[0] == true;
		}
		public function isLoggedIn(){
			return $this->loggedIn;
		}
		public function protect(){
			if(!$this->loggedIn) header("Location: ./");
			return !$this->loggedIn;
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
			$r = $this->db()->query($query);
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
			$entry = $this->db()->querySingle("select * from ".Config::$tblEntries." where reflected = 0 order by random() limit 1", true);
			if($entry){
				$this->db()->query("update ".Config::$tblEntries." set reflected = 1 where id = ".$entry['id']);
				return $entry;
			} else return false;
		}
		public function getAllEntries(){
			$q = $this->db()->query("select * from ".Config::$tblEntries." order by sent desc");
			while($row = $q->fetchArray()){$rows[] = $row;}
			return $rows;
		}
		public function getUniqueMonths(){
			$q = $this->db()->query("select sent from ".Config::$tblEntries." order by sent desc");
			while($row = $q->fetchArray()){$rows[date("Y-n", strtotime($row['sent']))] = date("F Y", strtotime($row['sent']));}
			return $rows;
		}
		/*
		 *	Gets latest mail to the user by reading user's mail file
		 *	Usually in /var/mail/username, but can be set in config
		 *	This is, by all accounts, a dirty hack
		 *	Triggering an event on postfix receive is much, much better.
		 *
		 *	Returns the raw source of the email.
		 */
		public function getMailFile(){
			$data = file_get_contents(Config::$mailFile);
			if($data == NULL || trim($data) == "") return false;
			return $data;
		}

		/*
		 *	Clears out the user's mailbox file.
		 *	Yes, you read that right.
		 *	Probably shouldn't be doing this. Could cause major problems.
		 *	Should change this to only delete the message passed in.
		 */
		public function deleteMail($mail){
			$f = fopen(Config::$mailFile, 'w');
			fwrite($f, "");
			fclose($f);
			return (trim(file_get_contents(Config::$mailboxes.Config::$mailUser)) == "");
		}

		/*
		 *	Grabs only the latest email from your email address.
		 *	Could be improved by checking more in the headers for the string "ohJournal."
		 *
		 */
		public function parseMailFile($raw){
			$m = preg_match("/^From ".str_replace(".", "\.", Config::$yourEmail).".+([\s\S]+?)^From /", $raw, $matches);
			return trim($matches[1]);
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
			$body = trim(quoted_printable_decode(preg_replace("/=[\n\r]+/", "", trim($body[1]))));

			return array($sendDate, $receiveDate, $header, $body);
		}

		/*
		 *	The worker function to receive new mail into the database.
		 *
		 */
		public function addEntry(){
			$raw = $this->parseMailFile($this->getMailFile());
			if(trim($raw) == "" || $raw == false)	return false;
			$data = $this->parseEmail($raw);
			if($this->submitEntry($data[0], $data[1], $data[2], $data[3])){
				$this->deleteMail($raw);
				return true;
			}
		}

		public function sendDaily(){
			date_default_timezone_set("America/Toronto");				//set your timezone here if this is incorrect
			$subject	=	"It's " . date("l, F jS") . " - How did your day go?";
			$body		=	"Just respond to this email with your entry, and it will be saved automagically.";
			$past		= 	$this->getRandomEntry();
			if($past != false)
				$body .= 	"\r\n\r\n\r\n".
							Config::$rememberText." ".
							Date_Difference::getStringResolved($past['received']).' ago'.
							" (on ".
							date("l, F jS, Y", strtotime($past['received'])).
							") you wrote:\r\n\r\n".
							$past['entry'];
			else $body .= "\r\n\r\n\r\nThere are no past journal entries to show you... so get writing!";
			$headers	=	'From: OhJournal <'.Config::$fromEmail.'>' . "\r\n" .
							'Reply-To: ' . Config::$serverEmail . "\r\n" .
							'X-Mailer: OhJournal from user <'.trim(shell_exec("whoami")).'> on PHP/' . phpversion();
			return mail(Config::$yourEmail, $subject, $body, $headers);
		}
	}
	if(isset($_SESSION['journal']))	$j = $_SESSION['journal'];
	else {
		$j = new Journal(Config::$dbFile);
		$_SESSION['journal'] = $j;
	}
?>
