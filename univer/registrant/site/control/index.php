<script language="php">

include "include.php";

print_header();

print("
<center >Управление доменными именами</center>
<br><br>
<form action=view_domain.php method=POST  name=login_form >
<center>
<table border=0 width=300px>
	<tr>

	<td width=100%>
		<table width=100% border=0 style=\"border: 3px solid ".$login_border."\">
			<tr>
				<td colspan=2 bgcolor=".$login_border." width=\"100%\" align=center >
					<b style=\"font-size:150%; color:333333;\">Вход в систему</b>
				</td>
			</tr>

			<tr height=44px>
				<td width=\"40%\" align=left >
					<b style=\"color:555555;\">Login:</b>
				</td>
				<td width=\"40%\" align=left >
					<input type=text name=login maxlength=255 size=20 >
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
</center>
<script language=javascript>
	document.login_form.login.focus();
</script>

</form>

</body>
</html>
");

</script>
