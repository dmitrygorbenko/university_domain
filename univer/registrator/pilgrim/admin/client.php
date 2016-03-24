<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage;

		$clients = $PM->CC->fetch_all_client();

		if ($clients == FALSE) {
			echo "Can't fetch all clients: ".$_SESSION["adminpanel_error"];
			return;
		}

		$empty = TRUE;
		echo "
			<form name=\"client_remove_form\" method=\"POST\" action=\"client_action.php?act=remove\">
			<input type=\"hidden\" name=\"ids\" value=\"\">
			<table cellspacing=\"3\" cellpadding=\"0\" border=\"0\" width=\"100%\">
			<tr valign=\"center\" height=\"62\" width=\"100%\">
				<td colspan=\"7\" width=\"100%\">
					<table width=\"100%\" class=\"standart\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							Clients
						</td>
						<td height=\"62\" width=\"27\" align=\"right\" background=\"../img/table/table_background.jpg\">
							<img src=\"../img/table/table_icon_close.jpg\" width=\"27\" height=\"62\">
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr valign=\"top\">
				<td width=\"4%\" class=\"content\">&#035;</td>
				<td width=\"2%\" class=\"content\">&nbsp;</td>
				<td width=\"43%\" class=\"content\">Login</td>
				<td width=\"30%\" class=\"content\">Creation Date</td>
				<td width=\"10%\" class=\"content\">Domains Count</td>
				<td width=\"5%\" class=\"content\">Active</td>
				<td width=\"5%\" class=\"content\">CP Access</td>
			</tr>
			<span id=\"clients_count\" title=\"".$PM->CC->SQL->get_num_rows($clients)."\"></span>";

		for ($i = 0; $i < $PM->CC->SQL->get_num_rows($clients); $i++) {
			$data = $PM->CC->SQL->get_object($clients);

			echo "<tr>
				<span id=\"client_".$i."\" title=\"".$data->id_table."\"></span>
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"client_checkbox_".$i."\"></td>
				<td class=\"content_3\"><a href=\"client_change.php?id=".$data->id_table."\">".$data->login."</a></td>
				<td class=\"content_3\">".$data->register_date."</td>
				<td class=\"content_3\" align=\"right\">".$data->zone_count."</td>
				<td class=\"content_3\" align=\"right\">".($data->active=="t"?"<font color=\"#007700\">On</font>":"<font color=\"#AA0000\">Off</font>")."</td>
				<td class=\"content_3\" align=\"right\">".($data->cp_access=="t"?"<font color=\"#007700\">On</font>":"<font color=\"#AA0000\">Off</font>")."</td>
			</tr>";
			$empty = FALSE;
		}

		if ($empty)
			echo "<tr><td align=\"center\" class=\"content_3\" colspan=\"7\">Empty</td></tr>";

		echo "  <tr valign=\"top\">
				<td colspan=\"7\" width=\"100%\">
					<table width=\"100%\">
					<tr width=\"100%\" valign=\"top\">
						<td width=\"100%\" align=\"left\" NOWRAP>
							".(!$empty?"<a href=\"javascript:checkall_clients(1)\">Select all</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href=\"javascript:checkall_clients(0)\">Clear all</a>":"")."
						</td>
						<td align=\"right\" NOWRAP>
							".(!$empty?"<input onClick=\"remove_clients()\" class=\"simple_button\" name=\"remove\" type=\"button\" id=\"remove\" value=\"Remove\">":"")."
							</form>
						</td>
						<td align=\"right\" NOWRAP>
							<form name=\"client\" method=\"POST\" action=\"client_add.php\">
							&nbsp;&nbsp;&nbsp;&nbsp;
							<input type=\"submit\" value=\"Create Client\" class=\"simple_button\">
							</form>
						</td>
					</tr>
					</table>
				</td>
			</tr>";

		echo "</table>
			</form>";
	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
