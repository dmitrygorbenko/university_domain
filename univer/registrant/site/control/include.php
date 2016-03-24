
<script language="php">

$login_border="#91D1FF";

function print_header()
{

	$bgcolor="#E9E9E9";
	$textcolor="#000000";
	$alink="#0000FF";
	$avisited="#0370FF";
	$ahover="#FF0000";
	$nav="#000000";
	$titlecolor="#000000";

	print("

		<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
		<html>
		<head>
		<style type=\"text/css\">
				body 	      {font-family: sans-serif;}
				td            {font-family: sans-serif; font-size: 15px; }
				td.param      {font-family: sans-serif; font-size: 15px; background-color: #e0e0e0;}
				td.value      {font-family: sans-serif; font-size: 15px; }
				A:link        {font-family: sans-serif; font-size: x-medium; text-decoration: none; color: ".$alink."}
				A:visited     {font-family: sans-serif; font-size: x-medium; text-decoration: none; color: ".$avisited."}
				A:hover       {font-family: sans-serif; font-size: x-medium; text-decoration: underline; color: ".$ahover."}
				A:link.nav    {font-family: sans-serif; color: ".$alink."}
				A:visited.nav {font-family: sans-serif; color: ".$avisited."}
				A:hover.nav   {font-family: sans-serif; color: ".$ahover."}
				.nav          {font-family: sans-serif; color: ".$nav."}
				center	{font-family: sans-serif; font-size: 25px; }
		//-->
		</style>
		   <meta http-equiv=Content-Type content=\"text/html; charset=koi8-r\">

		<title>Управление доменым именем</title>
		</head>

		<body BGCOLOR=".$bgcolor." text=".$textcolor." >
	");
};

function print_end()
{
	print(" <br> <br> <br> </body> </html> ");
};

function print_menu($number)
{
	$count = 3;
	
	$text = array("Домены", "Профиль", "Выход");
	$links = array("<A href=view_domain.php>Домены</A>",
			"<A href=profile.php>Профиль</A>",
			"<A href=exit.php>Выход</A>");

	print("<table width=100% border=0><tr>");

	for ($i = 0; $i < $count; $i++) {

		if ($i == $number)
			$data = $text[$i];
		else
			$data = $links[$i];
		
		if ($i == ($count-1))
			$align = "align=right";
		else
			$align = " width=10%";
		
		print("<td ".$align."><b>".$data."</b></td>");
	}

	print("</tr></table>");
};

$system_href_main = "http://192.168.0.1/domain/";
$system_href_main_admin = "http://zhuk/cgi-bin/engine";
$system_href_accept_query = "http://192.168.0.1/domain/accept_query.php";
$system_href_cancel_query = "http://192.168.0.1/domain/cancel_query.php";
$system_href_check = "http://192.168.0.1/domain/check_query.php?id=";
$system_href_confirm = "http://192.168.0.1/domain/confirm.php?id=";

$system_company_title = "Domain Name Registrator";

$system_problem_email = "registr@link.lan";
$system_error_email = $system_problem_email;
$system_check_confirm_email = "registr@link.lan";
$system_offence_email = "registr@link.lan";

$system_sql_driver = "Postgres SQL Server";
$system_sql_str_connect = "pgsql://postgres@192.168.0.10/domain";

$system_confirm_dir = "to_confirm";
$system_confirm_count = "confirm_count";
$system_confirm_max_files = 10;
$system_wait_to_confirm = 172800;

$system_cfg_dir = "cfg";
$system_cfg_countries = "countries";
$system_cfg_option = "default_option";
$system_cfg_domain_to_lease = "domain_to_lease";

$system_report_by_mail = "1";
$system_debug = "1";

</script>
