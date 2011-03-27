<?php
	//Routing code!
	require_once("ohjournal.php");
	
	if($_GET['url'] == "install" && !$j->isInstalled()){
		require_once("install.php");
	} else if(!$j->isInstalled()){
		header("Location: ./install");
	} else if(	$j->isAllowedIP($_SERVER['REMOTE_ADDR']) 
			&& ($j->isLoggedIn() || (isset($_POST['password']) && $j->login($_POST['password'])))){
		if(isset($_GET['url'])) $page = strtolower($_GET['url']);	//Get page from Apache .htaccess redirect
		else {
			$uricomponents = explode("/", $_SERVER['REQUEST_URI']);	//otherwise (lighttpd) use index.php as custom 404 handler
			$page = $uricomponents[count($uricomponents)-1];		//get last component of URL
		}
		switch($page){
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

