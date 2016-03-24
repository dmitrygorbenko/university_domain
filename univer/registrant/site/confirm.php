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
	print("<br><br><br></body></html>");
};

function print_need_id()
{
	print("<center><font size=+1>Извените, но должен быть номер.</font></center>");
};

function print_bad_id()
{
	print("<center><font size=+1>Извените, но номер вашей заявки не верен. Если вы уверены в обратном,
		<br>напишите об этом на адрес ".$system_error_email."</font></center>");
};

function print_already_confirmed()
{
	print("
		<center><br><font size=+2><b>Подтверждение заявки на регистрацию домена</b></font>
		<br><br><b>Заявка уже была подтверждена</b>
		</center>
	");
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
			$text = "Error: While executing confirm.php: 
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

	$query_time = $pp;
	$old_friend = $pp;
	$comlete_did = $pp;
	$comlete_pid = $pp;
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
		
		if (strstr($str, "query_time:"))
			$query_time = $pp;
		if (strstr($str, "old_friend:"))
			$old_friend = $pp;
		if (strstr($str, "comlete_pid:"))
			$comlete_pid = $pp;
		if (strstr($str, "comlete_did:"))
			$comlete_did = $pp;
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
	
	}
	fclose($file);

	if ($query_time == "") {
		print_already_confirmed();
		print_end();
		exit(0);
	}
	
	$host_name = gethostbyaddr(getenv("REMOTE_ADDR")); 
	if ($host_name == "")                              
		$host_name = getenv("REMOTE_ADDR");        

	$confirm_time = time();
		
	$file = fopen($system_confirm_dir."/".$id, "w+");
	
	fwrite ($file, "query_time: ".$query_time."\n");
	fwrite ($file, "confirm_time: ".$confirm_time."\n");
	fwrite ($file, "old_friend: ".$old_friend."\n");
	fwrite ($file, "comlete_pid: ".$comlete_pid."\n");
	fwrite ($file, "comlete_did: ".$comlete_did."\n");
	fwrite ($file, "domain_name: ".$domain_name."\n");
	fwrite ($file, "domain_zone: ".$domain_zone."\n");
	fwrite ($file, "lease_time: ".$lease_time."\n");
	fwrite ($file, "full_price: ".$full_price."\n");
	fwrite ($file, "first_name: ".$first_name."\n");
	fwrite ($file, "last_name: ".$last_name."\n");
	fwrite ($file, "company_name: ".$company_name."\n");
	fwrite ($file, "country: ".$country."\n");
	fwrite ($file, "region: ".$region."\n");
	fwrite ($file, "post_index: ".$post_index."\n");
	fwrite ($file, "city: ".$city."\n");
	fwrite ($file, "address1: ".$address1."\n");
	fwrite ($file, "address2: ".$address2."\n");
	fwrite ($file, "telephone: ".$telephone."\n");
	fwrite ($file, "telefax: ".$telefax."\n");
	fwrite ($file, "email_addr: ".$email_addr."\n");
	fwrite ($file, "add_info: ".$add_info."\n");
	fwrite ($file, "nichandle: ".$nichandle."\n");
	fwrite ($file, "query_from_host: ".$query_from_host."\n");
	fwrite ($file, "confirm_from_host: ".$host_name."\n");
	fclose($file);

	////////////////////////////////////////////////
	//	DECREASE CONFIRM COUNT

	$file = fopen($system_confirm_dir."/".$system_confirm_count, "r");
	if (!$file) {

		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");

		if ($system_report_by_mail == "1") {
			$text = "Error: While executing confirm.php:
				Can't open file ".$system_confirm_count." in \"".$system_confirm_dir."\" directory";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";

			mail($mail_to, $subject, $text);
		}		

		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Не могу открыть confirm_count файл</font></center>");
		}

		print_end();
		exit(0);
	}
	
	$count = fgets($file, 1024);
	fclose($file);
	
	$count--;
	$file = fopen($system_confirm_dir."/".$system_confirm_count, "w");
	fwrite($file, $count);
	fclose($file);
	
	////////////////////////////////////////////////
	//	PREPARE MAIL AND SEND IT to ADMINISTRATOR

	$curr_time = date("Y-m-d G:i:s", time());
	
	$mail_to = $email_addr;
	$subject = "Регистрация доменного имени ".$domain_name.".".$domain_zone.": К проверке";
	$text = "
	Сделан запрос на регистрацию доменного имени.
	Проверить: ".$system_href_check.$id."
	
	Время подтверждения заявки: ".$curr_time."";
	
	mail($mail_to, $subject, $text);

	/////////////////////////////////////////////////////////
	//		PRINT MAIN DATA

print("
<center ><font size=6 color=".$titlecolor.">Регистрация доменных имен</font></center>
<table border=0 width=100%>
	<tr width=100%>

	<td width=5%></td>
	<td width=95%>
		<table border=0>

			<tr><td align=center><br><font size=+2><b>Подтверждение заявки на регистрацию домена</b></font></td></tr>
			
			<tr><td><br>Доменное имя: <b>".$domain_name.".".$domain_zone."</b></td></tr>
			<tr><td>Срок регистрации: ".$lease_time." год(а)</td></tr>
			<tr><td>Стоимость: ".$full_price." грн.</td></tr>

			<tr><td><br><b>Заявка подтверждена</b>
			<br>После проверки Вашей завки на Ваш почтовый адрес (".$email_addr.") будет послано письмо с дальнейшими указаниями.</td></tr>
			
			<tr><td><br>
				<center><font size=+1><a href=\"".$system_href_main."\">Перейти на главную страницу</a></font></center></td></tr>
		</table>
	</td>
	</tr>
</table>
</form>
");
	print_end();

</script>
