<?php
	class Config{
		public static $dbFile 		= "journal.db";
		public static $mailFile 	= "/var/mail/psobot";
		public static $tblEntries 	= "entries";

		public static $yourEmail 	= "psobot+ohjournal@gmail.com";
		public static $serverEmail 	= "ohjournal@psobot.xen.prgmr.com";
		public static $fromEmail 	= "ohjournal@petersobot.com";

		public static $rememberText	= "Hey, remember this?";

		public static function error(){
			echo "Error!\n";
			foreach(func_get_args() as $error){
				var_dump($error);
			}
			die();
		}
	}
?>
