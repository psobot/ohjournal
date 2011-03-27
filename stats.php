<?php
	require_once("ohjournal.php");
	$j->protect($j->config->webRead);
	require_once("header.php");
	$data = $j->getAllEntries();
?>
<h2>Statistics</h2>
<?php
	//as we have to iterate over all of this to get a word histogram anyways,
	//we'll just use these loops to gather all of the data
	//miniscule speed difference from SQL queries
	$i = 0;
	foreach($data as $month => $days){
		$monthParts = explode("-", $month);
		$entriesPerMonth[] = array(
			"x" => "Date.UTC(".($monthParts[0]).", ".($monthParts[1]-1).", 0)", 
			"y" => count($days),
			"possible" => $j->countTotalDays($month)
		);
		foreach($days as $day => $entries){
			foreach($entries as $k => $entry){
				$dateParts = explode("-", $day);
				$lengths["Date.UTC(".($dateParts[0]).", ".($dateParts[1]-1).", ".($dateParts[2]).")"] = count(explode(" ", $entry['entry']));
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
	$topTwenty = array_slice(array_reverse($words), 0, 20, true);
	
	$i = 0;
	foreach($topTwenty as $k => $v) $topTwenty[$k] = array($k, $v);
?>
<script type="text/javascript">
	var frequency = <?php echo json_encode(array_values($topTwenty)); ?>;
	var lengths = [<?php foreach($lengths as $k => $v) echo "[$k, $v],"; ?>];
	var entriesPerMonth = [<?php foreach($entriesPerMonth as $v) echo "{x: ".$v['x'].", y: ".$v['y'].", possible: ".$v['possible']."},"; ?>];
</script>
<div id="length" class="chart"></div>
<div id="entriesPerMonth" class="chart"></div>
<div id="frequency" class="chart"></div>
<?php require_once("footer.php"); ?>
