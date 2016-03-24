<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	$PM = new Pilgrim_Manage();

	$act = safe_get("act", "GET", 255);

	if ($act == "add_zone") {

		$client = safe_get("client", "POST", 255);
		$delegation_zone = safe_get("delegation_zone", "POST", 255);
		$zone_name = safe_get("zone_name", "POST", 255);
		$zone_ttl = safe_get("zone_ttl", "POST", 10);
		$refresh = safe_get("refresh", "POST", 10);
		$retry = safe_get("retry", "POST", 10);
		$expire = safe_get("expire", "POST", 10);
		$negative = safe_get("negative", "POST", 10);

		$go_back =  "<html>
		<head>
		<title>Hosting.ai: Admin Panel</title>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=koi8-r\">
		</head>
		<body bgcolor=\"EEEEEE\">
		<form name=\"zone\" method=\"POST\" action=\"zone_add.php\">
		<input type=\"hidden\" name=\"client\" value=\"".$client."\">
		<input type=\"hidden\" name=\"delegation_zone\" value=\"".$delegation_zone."\">
		<input type=\"hidden\" name=\"zone_name\" value=\"".$zone_name."\">
		<input type=\"hidden\" name=\"zone_ttl\" value=\"".$zone_ttl."\">
		<input type=\"hidden\" name=\"refresh\" value=\"".$refresh."\">
		<input type=\"hidden\" name=\"retry\" value=\"".$retry."\">
		<input type=\"hidden\" name=\"expire\" value=\"".$expire."\">
		<input type=\"hidden\" name=\"negative\" value=\"".$negative."\">
		<script language=\"javascript\">
			document.zone.submit();
		</script>
		<noscript>
			You don't have JavaScript to be enabled. Please, push this button:<br>
			<input type=\"submit\">
		</noscript>
		</form>";

		if (	$client != "" &&
			$delegation_zone != "" &&
			$zone_name != "" &&
			$zone_ttl != "" &&
			$refresh != "" &&
			$retry != "" &&
			$expire != "" &&
			$negative != "") {

			if ($PM->ZC->add_zone($client, $delegation_zone, $zone_name, $zone_ttl, $refresh, $retry, $expire, $negative) != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't create zone: ".$_SESSION["adminpanel_error"];
				echo $go_back;
				exit;
			}
		}
		else {
			$_SESSION["adminpanel_error"] = "Not enough data";
			echo $go_back;
			exit;
		}

		Header("Location: zone.php\n\n");
		exit;
	}

	elseif ($act == "change_zone") {

		$id = safe_get("id", "POST", 255);

		$client = safe_get("client", "POST", 255);
		$delegation_zone = safe_get("delegation_zone", "POST", 255);
		$zone_name = safe_get("zone_name", "POST", 255);
		$zone_ttl = safe_get("zone_ttl", "POST", 10);
		$refresh = safe_get("refresh", "POST", 10);
		$retry = safe_get("retry", "POST", 10);
		$expire = safe_get("expire", "POST", 10);
		$negative = safe_get("negative", "POST", 10);

		if (	$id != "" &&
			$client != "" &&
			$delegation_zone != "" &&
			$zone_name != "" &&
			$zone_ttl != "" &&
			$refresh != "" &&
			$retry != "" &&
			$expire != "" &&
			$negative != "") {

			if ($PM->ZC->change_zone($id, $client, $delegation_zone, $zone_name, $zone_ttl, $refresh, $retry, $expire, $negative) != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't change zone: ".$_SESSION["adminpanel_error"];
				Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
				exit;
			}

		}
		else {
			$_SESSION["adminpanel_error"] = "Not enought data";
			Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
			exit;
		}

		Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
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
				if ($PM->ZC->remove_zone($id) != TRUE) {
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
