<?php
	$db = new SQLite3('journal.db');
	$res = $db->query('select * from entries');
	var_dump($res->fetchArray());
?>
