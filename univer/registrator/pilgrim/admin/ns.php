<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage;

		$ns_servers = $PM->NS->fetch_all_ns_servers();
		if ($ns_servers == FALSE) {
			echo "Can't fetch all ns servers: ".$_SESSION["adminpanel_error"];
			return;
		}

		$ns_servers_empty = TRUE;

		echo "
			<form name=\"ns_remove_form\" method=\"POST\" action=\"ns_action.php?act=remove\">
			<input type=\"hidden\" name=\"ids\" value=\"\">
			<table cellspacing=\"3\" cellpadding=\"0\" border=\"0\" width=\"100%\">
			<tr valign=\"center\" height=\"62\" width=\"100%\">
				<td colspan=\"6\" width=\"100%\">
					<table width=\"100%\" class=\"standart\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							NS Servers Manage
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
				<td width=\"10%\" class=\"content\">State</td>
				<td width=\"20%\" class=\"content\">Domain</td>
				<td width=\"20%\" class=\"content\">IP</td>
				<td width=\"44%\" class=\"content\">Title</td>
			</tr>
			<span id=\"ns_servers_count\" title=\"".$PM->NS->SQL->get_num_rows($ns_servers)."\"></span>
			";

		for ($i = 0; $i < $PM->NS->SQL->get_num_rows($ns_servers); $i++) {
			$data = $PM->NS->SQL->get_object($ns_servers);

			$state = true;

			echo "<tr>
				<span id=\"ns_".$i."\" title=\"".$data->id_table."\"></span>
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"ns_checkbox_".$i."\"></td>
				<td class=\"content_3\">".($state?"<font color=\"#00AA00\">ON</font>":"<font color=\"#FF0000\">OFF</font>")."</td>
				<td class=\"content_3\"><a href=\"ns_change.php?id=".$data->id_table."\">".$data->ns_domain."</a></td>
				<td class=\"content_3\">".$data->ns_ip."</td>
				<td class=\"content_3\">".$data->ns_title."</td>
			</tr>";
			$ns_servers_empty = FALSE;
		}

		if ($ns_servers_empty)
			echo "<tr><td align=\"center\" class=\"content_3\" colspan=\"6\">Empty</td></tr>";

		echo "  <tr valign=\"top\">
				<td colspan=\"6\" width=\"100%\">
					<table width=\"100%\">
					<tr  width=\"100%\" valign=\"top\">
						<td width=\"100%\" align=\"left\" NOWRAP>
							".(!$ns_servers_empty?"<a href=\"javascript:checkall_ns_servers(1)\">Select all</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href=\"javascript:checkall_ns_servers(0)\">Clear all</a>":"")."
						</td>
						<td align=\"right\" NOWRAP>
							".(!$ns_servers_empty?"<input onClick=\"remove_ns_servers()\" class=\"simple_button\" name=\"remove\" type=\"button\" id=\"remove\" value=\"Remove\">":"")."
							</form>
						</td>
						<td align=\"right\" NOWRAP>
							<form name=\"ns\" method=\"GET\" action=\"ns_add.php\">
							&nbsp;&nbsp;&nbsp;&nbsp;
							<input type=\"submit\" value=\"Create NS Server\" class=\"simple_button\">
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
