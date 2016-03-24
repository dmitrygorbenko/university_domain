<script language="php">

include "include.php";
include 'DB.php';
include 'DB/pgsql.php';

function print_header()
{

include "include.php";

print("

<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
<head>
<style type=\"text/css\">
		body {font-family: sans-serif;}
		A:link        {font-family: sans; font-size: x-medium; text-decoration: none; color: ".$alink."}
		A:visited     {font-family: sans; font-size: x-medium; text-decoration: none; color: ".$avisited."}
		A:hover       {font-family: sans; font-size: x-medium; text-decoration: underline; color: ".$ahover."}
		A:link.nav    {font-family: sans; color: ".$alink."}
		A:visited.nav {font-family: sans; color: ".$avisited."}
		A:hover.nav   {font-family: sans; color: ".$ahover."}
		.nav          {font-family: sans; color: ".$nav."}
//-->
</style>
   <meta http-equiv=Content-Type content=\"text/html; charset=koi8-r\">
  <title>Регистрация доменных имен</title>
</head>
<body BGCOLOR=".$bgcolor." text=".$textcolor.">
");

};

function print_end()
{
	print("<br><br><br></body></html>");
};

function print_need_id()
{
	print("<center><font size=+1>Извените, но должен быть номер.</font></center>");
};

function print_bad_id()
{
print("<center><font size=+1>Неверный номер заявки, или она уже в Базе данных.</font></center>");
};

function SQL_error($mail_descr, $debug_descr, $dbresult)
{
include "include.php";

		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую".
			"<br>Попробуйте повторить попытку через некоторое время".
			"<br>Извените за неудобства</font></center>");
	
		if ( $system_report_by_mail == "1") {
			$text = "Error: While executing accept_query.php: ".$mail_descr."";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";
			
			mail($mail_to, $subject, $text);
		}

		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: ".$debug_descr."</font></center>");
			print("<pre>");
			print_r($dbresult);
			print("</pre>");
		}

		print_end();
		exit(0);
};

function safe_get($HTTP_VARS, $param_name, $param_length)
{
	include "include.php";
	$value = "";
	
	if (isset($HTTP_VARS[$param_name]))
		$value = $HTTP_VARS[$param_name];
	else
		return $value;

	$value = substr($value, 0, $param_length);

	$value = strtr($value, $trans);

	return $value;
};

	print_header();

	/** Hm, Here we will determine, how to do with client data. It's very
	    dangerous part of code. Be sure, what you have complete defence 
	    
	    Main rule: NEVER TRUST CLIENT. Keep this rule active. **/

	$id = safe_get($HTTP_POST_VARS, "id", 255);

	/////////////////////////////////////////////////////////
	//		BEGIN TO HANDLE QUERY

	///////////////////////////////
	//	SO, CHECK - HAVE WE ID ?
	if ($id == "") {
		print_need_id();
		print_end();
		exit(0);
	}
	
	if (!is_file($system_confirm_dir."/".$id)) {
		print_bad_id();
		print_end();
		exit(0);
	}

	////////////////////////////////////////////////
	//	READ DATA FROM FILE

	$file = fopen($system_cfg_dir."/".$system_cfg_option, "r");
	if (!$file) {
		
		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");
		
		if ($system_report_by_mail == "1") {
			$text = "Error: While executing check_query.php: 
				Can't open file ".$system_cfg_option." in \"".$system_cfg_dir."\" directory";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";
			
			mail($mail_to, $subject, $text);
		}		

		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Не могу открыть confirm файл</font></center>");
		}

		print_end();
		exit(0);
	}

	$pp = "";

	$ns1 = $pp;
	$ns2 = $pp;
	$mx = $pp;

	while (!feof($file)) {
		$str = fgets($file, 1024);
		
		if ($str == "")
			continue;
		
		$str = trim($str);
		$pp = strtok($str, ":");
		$pp = strtok(":");
		$pp = trim($pp);
		
		if (strstr($str, "ns1:"))
			$ns1 = $pp;
		if (strstr($str, "ns2:"))
			$ns2 = $pp;
		if (strstr($str, "mx:"))
			$mx = $pp;
	}

	fclose($file);

	$file = fopen($system_confirm_dir."/".$id, "r");
	if (!$file) {
		
		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");
		
		if ($system_report_by_mail == "1") {
			$text = "Error: While executing check_query.php: 
				Can't open file ".$id." in \"".$system_confirm_dir."\" directory";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";
			
			mail($mail_to, $subject, $text);
		}		

		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Не могу открыть confirm файл</font></center>");
		}

		print_end();
		exit(0);
	}

	$pp = "";

	$old_friend = $pp;
	$comlete_did = $pp;
	$comlete_pid = $pp;
	
	$query_time = $pp;
	$confirm_time = $pp;
	$domain_name = $pp;
	$domain_zone = $pp;
	$lease_time = $pp;
	$full_price = $pp;
	$first_name = $pp;
	$last_name = $pp;
	$company_name = $pp;
	$country = $pp;
	$region = $pp;
	$post_index = $pp;
	$city = $pp;
	$address1 = $pp;
	$address2 = $pp;
	$telephone = $pp;
	$telefax = $pp;
	$email_addr = $pp;
	$add_info = $pp;
	$nichandle = $pp;

	while (!feof($file)) {
		$str = fgets($file, 1024);
		
		if ($str == "")
			continue;
		
		$str = trim($str);
		$pp = strtok($str, ":");
		$pp = strtok(":");
		$pp = trim($pp);
		
		if (strstr($str, "old_friend:"))
			$old_friend = $pp;
		if (strstr($str, "comlete_pid:"))
			$comlete_pid = $pp;
		if (strstr($str, "comlete_did:"))
			$comlete_did = $pp;
		
		if (strstr($str, "query_time:"))
			$query_time = $pp;
		if (strstr($str, "confirm_time:"))
			$confirm_time = $pp;
		if (strstr($str, "domain_name:"))
			$domain_name = $pp;
		if (strstr($str, "domain_zone:"))
			$domain_zone = $pp;
		if (strstr($str, "lease_time:"))
			$lease_time = $pp;
		if (strstr($str, "full_price:"))
			$full_price = $pp;
		if (strstr($str, "first_name:"))
			$first_name = $pp;
		if (strstr($str, "last_name:"))
			$last_name = $pp;
		if (strstr($str, "company_name:"))
			$company_name = $pp;
		if (strstr($str, "country:"))
			$country = $pp;
		if (strstr($str, "region:"))
			$region = $pp;
		if (strstr($str, "post_index:"))
			$post_index = $pp;
		if (strstr($str, "city:"))
			$city = $pp;
		if (strstr($str, "address1:"))
			$address1 = $pp;
		if (strstr($str, "address2:"))
			$address2 = $pp;
		if (strstr($str, "telephone:"))
			$telephone = $pp;
		if (strstr($str, "telefax:"))
			$telefax = $pp;
		if (strstr($str, "email_addr:"))
			$email_addr = $pp;
		if (strstr($str, "add_info:"))
			$add_info = $pp;
		if (strstr($str, "nichandle:"))
			$nichandle = $pp;
		if (strstr($str, "query_from_host:"))
			$query_from_host = $pp;
		if (strstr($str, "confirm_from_host:"))
			$confirm_from_host = $pp;
	}

	fclose($file);

	////////////////////////////////////////////////
	//	CONNECT TO DATABASE
	
	$db = new DB_pgsql();
	$dbresult = $db->connect( DB::parseDSN($system_sql_str_connect));
	if ( $dbresult != DB_OK)
		SQL_error("Can't connect to ".$system_sql_driver.":\n\t\tstring for connect: ".$system_sql_str_connect,
				"Не могу подключиться к SQL серверу", $dbresult);

	//////////////////////////////////////////////////////
	//	ADD DATA TO DOMAIN_DATA TABLE

// OK - WORKS

	$query = "INSERT INTO domain_data (full_name, d_name, d_zone, d_type) VALUES ('".$domain_name.".".$domain_zone."', '".$domain_name."', '".$domain_zone."', 0)";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->freeResult($dbresult);
	

	//////////////////////////////////////////////////////
	//	GET DOMAIN ID TO USE IT IN QUERIES
// OK - WORKS
	
	$query = "SELECT id_table FROM domain_data WHERE d_name = '".$domain_name."' AND d_zone = '".$domain_zone."'";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->fetchInto( $dbresult, $arr, 0 );
	$id_domain_data = $arr[0];
	print("<br>".$id_domain_data."<br>");
	$db->freeResult($dbresult);


//	$id_domain_data	= 1;

	//////////////////////////////////////////////////////
	//	UPDATE PERSON_TABLE

// OK - WORKS
	/* If $old_friend == "no", when create new record in PERSON_TABLE */

	If ($old_friend == "no")
		print("<br>Новый клиент<br>");
	else
		print("<br>Старый клиент<br>");
	
	If ($old_friend == "no") {
		$query = "INSERT INTO person_table ".
				"(firstname, lastname, company, country, region, postal, city, address1, address2, phone, fax, email, egrpou, userhdl, cash) ".
				"VALUES('".$first_name."',".
					" '".$last_name."',".
					" '".$company_name."',".
					" '".$country."',".
					" '".$region."',".
					" '".$post_index."',".
					" '".$city."',".
					" '".$address1."',".
					" '".$address2."',".
					" '".$telephone."',".
					" '".$telefax."',".
					" '".$email_addr."',".
					" 0,".
					" '".$nichandle."',".
					" '0.0' )";
		print("<br>".$query."<br>");

		$dbresult = $db->simpleQuery($query);
		if (DB::isError($dbresult))
			SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
					"Не могу выполнить запрос к SQL серверу", $dbresult);
		$db->freeResult($dbresult);
	}

	$query = "SELECT id_table FROM person_table WHERE firstname = '".$first_name."' AND lastname = '".$last_name."' AND postal = '".$post_index."' AND city = '".$city."' AND address1 = '".$address1."' AND phone = '".$telephone."' AND email = '".$email_addr."'";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->fetchInto( $dbresult, $arr, 0 );
	$id_person_data = $arr[0];
	print("<br>".$id_person_data."<br>");
	$db->freeResult($dbresult);


//	$id_person_data	= 1;

	//////////////////////////////////////////////////////
	//	UPDATE PERSON_OWNS_TABLE
// OK - WORKS

	$query = "INSERT INTO person_owns_table (id_person_data, id_domain_data) VALUES ('".$id_person_data."', '".$id_domain_data."')";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->freeResult($dbresult);


	//////////////////////////////////////////////////////
	//	FILL IN TECHNICAL_TABLE TABLE

// OK - WORKS
	
	$query = "INSERT INTO technical_table (id_domain_data, ns1, ns2, mx, query_from_host, confirm_from_host) VALUES ('".$id_domain_data."', '".$ns1."', '".$ns2."', '".$mx."', '".$query_from_host."', '".$confirm_from_host."')";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->freeResult($dbresult);


	//////////////////////////////////////////////////////
	//	FILL IN DATES_TABLE TABLE

// OK - WORKS

	$query_time = date("Y-m-d G:i:s", $query_time);
	$confirm_time = date("Y-m-d G:i:s", $confirm_time);
		
	$query = "INSERT INTO dates_table (id_domain_data, query_post_date, confirm_date, lease_time) VALUES ('".$id_domain_data."', '".$query_time."', '".$confirm_time."', '".$lease_time."')";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->freeResult($dbresult);
	
	//////////////////////////////////////////////////////
	//	FILL IN DESCR_TABLE TABLE

// OK - WORKS
	
	$query = "INSERT INTO descr_table (id_domain_data, add_info, why_released, why_refused, notes) VALUES ('".$id_domain_data."', '".$add_info."', '', '', '')";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->freeResult($dbresult);

	//////////////////////////////////////////////////////
	//	FILL IN DOMAIN_STATUS_TABLE TABLE

// OK - WORKS
	
	$query = "INSERT INTO domain_status_table (id_domain_data) VALUES ('".$id_domain_data."')";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->freeResult($dbresult);

	//////////////////////////////////////////////////////
	//	FILL IN TO_REGISTER TABLE

// OK - WORKS

	$query = "INSERT INTO wait_for_pay (id_domain_data) VALUES ('".$id_domain_data."')";
	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$db->freeResult($dbresult);

	//////////////////////////////////////////////////////
	//	DISCONNECT FROM SQL Drivet
	
	$db->disconnect();
	
	////////////////////////////////////////////////
	//	PREPARE MAIL AND SEND IT

	$mail_to = $email_addr;
	$subject = "Регистрация доменного имени ".$domain_name.".".$domain_zone.": Оплата";
	$text = "
	Здравствуйте, ".$first_name." ".$last_name." 
	
	Ваша заявка принята.
	Домен к регистрации:
	".$domain_name.".".$domain_zone."
	Срок регистрации: ".$lease_time." год(а)
	Стоимость: ".$full_price." грн.
		
	Теперь Вам необходимо оплатить услугу регистрации.
	Это можно сделать следующим способом...

	Помните: для оплаты регистрации домена Вам отводится две недели.
	Весь этот срок домен не будет зарегистрирован, и если кто-то другой
	также подаст заявку на такой же домен и оплатит его 
	раньше Вас - Вы потеряете домен.
	
	С наилучшими пожеланиями,	
	Регистратор доменных имен \"".$system_company_title."\".";
	
	mail($mail_to, $subject, $text);

	/////////////////////////////////////////////////////////
	//		PRINT MAIN DATA

	unlink($system_confirm_dir."/".$id);
	
/*
print("<HTML>\n<HEAD>\n<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-koi8-r\">\n
	<META HTTP-EQUIV=\"refresh\" CONTENT=\"0;url=".$system_href_main_admin."\">");
print("\n</HEAD>");
print("<body BGCOLOR=#E9E9E9 text=#000000 >");
print("\n</BODY>\n </HTML>");
*/
</script>
