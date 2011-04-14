<?php
	require_once("ohjournal.php");
	$j->protect($j->config->webRead);

	$title = "Your Entries";
	require_once("header.php");
	$data = $j->getAllEntries();
?>
<h2>Your Entries</h2>
<h4 class='percent'><a href="#" id="showall">toggle all entries</a></h4>
<h3 class="percent">
	<?php echo $a = $j->countDays(); ?> 
	entr<?php echo ($a>1)?"ies":"y"; ?> over 
	<?php echo $b = $j->countTotalDays(); ?> day<?php echo (($b>1)?"s":""); ?> (<?php echo count(array_keys($data)); ?> months). 
	(<?php echo intval($a/$b * 100); ?>%)
</h3>
<div id="jump">
	<p>	<?php
			$i = 0;
			foreach(array_keys($data) as $month){
				if($i % 4 == 0 && $i > 0)echo "<br />";
				echo "<a href='#".date("Y-n", strtotime($month))."' class='scroll ".( ($i % 4 == 0) ? "first" : "" )."'>".date("F Y", strtotime($month))."</a>";
				$i++;
			}
		?>
	</p>
</div>
<?php
	foreach($data as $month => $days){
		echo "<h3 id='".date("Y-n", strtotime($month))."'>".date("F Y", strtotime($month))."</h3>";
		$b = $j->countTotalDays($month);
		echo "<h4 class='percent'>".($a = count($days))." response".(($a>1)?"s":"")." over $b day".(($b>1)?"s":"").". (".intval($a/$b * 100)."%)</h4>";
		foreach($days as $day => $entries){
			$sent = date($j->config->webDate, strtotime($day));
?>
	<div class="entry" id="<?php echo $day ?>">
		<div class="bar">
			<div class="sent"><?php echo $sent; ?></div>
		</div>
	<?php
		foreach($entries as $row){
			$row['received'] .= " GMT";
			$row['sent'] .= " GMT";
			$body = trim(preg_replace("/[\n]/", "<br />", $row['entry']));
	?>
			<div class="body" id="entry_<?php echo $row['id'] ?>">
					<div class="received">
					<?php 
						if(DateCompare::daysApart($row['received'], $row['sent']) == 0) echo date("g:i a", strtotime($row['received']));
						else echo date("g:i a", strtotime($row['received'])) . 
								(($d = DateCompare::daysApart($row['received'], $row['sent'])) == 1 ? " the next day" : " ".TextHelper::numberToWords($d)." days later");
					?>
					</div>
				<?php
					echo $body; 
				?>
			<div class="sliderContainer">Rating: <span class="percent"><?php echo $row['mood']; ?></span>% <div class="slider" value="<?php echo $row['mood']; ?>"></div></div>
			</div>
	<?php
		}
	?>
	</div>
<?php
		}
	}
?>
<?php require_once("footer.php"); ?>
