<?php

function create_unique_id()
{
	$base62_chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

	$value = time();
	$yield1 = "------";
	for ($i = 5; $i >= 0; $i--) {
		$yield1[$i] = $base62_chars[$value % 62];
		$value = $value / 62;
	}

	$value = posix_getpid();
	$yield2 = "------";
	for ($i = 5; $i >= 0; $i--) {
		$yield2[$i] = $base62_chars[$value % 62];
		$value = $value / 62;
	}

	$value = posix_times();
	$value = $value["ticks"];
	$yield3 = "--";
	for ($i = 1; $i >= 0; $i--) {
		$yield3[$i] = $base62_chars[$value % 62];
		$value = $value / 62;
	}

	return $yield1."-".$yield2."-".$yield3;
}

function create_serial() {
	return date("Ymd01");
}

function convert_size ($size) {

	$size_conv = 1024;
	$size_suffix = array("b&nbsp;", "Kb", "Mb", "Gb", "Tb");

	$count = 0;
	$size_tmp = $size;

	settype($size_pre, "double");

	while ($size_tmp >= $size_conv) {
		$size_tmp = $size_tmp / $size_conv;
		$count = $count + 1;
		if ($count == 4)
			break;
	}

	$size_pre = round (( $size / ( pow( $size_conv, $count))),1);

	return $size_pre.'&nbsp;<font class="fixed">'.$size_suffix[$count].'</font>';
}

function safe_get($var_name, $method, $size = 0)
{
	global $trans, $HTTP_GET_VARS, $HTTP_POST_VARS;

	if ($method == "POST") {
		if (isset($HTTP_POST_VARS[$var_name]))
			$value = $HTTP_POST_VARS[$var_name];
		else
			return "";
	}
	else {
		if (isset($HTTP_GET_VARS[$var_name]))
			$value = $HTTP_GET_VARS[$var_name];
		else
			return "";
	}

	if ($size != 0) {
		if (strlen($value) > $size);
			$value = substr($value, 0, $size);
	}

	$value = strtr($value, $trans);

	$value = strtr($value, array("\\" => "", "\\\\" => "\\"));

	return $value;
}

function pure_get($var_name, $method)
{
	global $HTTP_GET_VARS, $HTTP_POST_VARS;

	if ($method == "POST") {
		if (isset($HTTP_POST_VARS[$var_name]))
			$value = $HTTP_POST_VARS[$var_name];
		else
			return "";
	}
	else {
		if (isset($HTTP_GET_VARS[$var_name]))
			$value = $HTTP_GET_VARS[$var_name];
		else
			return "";
	}

	$value = strtr($value, array("\\" => "", "\\\\" => "\\"));

	return $value;
}

function get_SID()
{
	$SID = "";

	if (isset($HTTP_GET_VARS))
		$SID = safe_get("SID", $HTTP_GET_VARS, 255);

	if ($SID == "") {
		if (isset($_REQUEST["PHPSESSID"]))
			$SID = $_REQUEST["PHPSESSID"];
		else
			$SID = "";
	}

	if ($SID == "") {
		if (isset($_COOKIE["PHPSESSID"]))
			$SID = $_COOKIE["PHPSESSID"];
		else
			$SID = "";
	}

	return $SID;
}

// sort an multidimension array
function array_qsort2 (&$array, $column=0, $order="ASC", $first=0, $last= -2) {
	if($last == -2) $last = count($array) - 1;
	if($last > $first) {
		$alpha = $first;
		$omega = $last;
		$guess = $array[$alpha][$column];
		while($omega >= $alpha) {
			if($order == "ASC") {
				while(strtolower($array[$alpha][$column]) < strtolower($guess)) $alpha++;
				while(strtolower($array[$omega][$column]) > strtolower($guess)) $omega--;
			} else {
				while(strtolower($array[$alpha][$column]) > strtolower($guess)) $alpha++;
				while(strtolower($array[$omega][$column]) < strtolower($guess)) $omega--;
			}
			if(strtolower($alpha) > strtolower($omega)) break;
			$temporary = $array[$alpha];
			$array[$alpha++] = $array[$omega];
			$array[$omega--] = $temporary;
		}
		array_qsort2 ($array, $column, $order, $first, $omega);
		array_qsort2 ($array, $column, $order, $alpha, $last);
	}
}

function get_cipher($ciphers) {
	$algorithms = mcrypt_list_algorithms ();
	if (!isset ($ciphers))
		$retval = $algorithms[0];
	else {
		foreach ($ciphers as $requested) {
			$requested = strtolower ($requested);
			if (in_array($requested, $algorithms)) {
				$retval = $requested;
				break;
			}
		}
		if (!isset($retval))
			$retval = $algorithms[0];
	}

	return $retval;
}

function encrypt_string ( $string, $key, $iv, $ciphers ) {
	$td = mcrypt_module_open (get_cipher($ciphers), "", MCRYPT_MODE_CFB, "");
	$key_max = mcrypt_enc_get_key_size ($td);

	if (strlen ($key) > $key_max )
		$key = substr($key, 0, $key_max);

	$iv = compat_str_pad ("", mcrypt_enc_get_iv_size ($td), $iv);

	mcrypt_generic_init ($td, $key, $iv);
	$encrypted_data = mcrypt_generic ($td, $string);
	mcrypt_generic_end ($td);

	return $encrypted_data;
}

function decrypt_string ( $string, $key, $iv, $ciphers ) {

	$td = mcrypt_module_open (get_cipher($ciphers), "", MCRYPT_MODE_CFB, "");
	$key_max = mcrypt_enc_get_key_size ($td);

	if (strlen ($key) > $key_max)
		$key = substr ($key, 0, $key_max);

	$iv = compat_str_pad ("", mcrypt_enc_get_iv_size ($td), $iv);

	mcrypt_generic_init ($td, $key, $iv);
	$decrypted_data = mdecrypt_generic ($td, $string);
	mcrypt_generic_end ($td);

	return $decrypted_data;
}

function compat_str_pad ($input, $pad_length) {
	if (!defined("STR_PAD_LEFT")) define("STR_PAD_LEFT", 0);
	if (!defined("STR_PAD_RIGHT")) define("STR_PAD_RIGHT", 1);
	if (!defined("STR_PAD_BOTH")) define("STR_PAD_BOTH", 2);

	if (func_num_args() >= 3)
		$pad_string = func_get_arg(2);
	else $pad_string = " ";

	if (func_num_args() >= 4)
		$pad_type = func_get_arg(3);
	else $pad_type = STR_PAD_RIGHT;

	return str_pad($input, $pad_length, $pad_string, $pad_type);

}

function build_regex($strSearch) {
	$strSearch = trim($strSearch);
	if($strSearch != "") {
		$strSearch = quotemeta($strSearch);
		$arSearch = split(" ",$strSearch);
		unset($strSearch);
		for($n=0;$n<count($arSearch);$n++)
			if($strSearch) $strSearch .= "|(".$arSearch[$n].")";
			else $strSearch .= "(".$arSearch[$n].")";
	}
	return $strSearch;
}


function domain_to_login($domain) {
	global $login_trans;

	return strtr($domain, $login_trans);
}

function domain_to_mysql_id($domain) {
	global $login_trans, $MySQL_Login_Max;

	return substr(strtr($domain, $login_trans), 0, $MySQL_Login_Max);
}

function check_domain_syntax($domain_name) {

	if (eregi("[^a-zA-Z0-9\\._\\-]", $domain_name, $regs))
		return FALSE;

	return TRUE;
}

function check_login_syntax($login_name) {

	if (eregi("[^a-zA-Z0-9_\\-]", $login_name, $regs))
		return FALSE;

	return TRUE;
}

function check_email_syntax($email_name) {

	if (eregi("[^a-zA-Z0-9\\.@_\\-]", $email_name, $regs))
		return FALSE;

	if (strpos($email_name, "@") == 0 || substr($email_name, 0, strpos($email_name, "@")) == "" || substr($email_name, strpos($email_name, "@")+1, strlen($email_name)) == "")
		return FALSE;

	return TRUE;
}

function check_ip_syntax($ip) {

	$octets = explode(".", $ip);

	for($i = 0; $i < 4; $i++) {
		$oct = substr($octets[$i], 0, strlen($octets[$i]));

		if (!is_numeric($oct))
			return FALSE;

		if ((int)$oct < 1 || 254 < (int)$oct)
			return FALSE;
	}

	return TRUE;
}


function my_print_r($var)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

?>
