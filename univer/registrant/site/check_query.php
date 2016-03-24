<script language="php">

include "include.php";

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
	print("<br></body></html>");
};

function print_need_id()
{
	print("<center><font size=+1>Извените, но должен быть номер.</font></center>");
};


function print_bad_id()
{
	print("<center><font size=+1>Неверный номер заявки, или она уже в Базе данных.</font></center>");
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

	$id = safe_get($HTTP_GET_VARS, "id", 255);

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
	}

	fclose($file);

	/////////////////////////////////////////////////////////
	//		PRINT MAIN DATA
print("
<center ><font size=6 color=".$titlecolor.">Регистрация доменных имен</font></center>
<br>
<center><font size=+1>В ".date("Y-m-d G:i:s", $query_time)." была подана заявка:</font></center>
<br>
<table border=0 width=100%>
	<tr width=100%>

	<td width=5%></td>
	<td width=45%>
		<table border=0>

 			<tr><td colspan=2><font size=+2><b>Содержание заявки:</b></font></td></tr>
			
			<tr><td colspan=2><hr></td></tr>
			
			<tr><td width=50%>Домен:</td>
				<td width=50%><b>".$domain_name.".".$domain_zone."</b></td></tr>
			<tr><td>Срок регистрации:</td>
				<td>".$lease_time." год(а)</td></tr>
			<tr><td>Стоимость:</td>
				<td>".$full_price." грн.</td></tr>

			<tr><td colspan=2><font size=+1><b>Информация о клиенте:</b></font></td></tr>");
			
	if ($old_friend == "yes")
		print("<tr><td colspan=2 align=left ><font color=#0000AA><b>Заявку подал старый клиент</b></font></td></tr>");
print("
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
		</table>
	
	<td width=50% valign=top>
		<table border=0>

			<tr><td><center><font size=+2><b>Решение:</b></font></center></td></tr>
			
			<tr><td><hr></td></tr>
			
			<tr><td><font size=+1 color=#00AA00><b>Принять</b></font></td></tr>
			<tr><td>
			<form action=".$system_href_accept_query." method=POST>
			<input type=hidden name=id value=\"".$id."\">
			<input type=submit value=\"Принять заявку\" >
			</form>
			</td></tr>

			<tr><td>
				<hr>
			</td></tr>
			
			<tr><td><font size=+1 color=#AA0000><b>Отказать</b></font></td></tr>
			<tr><td>
			<form action=".$system_href_cancel_query." method=POST>
			Причина отказа:<br>
			
			<textarea cols=50 rows=3 name=why >Не верная информация в запросе. Не подлежит поправке</textarea>
			<br>
			<input type=hidden name=id value=\"".$id."\">
			<input type=submit value=Отклонить >
			</form>
			</td></tr>

		</table>
	</td>
	</tr>
</table>

");
	print_end();

</script>
