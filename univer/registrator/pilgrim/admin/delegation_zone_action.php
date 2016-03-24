<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	$PM = new Pilgrim_Manage();

	$act = safe_get("act", "GET", 255);

	if ($act == "add_delegation_zone") {

		$zone_name = safe_get("zone_name", "POST", 255);
		$admin_email = safe_get("admin_email", "POST", 255);
		$lease_time = safe_get("lease_time", "POST", 255);
		$initial = safe_get("initial", "POST", 255);
		$grace = safe_get("grace", "POST", 255);
		$pending = safe_get("pending", "POST", 255);
		$zone_title = safe_get("zone_title", "POST", 255);

		$go_back =  "<html>
		<head>
		<title>Hosting.ai: Admin Panel</title>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=koi8-r\">
		</head>
		<body bgcolor=\"EEEEEE\">
		<form name=\"delegation_zone_manage\" method=\"POST\" action=\"delegation_zone_add.php\">
		<input type=\"hidden\" name=\"zone_name\" value=\"".$zone_name."\">
		<input type=\"hidden\" name=\"admin_email\" value=\"".$admin_email."\">
		<input type=\"hidden\" name=\"lease_time\" value=\"".$lease_time."\">
		<input type=\"hidden\" name=\"initial\" value=\"".$initial."\">
		<input type=\"hidden\" name=\"grace\" value=\"".$grace."\">
		<input type=\"hidden\" name=\"pending\" value=\"".$pending."\">
		<input type=\"hidden\" name=\"zone_title\" value=\"".$zone_title."\">
		<script language=\"javascript\">
			document.delegation_zone_manage.submit();
		</script>
		<noscript>
			You don't have JavaScript to be enabled. Please, push this button:<br>
			<input type=\"submit\">
		</noscript>
		</form>";

		if (	$zone_name != "" &&
			$admin_email != "" &&
			$lease_time != "" &&
			$initial != "" &&
			$grace != "" &&
			$pending != "") {

			if ($PM->DZ->add_delegation_zone($zone_name, $admin_email, $lease_time, $initial, $grace, $pending, $zone_title) != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't create delegation_zone: ".$_SESSION["adminpanel_error"];
				echo $go_back;
				exit;
			}
		}
		else {
			$_SESSION["adminpanel_error"] = "Not enough data";
			echo $go_back;
			exit;
		}

		Header("Location: delegation_zone.php\n\n");
		exit;
	}

	elseif ($act == "change_delegation_zone") {

		$id = safe_get("id", "POST", 255);
		$zone_name = safe_get("zone_name", "POST", 255);
		$admin_email = safe_get("admin_email", "POST", 255);
		$lease_time = safe_get("lease_time", "POST", 255);
		$initial = safe_get("initial", "POST", 255);
		$grace = safe_get("grace", "POST", 255);
		$pending = safe_get("pending", "POST", 255);
		$zone_title = safe_get("zone_title", "POST", 255);
		$active = safe_get("active", "POST", 255);

		if ($id != "" &&
			$zone_name != "" &&
			$admin_email != "" &&
			$lease_time != "" &&
			$initial != "" &&
			$grace != "" &&
			$pending != "") {

			if ($PM->DZ->change_delegation_zone($id, $zone_name, $admin_email, $lease_time, $initial, $grace, $pending, $zone_title, $active) != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't change zone: ".$_SESSION["adminpanel_error"];
				Header("Location: delegation_zone_change.php?id=".$id."\n\n");
				exit;
			}
		}
		else {
			$_SESSION["adminpanel_error"] = "Not enough data";
			Header("Location: delegation_zone_change.php?id=".$id."\n\n");
			exit;
		}

		Header("Location: delegation_zone.php\n\n");
		exit;
	}

	elseif ($act == "remove") {

		$ids = safe_get("ids", "POST");

		if ($ids == "") {
			$_SESSION["adminpanel_error"] = "Error - did not specified id's parameter";
			Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
			exit;
		}

		$id_separate = explode(":", $ids);

		for($i=0; $i<count($id_separate); $i++) {
			$id = trim($id_separate[$i]);
			if ($id != "") {
				if ($PM->DZ->remove_delegation_zone($id) != TRUE) {
					$_SESSION["adminpanel_error"] = "Can't remove zone: ".$_SESSION["adminpanel_error"];
					Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
					exit;
				}
			}
		}

		Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
		exit;
	}

	$_SESSION["adminpanel_error"] = "Unknown action";
	Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
	exit;
?>
