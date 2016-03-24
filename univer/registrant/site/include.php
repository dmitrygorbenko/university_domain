
<script language="php">

$bgcolor="#E9E9E9";
$textcolor="#000000";
$alink="#0000FF";
$avisited="#0370FF";
$ahover="#FF0000";
$nav="#000000";
$titlecolor="#000000";

$trans = array(">" => "&#062;", "<" => "&#060;", "\"" => "&#034;", "'" => "&#039;", "\$" => "&#036;");

$system_href_main = "http://localhost/domain/";
$system_href_main_admin = "http://localhost/cgi-bin/engine";
$system_href_accept_query = "http://localhost/domain/accept_query.php";
$system_href_cancel_query = "http://localhost/domain/cancel_query.php";
$system_href_check = "http://localhost/domain/check_query.php?id=";
$system_href_confirm = "http://localhost/domain/confirm.php?id=";

$system_company_title = "Domain Name Registrator";

$system_problem_email = "nial";
$system_error_email = $system_problem_email;
$system_check_confirm_email = "nial";
$system_offence_email = "nial";

$system_sql_driver = "Postgres SQL Server";
$system_sql_str_connect = "pgsql://postgres@localhost/domain";

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
