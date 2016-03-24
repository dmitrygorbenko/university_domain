<?php
	include_once ($INCLUDE_PATH."define.php");

	include_once ($INCLUDE_PATH."sql_core.php");
	include_once ($INCLUDE_PATH."control.php");

class NS_Control {

	var $SQL;
	var $PC;

	var $PARENT_PM;

	function NS_Control($parent) {

		$this->PARENT_PM = &$parent;

		if (isset($parent->SQL))
			$this->SQL = &$parent->SQL;
		else
			$this->SQL = new PGSQL;

		if (isset($parent->PC))
			$this->PC = &$parent->PC;
		else
			$this->PC = new Pilgrim_Control;
	}

	function add_ns_server($domain, $ip_addr, $title) {

		$domain = strtolower($domain);

		if (!check_domain_syntax($domain)) {
			$_SESSION["adminpanel_error"] = "Wrong Domain";
			return FALSE;
		}

		if (!check_ip_syntax($ip_addr)) {
			$_SESSION["adminpanel_error"] = "Wrong IP Address";
			return FALSE;
		}

		// Проверяем, есть ли такой ip_addr ?
		// Если да - тревога !
		$tmp_data = $this->fetch_ns_by_ip($ip_addr);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such ns server already exist !";
			return FALSE;
		}

		// Let's begin...
		$ns_data["ns_domain"] = $domain;
		$ns_data["ns_ip_addr"] = $ip_addr;
		$ns_data["ns_title"] = $title;

		$this->SQL->connect();

		$query = "INSERT INTO ns_list (ns_domain, ns_ip, ns_title) VALUES (";
		$query .= "'".$ns_data["ns_domain"]."', ";
		$query .= "'".$ns_data["ns_ip_addr"]."', ";
		$query .= "'".$ns_data["ns_title"]."' ";
		$query .= ")";

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function change_ns_server($id, $domain, $ip_addr, $title) {

		$domain = strtolower($domain);

		if (!check_domain_syntax($domain)) {
			$_SESSION["adminpanel_error"] = "Wrong Domain";
			return FALSE;
		}

		if (!check_ip_syntax($ip_addr)) {
			$_SESSION["adminpanel_error"] = "Wrong IP Address";
			return FALSE;
		}

		// Проверяем, есть ли такой ns server ?
		// Если есть и он не старый - тревога !
		$tmp_data = $this->fetch_ns_by_ip($ip_addr);
		if ($tmp_data != FALSE && $tmp_data->id_table != $id) {
			$_SESSION["adminpanel_error"] = "Such ns server already exist !";
			return FALSE;
		}

		$ns_data = $this->fetch_ns_server($id);
		if ($ns_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch ns server: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// Let's begin...

		// OLD
		$old_ns_data["ns_id"] = $ns_data->id_table;

		// NEW
		$new_ns_data["ns_domain"] = $domain;
		$new_ns_data["ns_ip_addr"] = $ip_addr;
		$new_ns_data["ns_title"] = $title;

		$this->SQL->connect();

		$query = "UPDATE ns_list SET ";
		$query .= "ns_domain='".$new_ns_data["ns_domain"]."', ";
		$query .= "ns_ip='".$new_ns_data["ns_ip_addr"]."', ";
		$query .= "ns_title='".$new_ns_data["ns_title"]."' ";
		$query .= " WHERE id_table=".$old_ns_data["ns_id"]."";

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function remove_ns_server($id) {

		$tmp_data = $this->fetch_ns_server($id);
		if ($tmp_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch ns server: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$this->SQL->connect();

		$result = $this->SQL->exec_query("DELETE FROM ns_list WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function fetch_ns_server($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			nsl.*
			FROM ns_list nsl
			WHERE nsl.id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);

		$this->SQL->disconnect();

		return $data;
	}

	function fetch_ns_by_ip($ip) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			nsl.*
			FROM ns_list nsl
			WHERE nsl.ns_ip='".$ip."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);

		$this->SQL->disconnect();

		return $data;
	}

	function fetch_all_ns_servers() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			nsl.*
			FROM ns_list nsl
			ORDER BY nsl.ns_domain");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_fetch_all_nss_of_zone($id_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ft.*
			FROM ns_table ft
			WHERE ft.id_zone_table = ".$id_zone."
			ORDER BY ft.login");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_fetch_all_zones_ns($id_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ft.*
			FROM ns_table ft
			WHERE ft.id_zone_table=".$id_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_get_nss_count() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			COUNT(ft.id_table) AS ns_count
			FROM ns_table ft");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function xxx_get_alone_nss_count() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			COUNT(ft.id_table) AS ns_count
			FROM ns_table ft
			WHERE ft.dummy=1");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

//####################################################
//||||||||||||||||||||||||||||||||||||||||||||||||||||
//####################################################

}

?>