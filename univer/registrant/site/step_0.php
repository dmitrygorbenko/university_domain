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

<body BGCOLOR=".$bgcolor." text=".$textcolor." >");

};

function print_end()
{
	print("<br><br></body></html>");
};

	print_header();
	
	////////////////////////////////////////////////////////////
	//		PRINT MAIN DATA	

print("

<center><font size=6 color=".$titlecolor.">Регистрация доменных имен</font></center>
<br>
<form action=step_1.php method=POST>

<table border=0 width=100%>
	<tr width=100%>

	<td width=5%></td>
	<td width=95%>
		<table border=0>");


	$file = fopen($system_cfg_dir."/".$system_cfg_domain_to_lease, "r");

	if (!$file) {
		
		print("<center><font size=+1>Извените, в данный момент мы не можем удовлетворить Вашу заявкую
		<br>Попробуйте повторить попытку через некоторое время
		<br>Извените за неудобства</font></center>");

		if ($system_report_by_mail == "1") {
			$text = "Error: While executing step_0.php:
				Can't open file ".$system_cfg_domain_to_lease." in \"".$system_cfg_dir."\" directory";
			$mail_to = $system_problem_email;
			$subject = "Alert: System dosn't work";
			
			mail($mail_to, $subject, $text);
		}		
		
		if ($system_debug == "1") {
			print("<br><br><center><font size=+1>Debug: Не могу открыть файл ".$system_cfg_dir."/".$system_cfg_domain_to_lease."</font></center>");
		}

		print_end();
		exit(0);
	}

	print("
			<tr><td width=30%>Доменное имя:</td>
				<td width=70%><input type=text size=15  name=domain_name >
				<select name=domain_zone >");

	while(!feof($file)) {
		$str = fgets($file, 1024);
		if ($str == "")
			continue;
		
		$str = trim($str);
		$pp = strtok($str ,":");
		
		print("
					<option value=\"$pp\">".$pp."</option>");
		
	}
	
	fclose($file);

print("
				</select></td>
				
			<tr><td colspan=2><br><input type=submit value=Продолжить ></td></tr>
		</table>
	</td>
	</tr>
</table>
</form>
");

	print_end();

</script>
