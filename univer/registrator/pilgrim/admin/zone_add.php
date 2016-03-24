<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage;

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

		$client = safe_get("client", "POST", 255);
		$delegation_zone = safe_get("delegation_zone", "POST", 255);
		$zone_name = safe_get("zone_name", "POST", 255);
		$zone_ttl = safe_get("zone_ttl", "POST", 10);
		$refresh = safe_get("refresh", "POST", 10);
		$retry = safe_get("retry", "POST", 10);
		$expire = safe_get("expire", "POST", 10);
		$negative = safe_get("negative", "POST", 10);

		echo "
			<form action=\"zone_action.php?act=add_zone\" method=\"POST\" name=\"zone_manage\">
			<table border=\"0\" cellspacing=\"3\" cellpadding=\"0\">
			<tr>
				<td colspan=\"2\" width=\"100%\">
					<table width=\"100%\" class=\"standart\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							Create Client's Zone
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
					<font color=\"#AA0000\">Client</font>
				</td>
				<td>
					&nbsp;<select name=\"client\" class=\"simple_select\">";

		for ($i = 0; $i < $PM->CC->SQL->get_num_rows($clients); $i++) {
			$data = $PM->CC->SQL->get_object($clients);

			echo "<option ".($client==$data->id_table?"selected":"")." value=\"".$data->id_table."\">".$data->login."</option>\n";
		}

		echo "
					</select>
				</td>
			</tr>
			<tr height=\"10\">
				<td colspan=\"2\">
				</td>
			</tr>
			<tr valign=\"top\">
				<td width=\"20%\" class=\"content2\">
					<font color=\"#AA0000\">Domain</font>
				</td>
				<td width=\"80%\">
					<input name=\"zone_name\" value=\"".$zone_name."\" class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\">
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

			echo "<option ".($delegation_zone==$data->id_table?"selected":"")." value=\"".$data->id_table."\">".$data->zone_name."</option>\n";
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
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"zone_ttl\" value=\"".($zone_ttl!=""?$zone_ttl:"86400")."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Refresh</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"refresh\" value=\"".($refresh!=""?$refresh:"86400")."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Retry</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"retry\" value=\"".($retry!=""?$retry:"7200")."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Expire</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"expire\" value=\"".($expire!=""?$expire:"604800")."\">&nbsp;Seconds
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Negative Caching</font>
				</td>
				<td NOWRAP>
					<input class=\"simple_input\" size=\"10\" maxlength=\"10\" type=\"text\" name=\"negative\" value=\"".($negative!=""?$negative:"86400")."\">&nbsp;Seconds
				</td>
			</tr>
			<tr height=\"40\">
				<td colspan=\"2\">
					&nbsp;<input class=\"simple_button\" type=\"submit\" value=\"Create Zone\">
				</td>
			</tr>
			</table>
			</form>
		<script language=\"javascript\">
			document.zone_manage.zone_name.focus();
		</script>
		";
	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
