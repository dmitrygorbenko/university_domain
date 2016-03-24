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

<body BGCOLOR=".$bgcolor." text=".$textcolor." >
");

};

function print_end()
{
	print("</body></html>");
};

function print_not_enought_data()
{
	print("<br><br><center><font size=+1>Вы не ввели доменное имя. Попробуйте заново
	<br><br><a alt=back href=step_0.php>Назад</a></font></center>");
};


function print_domain_in_use($name, $zone)
{
	print("<br><br><center><font size=+1>Извените, но домен <b>".$name.".".$zone."</b> уже используется
	<br>Попробуйте другое имя
	<br><br><a alt=back href=step_0.php>Назад</a></font></center>");
};

function print_domain_name($name, $zone)
{
	print("<br><br><center><font size=+1>В доменном имени <b>".$name."</b> содержатся недопустимые символы
	<br>Прочтите внимательно правила регистрации доменных имен
	<br><br><a alt=back href=step_0.php>Назад</a></font></center>");
};

function SQL_error($mail_descr, $debug_descr, $dbresult)
{
include "include.php";

		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую".
			"<br>Попробуйте повторить попытку через некоторое время".
			"<br>Извените за неудобства</font></center>");

		if ( $system_report_by_mail == "1") {
			$text = "Error: While executing step_1.php: ".$mail_descr."";
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
	if ($matches[0] != "step_0.php" && $matches[0] != "step_2.php") {
		print("<br><br><center><font size=+1>Надо последовательно выполнять все операции регистрации !</font></center>");
		print_end();
		exit(0);
	}

	$domain_name = safe_get($HTTP_POST_VARS, "domain_name", 255);
	$domain_zone = safe_get($HTTP_POST_VARS, "domain_zone", 255);

	//////////////////////////////////////////////////////////////
	//		BEGIN HANDLE QUERY

	if ($domain_name == "") {
		print_not_enought_data();
		print_end();
		exit(0);
	}
	
	$result = "ok";
	
	/////////////////////////////////////////
	//	CHECK FOR DOMAIN SYNTAX

	if (strstr($domain_name, "."))
		$result = "bad";

	if ($result == "bad") {
		print_domain_name($domain_name, $domain_zone);
		print_end();
		exit(0);
	}

	/////////////////////////////////////////
	// 	CHECK IF DOMAIN IS FREE

	$result = "ok";
	
	$db = new DB_pgsql();
	$dbresult = $db->connect( DB::parseDSN($system_sql_str_connect));
	if ( $dbresult != DB_OK)
		SQL_error("Can't connect to ".$system_sql_driver.":\n\t\tstring for connect: ".$system_sql_str_connect,
				"Не могу подключиться к SQL серверу", $dbresult);

	
	$query = "SELECT id_table FROM domain_data WHERE d_name = '".$domain_name."' AND d_zone = '".$domain_zone."'";
//	print("<br>".$query."<br>");
	$dbresult = $db->simpleQuery($query);
	if (DB::isError($dbresult))
		SQL_error("Can't make query to ".$system_sql_driver.":\n\t\tstring for query: ".$query,
				"Не могу выполнить запрос к SQL серверу", $dbresult);
	$rows = $db->numRows($dbresult);
	if ($rows == 1)
		$result = "false";
	if ($rows > 1) {
		print_error_lot_answers($query);
	}	
	$db->freeResult($dbresult);
	
	if ($result == "false") {
		print_domain_in_use($domain_name, $domain_zone);
		print_end();
		exit(0);
	}

	//////////////////////////////////////////////////////////
	//		PRINT MAIN DATA

print("
<center><font size=6 color=".$titlecolor.">Регистрация доменных имен</font></center>
<br>
<form action=step_2.php method=POST>
<table border=0 width=660px>
	<tr>

	<td width=60px></td>
	<td width=600px>
		<table border=0 width=600px>

			<tr><td colspan=2><input type=hidden name=domain_name value=\"".$domain_name."\"> </td></tr>
			<tr><td colspan=2><input type=hidden name=domain_zone value=\"".$domain_zone."\"> </td></tr>
		
			<tr><td width=200px>Домен:</td>
				<td width=400px><b>".$domain_name.".".$domain_zone."</b></td></tr>

			<tr><td>Срок регистрации:</td>
				<td>
				<select name=lease_time >
					<option value=1>1</option>
					<option value=2>2</option>
				</select>
				года(а)
				</td></tr>

			<!-- If user Allready registered...  -->
				
			<tr><td colspan=2><br><font size=+1><b>Если вы уже регистрировали у нас домен, то введите свой NIC-handle:</b></font></td></tr>
			<tr><td>NIC-handle:</td>
				<td><input type=text size=30 name=check_nichandle ></td></tr>
			<tr><td colspan=2>или домен, который вы регистрировали у нас (полное имя):</td></tr>
			<tr><td>Доменное имя:</td>
				<td><input type=text size=30 name=check_domain value=\"your_domain.our.zone\" ></td></tr>
			
			<tr><td></td><td><input type=submit value=Далее ></td></tr>

			<!-- If this is a new client...  -->
															
			<tr><td colspan=2><br><font size=+1><b>Если Вы в первый раз регистрируете у нас домен, то заполните форму:</b></font></td></tr>

			<tr><td>*Имя:</td>
				<td><input type=text size=30 name=first_name ></td></tr>
			<tr><td>*Фамилия:</td>
				<td><input type=text size=30 name=last_name ></td></tr>
			<tr><td>Компания:</td>
				<td><input type=text size=30 name=company_name  ></td></tr>
			<tr><td>Страна:</td>
				<td><select name=country>");

	$file = fopen($system_cfg_dir."/".$system_cfg_countries, "r");
	
	if ($file == "") {

		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");
		
		if ($system_report_by_mail == "1") {
			$text = "Error: While executing step_1.php:
				Can't open file ".$system_cfg_countries." in \"".$system_cfg_dir."\" directory";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";
			
			mail($mail_to, $subject, $text);
		}		
		
		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Не могу открыть ".$system_cfg_countries." файл</font></center>");
		}

		print_end();
		exit(0);
	}

	while(!feof($file)) {
		$str = fgets($file, 1024);
		if ($str == "")
			continue;
		$str = trim($str);
		print("
					".$str);
	}
	
	fclose($file);
print("
				</select>
			<tr><td>Область:</td>
				<td><input type=text size=30 name=region ></td></tr>
			<tr><td>*Почтовый индекс:</td>
				<td><input type=text size=30 name=post_index ></td></tr>
			<tr><td>*Город:</td>
				<td><input type=text size=30 name=city ></td></tr>
			<tr><td>*Адрес:</td>
				<td><input type=text size=30 name=address1 ></td></tr>
			<tr><td>Адрес 2:</td>
				<td><input type=text size=30 name=address2 ></td></tr>
			<tr><td>*Контактный телефон:</td>
				<td><input type=text size=30 name=telephone ></td></tr>
			<tr><td>Номер телефакса:</td>
				<td><input type=text size=30 name=telefax ></td></tr>
			<tr><td>*Адрес электронной почты:</td>
				<td><input type=text size=30 name=email_addr ></td></tr>
			<tr><td valign=top>Дополнительная информация о домене в свободной форме:</td>
				<td><textarea cols=50 rows=3 name=add_info ></textarea></td></tr>
			<tr><td>NIC-handle (если есть):</td>
				<td><input type=text size=30 name=nichandle ></td></tr>
			<tr><td colspan=2><input type=\"checkbox\"  name=rules_agree >С правилами домененной зоны ознакомлен и принимаю их</td></tr>
			<tr><td colspan=2>* - Обязательны для заполнения</td></tr>
			<tr height=44 ><td><input type=reset value=Очистить ></td><td>
				<input type=submit value=Далее ></td></tr>
		</table>
	</td>
	</tr>
</table>
</form>
<br>
<br>
");

	print_end();

</script>
