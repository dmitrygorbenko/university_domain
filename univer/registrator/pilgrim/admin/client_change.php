<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage;

		$id = safe_get("id", "GET", 255);
		if ($id == "") {
			echo "Can't find id !: ".$_SESSION["adminpanel_error"];
			return;
		}

		$client = $PM->CC->fetch_client($id);
		if ($client == FALSE) {
			echo "Can't fetch client: ".$_SESSION["adminpanel_error"];
			return;
		}

		$client_info = $PM->CC->fetch_client_info($id);
		if ($client_info == FALSE) {
			echo "Can't fetch client info: ".$_SESSION["adminpanel_error"];
			return;
		}

		$zones = $PM->CC->fetch_clients_zones($id);
		if ($zones == FALSE) {
			echo "Can't fetch client's zones ".$_SESSION["adminpanel_error"];
			return;
		}
		$zones_empty = TRUE;

		$counries = $PM->CC->fetch_countries("ru-koi8-r");
		if ($counries == FALSE) {
			echo "Can't fetch countries: ".$_SESSION["adminpanel_error"];
			return;
		}

		echo "
		<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
		<tr>
		<td width=\"100%\" colspan=\"2\">
			<table width=\"100%\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
			<tr valign=\"center\" height=\"62\" width=\"100%\">
				<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
					<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
				</td>
				<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
					Client: <strong>".$client->login."</strong>
				</td>
				<td height=\"62\" width=\"27\" align=\"right\" background=\"../img/table/table_background.jpg\">
					<img src=\"../img/table/table_icon_close.jpg\" width=\"27\" height=\"62\">
				</td>
			</tr>
			</table>
		</td>
		</tr>

		<tr>
		<td width=\"50%\" valign=\"top\" align=\"left\">

			<form action=\"client_action.php?act=change_account\" method=\"POST\" name=\"client_manage\">
			<input name=\"id\" value=\"".$client->id_table."\" size=\"30\" maxlength=\"255\" type=\"hidden\">
			<table border=\"0\" cellspacing=\"3\" cellpadding=\"0\">
			<tr>
				<td class=\"content\" colspan=\"2\">
					Fileds marked by <font color=\"#AA0000\">such font</font> are required
					<br>
					Leave password filed empty to save one
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Login</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"login\" value=\"".$client->login."\">
				</td class=\"content2\">
			</tr>
			<tr>
				<td class=\"content2\">
					Password
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"password\" name=\"password\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Again
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"password\" name=\"password2\">
				</td>
			</tr>
			<tr height=\"10\">
				<td colspan=\"2\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">First name</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"firstname\" value=\"".$client_info->firstname."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Last name</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"lastname\" value=\"".$client_info->lastname."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">E-mail</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"email\" value=\"".$client_info->email."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Company
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"company\" value=\"".$client_info->company."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Country</font>
				</td>
				<td>
					<select name=\"country\" class=\"simple_select\">
				";

		for ($i = 0; $i < $PM->CC->SQL->get_num_rows($counries); $i++) {
			$data = $PM->CC->SQL->get_object($counries);

			$s = "";

			if ($data->country_code == $client_info->country)
				$s = " selected";

			echo "	<option value=\"".$data->country_code."\" ".$s.">".$data->country."</option>\n";
		}

		echo "
					</select>
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Region
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"region\" value=\"".$client_info->region."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">City</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"city\" value=\"".$client_info->city."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Postal</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"postal\" value=\"".$client_info->postal."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Address</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"address\" value=\"".$client_info->address."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Phone</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"phone\" value=\"".$client_info->phone."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Fax
				</td>
				<td>
					<input class=\"simple_input\" size=\"30\" maxlength=\"255\" type=\"text\" name=\"fax\" value=\"".$client_info->fax."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Add info:
				</td>
				<td>
					<textarea cols=\"30\" name=\"add_info\" class=\"textinput\">".$client_info->add_info."</textarea>
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Active
				</td>
				<td>
					<input type=\"checkbox\" name=\"active\" ".($client->active=="t"?" checked ":"").">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					CP Access
				</td>
				<td>
					<input type=\"checkbox\" name=\"cp_access\" ".($client->cp_access=="t"?" checked ":"").">
				</td>
			</tr>
			<tr height=\"40\">
				<td colspan=\"2\">
					<table border=\"0\">
					<tr>
						<td NOWRAP>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input class=\"simple_button\" type=\"submit\" value=\"Apply change\">
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			</form>

		</td>
		<td width=\"50%\" valign=\"top\" align=\"left\">

			<form name=\"zone_remove_form\" method=\"POST\" action=\"zone_action.php?act=remove\">
			<input type=\"hidden\" name=\"ids\" value=\"\">
  			<table cellspacing=\"3\" cellpadding=\"0\" border=\"0\" width=\"100%\">
			<tr valign=\"top\">
				<td width=\"4%\" class=\"content\">&#035;</td>
				<td width=\"2%\" class=\"content\">&nbsp;</td>
				<td width=\"36%\" class=\"content\">Zone</td>
				<td width=\"24%\" class=\"content\">Domain Type</td>
				<td width=\"24%\" class=\"content\">Service Type</td>
			</tr>
			<span id=\"zones_count\" title=\"".$PM->ZC->SQL->get_num_rows($zones)."\"></span>";

		for ($i = 0; $i < $PM->ZC->SQL->get_num_rows($zones); $i++) {
			$data = $PM->ZC->SQL->get_object($zones);

			echo "<tr>
				<span id=\"zone_".$i."\" title=\"".$data->id_table."\"></span>
				<td class=\"content_3\" align=\"center\">".($i+1)."</td>
				<td class=\"content_3\" align=\"center\"><input type=\"checkbox\" id=\"zone_checkbox_".$i."\"></td>
				<td class=\"content_3\"><a href=\"zone_change.php?id=".$data->id_table."\">".$data->domain."</a></td>
				<td class=\"content_3\">".$data->type."</td>
				<td class=\"content_3\">".$data->type."</td>
			</tr>";
			$zones_empty = FALSE;
		}

		if ($zones_empty)
			echo "<tr><td align=\"center\" class=\"content_3\" colspan=\"5\">Empty</td></tr>";

		echo "  <tr valign=\"top\">
				<td colspan=\"5\" width=\"100%\">
					<table width=\"100%\">
					<tr width=\"100%\" valign=\"top\">
						<td width=\"100%\" align=\"left\" NOWRAP>
							".(!$zones_empty?"<a href=\"javascript:checkall_zones(1)\">Select all</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href=\"javascript:checkall_zones(0)\">Clear all</a>":"")."
						</td>
						<td align=\"right\" NOWRAP>
							".(!$zones_empty?"<input onClick=\"remove_zones()\" class=\"simple_button\" name=\"remove\" type=\"button\" id=\"remove\" value=\"Remove\">":"")."
							</form>
						</td>
						<td align=\"right\" NOWRAP>
							<form name=\"zone\" method=\"POST\" action=\"zone_add.php\">
							<input type=\"hidden\" name=\"client\" value=\"".$id."\">
							<input type=\"submit\" value=\"Create Zone\" class=\"simple_button\">
							</form>
						</td>
					</tr>
					</table>
				</td>
			</tr>";

		echo "
			</table>

		</td>
		</tr>
		</table>";
	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
