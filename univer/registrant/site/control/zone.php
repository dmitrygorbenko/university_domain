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

	print("<center>Управление доменными именами</center>");

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

	print("	<hr>

		<script language=javascript>
			
		function check_form(form_id) {
		
			form = document.getElementById(form_id);
			new_rr = form.new_rr.value;

			if (new_rr == \"\") {
				alert('Заполните все поля');
				return false;
			}

			if (form_id == \"modify_d_a\") {
				alert('get: modify_d_a, rr: '+new_rr);

				form.submit();
			}
			else if (form_id == \"modify_d_mx\") {
				new_pri = form.new_pri.value;
				
				all_right = \"true\";
				
				alert('get: modify_d_mx, pri: '+new_pri+', rr: '+new_rr);
			
				if (new_pri < 1 && new_pri != \"\") {
					alert('Нельзя установить для записи MX приоритет меньше единицы');
					all_right = \"false\";
				}

				if (new_pri == \"\") {
					alert('Не определено поле \"Приоритет\"');
					all_right = \"false\";
				}

				if (all_right == \"true\") {
					form.submit();
				}
			
			}
			else if (form_id == \"new_record\") {
				new_d_name = form.new_d_name.value;
				new_rt = form.new_rt.options[form.new_rt.selectedIndex].value;
				new_pri = form.new_pri.value;
				
				all_right = \"true\";
				
				alert('get: new_record, d_name: '+new_d_name+', rt: '+new_rt+', pri: '+new_pri+', rr: '+new_rr);

				if (new_d_name == \"\" || new_rt == \"\") {
					alert('Заполните все поля');
					all_right = \"false\";
				}
		
				if (new_d_name == \"@\" || new_d_name == \"domain_name\") {
					alert('Для определения домена воспользуйтесь двумя первыми записями');
					all_right = \"false\";
				}

				if (new_rt == \"MX\") {
					if (new_pri < 1 && new_pri != \"\") {
						alert('Нельзя установить для записи MX приоритет меньше единицы');
						all_right = \"false\";
					}
					if (new_pri == \"\") {
						alert('Не определено поле \"Приоритет\"');
						all_right = \"false\";
					}

				}
				
				if (all_right == \"true\") {
					form.submit();
				}
			
			}
			else {
				new_d_name = form.new_d_name.value;
				new_rt = form.new_rt.options[form.new_rt.selectedIndex].value;
				new_pri = form.new_pri.value;
				
				alert('get: modify_record, d_name: '+new_d_name+', rt: '+new_rt+', pri: '+new_pri+', rr: '+new_rr);

				all_right = \"true\";
	      	
				if (new_d_name == \"\" || new_rt == \"\") {
					alert('Заполните все поля');
						all_right = \"false\";
					}
		
				if (new_d_name == \"@\" || new_d_name == \"domain_name\") {
					alert('Для определения домена воспользуйтесь двумя первыми записями');
						all_right = \"false\";
					}

				if (new_rt == \"MX\") {
					if (new_pri < 1 && new_pri != \"\") {
						alert('Нельзя установить для записи MX приоритет меньше единицы');
						all_right = \"false\";
					}

					if (new_pri == \"\") {
						alert('Не определено поле \"Приоритет\"');
						all_right = \"false\";
					}


				}
				
				if (all_right == \"true\") {
					form.submit();
				}
			}
		}

		function check_rt(form_id) {
		
			form = document.getElementById(form_id);
			new_rt = form.new_rt.options[form.new_rt.selectedIndex].value;
			
			if (new_rt == \"MX\") {
				form.new_pri.style.display = \"\";
			}
			else {
				form.new_pri.style.display = \"none\";
			}
				
	
		}


		</script>

		<table width=930px border=0>
			<tr><td width=200 valign=top>
			<table border=0 width=100%>
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

			<td width=700px valign=top>
			<table border=0 cellspacing=5>
				<tr height=30px >
					<td width=15px></td>
					<td>
					<br>
					Домен: <b>roman.com.ua</b>. Действителен до 2006-02-12
					<hr>
						<form action=\"dns\" method=POST>
						<table>
						<tr>
							<td><b>TTL:</b></td><td>86400</td>
						</tr>
						<tr>
							<td><b>Serial:</b></td><td>2005013102</td>
						</tr>
						<tr>
							<td><b>Refresh:</b></td><td><input type=text name=\"refresh\" value=\"86400\" ></td>
						</tr>
						<tr>
							<td><b>Retry:</b></td><td><input type=text name=\"retry\" value=\"7200\" ></td>
						</tr>
						<tr>
							<td><b>Negative caching:</b></td><td><input type=text name=\"negative\" value=\"604800\" ></td>
						</tr>
						</table>
						<input type=submit value=\"Обновить\">
						</form>
			<hr>

	<table width=700px border=0 cellspacing=0 cellpadding=0>
	<tr><td><table width=100% border=0 cellspacing=0 cellpadding=0><tr>
			<td width=22% align=center>Домен</td>
			<td width=13% align=center>Тип записи</td>
			<td width=13% align=center>Приоритет</td>
			<td width=25% align=center>Значение</td>
			<td align=center>Действия</td>
	</tr></table></tr>

	<tr><td>
		<form action=\"dns\" method=POST id=\"modify_d_a\" name=\"modify_d_a\">
		<input type=hidden name=\"a\" value=\"modify_d_a\">
		<input type=hidden name=\"a_type\" value=\"modify_d_a\">
		<input type=hidden name=\"old_rr\" value=\"195.24.135.44\">
		<table width=100% border=0 cellspacing=0 cellpadding=0><tr>
			<td width=22% align=center>
				@<td>
			<td width=13% align=center>
				A<td>
			<td width=13% align=center>
				</td>
			<td width=25% align=center>
				<input type=text name=\"new_rr\" value=\"195.24.135.44\" ></td>
			<td align=center>
				<input type=button name=\"update\" value=\"Обновить\" onClick=\"check_form('modify_d_a')\">
				</td>
		</form>
	</tr></table></tr>



	<tr><td>
		<form action=\"dns\" method=POST id=\"modify_d_mx\" name=\"modify_d_mx\">
		<input type=hidden name=\"a\" value=\"modify_d_mx\">
		<input type=hidden name=\"a_type\" value=\"modify_d_mx\">
		<input type=hidden name=\"old_pri\" value=\"10\">
		<input type=hidden name=\"old_rr\" value=\"mx.@\">
		<table width=100% border=0 cellspacing=0 cellpadding=0><tr>
			<td width=22% align=center>
				@<td>
			<td width=13% align=center>
				MX<td>
			<td width=13% align=center>
				<input type=text name=\"new_pri\" size=4 value=\"10\" ></td>
			<td width=25% align=center>
				<input type=text name=\"new_rr\" value=\"mx.@\" ></td>
			<td align=center>
				<input type=button name=\"update\" value=\"Обновить\" onClick=\"check_form('modify_d_mx')\">
				</td>
		</form>
	</tr></table></tr>


	<tr><td>
		<form action=\"dns.php\" method=POST id=\"modify_record_xxx\" name=\"modify_record_xxx\">
		<input type=hidden name=\"a\" value=\"zone_modify\">
		<input type=hidden name=\"a_type\" value=\"modify_record\">
		<input type=hidden name=\"a_type_2\" value=\"undefined\">
		<input type=hidden name=\"old_d_name\" value=\"ftp\">
		<input type=hidden name=\"old_rt\" value=\"A\">
		<input type=hidden name=\"old_pri\" value=\"10\">
		<input type=hidden name=\"old_rr\" value=\"195.24.135.44\">
		<table width=100% border=0 cellspacing=0 cellpadding=0><tr>
			<td width=22% align=center>
				<input type=text size=17 maxlength=255 name=\"new_d_name\" value=\"ftp\" ></td>
			<td width=13% align=center>
				<select name=\"new_rt\" onChange=\"check_rt('modify_record_xxx')\">
					<option value=\"A\" selected>A</option><option value=\"CNAME\">CNAME</option><option value=\"MX\">MX</option></select></td>
			<td width=13% align=center>
				<input type=text name=\"new_pri\" size=4 value=\"10\" ></td>
			<td width=25% align=center>
				<input type=text name=\"new_rr\" value=\"195.24.135.44\" ></td>
			<td align=center>
				<input type=button name=\"update\" value=\"Обновить\" onClick=\"(document.getElementById('modify_record_xxx')).a_type_2.value = 'update';check_form('modify_record_xxx')\">
				<input type=button name=\"delete\" value=\"Удалить\" onClick=\"(document.getElementById('modify_record_xxx')).a_type_2.value = 'delete';check_form('modify_record_xxx')\"></td>

		</form>
		<script language=javascript>
			check_rt('modify_record_xxx');
		</script>
	</tr></table></tr>


<!-- One empty record -->

	<tr><td>
		<form action=\"dns\" method=POST id=\"new_record\" name=\"new_record\">
		<input type=hidden name=\"a\" value=\"zone_modify\">
		<input type=hidden name=\"a_type\" value=\"new_record\">
		<table width=100% border=0 cellspacing=0 cellpadding=0><tr>
			<td width=22% align=center>
				<input type=text size=17 maxlength=255 name=\"new_d_name\" value=\"\" ></td>
			<td width=13% align=center>
				<select name=\"new_rt\" onChange=\"check_rt('new_record')\">
					<option value=\"A\" selected>A</option><option value=\"CNAME\">CNAME</option><option value=\"MX\">MX</option></select></td>
			<td width=13% align=center>
				<input type=text name=\"new_pri\" size=4 value=\"10\" ></td>
			<td width=25% align=center>
				<input type=text name=\"new_rr\" value=\"195.24.135.44\" ></td>
			<td align=center>
				<input type=button name=\"add\" value=\"Добавить\" onClick=\"return check_form('new_record')\">
				</td>

		</form>
		<script language=javascript>
			check_rt('new_record');
		</script>

	</tr></table></tr>

	</table>	
	
					</td>
				</tr>
			</table>
			</td></tr>
		</table>
	");

</script>
