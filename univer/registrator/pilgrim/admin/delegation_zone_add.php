<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage;

		$zone_name = safe_get("zone_name", "POST", 255);
		$admin_email = safe_get("admin_email", "POST", 255);
		$lease_time = safe_get("lease_time", "POST", 255);
		$initial = safe_get("initial", "POST", 255);
		$grace = safe_get("grace", "POST", 255);
		$pending = safe_get("pending", "POST", 255);
		$zone_title = safe_get("zone_title", "POST", 255);

		$ns_servers = $PM->NS->fetch_all_ns_servers();
		if ($ns_servers == FALSE) {
			echo "Can't fetch all ns servers: ".$_SESSION["adminpanel_error"];
			return;
		}

		$mx_servers = $PM->MX->fetch_all_mx_servers();
		if ($mx_servers == FALSE) {
			echo "Can't fetch all mx servers: ".$_SESSION["adminpanel_error"];
			return;
		}

		echo "
			<form action=\"delegation_zone_action.php?act=add_delegation_zone\" method=\"POST\" name=\"delegation_zone_manage\">
			<table border=\"0\" cellspacing=\"3\" cellpadding=\"0\">
			<tr>
				<td colspan=\"2\" width=\"100%\">
					<table width=\"100%\" class=\"standart\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							Create Delegation Zone
						</td>
						<td height=\"62\" width=\"27\" align=\"right\" background=\"../img/table/table_background.jpg\">
							<img src=\"../img/table/table_icon_close.jpg\" width=\"27\" height=\"62\">
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class=\"content\" colspan=\"2\">
					Fileds marked by <font color=\"#AA0000\">such font</font> are required
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Zone Name</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"zone_name\" value=\"".$zone_name."\">
				</td class=\"content2\">
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Zone Admin E-Mail</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"admin_email\" value=\"".$admin_email."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Lease Time</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"lease_time\" value=\"".$lease_time."\">&nbsp; Year(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Initial Period</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"initial\" value=\"".($initial!=""?$initial:"10")."\">&nbsp; Day(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Grace Period</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"grace\" value=\"".($grace!=""?$grace:"10")."\">&nbsp; Day(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Pending Period</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"pending\" value=\"".($pending!=""?$pending:"10")."\">&nbsp; Day(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Zone Title
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"zone_title\" value=\"".$zone_title."\">
				</td class=\"content2\">
			</tr>
			<tr>
				<td colspan=\"2\" class=\"content_3_center\">
					NS and MX Records Setup
				</td>
			</tr>
			<tr>
				<td colspan=\"2\">
					<table border=\"1\">
					<tr valign=\"top\">
						<td width=\"4%\" class=\"content\">&#035;</td>
						<td width=\"2%\" class=\"content\">Use</td>
						<td width=\"10%\" class=\"content\">Type</td>
						<td width=\"20%\" class=\"content\">Domain</td>
						<td width=\"20%\" class=\"content\">IP</td>
						<td width=\"44%\" class=\"content\">Title</td>
					</tr>
					<span id=\"ns_count\" title=\"".$PM->NS->SQL->get_num_rows($ns_servers)."\"></span>";

		for ($i = 0; $i < $PM->NS->SQL->get_num_rows($ns_servers); $i++) {
			$data = $PM->NS->SQL->get_object($ns_servers);

			$state = true;

			echo "<tr>
				<input type=\"hidden\" name=\"ns_id_".$i."\" value=\"".$data->id_table."\">
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"ns_checkbox_".$i."\" name=\"ns_checkbox_".$i."\" checked></td>
				<td class=\"content_3\">
					<select name=\"ns_role_".$i."\" id=\"ns_role_".$i."\" class=\"simple_select\">
						<option value=\"primary\" ".($i==0?"selected":"").">Primary</option>
						<option value=\"secondary\" ".($i!=0?"selected":"").">Secondary</option>
					</select>
				</td>
				<td class=\"content_3\">".$data->ns_domain."</td>
				<td class=\"content_3\">".$data->ns_ip."</td>
				<td class=\"content_3\">".$data->ns_title."</td>
			</tr>";
		}

		echo "
					</table>
				</td>
			</tr>
			<tr>
				<td colspan=\"2\">
					<table border=\"1\">
					<tr valign=\"top\">
						<td width=\"4%\" class=\"content\">&#035;</td>
						<td width=\"2%\" class=\"content\">Use</td>
						<td width=\"10%\" class=\"content\">Prior</td>
						<td width=\"20%\" class=\"content\">Domain</td>
						<td width=\"20%\" class=\"content\">IP</td>
						<td width=\"44%\" class=\"content\">Title</td>
					</tr>
					<span id=\"mx_count\" title=\"".$PM->MX->SQL->get_num_rows($mx_servers)."\"></span>";

		for ($i = 0; $i < $PM->MX->SQL->get_num_rows($mx_servers); $i++) {
			$data = $PM->MX->SQL->get_object($mx_servers);

			$state = true;

			echo "<tr>
				<input type=\"hidden\" name=\"mx_id_".$i."\" value=\"".$data->id_table."\">
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"mx_checkbox_".$i."\" name=\"mx_checkbox_".$i."\" checked></td>
				<td class=\"content_3\">
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"mx_prior_".$i."\" id=\"mx_prior_".$i."\" value=\"".(($i + 1) * 10)."\">
				</td>
				<td class=\"content_3\">".$data->mx_domain."</td>
				<td class=\"content_3\">".$data->mx_ip."</td>
				<td class=\"content_3\">".$data->mx_title."</td>
			</tr>";
		}


		echo "
					</table>
				</td>
			</tr>
			<tr height=\"40px\">
				<td colspan=\"2\">
					<input class=\"simple_button\" type=\"button\" value=\"Create Zone\" OnClick=\"Check_Confirm()\">
				</td>
			</tr>
			</table>
			</form>
		<script language=\"javascript\">
			document.delegation_zone_manage.zone_name.focus();
		</script>

		";

	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
