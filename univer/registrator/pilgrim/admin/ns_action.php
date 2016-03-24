<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	$PM = new Pilgrim_Manage();

	$act = safe_get("act", "GET", 255);

	if ($act == "add_ns") {

		$domain = safe_get("domain", "POST", 255);
		$ip_addr = safe_get("ip_addr", "POST", 255);
		$title = safe_get("title", "POST", 255);

		$go_back =  "<html>
		<head>
		<title>Hosting.ai: Admin Panel</title>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=koi8-r\">
		</head>
		<body bgcolor=\"EEEEEE\">
		<form name=\"ns\" method=\"POST\" action=\"ns_add.php\">
		<input type=\"hidden\" name=\"domain\" value=\"".$domain."\">
		<input type=\"hidden\" name=\"ip_addr\" value=\"".$ip_addr."\">
		<input type=\"hidden\" name=\"title\" value=\"".$title."\">
		<script language=\"javascript\">
			document.ns.submit();
		</script>
		<noscript>
			You don't have JavaScript to be enabled. Please, push this button:<br>
			<input type=\"submit\">
		</noscript>
		</form>";

		if (	$domain != "" &&
			$ip_addr != "") {

			if ($PM->NS->add_ns_server($domain, $ip_addr, $title) != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't create ns server: ".$_SESSION["adminpanel_error"];
				echo $go_back;
				exit;
			}
		}
		else {
			$_SESSION["adminpanel_error"] = "Not enough data";
			echo $go_back;
			exit;
		}

		Header("Location: ns.php\n\n");
		exit;
	}

	elseif ($act == "change_ns") {

		$id = safe_get("id", "POST", 255);
		$domain = safe_get("domain", "POST", 255);
		$ip_addr = safe_get("ip_addr", "POST", 255);
		$title = safe_get("title", "POST", 255);

		if ($id != "" &&
			$domain != "" &&
			$ip_addr != "") {

			if ($PM->NS->change_ns_server($id, $domain, $ip_addr, $title) != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't change ns server: ".$_SESSION["adminpanel_error"];
				Header("Location: ns_change.php?id=".$id."\n\n");
				exit;
			}
		}
		else {
			$_SESSION["adminpanel_error"] = "Not enough data";
			Header("Location: ns_change.php?id=".$id."\n\n");
			exit;
		}

		Header("Location: ns.php\n\n");
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
				if ($PM->NS->remove_ns_server($id) != TRUE) {
					$_SESSION["adminpanel_error"] = "Can't remove ns seerrv: ".$_SESSION["adminpanel_error"];
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
