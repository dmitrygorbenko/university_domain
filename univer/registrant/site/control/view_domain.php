<script language="php">

include "include.php";

//include 'DB.php';
//include 'DB/pgsql.php';


function print_not_enought()
{
	print("<br><br><center><font size=+1>Вы должны ввести имя и пароль учетной записи
		<br><br><a alt=back href=index.php>Назад</a></font></center>");
};

function print_invalid_password($login_name)
{

	include "include.php";

	print("
		<br>

		<form action=select_action.php method=POST  name=login_form >

		<table border=0 width=100%>
			<tr>

			<td width=30%></td>

			<td width=70%>
				<table width=300px border=0 style=\"border: 3px solid ".$login_border."\">
					<tr>
						<td colspan=2 bgcolor=".$login_border." width=\"100%\" align=center >
							<b style=\"font-size:150%; color:CC1111;\">Неверный пароль</b>
						</td>
					</tr>

					<tr height=44px>
						<td width=\"40%\" align=left >
							<b style=\"color:555555;\">Login:</b>
						</td>
						<td width=\"40%\" align=left >
							<input type=text name=login maxlength=255 size=20 value=\"".$login_name."\">
						</td>
					</tr>

					<tr height=22px>
						<td width=\"40%\" align=left >
							<b style=\"color:555555;\">Password:</b>
						</td>
						<td width=\"40%\" align=left >
							<input type=password name=password maxlength=255 size=20 >
						</td>
					</tr>

					<tr height=44px>
						<td width=\"40%\" align=center >
							<input type=submit value=\"   Login   \" >
						</td>
						<td width=\"40%\" align=right >
							<a href=remind.php>Напомнить пароль</a>
						</td>
					</tr>

				<table>
			</td>
			</tr>
		</table>

		<script language=javascript>
			document.login_form.password.focus();
		</script>

		</form>

	");
};

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

function safe_get($HTTP_VARS, $param_name, $param_length)
{
	$value = "";
	$trans = array(">" => "]", "<" => "[");

	if (isset($HTTP_VARS[$param_name]))
		$value = $HTTP_VARS[$param_name];
	else
		return $value;

	$value = substr($value, 0, $param_length);

	$value = strtr($value, $trans);

	return $value;
};

	print_header();
	
	print("<center >Управление доменными именами</center>");

	/** Hm, Here we will determine, how to do with client data. It's very
	    dangerous part of code. Be sure, what you have complete defence

	    Main rule: NEVER TRUST CLIENT. Keep this rule active. **/

/*
	$referer = getenv("HTTP_REFERER");

	preg_match("/[^\.\/]+\.[^\.\/]+$/", $referer, $matches);
	if (!isset($matches[0])) {
		print("<br><br><center><font size=+1>Начните с главной страницы</font></center>");
		print_end();
		exit(0);
	}
	if ($matches[0] != "index.php") {
		print("<br><br><center><font size=+1>Начните с главной страницы</font></center>");
		print_end();
		exit(0);
	}
*/
	$login = safe_get($HTTP_POST_VARS, "login", 255);
	$password = safe_get($HTTP_POST_VARS, "password", 255);

/*
	if ($login == "") {
		print_not_enought();
		print_end();
		exit(0);
	}
*/
//	print_invalid_password($login);
//	print_end();
//	exit(0);

	$client_id = "1";
	$uid = "sfsdf34s44535645";
	$domain_count = "15";

	print("<hr>");	

	print_menu(0);

	print("<hr>
	
		<table width=1000px border=0>
			<tr>
			<td valign=top width=200px>
			<table border=0 width=100%>
				<tr height=10px><td></td></tr>
				<tr width=100%>
					<td valign=\"top\" >
						Настройка домена позволяет указать NS и MX 
						сервера, указать A, PTR и CNAME записи для 
						домена. Для этого следует нажать на названии 
						домена.
						<br><br>
					</td>
				</tr>
				<tr width=100%>
					<td valign=\"top\" >
						Настройка профиля позволяет изменить информацию, 
						введенную при регистрции учетной записи пользователя
					</td>

				</tr>
			</table>
			</td>

			<td width=800px valign=top>
			<table border=0 cellspacing=5>
				<tr height=30px >
					<td width=15px></td>
					<td align=left >Всего доменов: ".$domain_count."</td>
				</tr>
				<tr>
					<td width=15px></td>
					<td>
						<table  border=0 style=\"border: 3px solid ".$login_border."\" width=100% >
							<tr>
								<td align=center >Домен:</td>
								<td width=120px align=center >Состояние:</td>
								<td width=140px align=center >Действителен до:</td>
								<td width=120px align=center >Код:</td>
							<tr>

	");

	print("
							<tr>
								<td align=center ><A href=domain.php?a=edit&id=16>abc.ua</A></td>
								<td align=center >Активен</td>
								<td align=center >29.03.2006</td>
								<td align=center >13234</td>
							</tr>
		");

	print("
						</table>
					</td>
				</tr>
			</table>
			</td></tr>
		</table>
	");

</script>
