<?php
	class Config{
		public static $dbFile = "journal.db";
		public static $tblEntries = "entries";

		public static $yourEmail = "psobot+ohjournal@gmail.com";
		public static $serverEmail = "ohjournal@psobot.xen.prgmr.com";
		public static $fromEmail = "ohjournal@petersobot.com";
		public static $backupEmail = "psobot+ohjournalresponse@gmail.com";

		public static $mailUser = "psobot";
		public static $mailboxes = "/var/mail/";

		public static function error(){
			echo "Error!\n";
			foreach(func_get_args() as $error){
				var_dump($error);
			}
			die();
		}
	}
?>
