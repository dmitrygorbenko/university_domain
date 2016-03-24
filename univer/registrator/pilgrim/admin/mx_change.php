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

		$data = $PM->MX->fetch_mx_server($id);
		if ($data == FALSE) {
			echo "Can't fetch mx server: ".$_SESSION["adminpanel_error"];
			return;
		}

		echo "
			<form action=\"mx_action.php?act=change_mx\" method=\"POST\" name=\"mx_manage\">
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
							NS Server: <strong>".$data->mx_domain."</strong>
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
			<tr valign=\"top\">
				<td width=\"10%\" class=\"content2\" NOWRAP>
					<font color=\"#AA0000\">Domain</font>
				</td>
				<td width=\"90%\">
					<input name=\"domain\" value=\"".$data->mx_domain."\" class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\">
				</td>
			</tr>
			<tr valign=\"top\">
				<td class=\"content2\">
					<font color=\"#AA0000\">IP Address</font>
				</td>
				<td>
					<input name=\"ip_addr\" value=\"".$data->mx_ip."\" class=\"simple_input\" size=\"20\" maxlength=\"255\" type=\"text\">
				</td>
			</tr>
			<tr valign=\"top\">
				<td class=\"content2\">
					Title
				</td>
				<td>
					<input name=\"title\" value=\"".$data->mx_title."\" class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\">
				</td>
			</tr>
			<tr height=\"40px\">
				<td colspan=\"2\">
					<table border=\"0\">
					<tr>
						<td NOWRAP>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input class=\"simple_button\" type=\"submit\" value=\"Apply changes\">
						</td>
					</tr>
					</table>
				</td>
			</tr>
			";
		echo "</table>
			</form>";
	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
