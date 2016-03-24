<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$domain = safe_get("domain", "POST", 255);
		$ip_addr = safe_get("ip_addr", "POST", 255);
		$title = safe_get("title", "POST", 255);

		echo "
			<form action=\"ns_action.php?act=add_ns\" method=\"POST\" name=\"ns_manage\">
			<table border=\"0\" cellspacing=\"3\" cellpadding=\"0\">
			<tr>
				<td colspan=\"2\" width=\"100%\">
					<table width=\"100%\" class=\"standart\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							Create NS Server
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
					<font color=\"#AA0000\">Domain</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"domain\" value=\"".$domain."\">
				</td class=\"content2\">
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">IP Address</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"20\" maxlength=\"255\" type=\"text\" name=\"ip_addr\" value=\"".$ip_addr."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Title
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"title\" value=\"".$title."\">
				</td>
			</tr>
			<tr height=\"40px\">
				<td colspan=\"2\">
					<input class=\"simple_button\" type=\"submit\" value=\"Create NS Server\">
				</td>
			</tr>
			</table>
			</form>
		<script language=\"javascript\">
			document.ns_manage.domain.focus();
		</script>

		";

	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
