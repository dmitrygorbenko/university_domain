<?php
	require_once ("header.php");
	include_once ($INCLUDE_PATH."manage.php");

	function body_page() {

		$PM = new Pilgrim_Manage();

		$login = safe_get("login", "POST", 255);
		$email = safe_get("email", "POST", 255);
		$firstname = safe_get("firstname", "POST", 255);
		$lastname = safe_get("lastname", "POST", 255);
		$company = safe_get("company", "POST", 255);
		$country = safe_get("country", "POST", 255);
		$region = safe_get("region", "POST", 255);
		$city = safe_get("city", "POST", 255);
		$postal = safe_get("postal", "POST", 255);
		$address = safe_get("address", "POST", 255);
		$phone = safe_get("phone", "POST", 255);
		$fax = safe_get("fax", "POST", 255);
		$active = safe_get("active", "POST", 5);
		$cp_access = safe_get("cp_access", "POST", 5);

		$counries = $PM->CC->fetch_countries("ru-koi8-r");
		if ($counries == FALSE) {
			echo "Can't fetch countries: ".$_SESSION["adminpanel_error"];
			return;
		}

		echo "
			<form action=\"client_action.php?act=add_client\" method=\"POST\" name=\"client_manage\">
			<table border=\"0\" cellspacing=\"3\" cellpadding=\"0\">
			<tr>
				<td colspan=\"2\" width=\"100%\">
					<table width=\"100%\" class=\"standart\" cellspacing=\"0\"  cellpadding=\"0\" border=\"0\">
					<tr valign=\"center\" height=\"62\" width=\"100%\">
						<td height=\"62\" width=\"85\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\">
							<img src=\"../img/table/table_icon.jpg\" width=\"85\" height=\"62\">
						</td>
						<td height=\"62\" width=\"99%\" align=\"left\" background=\"../img/table/table_background.jpg\" class=\"title\" colspan=\"3\">
							New Client
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
					<font color=\"#AA0000\">Login</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"login\" value=\"".$login."\">
				</td class=\"content2\">
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Password</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"password\" name=\"password\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Again</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"password\" name=\"password2\">
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
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"firstname\" value=\"".$firstname."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Last name</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"lastname\" value=\"".$lastname."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">E-mail</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"email\" value=\"".$email."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Company
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"company\" value=\"".$company."\">
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

			if ($data->country_code == ($country!=""?$country:"UA"))
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
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"region\" value=\"".$region."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">City</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"city\" value=\"".$city."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Postal</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"postal\" value=\"".$postal."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Address</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"address\" value=\"".$address."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					<font color=\"#AA0000\">Phone</font>
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"phone\" value=\"".$phone."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Fax
				</td>
				<td>
					<input class=\"simple_input\" size=\"40\" maxlength=\"255\" type=\"text\" name=\"fax\" value=\"".$fax."\">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					Active
				</td>
				<td>
					<input type=\"checkbox\" name=\"active\" ".($active==""?"checked":($active=="on"?"checked":"")).">
				</td>
			</tr>
			<tr>
				<td class=\"content2\">
					CP Access
				</td>
				<td>
					<input type=\"checkbox\" name=\"cp_access\" ".($cp_access==""?"checked":($cp_access=="on"?"checked":"")).">
				</td>
			</tr>

			<tr height=\"40\">
				<td colspan=\"2\">
					&nbsp;<input class=\"simple_button\" type=\"submit\" value=\"Create Client\">
				</td>
			</tr>
			</table>
			</form>
		<script language=\"javascript\">
			document.client_manage.login.focus();
		</script>

		";
	}

	entire_html();

	$_SESSION["adminpanel_error"] = "";
?>
