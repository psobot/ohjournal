#!/usr/bin/php
<?php
	require_once("ohjournal.php");
	if(isset($_SERVER['HTTP_USER_AGENT'])){
		$title = "Install OhJournal";
		require("header.php");
		echo "<h2>Install OhJournal</h2><p>This script needs to be run from command line.</p>";
		require("footer.php");
		die();
	}
	echo "\n";
	if($j->isInstalled())die("OhJournal is already installed!\n\n");

	$aliasfile = "/etc/aliases";
	if(!is_writeable($aliasfile))die("Not enough permissions.\nYou need to be able to modify /etc/aliases and your crontab.\nTry running as root?\n\n");

	echo "===	OhJournal Installer	===\n\n";

	//	Database creation and population
	echo "Creating database file (journal.db)... ";
	if($j->createDatabase($message)) echo "done!\n";
	else die("Database creation failed! (error: $message)\nTry running as root maybe?\n\n");

	//	Cronjob addition
	echo "Installing cronjob:\n";
	$crontab = trim(shell_exec("crontab -l 2>&1"));
	if(strpos($crontab, "crontab: no crontab") !== FALSE){
		echo "\tNo crontab currently installed for ".trim(`whoami`)."\n\tInstalling fresh crontab... ";

		$tempCrontab = "./crontab.tmp";
		$f = fopen($tempCrontab, "w");
		fwrite($f, "#OhJournal daily email:\n0	20	*	*	*	php ".getcwd()."/daily.php\n");
		//currently hardcoded to 8:00 PM whatever timezone the server is in
		fclose($f);

		$result = trim(`crontab $tempCrontab`);
		unlink($tempCrontab);
		echo "done!\n";
	} else {
		echo "\tAppending to (or replacing existing OhJournal entry in) crontab for ".trim(`whoami`)."... ";

		//remove any lines with "OhJournal" in them from the current crontab
		$crontab = trim(preg_replace('/.+OhJournal.+/', '', $crontab));

		$tempCrontab = "./crontab.tmp";
		$f = fopen($tempCrontab, "w");
		fwrite($f, "$crontab\n0	20	*	*	*	php ".getcwd()."/daily.php\t#OhJournal daily email\n");
		//currently hardcoded to 8:00 PM whatever timezone the server is in
		fclose($f);

		$result = trim(`crontab $tempCrontab`);
		unlink($tempCrontab);
		echo "done!\n";
	}
	
	//	Email alias addition
	echo "\nInstalling postfix receive script for ohjournal@yourserver.tld:\n";
	$aliases = file_get_contents($aliasfile);
	echo "\tAppending to (or replacing existing OhJournal entry in) aliases file... ";

	//remove any lines with "process.php" in them from the current aliases file
	$aliases = trim(preg_replace('/.+process.php.+/', '', $aliases));

	$f = fopen($aliasfile, "w");
	fwrite($f, "$aliases\n#OhJournal email receiver\nohjournal:\t".trim(`whoami`).", \"| php ".getcwd()."/process.php\"\n");
	fclose($f);
	echo "done!";

	echo "\nRefreshing aliases file (running newaliases)... ";
	echo "`".trim(`sudo newaliases`)."`: ";
	echo "done!\n";

	echo "\nOhJournal should now be properly installed.\nVisit the web interface + /config to configure email addresses and other settings.\n\n";

	echo "\n";
?>
