<?php
	$title = "Settings";
	require("ohjournal.php");
	$j->protect();
	require("header.php");
?>

<h2>Settings</h2>
	<div id="settings">
		<h3>System</h3>
		<p>
			Running PHP <?php echo phpversion() . " as user ".trim(shell_exec("whoami"))." on " . PHP_OS; ?>.<br />
			<?php
				if(System::installed()){
					echo "OhJournal is installed properly.";
				} else {
					echo "OhJournal is not installed properly!";
					echo "<ul>";
					echo "<li>Database file: ".(is_writeable(Config::$dbFile) ? "writeable" : "not writeable!")."</li>";
					echo "<li>Mail file: ".(is_writeable(Config::$mailFile) ? "writeable" : "not writeable!")."</li>";
				}
				echo System::readCrontab();
			?>
		</p>
	</div>
<?php require("footer.php"); ?>


