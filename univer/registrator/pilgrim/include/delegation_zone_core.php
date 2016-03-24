<?php
	include_once ($INCLUDE_PATH."define.php");

	include_once ($INCLUDE_PATH."sql_core.php");
	include_once ($INCLUDE_PATH."control.php");

class DZ_Control {

	var $SQL;
	var $PC;

	var $PARENT_PM;

	function DZ_Control($parent) {

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

	function add_delegation_zone($zone_name, $admin_email, $lease_time, $initial, $grace, $pending, $zone_title) {

		$zone_name = strtolower($zone_name);

		if (!check_domain_syntax($zone_name)) {
			$_SESSION["adminpanel_error"] = "Wrong Zone Name";
			return FALSE;
		}

		if (!check_email_syntax($admin_email)) {
			$_SESSION["adminpanel_error"] = "Wrong E-Mail Address";
			return FALSE;
		}

		// Проверяем, есть ли такая зона ?
		// Если да - тревога !
		$tmp_data = $this->fetch_delegation_zone_by_name($zone_name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such delegation_zone already exist !";
			return FALSE;
		}

		// Let's begin...
		$zone_data["zone_name"] = $zone_name;
		$zone_data["admin_email"] = $admin_email;
		$zone_data["lease_time"] = $lease_time;
		$zone_data["initial"] = $initial;
		$zone_data["grace"] = $grace;
		$zone_data["pending"] = $pending;
		$zone_data["zone_title"] = $zone_title;

		$this->SQL->connect();

		$query = "INSERT INTO root_zone (zone_name, admin_email, max_lease_time, initial, grace, pending, zone_title, active) VALUES (";
		$query .= "'".$zone_data["zone_name"]."', ";
		$query .= "'".$zone_data["admin_email"]."', ";
		$query .= "".$zone_data["lease_time"].", ";
		$query .= "".$zone_data["initial"].", ";
		$query .= "".$zone_data["grace"].", ";
		$query .= "".$zone_data["pending"].", ";
		$query .= "'".$zone_data["zone_title"]."', true";
		$query .= ")";

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		if ($this->add_ns_and_mx_to_zone($zone_name) != TRUE) {
			$_SESSION["adminpanel_error"] = "Can't connect NS or MX servers with Zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function change_delegation_zone($id, $zone_name, $admin_email, $lease_time, $initial, $grace, $pending, $zone_title, $active) {

		$zone_name = strtolower($zone_name);

		if (!check_domain_syntax($zone_name)) {
			$_SESSION["adminpanel_error"] = "Wrong Zone Name";
			return FALSE;
		}

		if (!check_email_syntax($admin_email)) {
			$_SESSION["adminpanel_error"] = "Wrong E-Mail Address";
			return FALSE;
		}

		// Проверяем, есть ли такой ns server ?
		// Если есть и он не старый - тревога !
		$tmp_data = $this->fetch_delegation_zone_by_name($zone_name);
		if ($tmp_data != FALSE && $tmp_data->id_table != $id) {
			$_SESSION["adminpanel_error"] = "Such zone already exist !";
			return FALSE;
		}

		$zone_data = $this->fetch_delegation_zone($id);
		if ($zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// Let's begin...

		// OLD
		$old_zone_data["id"] = $zone_data->id_table;

		// NEW
		$new_zone_data["zone_name"] = $zone_name;
		$new_zone_data["admin_email"] = $admin_email;
		$new_zone_data["lease_time"] = $lease_time;
		$new_zone_data["initial"] = $initial;
		$new_zone_data["grace"] = $grace;
		$new_zone_data["pending"] = $pending;
		$new_zone_data["zone_title"] = $zone_title;
		$new_zone_data["active"] = $active!=""?"true":"false";

		$this->SQL->connect();

		$query = "UPDATE root_zone SET ";
		$query .= "zone_name='".$new_zone_data["zone_name"]."', ";
		$query .= "admin_email='".$new_zone_data["admin_email"]."', ";
		$query .= "zone_title='".$new_zone_data["zone_title"]."', ";
		$query .= "max_lease_time=".$new_zone_data["lease_time"].", ";
		$query .= "initial=".$new_zone_data["initial"].", ";
		$query .= "grace=".$new_zone_data["grace"].", ";
		$query .= "pending=".$new_zone_data["pending"].", ";
		$query .= "active=".$new_zone_data["active"]." ";
		$query .= " WHERE id_table=".$old_zone_data["id"]."";

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		if ($this->delete_ns_and_mx_from_zone($new_zone_data["zone_name"]) != TRUE) {
			$_SESSION["adminpanel_error"] = "Can't disconnect NS or MX servers from Zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		if ($this->add_ns_and_mx_to_zone($new_zone_data["zone_name"]) != TRUE) {
			$_SESSION["adminpanel_error"] = "Can't connect NS or MX servers with Zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function remove_delegation_zone($id) {

		$tmp_data = $this->fetch_delegation_zone($id);
		if ($tmp_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$this->SQL->connect();

		$result = $this->SQL->exec_query("DELETE FROM root_zone WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function add_ns_and_mx_to_zone($zone_name) {

		$zone_data = $this->fetch_delegation_zone_by_name($zone_name);
		if ($zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't find zone !";
			return FALSE;
		}

		# fetch NS and MX servers
		$ns_servers = $this->PARENT_PM->NS->fetch_all_ns_servers();
		if ($ns_servers == FALSE) {
			echo "Can't fetch all ns servers: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$mx_servers = $this->PARENT_PM->MX->fetch_all_mx_servers();
		if ($mx_servers == FALSE) {
			echo "Can't fetch all mx servers: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		for ($i = 0; $i < $this->PARENT_PM->NS->SQL->get_num_rows($ns_servers); $i++) {
			$data = $this->PARENT_PM->NS->SQL->get_object($ns_servers);

			$post_ns_id = safe_get("ns_id_".$i, "POST", 255);
			$post_ns_use = safe_get("ns_checkbox_".$i, "POST", 255);
			$post_ns_role = safe_get("ns_role_".$i, "POST", 255);

			if ($post_ns_use != "") {

				$ns_data = $this->PARENT_PM->NS->fetch_ns_server($post_ns_id);

				if ($ns_data == FALSE) {
					$_SESSION["adminpanel_error"] = "<br>Can't find NS Server (posted ns_id = ".$post_ns_id."): ".$_SESSION["adminpanel_error"];
					continue;
				}

				if (strtolower($post_ns_role) == "primary")
					$prim = 1;
				else
					$prim = 0;

				$query = "INSERT INTO ns_of_root_zone (id_ns, id_root_zone, ns_master) VALUES (";
				$query .= "".$post_ns_id.", ";
				$query .= "".$zone_data->id_table.", ";
				$query .= "".$prim.")";

				$result = $this->SQL->exec_query($query);

				if ($result == FALSE) {
					$_SESSION["adminpanel_error"] = "<br>Can't connect NS Server with Zone (posted ns_id = ".$post_ns_id."): ".$this->SQL->get_error();
					continue;
				}
			}
		}

		for ($i = 0; $i < $this->PARENT_PM->NS->SQL->get_num_rows($mx_servers); $i++) {
			$data = $this->PARENT_PM->NS->SQL->get_object($mx_servers);

			$post_mx_id = safe_get("mx_id_".$i, "POST", 255);
			$post_mx_use = safe_get("mx_checkbox_".$i, "POST", 255);
			$post_mx_prior = safe_get("mx_prior_".$i, "POST", 255);

			if ($post_mx_use != "") {

				$ns_data = $this->PARENT_PM->NS->fetch_ns_server($post_mx_id);

				if ($ns_data == FALSE) {
					$_SESSION["adminpanel_error"] = "<br>Can't find MX Server (posted mx_id = ".$post_mx_id."): ".$_SESSION["adminpanel_error"];
					continue;
				}

				$query = "INSERT INTO mx_of_root_zone (id_mx, id_root_zone, mx_prior) VALUES (";
				$query .= "".$post_mx_id.", ";
				$query .= "".$zone_data->id_table.", ";
				$query .= "".$post_mx_prior.")";

				$result = $this->SQL->exec_query($query);

				if ($result == FALSE) {
					$_SESSION["adminpanel_error"] = "<br>Can't connect MX Server with Zone (posted mx_id = ".$post_mx_id."): ".$this->SQL->get_error();
					continue;
				}
			}
		}

		return TRUE;
	}

	function delete_ns_and_mx_from_zone($zone_name) {

		$zone_data = $this->fetch_delegation_zone_by_name($zone_name);

		if ($zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't find zone !";
			return FALSE;
		}

		$query = "DELETE FROM ns_of_root_zone where id_root_zone = ".$zone_data->id_table;

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't remove MX Server from Zone: ".$this->SQL->get_error();
			return FALSE;
		}

		$query = "DELETE FROM mx_of_root_zone where id_root_zone = ".$zone_data->id_table;

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't remove MX Server from Zone: ".$this->SQL->get_error();
			return FALSE;
		}

		return TRUE;
	}

	function fetch_delegation_zone($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			rz.*
			FROM root_zone rz
			WHERE rz.id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);

		$this->SQL->disconnect();

		return $data;
	}

	function fetch_delegation_zone_by_name($zone_name) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			rz.*
			FROM root_zone rz
			WHERE rz.zone_name='".$zone_name."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);

		$this->SQL->disconnect();

		return $data;
	}

	function fetch_all_delegation_zones() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			rz.*
			FROM root_zone rz
			ORDER BY rz.zone_name");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_ns_servers_of_zone($id_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			norz.*
			FROM ns_of_root_zone norz
			WHERE norz.id_root_zone = ".$id_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_mx_servers_of_zone($id_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			morz.*
			FROM mx_of_root_zone morz
			WHERE morz.id_root_zone = ".$id_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function get_authority_server($delegation_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			rz.admin_email, nl.ns_domain
			FROM root_zone rz, ns_list nl, ns_of_root_zone norz
			WHERE rz.id_table = ".$delegation_zone."
			AND norz.id_root_zone = rz.id_table
			AND nl.id_table = norz.id_ns");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}
		$data = $this->SQL->get_object($result);

		$this->SQL->disconnect();

		return $data;
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