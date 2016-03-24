<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	$PM = new Pilgrim_Manage();

	$act = safe_get("act", "GET", 255);

	if ($act == "add_client") {

		$login = safe_get("login", "POST", 255);
		$password = safe_get("password", "POST", 255);
		$password2 = safe_get("password2", "POST", 255);
		$email = safe_get("email", "POST", 255);
		$firstname = safe_get("firstname", "POST", 255);
		$lastname = safe_get("lastname", "POST", 255);
		$company = safe_get("company", "POST", 255);
		$country = safe_get("country", "POST", 255);
		$region = safe_get("region", "POST", 255);
		$postal = safe_get("postal", "POST", 255);
		$city = safe_get("city", "POST", 255);
		$address = safe_get("address", "POST", 255);
		$phone = safe_get("phone", "POST", 255);
		$fax = safe_get("fax", "POST", 255);
		$egrpou = safe_get("egrpou", "POST", 255);
		$userhdl = safe_get("userhdl", "POST", 255);
		$active = safe_get("active", "POST", 5) == "on"?TRUE:FALSE;
		$cp_access = safe_get("cp_access", "POST", 5) == "on"?TRUE:FALSE;

		$go_back =  "<html>
		<head>
		<title>Hosting.ai: Admin Panel</title>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=koi8-r\">
		</head>
		<body bgcolor=\"EEEEEE\">
		<form name=\"client\" method=\"POST\" action=\"client_add.php\">
		<input type=\"hidden\" name=\"login\" value=\"".$login."\">
		<input type=\"hidden\" name=\"email\" value=\"".$email."\">
		<input type=\"hidden\" name=\"firstname\" value=\"".$firstname."\">
		<input type=\"hidden\" name=\"lastname\" value=\"".$lastname."\">
		<input type=\"hidden\" name=\"company\" value=\"".$company."\">
		<input type=\"hidden\" name=\"country\" value=\"".$country."\">
		<input type=\"hidden\" name=\"region\" value=\"".$region."\">
		<input type=\"hidden\" name=\"postal\" value=\"".$postal."\">
		<input type=\"hidden\" name=\"city\" value=\"".$city."\">
		<input type=\"hidden\" name=\"address\" value=\"".$address."\">
		<input type=\"hidden\" name=\"phone\" value=\"".$phone."\">
		<input type=\"hidden\" name=\"fax\" value=\"".$fax."\">
		<input type=\"hidden\" name=\"egrpou\" value=\"".$egrpou."\">
		<input type=\"hidden\" name=\"userhdl\" value=\"".$userhdl."\">
		<input type=\"hidden\" name=\"active\" value=\"".($active?"on":"off")."\">
		<input type=\"hidden\" name=\"cp_access\" value=\"".($cp_access?"on":"off")."\">

		<script language=\"javascript\">
			document.client.submit();
		</script>
		<noscript>
			You don't have JavaScript to be enabled. Please, push this button:<br>
			<input type=\"submit\">
		</noscript>
		</form>";

		if ($password !== $password2) {
			$_SESSION["adminpanel_error"] = "Passwords do not match";
			echo $go_back;
			exit;
		}

		if (	$login != "" &&
			$password != "" &&
			$firstname != "" &&
			$lastname != "" &&
			$email != "" &&
			$country != "" &&
			$city != "" &&
			$postal != "" &&
			$address != "" &&
			$phone != "") {

			$passwd = crypt($password);

			if ($PM->CC->add_client($login, $passwd, $email, $firstname, $lastname, $company, $country, $region, $postal, $city, $address, $phone, $fax, $egrpou, $userhdl, $active, $cp_access, "") != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't create client: ".$_SESSION["adminpanel_error"];
				echo $go_back;
				exit;
			}
		}
		else {
			$_SESSION["adminpanel_error"] = "Not enough data";
			echo $go_back;
			exit;
		}

		Header("Location: client.php\n\n");
		exit;
	}

	elseif ($act == "change_account") {

		$id = safe_get("id", "POST", 255);
		$login = safe_get("login", "POST", 255);
		$password = safe_get("password", "POST", 255);
		$password2 = safe_get("password2", "POST", 255);
		$email = safe_get("email", "POST", 255);
		$firstname = safe_get("firstname", "POST", 255);
		$lastname = safe_get("lastname", "POST", 255);
		$company = safe_get("company", "POST", 255);
		$country = safe_get("country", "POST", 255);
		$region = safe_get("region", "POST", 255);
		$postal = safe_get("postal", "POST", 255);
		$city = safe_get("city", "POST", 255);
		$address = safe_get("address", "POST", 255);
		$phone = safe_get("phone", "POST", 255);
		$fax = safe_get("fax", "POST", 255);
		$egrpou = safe_get("egrpou", "POST", 255);
		$userhdl = safe_get("userhdl", "POST", 255);
		$active = safe_get("active", "POST", 5) == "on"?TRUE:FALSE;
		$cp_access = safe_get("cp_access", "POST", 5) == "on"?TRUE:FALSE;
		$add_info = pure_get("add_info", "POST");

		if ($password !== $password2) {
			$_SESSION["adminpanel_error"] = "Password do not match";
			Header("Location: ".$_SERVER["HTTP_REFERER"]."\n\n");
			exit;
		}

		if (	$id != "" &&
			$login != "" &&
			$firstname != "" &&
			$lastname != "" &&
			$email != "" &&
			$country != "" &&
			$city != "" &&
			$postal != "" &&
			$address != "" &&
			$phone != "") {

			if ($password != "") {
				$passwd = crypt($password);
			}
			else
				$passwd = "";

			if ($PM->CC->change_client($id, $login, $passwd, $email, $firstname, $lastname, $company, $country, $region, $postal, $city, $address, $phone, $fax, $egrpou, $userhdl, $active, $cp_access, $add_info) != TRUE) {
				$_SESSION["adminpanel_error"] = "Can't change client: ".$_SESSION["adminpanel_error"];
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
				if ($PM->CC->remove_client($id) != TRUE) {
					$_SESSION["adminpanel_error"] = "Can't remove client: ".$_SESSION["adminpanel_error"];
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
