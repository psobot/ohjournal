<?php
	require("ohjournal.php");
	$j->protect();
	require("header.php");
?>
<h2>Your Entries</h2>
<div id="jump">
	<p>	<?php
			$i = 0;
			foreach($j->getUniqueMonths() as $id => $text){
				if($i % 4 == 0 && $i > 0)echo "<br />";
				echo "<a href='#$id' class='scroll ".( ($i % 4 == 0) ? "first" : "" )."'>$text</a>";
				$i++;
			}
		?>
	</p>
</div>
	<?php 
	$lastDate = null;
	foreach($j->getAllEntries() as $row): 
		$row['sent'] .= " GMT";
		$row['received'] .= " GMT";
		$sent = date(Config::$webDate, strtotime($row['sent']));
		$body = trim(preg_replace("/[\n\r]/", "<br />", $row['entry']));
		if(date("n", strtotime($lastDate)) != date("n", strtotime($row['sent'])))
			echo "<h3 id='".date("Y-n", strtotime($row['sent']))."'>".date("F Y", strtotime($row['sent']))."</h3>";
	?>
		<div class="entry">
			<a href="#" class="down button"></a>
			<a href="#" class="up button"></a>
			<a href="#" class="delete button"></a>
			<div class="bar">
				<div class="sent"><?php echo $sent; ?></div>
			</div>
			<div class="body"><?php echo $body; ?></div>
		</div>
	<?php
		$lastDate = $row['sent'];
	endforeach;
	?>
<?php require("footer.php"); ?>
