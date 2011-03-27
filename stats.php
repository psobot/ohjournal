<?php
	require_once("ohjournal.php");
	$j->protect($j->config->webRead);
	require_once("header.php");
	$data = $j->getAllEntries();
?>
<h2>Statistics</h2>
<h3 class="percent">
	<?php echo $a = $j->countDays(); ?> 
	entr<?php echo ($a>1)?"ies":"y"; ?> over 
	<?php echo $b = $j->countTotalDays(); ?> day<?php echo (($b>1)?"s":""); ?>. 
	(<?php echo intval($a/$b * 100); ?>%)
</h3>
<?php 
	foreach($data as $month => $days){
		foreach($days as $day => $entries){
			foreach($entries as $entry){
				foreach(explode(" ", $entry['entry']) as $word){
					$word = strtolower(preg_replace('/[^a-zA-Z0-9 ]/','',$word));	//remove non-alphanumeric chars
					$words[$word]++;			//increment histogram
					$wordCount++;				//increment wordcount
					$charCount+=strlen($word);	//increment charcount
				}	
			}
		}
	}
	asort($words);
	$topTwenty = array_slice(array_reverse($words), 0, 30, true);
	
	$sum = 0;
	foreach($topTwenty as $k => $v)	$sum += $v;

	$i = 0;
	foreach($topTwenty as $k => $v) $topTwenty[$k] = array($k, $v);
?>
<script type="text/javascript">
	var frequency = <?php echo json_encode(array_values($topTwenty)); ?>
</script>
<div id="chart">

</div>
<?php require_once("footer.php"); ?>

