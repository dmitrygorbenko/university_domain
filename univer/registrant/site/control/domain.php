<script language="php">

include "include.php";

//include 'DB.php';
//include 'DB/pgsql.php';


function print_not_enought()
{
	print("<br><br><center><font size=+1>Вы должны ввести имя и пароль учетной записи
		<br><br><a alt=back href=index.php>Назад</a></font></center>");
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


	$uid = "dsfsdfsdf";

	print("<hr>");

	print_menu(-1);

	print("<hr>
	
		<table width=800 border=0>
			<tr><td width=200 valign=top>
			<table border=0>
				<tr height=10px ><td></td></tr>
				<tr>
					<td valign=\"top\" >
						Настройка домена позволяет указать NS и MX 
						сервера, указать A, PTR и CNAME записи для 
						домена. Для этого следует нажать на названии 
						домена.
						<br><br>
					</td>
				</tr>
				<tr>
					<td valign=\"top\" >
						Настройка профиля позволяет изменить информацию, 
						введенную при регистрции учетной записи пользователя
					</td>

				</tr>
			</table>
			</td>

			<td width=600 valign=top>
			<table border=0 cellspacing=5 width=100%>
				<tr height=30px >
					<td width=15px></td>
					<br>
					Домен: <b>roman.com.ua</b>. Действителен до 2006-02-12
					<ul>
					<li>
					<A href=\"contact.php\">Контактная информация</A>
						<ul>
						Настройка контактной информации домена
						</ul>

					</li>
					<br>
					<li>
					<A href=\"zone.php\">Настройка домена</A>
						<ul>
						Настройка Вашей доменной зоны. Можно внести A и CNAME
						записи для домена. Также можно указать MX запись
						для почты.
						</ul>
					</li>
					<br>
					<li>
					<A href=\"nameservers.php\">Настройка NS cерверов</A>
						<ul>
						Настройка NS серверов для доменного имени.
						</ul>
					</li>
					</ul>
				<tr>
				</tr>
			</table>
			</td></tr>
		</table>
	");

</script>
