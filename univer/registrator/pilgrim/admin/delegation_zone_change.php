<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage;

		$id = safe_get("id", "GET", 255);
		if ($id == "") {
			echo "Can't find id !";
			return;
		}

		$data = $PM->DZ->fetch_delegation_zone($id);
		if ($data == FALSE) {
			echo "Can't fetch zone: ".$_SESSION["adminpanel_error"];
			return;
		}

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

		$current_ns_servers = $PM->DZ->fetch_ns_servers_of_zone($id);
		if ($current_ns_servers == FALSE) {
			echo "Can't fetch ns servers of zone: ".$_SESSION["adminpanel_error"];
			return;
		}

		$current_mx_servers = $PM->DZ->fetch_mx_servers_of_zone($id);
		if ($current_mx_servers == FALSE) {
			echo "Can't fetch mx servers of zone: ".$_SESSION["adminpanel_error"];
			return;
		}

		$zones_ns_id = array();
		$zones_ns_role = array();
		$zones_mx_id = array();
		$zones_mx_prior = array();

		for ($k = 0; $k < $PM->DZ->SQL->get_num_rows($current_ns_servers); $k++) {
			$tmp_data = $PM->DZ->SQL->get_object($current_ns_servers);
			array_push($zones_ns_id, $tmp_data->id_ns);
			array_push($zones_ns_role, $tmp_data->ns_master);
		}

		for ($k = 0; $k < $PM->DZ->SQL->get_num_rows($current_mx_servers); $k++) {
			$tmp_data = $PM->DZ->SQL->get_object($current_mx_servers);
			array_push($zones_mx_id, $tmp_data->id_mx);
			array_push($zones_mx_prior, $tmp_data->mx_prior);
		}

		echo "
			<form action=\"delegation_zone_action.php?act=change_delegation_zone\" method=\"POST\" name=\"delegation_zone_manage\">
			<input name=\"id\" value=\"".$data->id_table."\" size=\"60\" maxlength=\"255\" type=\"hidden\">
			<table cellspacing=\"3\" cellpadding=\"0\" border=\"0\" width=\"100%\">
			<tr valign=\"center\" height=\"62\" width=\"100%\">
				<td colspan=\"5\" width=\"100%\">
					<table width=\"100%\" class=\"standart\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							Delegation Zone: <strong>".$data->zone_name."</strong>
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
					Fileds marked by <font color=\"#AA0000\">such font</font> are required.<br>
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Zone Name</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"zone_name\" value=\"".$data->zone_name."\">
				</td class=\"content2\">
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Zone Admin E-Mail</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"admin_email\" value=\"".$data->admin_email."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Lease Time</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"lease_time\" value=\"".$data->max_lease_time."\">&nbsp; Year(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Initial Period</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"initial\" value=\"".$data->initial."\">&nbsp; Day(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Grace Period</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"grace\" value=\"".$data->grace."\">&nbsp; Day(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Pending Period</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"pending\" value=\"".$data->pending."\">&nbsp; Day(s)
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Zone Title
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"zone_title\" value=\"".$data->zone_title."\">
				</td class=\"content2\">
			</tr>
			<tr>
				<td class=\"content2\">
					Zone Active
				</td>
				<td NOWRAP>
					<input type=\"checkbox\" id=\"active\" name=\"active\" ".($data->active?"checked":"").">
				</td>
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

			$used = false;
			$master = 0;

			for ($k = 0; $k < count($zones_ns_id) && !$used; $k++) {
				if ($data->id_table == $zones_ns_id[$k]) {
					$used = true;
					$master = $zones_ns_role[$k];
				}
			}

			$state = true;

			echo "<tr>
				<input type=\"hidden\" name=\"ns_id_".$i."\" value=\"".$data->id_table."\">
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"ns_checkbox_".$i."\" name=\"ns_checkbox_".$i."\" ".($used?"checked":"")."></td>
				<td class=\"content_3\">
					<select name=\"ns_role_".$i."\" id=\"ns_role_".$i."\" class=\"simple_select\">
						<option value=\"primary\" ".($master==1?"selected":"").">Primary</option>
						<option value=\"secondary\" ".($master!=1?"selected":"").">Secondary</option>
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

			$used = false;
			$prior = ($i + 1) * 10;

			for ($k = 0; $k < count($zones_mx_id) && !$used; $k++)
				if ($data->id_table == $zones_mx_id[$k]) {
					$used = true;
					$prior = $zones_mx_prior[$k];
				}

			$state = true;

			echo "<tr>
				<input type=\"hidden\" name=\"mx_id_".$i."\" value=\"".$data->id_table."\">
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"mx_checkbox_".$i."\" name=\"mx_checkbox_".$i."\" ".($used?"checked":"")."></td>
				<td class=\"content_3\">
					<input class=\"simple_input\" size=\"5\" maxlength=\"5\" type=\"text\" name=\"mx_prior_".$i."\" id=\"mx_prior_".$i."\" value=\"".$prior."\">
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
					<table border=\"0\">
					<tr>
						<td NOWRAP>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input class=\"simple_button\" type=\"button\" value=\"Apply Changes\" OnClick=\"Check_Confirm()\">
						</td>
					</tr>
					</table>
				</td>
			</tr>
			";
		echo "</table>
			</form>
		<script language=\"javascript\">
			document.delegation_zone_manage.zone_name.focus();
		</script>
		";
	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
