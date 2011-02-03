<?php
	date_default_timezone_set("America/Toronto");
	class Config{
		public static $owner		= "Peter Sobot";

		public static $dbFile 		= "journal.db";
		public static $cronUser		= "psobot";
		public static $mailFile 	= "/var/mail/psobot";

		public static $tblEntries 	= "entries";
		public static $tblUser	 	= "user";
		public static $tblVisits	= "visits";

		public static $yourEmail 	= "psobot+ohjournal@gmail.com";
		public static $serverEmail 	= "ohjournal@psobot.xen.prgmr.com";
		public static $fromEmail 	= "ohjournal@petersobot.com";

		public static $rememberText	= "Hey, remember this?";
		public static $emailDate	= "l, F jS, Y";
		public static $emailTime	= "8:00 PM EST";

		public static $webRead		= true;
		public static $webIPs		= array("24.141.250.42");
		public static $webDate		= "l, F j\<\s\up\>S\</\s\up\>, Y";

		public static function responseEmail(){
			return preg_replace("/\+.+@/", "@", Config::$yourEmail);
		}
	}
	class System{
		public static function error(){
			echo "Error!\n";
			foreach(func_get_args() as $error){
				var_dump($error);
			}
			die();
		}
		public static function installed(){
			return is_writeable(Config::$dbFile) && is_writeable(Config::$mailFile);
		}
		public static function readCrontab(){
			return trim(shell_exec("crontab -l -u ".Config::$cronUser));
		}
		/*
		 *	Gets latest mail to the user by reading user's mail file
		 *	Usually in /var/mail/username, but can be set in config
		 *	This is, by all accounts, a dirty hack
		 *	Triggering an event on postfix receive is much, much better.
		 *
		 *	Returns the raw source of the email.
		 */
		public static function getMail(){
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
		public static function deleteMail($mail = NULL){
			$f = fopen(Config::$mailFile, 'w');
			fwrite($f, "");
			fclose($f);
			return (trim(file_get_contents(Config::$mailFile)) == "");
		}
		public static function isAllowedIP($ip){
			if($ip == "fe80::1" || $ip == "127.0.0.1" || empty(Config::$webIPs)) return true;
			else return in_array($ip, Config::$webIPs);
		}
	}
?>
