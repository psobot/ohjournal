<?php
	require_once("ohjournal.php");
	$title = "Manage Your Journal";
	$j->protect();
	require_once("header.php");
?>

<h2><?php echo $title; ?></h2>
	<div id="info">
		<h3>System</h3>
		<p>
			Running PHP <?php echo phpversion() . " as user ".trim(shell_exec("whoami"))." on " . PHP_OS; ?>.<br />
			<?php
				if($j->isInstalled()){
					echo "OhJournal is installed properly.";
				} else {
					echo "OhJournal is not installed properly!";
					echo "<ul>";
					echo "<li>Database file: ".(is_writeable($j->dbFile) ? "writeable" : "not writeable!")."</li>";
					echo "<li>Mail file: ".(is_writeable($j->config->mailFile) ? "writeable" : "not writeable!")."</li>";
				}
				echo $j->readCrontab();
			?>
		</p>
	</div>
<?php require_once("footer.php"); ?>


