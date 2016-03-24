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

		<table width=100% border=0>
			<tr><td width=200px valign=top>
				<table border=0 width=200px>
				<tr height=10px ><td></td></tr>
				<tr>
					<td valign=\"top\" width=100%>
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

			<td width=750px valign=top>
			<table border=0 cellspacing=5 width=100%>
				<tr height=30px >
					<td width=15px></td>
					<td width=100%>

<!-- ***************** OWNER-C **************************** -->

					<p>

					<form action=\"dns\" method=POST>
					<input type=hidden name=\"a\" value=\"update_owner-c\">
					<b>Запись: owner-c</b>
					<table border=0 width=450px cellspacing=0 cellpadding=0>

	<tr bgcolor=#91d1ff ><td width=100px valign=top align=left >
		<b>Поле</b></td>
	    <td width=200px valign=top align=left >
	    	<b>Значение</b></td>
	    <td width=150px valign=top align=left >
	    	<b>Новое значение</b></td>
	</tr>

	<tr height=5px ><td width=100px valign=top align=left ></td>
	    <td width=200px valign=top align=left ></td>
	    <td width=150px valign=top align=left ></td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Имя:</td>
	    <td class=\"old_value\" valign=top align=left >Дмитрий</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Фамилия:</td>
	    <td class=\"old_value\" valign=top align=left >Горбенко</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Компания:</td>
	    <td class=\"old_value\" valign=top align=left >Трайд компани</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >E-mail:</td>
	    <td class=\"old_value\" valign=top align=left >nial@ukr.net</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Страна:</td>
	    <td class=\"old_value\" valign=top align=left >Украина</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Область:</td>
	    <td class=\"old_value\" valign=top align=left >Днепропетровская</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Город:</td>
	    <td class=\"old_value\" valign=top align=left >Новомосковск</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Индекс:</td>
	    <td class=\"old_value\" valign=top align=left >51200</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес:</td>
	    <td class=\"old_value\" valign=top align=left >Сучкова 222</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес 2:</td>
	    <td class=\"old_value\" valign=top align=left >452 кв. 65</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Телефон:</td>
	    <td class=\"old_value\" valign=top align=left >55112364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>


	<tr><td class=\"param\" valign=top align=left >Факс:</td>
	    <td class=\"old_value\" valign=top align=left >345364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

					<td></td>
					<td></td>
					<td><input type=submit value=\"Обновить\"></td>					
					</table>
					</form>

<!-- ***************** ADMIN-C **************************** -->

					<p>

					<form action=\"dns\" method=POST>
					<input type=hidden name=\"a\" value=\"update_owner-c\">
					<b>Запись: admin-c</b>
					<table border=0 width=450px cellspacing=0 cellpadding=0>

	<tr bgcolor=#91d1ff ><td width=100px valign=top align=left >
		<b>Поле</b></td>
	    <td width=200px valign=top align=left >
	    	<b>Значение</b></td>
	    <td width=150px valign=top align=left >
	    	<b>Новое значение</b></td>
	</tr>

	<tr height=5px ><td width=100px valign=top align=left ></td>
	    <td width=200px valign=top align=left ></td>
	    <td width=150px valign=top align=left ></td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Имя:</td>
	    <td class=\"old_value\" valign=top align=left >Дмитрий</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Фамилия:</td>
	    <td class=\"old_value\" valign=top align=left >Горбенко</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Компания:</td>
	    <td class=\"old_value\" valign=top align=left >Трайд компани</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >E-mail:</td>
	    <td class=\"old_value\" valign=top align=left >nial@ukr.net</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Страна:</td>
	    <td class=\"old_value\" valign=top align=left >Украина</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Область:</td>
	    <td class=\"old_value\" valign=top align=left >Днепропетровская</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Город:</td>
	    <td class=\"old_value\" valign=top align=left >Новомосковск</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Индекс:</td>
	    <td class=\"old_value\" valign=top align=left >51200</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес:</td>
	    <td class=\"old_value\" valign=top align=left >Сучкова 222</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес 2:</td>
	    <td class=\"old_value\" valign=top align=left >452 кв. 65</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Телефон:</td>
	    <td class=\"old_value\" valign=top align=left >55112364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>


	<tr><td class=\"param\" valign=top align=left >Факс:</td>
	    <td class=\"old_value\" valign=top align=left >345364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

					<td></td>
					<td></td>
					<td><input type=submit value=\"Обновить\"></td>					
					</table>
					</form>

<!-- ***************** BILLING-C **************************** -->

					<p>

					<form action=\"dns\" method=POST>
					<input type=hidden name=\"a\" value=\"update_owner-c\">
					<b>Запись: billing-c</b>
					<table border=0 width=450px cellspacing=0 cellpadding=0>

	<tr bgcolor=#91d1ff ><td width=100px valign=top align=left >
		<b>Поле</b></td>
	    <td width=200px valign=top align=left >
	    	<b>Значение</b></td>
	    <td width=150px valign=top align=left >
	    	<b>Новое значение</b></td>
	</tr>

	<tr height=5px ><td width=100px valign=top align=left ></td>
	    <td width=200px valign=top align=left ></td>
	    <td width=150px valign=top align=left ></td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Имя:</td>
	    <td class=\"old_value\" valign=top align=left >Дмитрий</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Фамилия:</td>
	    <td class=\"old_value\" valign=top align=left >Горбенко</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Компания:</td>
	    <td class=\"old_value\" valign=top align=left >Трайд компани</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >E-mail:</td>
	    <td class=\"old_value\" valign=top align=left >nial@ukr.net</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Страна:</td>
	    <td class=\"old_value\" valign=top align=left >Украина</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Область:</td>
	    <td class=\"old_value\" valign=top align=left >Днепропетровская</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Город:</td>
	    <td class=\"old_value\" valign=top align=left >Новомосковск</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Индекс:</td>
	    <td class=\"old_value\" valign=top align=left >51200</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес:</td>
	    <td class=\"old_value\" valign=top align=left >Сучкова 222</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес 2:</td>
	    <td class=\"old_value\" valign=top align=left >452 кв. 65</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Телефон:</td>
	    <td class=\"old_value\" valign=top align=left >55112364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>


	<tr><td class=\"param\" valign=top align=left >Факс:</td>
	    <td class=\"old_value\" valign=top align=left >345364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

					<td></td>
					<td></td>
					<td><input type=submit value=\"Обновить\"></td>					
					</table>
					</form>


<!-- ***************** TECH-C **************************** -->

					<p>

					<form action=\"dns\" method=POST>
					<input type=hidden name=\"a\" value=\"update_owner-c\">
					<b>Запись: tech-c</b>
					<table border=0 width=450px cellspacing=0 cellpadding=0>

	<tr bgcolor=#91d1ff ><td width=100px valign=top align=left >
		<b>Поле</b></td>
	    <td width=200px valign=top align=left >
	    	<b>Значение</b></td>
	    <td width=150px valign=top align=left >
	    	<b>Новое значение</b></td>
	</tr>

	<tr height=5px ><td width=100px valign=top align=left ></td>
	    <td width=200px valign=top align=left ></td>
	    <td width=150px valign=top align=left ></td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Имя:</td>
	    <td class=\"old_value\" valign=top align=left >Дмитрий</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Фамилия:</td>
	    <td class=\"old_value\" valign=top align=left >Горбенко</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Компания:</td>
	    <td class=\"old_value\" valign=top align=left >Трайд компани</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >E-mail:</td>
	    <td class=\"old_value\" valign=top align=left >nial@ukr.net</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Страна:</td>
	    <td class=\"old_value\" valign=top align=left >Украина</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Область:</td>
	    <td class=\"old_value\" valign=top align=left >Днепропетровская</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Город:</td>
	    <td class=\"old_value\" valign=top align=left >Новомосковск</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Индекс:</td>
	    <td class=\"old_value\" valign=top align=left >51200</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес:</td>
	    <td class=\"old_value\" valign=top align=left >Сучкова 222</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Адрес 2:</td>
	    <td class=\"old_value\" valign=top align=left >452 кв. 65</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

	<tr><td class=\"param\" valign=top align=left >Телефон:</td>
	    <td class=\"old_value\" valign=top align=left >55112364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>


	<tr><td class=\"param\" valign=top align=left >Факс:</td>
	    <td class=\"old_value\" valign=top align=left >345364</td>
	    <td class=\"new_value\" valign=center align=left ><input maxlenght=255 type=text name=\"\" value=\"\" > </td>
	</tr>

					<td></td>
					<td></td>
					<td><input type=submit value=\"Обновить\"></td>					
					</table>
					</form>

<!-- ***************** END **************************** -->

					</td>
				</tr>
			</table>
			</td></tr>
		</table>
	");

</script>
