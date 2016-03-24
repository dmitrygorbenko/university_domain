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
	print(" <br> <br> <br> </body> </html> ");
};

function print_not_enought($name, $zone)
{
	print("<br><br><center><font size=+1>Извените, но Вы должны заполнить все необходимые поля
	<br><br>
	<form action=step_1.php method=POST>
	<input type=hidden name=domain_name value=\"".$name."\">
	<input type=hidden name=domain_zone value=\"".$zone."\">
	<input type=submit value=\"  Назад  \" >
	</form>
	</font></center>");
};

function print_not_agree($name, $zone)
{
	print("<br><br><center><font size=+1>Извените, но Вы должны принять правила домена
	<br><br>
	<form action=step_1.php method=POST>
	<input type=hidden name=domain_name value=\"".$name."\">
	<input type=hidden name=domain_zone value=\"".$zone."\">
	<input type=submit value=\"  Назад  \" >
	</form>
	</font></center>");
};

function print_invalid_email($email, $name, $zone)
{
	print("<br><br><center><font size=+1>Вы ввели неверный адрес электронной почты: <b>".$email."</b>
	<br><br>
	<form action=step_1.php method=POST>
	<input type=hidden name=domain_name value=\"".$name."\">
	<input type=hidden name=domain_zone value=\"".$zone."\">
	<input type=submit value=\"  Назад  \" >
	</form>
	</font></center>");
};

function print_bad_exists_data($check_domain, $check_nichandle, $name, $zone) {
	print("<br><br><center><font size=+1>Вы ввели неверный домен или NIC-handle.
	<br><br>
	<form action=step_1.php method=POST>
	<input type=hidden name=domain_name value=\"".$name."\">
	<input type=hidden name=domain_zone value=\"".$zone."\">
	<input type=submit value=\"  Назад  \" >
	</form>
	</font></center>");
}

function print_abuse_NIC($name, $zone)
{
	include "include.php";

	print("<br><br><center><font size=+1>Вы ввели NIC-handle, который уже находится в нашей Базе данных.
	</font>
	<br><br>
	Возможно, Вы забыли как регистрировали домен у нас. 
	<br>
	Вернитесь назад и укажите в разделе \"Я уже регистрировался\" этот NIC-handle.
	<br><br>
	Если вы уверены, что Вы не регистрировались у нас, пожалуйста, сообщите об этом этому адресу <A href=\"mailto:".$system_offence_email."\">".$system_offence_email."</A>
	<br><br>
	<form action=step_1.php method=POST>
	<input type=hidden name=domain_name value=\"".$name."\">
	<input type=hidden name=domain_zone value=\"".$zone."\">
	<input type=submit value=\"  Назад  \" >
	</form>
	</center>
	");
}

function SQL_error($mail_descr, $debug_descr, $dbresult)
{
	include "include.php";

	print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую".
		"<br>Попробуйте повторить попытку через некоторое время".
		"<br>Извените за неудобства</font></center>");

	if ( $system_report_by_mail == "1") {
		$text = "Error: While executing step_2.php: ".$mail_descr."";
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

function print_error_lot_answers($query)
{
	include "include.php";

	print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую".
		"<br>Попробуйте повторить попытку через некоторое время".
		"<br>Извените за неудобства</font></center>");

	if ( $system_report_by_mail == "1") {
		$text = "Error: While executing step_2.php: \n\t\tquery: ".$query."\n\n\tResult contains more that 1 row";
		$mail_to = $system_problem_email;
		$subject = "Critical Error: Database broken";
		
		mail($mail_to, $subject, $text);
	}

	if ($system_debug == "1") {
		print("<br><br><center><font size=+1>Debug: A lot of results. Query: ".$query."</font></center>");
	}

	print_end();
	exit(0);
};

function load_price_from_file($domain_zone)
{
	include "include.php";

	$file = fopen("cfg/domain_to_lease", "r");

	if (!$file) {

		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");

		if ($system_report_by_mail == "1") {
			$text = "Error: While executing step_2.php:
				Can't create file domain_to_lease in \"cfg\" directory";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";

			mail($mail_to, $subject, $text);
		}

		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Системная ошибка - не найден файл cfg/domain_to_lease
			<br><br><a alt=back href=step_1.php>Назад</a></font></center>");
		}

		print_end();
		exit(0);
	}

	while(!feof($file)) {
		$str = fgets($file, 1024);
		if ($str == "")
			continue;
		
		$str = trim($str);
		$pp = strtok($str ,":");
		if ($domain_zone == $pp) {
			$pp = strtok(":");
			$pp = strtok(":");
			$pp = strtok(":");
			$pp = strtok(":");
			$price = $pp;
			break;
		}
	}

	fclose($file);

	return $price;
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


	$referer = getenv("HTTP_REFERER");
	preg_match("/[^\.\/]+\.[^\.\/]+$/", $referer, $matches);
	if (!isset($matches[0])) {
		print("<br><br><center><font size=+1>Надо последовательно выполнять все операции регистрации !</font></center>");
		print_end();
		exit(0);
	}
	if ($matches[0] != "step_1.php") {
		print("<br><br><center><font size=+1>Надо последовательно выполнять все операции регистрации !</font></center>");
		print_end();
		exit(0);
	}

	$check_nichandle = safe_get($HTTP_POST_VARS, "check_nichandle", 255);
	$check_domain = safe_get($HTTP_POST_VARS, "check_domain", 255);

	$domain_name = safe_get($HTTP_POST_VARS, "domain_name", 255);
	$domain_zone = safe_get($HTTP_POST_VARS, "domain_zone", 255);
	$lease_time = safe_get($HTTP_POST_VARS, "lease_time", 10);
	$first_name = safe_get($HTTP_POST_VARS, "first_name", 255);
	$last_name = safe_get($HTTP_POST_VARS, "last_name", 255);
	$company_name = safe_get($HTTP_POST_VARS, "company_name", 255);
	$country = safe_get($HTTP_POST_VARS, "country", 255);
	$region = safe_get($HTTP_POST_VARS, "region", 255);
	$post_index = safe_get($HTTP_POST_VARS, "post_index", 20);
	$city = safe_get($HTTP_POST_VARS, "city", 255);
	$address1 = safe_get($HTTP_POST_VARS, "address1", 255);
	$address2 = safe_get($HTTP_POST_VARS, "address2", 222);
	$telephone = safe_get($HTTP_POST_VARS, "telephone", 20);
	$telefax = safe_get($HTTP_POST_VARS, "telefax", 20);
	$email_addr = safe_get($HTTP_POST_VARS, "email_addr", 20);
	$add_info = safe_get($HTTP_POST_VARS, "add_info", 500);
	$nichandle = safe_get($HTTP_POST_VARS, "nichandle", 255);
	$rules_agree = safe_get($HTTP_POST_VARS, "rules_agree", 20);

	$domain_name = strtolower($domain_name);
	$domain_zone = strtolower($domain_zone);
	
	$check_domain = strtolower($check_domain);
	$check_nichandle = strtoupper($check_nichandle);
	$nichandle = strtoupper($nichandle);

	//////////////////////////////////////////////////////
	//		BEGIN HANDLE QUERY

	/* Main Ideology: We just check client's data (NIC or domain_name)
	   If check will fail - print_error.
	   if all right - print his person info and contunue registration */
	
	$id_person_data = 0;
	$id_domain_data = 0;

	if (($check_domain != "your_domain.our.zone" && $check_domain != "") || $check_nichandle != "") {
		
		$db = new DB_pgsql();
		$dbresult = $db->connect( DB::parseDSN($system_sql_str_connect));
		if ( $dbresult != DB_OK)
			SQL_error("Can't connect to ".$system_sql_driver.":\n\t\tstring for connect: ".$system_sql_str_connect,
					"Не могу подключиться к SQL серверу", $dbresult);
	
		$person_exists = "false";
		$domain_exists = "false";

		if ($check_nichandle != "") {
			/* Find your client in person table ... */
			$query = "SELECT id_table FROM person_table WHERE userhdl = '".$check_nichandle."'";
			print("<br>".$query."<br>");
			$dbresult = $db->simpleQuery($query);
			if (DB::isError($dbresult))
				SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
						"Не могу выполнить запрос к SQL серверу", $dbresult);
			$rows = $db->numRows($dbresult);
			if ($rows == 1) {
				$person_exists = "true";
				$db->fetchInto( $dbresult, $arr, 0 );
				$id_person_data = $arr[0];
				print("<br>id_person_data: ".$id_person_data."<br>");
			}
			if ($rows > 1) {
				print_error_lot_answers($query);
			}
			$db->freeResult($dbresult);
		}
		if ($check_domain != "your_domain.our.zone") {
			/* Well, check  domain name - is domain in table ?*/
			$query = "SELECT id_table FROM domain_data WHERE full_name = '".$check_domain."'";
			print("<br>".$query."<br>");
			$dbresult = $db->simpleQuery($query);
			if (DB::isError($dbresult))
				SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
						"Не могу выполнить запрос к SQL серверу", $dbresult);
			$rows = $db->numRows($dbresult);
			if ($rows == 1) {
				$domain_exists = "true";
				$person_exists = "true";
				$db->fetchInto( $dbresult, $arr, 0 );
				$id_domain_data = $arr[0];
				print("<br>id_domain_data: ".$id_domain_data."<br>");
			}
			if ($rows > 1) {
				print_error_lot_answers($query);
			}
			$db->freeResult($dbresult);
	
			if ($domain_exists == "true") {
				/* Realy domain exists ? - ok, well find his id in person table */
				$query = "SELECT id_table, id_person_data FROM person_owns_table WHERE id_domain_data = '".$id_domain_data."'";
				print("<br>".$query."<br>");
				$dbresult = $db->simpleQuery($query);
				if (DB::isError($dbresult))
					SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
							"Не могу выполнить запрос к SQL серверу", $dbresult);
				$rows = $db->numRows($dbresult);
				if ($rows == 1) {
					$db->fetchInto( $dbresult, $arr, 0 );
					$id_person_data = $arr[1];
					print("<br>id_person_data: ".$id_person_data."<br>");
				}
				if ($rows > 1) {
					print_error_lot_answers($query);
				}
				$db->freeResult($dbresult);
			}			
		}

		if ($person_exists != "true") {
			/*	Your client is a liar ! Get him out :-) ! */
			print_bad_exists_data($check_domain, $check_nichandle, $domain_name, $domain_zone);
			print_end();
			exit(0);
		}
		
		/* Well Finally client is realy your old friend - put his info into HTML browser */
		
		/*	Get info about him from table */
		
		$query = "SELECT * FROM person_table WHERE id_table = '".$id_person_data."'";
		print("<br>".$query."<br>");
		$dbresult = $db->simpleQuery($query);
		if (DB::isError($dbresult))
			SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
					"Не могу выполнить запрос к SQL серверу", $dbresult);
		$rows = $db->numRows($dbresult);
		if ($rows > 0) {
			$db->fetchInto( $dbresult, $arr, 0 );
			$id_person_data = $arr[0];

			$first_name = $arr[1];
				$first_name = trim($first_name);
			$last_name = $arr[2];
				$last_name = trim($last_name);
			$company_name = $arr[3];
				$company_name = trim($company_name);
			$country = $arr[4];
				$country = trim($country);
			$region = $arr[5];
				$region = trim($region);
			$post_index = $arr[6];
				$post_index = trim($post_index);
			$city = $arr[7];
				$city = trim($city);
			$address1 = $arr[8];
				$address1 = trim($address1);
			$address2 = $arr[9];
				$address2 = trim($address2);
			$telephone = $arr[10];
				$telephone = trim($telephone);
			$telefax = $arr[11];
				$telefax = trim($telefax);
			$email_addr = $arr[12];
				$email_addr = trim($email_addr);
			$nichandle = $arr[14];
				$nichandle = trim($nichandle);
		}
		
		$db->freeResult($dbresult);
		
		/* Now, at least, disconnect from database */
		
		$db->disconnect();
		
		/* ... and print his data )) */

	}
	else {
	
	/*	Well, this is a new client - check his data :-)) */
	
		if ($domain_name == "" || 
			$first_name == "" || 
			$last_name == "" ||
			$post_index == "" ||
			$city == "" ||
			$address1 == "" ||
			$telephone == "" ||
			$email_addr == "") {
		
			print_not_enought($domain_name, $domain_zone);
			print_end();
			exit(0);
		}
	
		if ($rules_agree == "") {
			print_not_agree($domain_name, $domain_zone);
			print_end();
			exit(0);
		}

		////////////////////////////////////////
		//	CHECK EMAIL ADDR
	
		if (!strstr($email_addr, "@")) {
			print_invalid_email($email_addr, $domain_name, $domain_zone);
			print_end();
			exit(0);
		}
	
		$pos = strpos($email_addr, "@");
		if ($email_addr[$pos+1] == "") {
			print_invalid_email($email_addr, $domain_name, $domain_zone);
			print_end();
			exit(0);
		}
	
		if ($email_addr[$pos-1] == "") {
			print_invalid_email($email_addr, $domain_name, $domain_zone);
			print_end();
			exit(0);
		}
	
		/* Now, if client set up NIC-handle and we will check if we have same in out database,,,
		maybe he forgot what had registered domain in your database... */
		
		if ($nichandle != "") {
			
			$db = new DB_pgsql();
			$dbresult = $db->connect( DB::parseDSN($system_sql_str_connect));
			if ( $dbresult != DB_OK)
				SQL_error("Can't connect to ".$system_sql_driver.":\n\t\tstring for connect: ".$system_sql_str_connect,
						"Не могу подключиться к SQL серверу", $dbresult);
		
			$person_exists = "false";
	
			/* Find your client in person table ... */
			$query = "SELECT id_table FROM person_table WHERE userhdl = '".$nichandle."'";
			print("<br>".$query."<br>");
			$dbresult = $db->simpleQuery($query);
			if (DB::isError($dbresult))
				SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
						"Не могу выполнить запрос к SQL серверу", $dbresult);
			$rows = $db->numRows($dbresult);
			if ($rows == 1) {
				$person_exists = "true";
				$db->fetchInto( $dbresult, $arr, 0 );
				$id_person_data = $arr[0];
				print("<br>id_person_data: ".$id_person_data."<br>");
			}
			if ($rows > 1) {
				print_error_lot_answers($query);
			}

			$db->freeResult($dbresult);
		
			/* What we have to do if client in your database ?...
			   1. Tell to him what NIC in base and let him go...
			   2. Or get clients data from database ...
			   
			   But what if he filled data at step_1 another that we have in database ?
			   He want to bug us ? 
			   
			   So, I will tell him, what his NIC already in database, 
			   and let he set up this NIC in "existen NIC" or send an email to system administrator */
			
			$db->disconnect();
			   
			if ($person_exists == "true") {
				print_abuse_NIC($domain_name, $domain_zone);
				print_end();
				
				exit(0);
			}
		}
	}
	
	/* Load price data */

	$price = load_price_from_file($domain_zone);
	
	///////////////////////////////////////////////////////
	//		PRINT MAIN DATA	

	print("
	<center ><font size=6 color=".$titlecolor.">Регистрация доменных имен</font></center>
	<table border=0 width=660px>
		<tr>
	
		<td width=60px></td>
		<td width=600px>
			<table border=0 width=600px>
	
				<tr><td width=200px>Домен:</td>
					<td width=400px><b>".$domain_name.".".$domain_zone."</b></td></tr>
				<tr><td>Срок регистрации:</td>
					<td>".$lease_time." год(а)</td></tr>
				<tr><td>Стоимость:</td>
					<td>".$price." x ".$lease_time." = ".($price * $lease_time)." грн.</td></tr>
	
				<tr><td colspan=2><font size=+1><b>Информация о клиенте:</b></font></td></tr>
	
				<tr><td>Имя:</td>
					<td>".$first_name."</tr>
				<tr><td>Фамилия:</td>
					<td>".$last_name."</td></tr>
				<tr><td>Компания:</td>
					<td>".$company_name."</td></tr>
				<tr><td>Страна:</td>
					<td>".$country."</td></tr>
				<tr><td>Область:</td>
					<td>".$region."</td></tr>
				<tr><td>Почтовый индекс:</td>
					<td>".$post_index."</td></tr>
				<tr><td>Город:</td>
					<td>".$city."</td></tr>
				<tr><td>Адрес:</td>
					<td>".$address1."</td></tr>
				<tr><td>Адрес 2:</td>
					<td>".$address2."</td></tr>
				<tr><td>Контактный телефон:</td>
					<td>".$telephone."</td></tr>
				<tr><td>Номер телефакса:</td>
					<td>".$telefax."</td></tr>
				<tr><td>Адрес электронной почты:</td>
					<td>".$email_addr."</td></tr>
				<tr><td valign=top>Дополнительная информация:</td>
					<td valign=top>".$add_info."</td></tr>
				<tr><td>NIC-handle:</td>
					<td>".$nichandle."</td></tr>
	
				<tr height=44><td>
				
				<form action=step_1.php method=POST>
				<input type=hidden name=domain_name value=\"".$domain_name."\">
				<input type=hidden name=domain_zone value=\"".$domain_zone."\">
				<input type=submit value=Назад >
				</form>
				
				</td>
				<td>

				<form action=complete.php method=POST>
				<input type=hidden name=comlete_pid value=\"".$id_person_data."\">
				<input type=hidden name=comlete_did value=\"".$id_domain_data."\">
				
				<input type=hidden name=domain_name value=\"".$domain_name."\">
				<input type=hidden name=domain_zone value=\"".$domain_zone."\">
				<input type=hidden name=lease_time value=\"".$lease_time."\">
				<input type=hidden name=first_name value=\"".$first_name."\">
				<input type=hidden name=last_name value=\"".$last_name."\">
				<input type=hidden name=company_name value=\"".$company_name."\">
				<input type=hidden name=country value=\"".$country."\">
				<input type=hidden name=region value=\"".$region."\">
				<input type=hidden name=post_index value=\"".$post_index."\">
				<input type=hidden name=city value=\"".$city."\">
				<input type=hidden name=address1 value=\"".$address1."\">
				<input type=hidden name=address2 value=\"".$address2."\">
				<input type=hidden name=telephone value=\"".$telephone."\">
				<input type=hidden name=telefax value=\"".$telefax."\">
				<input type=hidden name=email_addr value=\"".$email_addr."\">
				<input type=hidden name=add_info value=\"".$add_info."\">
				<input type=hidden name=nichandle value=\"".$nichandle."\">
				<input type=hidden name=full_price value=\"".($price * $lease_time)."\">
				<input type=submit value=Готово >
				</form>
				</td>
				</tr>
			</table>
		</td>
		</tr>
	</table>
	");
	
	print_end();

</script>
