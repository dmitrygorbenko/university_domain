<?php
	include_once ($INCLUDE_PATH."define.php");

	include_once ($INCLUDE_PATH."sql_core.php");
	include_once ($INCLUDE_PATH."control.php");

class MX_Control {

	var $SQL;
	var $PC;

	var $PARENT_PM;

	function MX_Control($parent) {

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

	function add_mx_server($domain, $ip_addr, $title) {

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
		$tmp_data = $this->fetch_mx_by_ip($ip_addr);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such mx server already exist !";
			return FALSE;
		}

		// Let's begin...
		$mx_data["mx_domain"] = $domain;
		$mx_data["mx_ip_addr"] = $ip_addr;
		$mx_data["mx_title"] = $title;

		$this->SQL->connect();

		$query = "INSERT INTO mx_list (mx_domain, mx_ip, mx_title) VALUES (";
		$query .= "'".$mx_data["mx_domain"]."', ";
		$query .= "'".$mx_data["mx_ip_addr"]."', ";
		$query .= "'".$mx_data["mx_title"]."' ";
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

	function change_mx_server($id, $domain, $ip_addr, $title) {

		$domain = strtolower($domain);

		if (!check_domain_syntax($domain)) {
			$_SESSION["adminpanel_error"] = "Wrong Domain";
			return FALSE;
		}

		if (!check_ip_syntax($ip_addr)) {
			$_SESSION["adminpanel_error"] = "Wrong IP Address";
			return FALSE;
		}

		// Проверяем, есть ли такой mx server ?
		// Если есть и он не старый - тревога !
		$tmp_data = $this->fetch_mx_by_ip($ip_addr);
		if ($tmp_data != FALSE && $tmp_data->id_table != $id) {
			$_SESSION["adminpanel_error"] = "Such mx server already exist !";
			return FALSE;
		}

		$mx_data = $this->fetch_mx_server($id);
		if ($mx_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch mx server: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// Let's begin...

		// OLD
		$old_mx_data["mx_id"] = $mx_data->id_table;

		// NEW
		$new_mx_data["mx_domain"] = $domain;
		$new_mx_data["mx_ip_addr"] = $ip_addr;
		$new_mx_data["mx_title"] = $title;

		$this->SQL->connect();

		$query = "UPDATE mx_list SET ";
		$query .= "mx_domain='".$new_mx_data["mx_domain"]."', ";
		$query .= "mx_ip='".$new_mx_data["mx_ip_addr"]."', ";
		$query .= "mx_title='".$new_mx_data["mx_title"]."' ";
		$query .= " WHERE id_table=".$old_mx_data["mx_id"]."";

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function remove_mx_server($id) {

		$tmp_data = $this->fetch_mx_server($id);
		if ($tmp_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch mx server: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$this->SQL->connect();

		$result = $this->SQL->exec_query("DELETE FROM mx_list WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function fetch_mx_server($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			mxl.*
			FROM mx_list mxl
			WHERE mxl.id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);

		$this->SQL->disconnect();

		return $data;
	}

	function fetch_mx_by_ip($ip) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			mxl.*
			FROM mx_list mxl
			WHERE mxl.mx_ip='".$ip."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);

		$this->SQL->disconnect();

		return $data;
	}

	function fetch_all_mx_servers() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			mxl.*
			FROM mx_list mxl
			ORDER BY mxl.mx_domain");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_fetch_all_mxs_of_zone($id_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ft.*
			FROM mx_table ft
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

	function xxx_fetch_all_zones_mx($id_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ft.*
			FROM mx_table ft
			WHERE ft.id_zone_table=".$id_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_get_mxs_count() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			COUNT(ft.id_table) AS mx_count
			FROM mx_table ft");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function xxx_get_alone_mxs_count() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			COUNT(ft.id_table) AS mx_count
			FROM mx_table ft
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