<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage;

		$delegation_zones = $PM->DZ->fetch_all_delegation_zones();
		if ($delegation_zones == FALSE) {
			echo "Can't fetch all delegation zones: ".$_SESSION["adminpanel_error"];
			return;
		}

		$delegation_zones_empty = TRUE;

		echo "
			<form name=\"delegation_zone_remove_form\" method=\"POST\" action=\"delegation_zone_action.php?act=remove\">
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
							Delegation Zone Manage
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
				<td width=\"54%\" class=\"content\">Zone Name</td>
				<td width=\"10%\" class=\"content\">Zones Amount</td>
				<td width=\"10%\" class=\"content\">Price</td>
				<td width=\"12%\" class=\"content\">Lease Time</td>
				<td width=\"10%\" class=\"content\">State</td>
			</tr>
			<span id=\"delegation_zones_count\" title=\"".$PM->DZ->SQL->get_num_rows($delegation_zones)."\"></span>
			";

		for ($i = 0; $i < $PM->DZ->SQL->get_num_rows($delegation_zones); $i++) {
			$data = $PM->DZ->SQL->get_object($delegation_zones);

			$state = true;

			echo "<tr>
				<span id=\"delegation_zone_".$i."\" title=\"".$data->id_table."\"></span>
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"delegation_zone_checkbox_".$i."\"></td>
				<td class=\"content_3\"><a href=\"delegation_zone_change.php?id=".$data->id_table."\">".$data->zone_name."</a></td>
				<td class=\"content_3\"><i>some_value</i></td>
				<td class=\"content_3\"><i>some_value</i></td>
				<td class=\"content_3\">".$data->max_lease_time."</td>
				<td class=\"content_3\">".($state?"<font color=\"#00AA00\">ON</font>":"<font color=\"#FF0000\">OFF</font>")."</td>
			</tr>";
			$delegation_zones_empty = FALSE;
		}

		if ($delegation_zones_empty)
			echo "<tr><td align=\"center\" class=\"content_3\" colspan=\"7\">Empty</td></tr>";

		echo "  <tr valign=\"top\">
				<td colspan=\"7\" width=\"100%\">
					<table width=\"100%\">
					<tr  width=\"100%\" valign=\"top\">
						<td width=\"100%\" align=\"left\" NOWRAP>
							".(!$delegation_zones_empty?"<a href=\"javascript:checkall_delegation_zones(1)\">Select all</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href=\"javascript:checkall_delegation_zones(0)\">Clear all</a>":"")."
						</td>
						<td align=\"right\" NOWRAP>
							".(!$delegation_zones_empty?"<input onClick=\"remove_delegation_zones()\" class=\"simple_button\" name=\"remove\" type=\"button\" id=\"remove\" value=\"Remove\">":"")."
							</form>
						</td>
						<td align=\"right\" NOWRAP>
							<form name=\"root\" method=\"GET\" action=\"delegation_zone_add.php\">
							&nbsp;&nbsp;&nbsp;&nbsp;
							<input type=\"submit\" value=\"Create Zone\" class=\"simple_button\">
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
