<script language="php">

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
   <title>Делегирование доменных имен</title>
</head>

<body BGCOLOR=".$bgcolor." text=".$textcolor." >

<center ><font size=6 color=".$titlecolor.">Название компании</font></center>

<br><br>

<table border=0 width=100%>
	<tr><td><a href=index.php target=_top>На главную</a></tr>
	<tr><td><a href=uslugi.php target=dataframe >Наши услуги</a></tr>
	<tr><td><b>Регистрация имен</b></td></tr>
	<tr><td><a href=step_0.php target=dataframe >Подать заявку</a></td></tr>
	<tr><td><a href=tax.php target=dataframe >Тарифы</a></td></tr>
	<tr><td><b>Правила доменов</b></td></td></tr>
	<tr><td><a href=Rules/Policy_of_.UA.pdf target=dataframe >Домена .UA</a></td></td></tr>
	<tr><td><a href=Rules/Policy_of_COM_UA.pdf target=dataframe >Домена COM.UA</a></td></td></tr>
	<tr><td><b>О нас</b></td></td></tr>
	<tr><td>Карта сайта</td></td></tr>
	<tr><td>Ссылки</td></td></tr>
</table>

</body>
</html>
");

</script>
