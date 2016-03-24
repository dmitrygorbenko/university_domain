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
	if ($matches[0] != "step_2.php") {
		print("<br><br><center><font size=+1>Надо последовательно выполнять все операции регистрации !</font></center>");
		print_end();
		exit(0);
	}

	$comlete_did = safe_get($HTTP_POST_VARS, "comlete_did", 255);
	$comlete_pid = safe_get($HTTP_POST_VARS, "comlete_pid", 255);
	
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

	$host_name = gethostbyaddr(getenv("REMOTE_ADDR"));
	if ($host_name == "")                             
		$host_name = getenv("REMOTE_ADDR");       
	
	/////////////////////////////////////////////////////////
	//		BEGIN TO HANDLE QUERY

	/* Check if count confirm files are above MAX_CONFIRM_FILES */
	
	$file = fopen($system_confirm_dir."/".$system_confirm_count, "r");
	if (!$file) {

		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");
	
		if ($system_report_by_mail == "1") {
			$text = "Error: While executing complete.php:
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
	
	if ((int)$count >= $system_confirm_max_files) {
		
		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");
	
		if ($system_report_by_mail == "1") {
			$text = "Alarm: While executing complete.php: 
				We have reach max count of confirm files. Max is: ".$system_confirm_max_files."";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";
			
			mail($mail_to, $subject, $text);
		}		
		
		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Мы достигли предельного количества confirm файлов</font></center>");
		}

		print_end();
		exit(0);
	}

	$count++;
	$file = fopen($system_confirm_dir."/".$system_confirm_count, "w");
	fwrite($file, $count);
	fclose($file);

	////////////////////////////////////////////////
	//	ADD DATA TO DIRECTORY

	/* Calculate random value */
	
	$rand = "false";
	$service_code = "";
	
	while ($rand == "false") {
		$service_code = rand();
		
		if (is_file($system_confirm_dir."/".$service_code))
			$rand = "false";
		else
			$rand = "ok";
	}
	
	/* Write client data to files */
	
	$file = fopen($system_confirm_dir."/".$service_code, "w");
	if (!$file) {
		
		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");

		if ($system_report_by_mail == "1") {
			$text = "Error: While executing complete.php: 
				Can't create file ".$service_code." in \"".$system_confirm_dir."\" directory";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";
			
			mail($mail_to, $subject, $text);
		}
		
		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Не могу создать confirm файл</font></center>");
		}

		print_end();
		exit(0);
	}

	$add = $add_info;

	$new_add = "";
	$lastpos = 0;
	$symbols = 0;

	for($i=0; $i < strlen($add)-1; $i++) {
		if ($add[$i] == chr(10)) {
		    $sub = substr($add, $lastpos, ($i-1)-$lastpos);
		    $new_add = $new_add.$sub."<br>";
		    $lastpos = $i+1;
		    $symbols = 0;
		}

		$symbols++;

		if ($symbols >= 40 && $add[$i] == ' ') {
		    $sub = substr($add, $lastpos, $i-$lastpos);
		    $new_add = $new_add.$sub."<br>";
		    $lastpos = $i+1;
		    $symbols = 0;
		}
	}

	if ((strlen($add) - ($lastpos)) > 0)
		$new_add = $new_add.substr($add, $lastpos, (strlen($add) - ($lastpos)));

	/* So, here we will check - if step_2.ph return info about old friend
	   We just put two entries into file: id_domain and id_person */

	$time = time();
	fwrite ($file, "query_time: ".$time."\n");
	
	print("<br>comlete_pid: ".$comlete_pid."<br>");
	print("<br>comlete_did: ".$comlete_did."<br>");
	
	if (($comlete_pid == "" || (int)$comlete_pid == 0 ) && ($comlete_did == "" || $comlete_did == "your_domain.our.zone" || (int)$comlete_did == 0) )
		fwrite ($file, "old_friend: no\n");
	else
		fwrite ($file, "old_friend: yes\n");

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
	fwrite ($file, "add_info: ".$new_add."\n");
	fwrite ($file, "nichandle: ".$nichandle."\n");
	fwrite ($file, "query_from_host: ".$host_name."\n");
	fclose($file);

	////////////////////////////////////////////////
	//	PREPARE MAIL AND SEND IT

	$mail_to = $email_addr;
	$subject = "Регистрация доменного имени ".$domain_name.".".$domain_zone.": Подтверждение";
	$text = "
	Здравствуйте, ".$first_name." ".$last_name." 
		
	Вами был подан заказ на регистрацию доменного имени:
	".$domain_name.".".$domain_zone."
	Срок регистрации: ".$lease_time." год(а)
	Стоимость: ".$full_price." грн.
		
	Для подтверждения заявки, Вам необходимо зайти сюда:
	".$system_href_confirm.$service_code."
	Здесь Вы также найдете дальнейшие указания.
		
	Для подтверждения заявки Вам отводится 1 (один) час.
	
	Если Вы не подавали никаких заявок - просто не отвечайте на это письмо.
	
	С наилучшими пожеланиями,	
	Регистратор доменных имен \"".$system_company_title."\".";
	
	mail($mail_to, $subject, $text);

	/////////////////////////////////////////////////////////
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
				<td>".$full_price." грн.</td></tr>

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
		</table>

		<table border=0>

			<tr><td><br>
				На Ваш e-mail (".$email_addr.") выслано письмо, для пояснения дальнейших действий
				<br></td></tr>
			<tr><td align=center><font size=+2><b>Оплата регистрации домена:</b></font></td></tr>
			<tr><td><a href=none.html> Платежное поручение (для физ. лиц)</a></td></tr>
			<tr><td><a href=none.html> Счет-фактура (для юр. лиц)</a></td></tr>
			<tr><td><a href=none.html> Кредитная карта Visa</a></td></tr>
			<tr><td><a href=none.html> WebMoney</a></td></tr>
			<tr><td><a href=none.html> PayCash</a></td></tr>
		</table>
	</td>
	</tr>
</table>
</form>
");
	print_end();

</script>
