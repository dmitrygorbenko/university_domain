<?php
	require_once ("header.php");

	if ($_SESSION["pilgrim_apps"] == 1) {
		session_destroy();
	}
	else {
		$_SESSION["pilgrim_apps"] -= 1;
		unset($_SESSION["adminpanel_id_table"]);
		unset($_SESSION["adminpanel_login"]);
		unset($_SESSION["adminpanel_ip"]);
		unset($_SESSION["adminpanel_error"]);
	}

	mail("bazil@profi.kharkov.ua", "Domain: Logout of user: ".$login." at ".date("l dS of F Y H:i:s"), "dummy");

	header("Location: index.php\n\n");
	exit();
?>