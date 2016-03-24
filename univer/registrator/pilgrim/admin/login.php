<?php
	$INCLUDE_PATH="../include/";
	require_once ($INCLUDE_PATH."define.php");
	require_once ($INCLUDE_PATH."tools.php");
	require_once ($INCLUDE_PATH."sql_core.php");

	$login = safe_get("login", "POST", 255);
	$password = safe_get("password", "POST", 255);

	if (empty($login) || empty($password)) {
		header("Location: index.php?error=0\n\n");
		exit;
	}

	$SQL = new PGSQL;
	$SQL->connect();

	$query = "SELECT * FROM admin_table WHERE login='".$login."'";
	$result = $SQL->exec_query($query);
	if ($result == FALSE) {
		echo $SQL->get_error();
		exit;
	}

	if ($SQL->get_num_rows($result) != 1) {
		// Login incorrect
		header("Location: index.php?error=1\n\n");
		exit;
	}

	$main_data = $SQL->get_object($result);

	$SQL->disconnect();

	if (crypt($password, $main_data->passwd) !== $main_data->passwd) {
		// Login incorrect
		header("Location: index.php?error=1\n\n");
		exit;
	}

	session_start();

	mail("bazil@profi.kharkov.ua", "Domain: Login of user: ".$login." at ".date("l dS of F Y H:i:s"), "dummy");

	if (!isset($_SESSION["pilgrim_apps"])) {
		$_SESSION["pilgrim_apps"] = 1;
	}
	else {
		$_SESSION["pilgrim_apps"] += 1;
	}

	$_SESSION["adminpanel_login"] = $main_data->login;
	$_SESSION["adminpanel_id_table"] = $main_data->id_table;
	$_SESSION["adminpanel_ip"] = $_SERVER["REMOTE_ADDR"];
	$_SESSION["adminpanel_error"] = "";

	header("Location: frame.php\n\n");

?>
