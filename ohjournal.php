<?php
	session_start();
	require_once("helpers.php");
	class NotInstalledException extends Exception {}

	$version = "0.5";

	class Journal{
		public $db = null;
		public $installed = false;
		public $config = null;
		public $entries = null;
		public $dbFile = null;
		
		function __construct($database = NULL){
			if($database == NULL) $database = dirname(__FILE__)."/"."journal.db";
			$this->dbFile = $database;
			if($this->isInstalled()){
				$this->db = new SQLite3($this->dbFile);
			} else {
				fwrite(STDERR, "Database error - could not write to Database file!");
				exit(1);
			}
			$this->initConfig();
		}
		function __destruct(){
			if($this->isInstalled()) $this->db->close();
		}
		public function initConfig(){
			$r = @$this->db->query("select * from config");
			if(!$r) return false;
			while($v = $r->fetchArray()) $this->config->$v[1] = $v[2];
			
			$r = @$this->db->query("select * from ".$this->config->tblIPs);
			if(!$r) return false;
			while($v = $r->fetchArray()) $this->config->webIPs[] = $v[1];

			date_default_timezone_set($this->config->timezone);

			return !empty($this->config);
		}
		public function responseEmail(){
			return preg_replace("/\+.+@/", "@", $this->config->userEmail);
		}
		public function isAllowedIP($ip){
			if($ip == "fe80::1" || $ip == "127.0.0.1" || empty($this->config->webIPs)) return true;
			else return in_array($ip, $this->config->webIPs);
		}
		public function isInstalled(){
			return is_writeable($this->dbFile);
		}
		public function createDatabase(&$error){
			if($this->isInstalled() && !empty($this->config)){ $error = "OhJournal is already installed."; return true; }
			$this->dbFile = "journal.db";
			$this->db = new SQLite3(dirname(__FILE__)."/".$this->dbFile);
			if(!$this->db){ $error = "Database file creation failed!"; return false; }
			else {
				if(!file_exists("schema.sql")){ $error = "Missing schema definition file!"; return false; }
				$this->db->query(file_get_contents("schema.sql"));
				$this->initConfig();
			}
			return true;
		}
		/*
		*      Gets latest mail to the user by reading user's mail file
		*      Usually in /var/mail/username, but can be set in config
		*      This is, by all accounts, a dirty hack
		*      Triggering an event on postfix receive is much, much better.
		*
		*      Returns the raw source of the email.
		*/
		public function getMail(){
			$data = file_get_contents($this->config->mailFile);
			if($data == NULL || trim($data) == "") return false;
			return $data;
		}
		/*
		*      Clears out the user's mailbox file.
		*      Yes, you read that right.
		*      Probably shouldn't be doing this. Could cause major problems.
		*      Should change this to only delete the message passed in.
		*/
		public function deleteMail($mail = NULL){
			if($mail == NULL){
				$f = fopen($this->config->mailFile, 'w');
				fwrite($f, "");
				fclose($f);
				return (trim(file_get_contents($this->config->mailFile)) == "");
			} else {
				$mailFileContents = file_get_contents($this->config->mailFile);
				$f = fopen($this->config->mailFile, 'w');
				$r = fwrite($f, preg_replace("/".preg_quote($mail, '/')."\s+/", "", $mailFileContents));
				fclose($f);
				return !($r == false);
			}
		}
		public function login($password = ""){
			if(!$this->isAllowedIP($_SERVER['REMOTE_ADDR'])) return false;
			$query = "select count(*) as login from ".$this->config->tblUser." where password = '".md5($password)."'";
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
			$stmt = $this->db->prepare('insert into '.$this->config->tblVisits.' 
										values(NULL, datetime("now"), :ip, :page, :loggedin)');
			$stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
			$stmt->bindValue(':page', $_SERVER['PHP_SELF']);
			$stmt->bindValue(':loggedin', ($this->isLoggedIn() ? 1 : 0));
			return $stmt->execute();
		}
		/*
		 *	Adds a Journal entry to the DB.
		 *	Returns through from DB call. (false on error)
		 *
		 */
		public function submitEntry($sendDate, $receiveDate, $header, $body){
			$stmt = $this->db->prepare('insert into '.$this->config->tblEntries.' 
										values(NULL, datetime(:send, "unixepoch"), datetime(:receive, "unixepoch"), :header, :body, 0)');
			$stmt->bindValue(':send', $sendDate);
			$stmt->bindValue(':receive', $receiveDate);
			$stmt->bindValue(':header', htmlentities($header));
			$stmt->bindValue(':body', htmlentities($body));
			return $stmt->execute();
		}

		/*
		 *	Gets a random journal entry that hasn't been reflected at the user yet.
		 *	Used for peeks back at the past.
		 *	After grabbing a row, it automatically sets its "reflected" flag to 1 (true)
		 *	Returns array of row data or false on no historical rows left.
		 *
		 */
		public function getRandomEntry(){
			$entry = $this->db->querySingle("select * from ".$this->config->tblEntries." where reflected = 0 order by random() limit 1", true);
			if($entry){
				$this->db->query("update ".$this->config->tblEntries." set reflected = 1 where id = ".$entry['id']);
				return $entry;
			} else return false;
		}
		public function getAllEntries(){
			$q = $this->db->query("select * from ".$this->config->tblEntries." order by sent desc, received asc");
			while($row = $q->fetchArray()){$in[] = $row;}
			foreach($in as $key => $row){
				$out[date("Y-m", strtotime($row['sent']." GMT"))][date("Y-m-d", strtotime($row['sent']." GMT"))][] = $row;
			}
			$this->entries = $out;
			return $out;
		}
		public function countDays(){
			$count = 0;
			foreach($this->entries as $month => $days)
				foreach($days as $day => $entries) $count += count($entries);
			return $count;
		}

		/*
		 *	TODO: Fix this function. It is filled with dirty (kinda pointless) hacks.
		 */
		public function countTotalDays($m = NULL){
			$count = 0;
			$first = true;
			foreach(array_reverse(array_keys($this->entries)) as $month){
				if($month == $m) $count = 0;
				if($first){
					$first = array_keys(array_reverse($this->entries[$month]));
					$first = explode("-", $firstmonth[0]);
					$count += date("n", strtotime($month)) - $first[2] - 1;
					$first = false;
				} else if(date("n", strtotime($month)) == date("n")) $count += date("j") - (time() < strtotime($this->config->emailTime));
				else $count += date("t", strtotime($month));
				if($month == $m) break;
			}
			return $count;
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
			$m = preg_match("/^(From ".preg_quote($this->responseEmail()).".+)(^From )?/ms", $raw, $matches);
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

			preg_match("%^([\s\S]+?)On .+? \d\d?, \d{4}, at %", $body, $body);	//Remove previous email from what's being saved
			$body = trim(quoted_printable_decode(preg_replace("/=[\n\r]+/", "", trim($body[1]))));

			return array($sendDate, $receiveDate, $header, $body);
		}

		public function sendDaily(){
			date_default_timezone_set("America/Toronto");				//set your timezone here if this is incorrect
			$subject	=	"It's " . date("l, F jS") . " - How did your day go?";
			$body		=	"Just respond to this email with your entry, and it will be saved automagically.";
			$past		= 	$this->getRandomEntry();
			if($past != false)
				$body .= 	"\r\n\r\n\r\n".
							$this->config->rememberText." ".
							DateCompare::differenceInWords($past['received']).
							" (on ".
							date("l, F jS, Y", strtotime($past['received'])).
							") you wrote:\r\n\r\n".
							html_entity_decode($past['entry']);
			else $body .= "\r\n\r\n\r\nThere are no past journal entries to show you... so get writing!";
			$headers	=	'From: OhJournal <'.$this->config->fromEmail.'>' . "\r\n" .
							'Reply-To: ' . $this->config->serverEmail . "\r\n" .
							'X-Mailer: OhJournal from user <'.trim(shell_exec("whoami")).'> on PHP/' . phpversion();
			return mail($this->config->userEmail, $subject, $body, $headers);
		}
	}
	$j = new Journal();
?>
