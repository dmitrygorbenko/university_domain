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

		$clients = $PM->CC->fetch_all_client_light();
		if ($clients == FALSE) {
			echo "Can't fetch all client: ".$_SESSION["adminpanel_error"];
			return;
		}

		$delegation_zones = $PM->DZ->fetch_all_delegation_zones();
		if ($delegation_zones == FALSE) {
			echo "Can't fetch all delegation zones: ".$_SESSION["adminpanel_error"];
			return;
		}

		$zone = $PM->ZC->fetch_zone($id);
		if ($zone == FALSE) {
			echo "Can't fetch zone: ".$_SESSION["adminpanel_error"];
			return;
		}

		$zone_cfg = $PM->ZC->fetch_zone_config($zone->id_table);
		if ($zone_cfg == FALSE) {
			echo "Can't fetch zone's config: ".$_SESSION["adminpanel_error"];
			return;
		}

		$client = $PM->CC->fetch_client($zone->id_client);
		if ($client == FALSE) {
			echo "Can't fetch client info: ".$_SESSION["adminpanel_error"];
			return;
		}

		echo "
			<form name=\"subdomain_remove_form\" method=\"POST\" action=\"subdomain_action.php?act=remove\">
			<table border=\"0\" cellspacing=\"3\" cellpadding=\"0\">
			<tr>
				<td width=\"100%\" colspan=\"2\">
					<table width=\"100%\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\" width=\"100%\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							Client's Zone <strong>".$zone->fqdn."</strong> of client <a class=\"title\" href=\"client_change.php?id=".$client->id_table."\">".$client->login."</a>
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
					<font color=\"#AA0000\">Move zone to Client</font>
				</td>
				<td>
					&nbsp;<select name=\"client\" class=\"simple_select\">";

		for ($i = 0; $i < $PM->CC->SQL->get_num_rows($clients); $i++) {
			$data = $PM->CC->SQL->get_object($clients);

			$id_client = $data->id_table;
			$title = $data->login;
			$selected = "";

			if ($zone->id_client == $data->id_table) {
				$id_client = "-1";
				$title = "Don't move";
				$selected = "selected";
			}

			echo "<option ".$selected." value=\"".$id_client."\">".$title."</option>\n";
		}

		echo "
					</select>
				</td>
			</tr>
			<tr valign=\"top\">
				<td width=\"10%\" class=\"content2\">
					<font color=\"#AA0000\">Domain</font>
				</td>
				<td width=\"80%\">
					<input name=\"zone_name\" value=\"".$zone->domain."\" class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Root Zone</font>
				</td>
				<td>
					&nbsp;<select name=\"delegation_zone\" class=\"simple_select\">";

		for ($i = 0; $i < $PM->CC->SQL->get_num_rows($delegation_zones); $i++) {
			$data = $PM->CC->SQL->get_object($delegation_zones);

			echo "<option ".($data->id_table==$zone->id_root_zone?"selected":"")." value=\"".$data->id_table."\">".$data->zone_name."</option>\n";
		}

		echo "
					</select>
				</td>
			</tr>
			<tr height=\"10\">
				<td colspan=\"2\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Zone TTL</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"zone_ttl\" value=\"".$zone_cfg->ttl."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Refresh</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"refresh\" value=\"".$zone_cfg->refresh."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Retry</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"retry\" value=\"".$zone_cfg->retry."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Expire</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"expire\" value=\"".$zone_cfg->expire."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Negative Caching</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"negative\" value=\"".$zone_cfg->negative."\">&nbsp;Seconds
				</td>
			</tr>
			<tr height=\"40\">
				<td valign=\"center\" NOWRAP>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input class=\"simple_button\" type=\"submit\" value=\"Apply changes\">
				</td>
				<td valign=\"center\">
					&nbsp;&nbsp;||
					&nbsp;&nbsp;
					<input type=\"button\" value=\"Manage Subdomain\" class=\"simple_button\" onClick=\"document.location='zone_manage_change.php?id_zone=".$id."'\">
				</td>
			</tr>
			</table>
			</form>
		";
	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
