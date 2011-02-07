<?php
	//Routing code!
	require_once("ohjournal.php");
	
	if(		$j->isAllowedIP($_SERVER['REMOTE_ADDR']) 
		&& ($j->isLoggedIn() || (isset($_POST['password'] && $j->login($_POST['password']))))){

		switch(strtolower($_GET['url'])){
			case "lock":
				require_once("lock.php");
				break;
			case "manage":
				require_once("manage.php");
				break;
			case "write":
				require_once("write.php");
				break;
			case NULL:
				if($j->config->webRead) require_once("read.php");
				else require_once("write.php");
				break;				
			default:
				header("Location: ./");
		}
	} else if(!is_null($_GET['url'])) {
		header("Location: ./");
	} else require_once("login.php");
?>
