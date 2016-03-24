<?php

class Pilgrim_Control {

	var $server = Array();
	var $client = Array();
	var $f_res = Array();
	var $debug = FALSE;

	function Pilgrim_Control() {
		$this->server["ip"] = "192.168.5.19";
		$this->server["port"] = "7777";

		$this->client["ip"] = "192.168.5.19";
		$this->client["port"] = "7776";
		$this->client["sock"] = 0;
	}

	function Init() {
		$this->server["ip"] = "192.168.5.19";
		$this->server["port"] = "7777";

		$this->client["ip"] = "192.168.5.19";
		$this->client["port"] = "7776";
		$this->client["sock"] = 0;
	}

	function get_line() {
		if ($this->client["sock"] == 0)
			return "";

		return chop(fgets($this->client["sock"], 1024));
	}

	function send_command($cmd) {
		if ($this->client["sock"] == 0)
			return;

		fwrite($this->client["sock"], $cmd."\n");
	}

	function connect() {

		$remote_server_login = "REChW6uHBBx7BtGz";

		if ($this->client["sock"] != 0) {
			$this->f_res["error"] = FALSE;
			return $this->f_res;
		}

		$this->client["sock"] = @fsockopen($this->server["ip"], $this->server["port"], $errno, $errstr, 0);

		if (!$this->client["sock"]) {
			$this->f_res["mess"] = "Server not found";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		@stream_set_timeout($this->client["sock"], 10);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			fclose($this->client["sock"]);
			$this->client["sock"] = 0;

			$this->f_res["mess"] = "Server not ready to receive commands";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->send_command("LOGIN ".$remote_server_login);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			fclose($this->client["sock"]);
			$this->client["sock"] = 0;

			$this->f_res["mess"] = "Failed to login";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function disconnect() {

		if ($this->client["sock"] == 0)
			return;

		$this->send_command("LOGOUT");
		$tmp = $this->get_line();

		fclose($this->client["sock"]);

		$this->client["sock"] = 0;

		if ($this->debug) echo "Disconnected !!!<br>";
	}

	function create_dns_zone($zone) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->send_command("DNS CREATE ZONE ".$zone);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not create dns zone";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function rename_dns_zone($zone, $new_zone) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->send_command("DNS UPDATE ZONE ".$zone." ".$new_zone);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not update dns zone";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function remove_dns_zone($zone) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->send_command("DNS DELETE ZONE ".$zone);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not remove dns zone";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function create_dns_domain($zone, $domain, $type, $prior, $record) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		if ($type == "MX")
			$this->send_command("DNS CREATE DOMAIN ".$zone." ".$domain." ".$type." ".$prior." ".$record);
		else
			$this->send_command("DNS CREATE DOMAIN ".$zone." ".$domain." ".$type." ".$record);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not create dns domain";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function rename_dns_domain($zone, $domain, $type, $prior, $new_domain, $new_type, $new_prior, $new_record) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		if ($type == "MX")
			$cmd = "DNS UPDATE DOMAIN ".$zone." ".$domain." ".$type." ".$prior;
		else
			$cmd = "DNS UPDATE DOMAIN ".$zone." ".$domain." ".$type;

		if ($new_type == "MX")
			$cmd .= " ".$new_domain." ".$new_type." ".$new_prior." ".$new_record;
		else
			$cmd .= " ".$new_domain." ".$new_type." ".$new_record;

		$this->send_command($cmd);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not update dns domain";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function remove_dns_domain($zone, $domain, $type, $prior) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		if ($type == "MX")
			$this->send_command("DNS DELETE DOMAIN ".$zone." ".$domain." ".$type." ".$prior);
		else
			$this->send_command("DNS DELETE DOMAIN ".$zone." ".$domain." ".$type);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not remove dns domain";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function info_zone_info($zone, $type) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["result"] = "";

		$this->send_command("DNS INFO ZONE_INFO ".$zone." ".$type);

		while(1) {

			$buffer = $this->get_line();

			if (ereg("^\\+OK", $buffer)) {
				break;
			}

			if (ereg("^\\-NO", $buffer) || $buffer == "") {
				$this->f_res["mess"] = "Can not fetch zone info";
				$this->f_res["error"] = TRUE;
				return $this->f_res;
			}

			$this->f_res["result"] .= "\n".$buffer;
		}

		$this->f_res["result"] = base64_decode($this->f_res["result"]);

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function info_zone($zone) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["result"] = "";

		$this->send_command("DNS INFO ZONE ".$zone);

		while(1) {

			$buffer = $this->get_line();

			if (ereg("^\\+OK", $buffer)) {
				break;
			}

			if (ereg("^\\-NO", $buffer) || $buffer == "") {
				$this->f_res["mess"] = "Can not fetch zone plain text";
				$this->f_res["error"] = TRUE;
				return $this->f_res;
			}

			$this->f_res["result"] .= "\n".$buffer;
		}

		$this->f_res["result"] = base64_decode($this->f_res["result"]);

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function create_zone_info($zone, $type, $pri, $rr) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		if ($type == "MX")
			$this->send_command("DNS CREATE ZONE_INFO ".$zone." ".$type." ".$pri." ".$rr);
		else
			$this->send_command("DNS CREATE ZONE_INFO ".$zone." ".$type." ".$rr);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not create zone info";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function update_zone_info($zone, $old_type, $old_pri, $new_type, $new_pri, $new_rr) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->send_command("DNS UPDATE ZONE_INFO ".$zone." ".$old_type." ".$old_pri." ".$new_type." ".$new_pri." ".$new_rr);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not update zone info";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function update_zone_manual($zone, $content) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->send_command("DNS UPDATE ZONE_MANUAL ".$zone." ".$content);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not update zone manual";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function remove_zone_info($zone, $type, $pri, $rr) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		if ($type == "MX")
			$this->send_command("DNS DELETE ZONE_INFO ".$zone." ".$type." ".$pri);
		else
			$this->send_command("DNS DELETE ZONE_INFO ".$zone." ".$type." ".$rr);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not remove zone info";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

	function service_control($action, $service) {
		if ($this->client["sock"] == 0) {
			$this->f_res["mess"] = "Not connected to Remote Server !";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->send_command("SERVICE ".$action." ".$service);

		$buffer = $this->get_line();

		if (!ereg("^\\+OK", $buffer)) {
			$this->f_res["mess"] = "Can not complete requested action";
			$this->f_res["error"] = TRUE;
			return $this->f_res;
		}

		$this->f_res["error"] = FALSE;
		return $this->f_res;
	}

}

?>
