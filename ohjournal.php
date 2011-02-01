<?php
	session_start();
	require("config.php");
	require("helpers.php");
	class Journal{
		private $db = null;

		function __construct($database){
			if($database == NULL) $database = Config::$dbFile;
			$this->db = new SQLite3(dirname(__FILE__)."/".$database);
		}
		function __destruct(){
			$this->db->close();
		}
		public function login($password){
			if(!System::isAllowedIP($_SERVER['REMOTE_ADDR'])) return false;
			$query = "select count(*) as login from ".Config::$tblUser." where password = '".md5($password)."'";
			$r = $this->db->query($query);
			$a = $r->fetchArray();
			$_SESSION['loggedin'] = $a[0] == 1;
			return $a[0] == 1;
		}
		public function protect($var = true){
			if(!$this->isLoggedIn() || !$var) header("Location: ./");
			return !$this->isLoggedIn();
		}
		public function isLoggedIn(){
			return $_SESSION['loggedin'];
		}
		public function logVisit(){
			$stmt = $this->db->prepare('insert into '.Config::$tblVisits.' 
										values(NULL, datetime("now"), :ip, :page, :loggedin)');
			$stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
			$stmt->bindValue(':page', $_SERVER['PHP_SELF']);
			$stmt->bindValue(':loggedin', ($this->isLoggedIn() ? 1 : 0));
			$r = $stmt->execute();
			if ($r == false) var_dump($query);
			return $r;			
		}
		/*
		 *	Adds a Journal entry to the DB.
		 *	Returns through from DB call. (false on error)
		 *
		 */
		public function submitEntry($sendDate, $receiveDate, $header, $body){
			$stmt = $this->db->prepare('insert into '.Config::$tblEntries.' 
										values(NULL, datetime(:send, "unixepoch"), datetime(:receieve, "unixepoch"), :header, :body, 0)');
			$stmt->bindValue(':send', $sendDate);
			$stmt->bindValue(':receive', $receiveDate);
			$stmt->bindValue(':header', htmlentities($header));
			$stmt->bindValue(':body', htmlentities($body));
			$r = $stmt->execute();
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
		public function getAllEntries(){
			$q = $this->db->query("select * from ".Config::$tblEntries." order by sent desc");
			while($row = $q->fetchArray()){$rows[] = $row;}
			return $rows;
		}
		public function getAllDailyEntries(){
			$rows = $this->getAllEntries();
			foreach($rows as $key => $row){
				var_dump($row['sent'] . " " . $row['received']);
			}
			return $rows;
		}
		public function getUniqueMonths(){
			$q = $this->db->query("select sent from ".Config::$tblEntries." order by sent desc");
			while($row = $q->fetchArray()){$rows[date("Y-n", strtotime($row['sent']))] = date("F Y", strtotime($row['sent']));}
			return $rows;
		}

		/*
		 *	Grabs only the latest email from your email address.
		 *	Could be improved by checking more in the headers for the string "ohJournal."
		 *
		 */
		public function parseMailFile($raw){
			if($raw === false) return false;
			$s = array(	".", 	"+"		);
			$r = array(	"\.", 	"\+"	);
			$m = preg_match("/^(From ".str_replace(".", "\.", Config::responseEmail()).".+)(^From )?/ms", $raw, $matches);
			if($m)	return trim($matches[1]);
			else return null;
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
			$raw = $this->parseMailFile(System::getMail());
			if(trim($raw) == "" || $raw == false)	return false;
			$data = $this->parseEmail($raw);
			if($this->submitEntry($data[0], $data[1], $data[2], $data[3])){
				System::deleteMail($raw);
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
	$j = new Journal(Config::$dbFile);
?>
