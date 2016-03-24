<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."sql_core.php");

function body_page() {

	$SQL = new PGSQL;

	$SQL->connect();

	$query = "SELECT COUNT(id_table) FROM client";
	$result = $SQL->exec_query($query);
	if ($result == FALSE) {
		echo "Error while retrive client count information: ".$SQL->get_error();
		return;
	}
	$c_data = $SQL->get_object($result);

	$query = "SELECT COUNT(id_table) FROM domain_zone";
	$result = $SQL->exec_query($query);
	if ($result == FALSE) {
		echo "Error while retrive zone count information: ".$SQL->get_error();
		return;
	}
	$z_data = $SQL->get_object($result);
/*
	$query = "SELECT COUNT(id_table) FROM subdomain_table";
	$result = $SQL->exec_query($query);
	if ($result == FALSE) {
		echo "Error while retrive subdomain count information: ".$SQL->get_error();
		return;
	}
	$sd_data = $SQL->get_object($result);

	$query = "SELECT COUNT(id_table) FROM ftp_table";
	$result = $SQL->exec_query($query);
	if ($result == FALSE) {
		echo "Error while retrive ftp count information: ".$SQL->get_error();
		return;
	}
	$f_data = $SQL->get_object($result);

	$query = "SELECT COUNT(id_table) FROM mail_table";
	$result = $SQL->exec_query($query);
	if ($result == FALSE) {
		echo "Error while retrive mail count information: ".$SQL->get_error();
		return;
	}
	$m_data = $SQL->get_object($result);
*/
	$SQL->disconnect();

echo "
	<center>
		<span class=\"title\">
			DCS - Domain Control System&nbsp;/&nbsp;Pilgrim
		</span>
		<br>
	</center>

	<br>

	<table>
	<tr>
		<td valign=\"top\">
			<table cellpadding=\"0\" cellspacing=\"3\">
			<tr>
				<td class=\"general\" width=\"100px\" colspan=\"2\">
					<strong>Domain Status:</strong>
				</td>
			</tr>
			<tr>
				<td class=\"general_1\" width=\"100px\">
					Clients count
				</td>
				<td class=\"general_2\" width=\"100px\">
					".$c_data->count."
				</td>
			</tr>
			<tr>
				<td class=\"general_1\" width=\"100px\">
					Zones count
				</td>
				<td class=\"general_2\" width=\"100px\">
					".$z_data->count."
				</td>
			</tr>
			<tr>
				<td class=\"general_1\" width=\"100px\">
					Subdomain count
				</td>
				<td class=\"general_2\" width=\"100px\">
					".$sd_data->count."
				</td>
			</tr>
			<tr>
				<td class=\"general_1\" width=\"100px\">
					Redirect amount
				</td>
				<td class=\"general_2\" width=\"100px\">
					".$m_data->count."
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
";

}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
