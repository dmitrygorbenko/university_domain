<?php

	include_once ($INCLUDE_PATH."define.php");

	include_once ($INCLUDE_PATH."sql_core.php");
	include_once ($INCLUDE_PATH."control.php");

	include_once ($INCLUDE_PATH."subdomain_core.php");
	include_once ($INCLUDE_PATH."redirect_core.php");

class Zone_Control {

	var $SQL;
	var $PC;
	var $HC;

	var $SDC;

	var $PARENT_CC;

	function Zone_Control($parent) {

		$this->PARENT_CC = &$parent;

		if (isset($parent->SQL))
			$this->SQL = &$parent->SQL;
		else
			$this->SQL = new PGSQL;

		if (isset($parent->PC))
			$this->PC = &$parent->PC;
		else
			$this->PC = new Pilgrim_Control;

		if (isset($parent->HC))
			$this->HC = &$parent->HC;
		else
			$this->HC = new Pilgrim_Control;

		$this->SDC = new SubDomain_Control(&$this);
	}

	function add_zone($id_client, $delegation_zone, $zone_name, $zone_ttl, $refresh, $retry, $expire, $negative) {

		$zone_name = strtolower($zone_name);

		if (!check_domain_syntax($zone_name)) {
			$_SESSION["adminpanel_error"] = "Wrong Zone Name";
			return FALSE;
		}

		$delegation_zone_data = $this->PARENT_CC->PARENT_PM->DZ->fetch_delegation_zone($delegation_zone);
		if ($delegation_zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch delegation_zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$zone_fqdn = $zone_name.".".$delegation_zone_data->zone_name;

		// Есть ли такая зона уже ?
		// Если есть - тревога !
		$tmp_data = $this->fetch_zone_by_fqdn($zone_fqdn);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such zone already exist";
			return FALSE;
		}

		$zone_type = "1";

		$authority_server = $this->PARENT_CC->PARENT_PM->DZ->get_authority_server($delegation_zone);
		if ($authority_server == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't get authority_server: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

/*
		$result = $this->HC->connect();
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->create_dns_zone($zone_name);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();
*/
		$this->SQL->connect();

		// create zone
		$query = "INSERT INTO domain_zone (id_client, id_root_zone, fqdn, domain, type, register_date) VALUES (";
		$query .= "".$id_client.", ";
		$query .= "".$delegation_zone.", ";
		$query .= "'".$zone_fqdn."', ";
		$query .= "'".$zone_name."', ";
		$query .= "".$zone_type.", ";
		$query .= "'".date("F j, Y, G:i:s")."' ";
		$query .= ")";

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$zone_data = $this->fetch_zone_by_fqdn($zone_fqdn);
		if ($zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch newly created zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$query = "INSERT INTO domain_zone_cfg (
				id_domain_zone, ttl, authority_server,
				root_email, serial, refresh,
				retry, expire, negative) VALUES (";
		$query .= "".$zone_data->id_table.", ";
		$query .= "".$zone_ttl.", ";
		$query .= "'".$authority_server->ns_domain."', ";
		$query .= "'".$authority_server->admin_email."', ";
		$query .= "'".create_serial()."', ";
		$query .= "".$refresh.", ";
		$query .= "".$retry.", ";
		$query .= "".$expire.", ";
		$query .= "".$negative." ";
		$query .= ")";

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();

			$result = $this->really_remove_zone($client_data->id_table);
			if ($result == FALSE) {
				$_SESSION["adminpanel_error"] = $_SESSION["adminpanel_error"]."<br>Additionally, can't remove data from zone table: ".$this->SQL->get_error();
			}

			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function change_zone($id, $new_zone_data) {

		$new_zone_data["zone_name"] = strtolower($new_zone_data["zone_name"]);

		if (eregi("[^a-zA-Z0-9\\.]", $new_zone_data["zone_name"], $regs)) {
			$_SESSION["adminpanel_error"] = "Restricted symbols in zone_name. Allowed only A-Z, a-z, 0-9 and dot(.)";
			return FALSE;
		}

		// Есть ли такая зона уже ?
		// Если есть и она не старая - тревога !
		$tmp_data = $this->fetch_zone_by_name($new_zone_data["zone_name"]);
		if ($tmp_data != FALSE && $tmp_data->id_table != $id) {
			$_SESSION["adminpanel_error"] = "Such zone already exist";
			return FALSE;
		}


		// Prepearing data...

		$old_zone_data = $this->fetch_zone($id);
		if ($old_zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$old_zone_service_data = $this->fetch_zone_service($id);
		if ($old_zone_service_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch old service of zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$old_zone_data->service = $old_zone_service_data;
		$old_zone_data->system_login = domain_to_login($old_zone_data->name);

		$new_zone_data["system_login"] = domain_to_login($new_zone_data["zone_name"]);

		$client_data = $this->PARENT_CC->fetch_client_account($old_zone_data->id_client_table);
		if ($client_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch client account: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		if ($old_zone_data->name != $new_zone_data["zone_name"]) {
			$_SESSION["adminpanel_error"] = "Change zone name: such capability does not supported yet !";
			return FALSE;
		}

		// Well, now begin to make changes
		// Firs of all, we will change zone restrictions

		$result = $this->change_zone_restrictions($old_zone_data, $new_zone_data);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't change zone restrictions: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// Now, change ID of zone
		$result = $this->change_zone_ID($old_zone_data, $new_zone_data);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't change zone ID: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$this->SQL->connect();

		$query = "UPDATE zone_table SET ";
		$query .= "name='".$new_zone_data["zone_name"]."', ";
		$query .= "zone_type='".$new_zone_data["zone_type"]."', ";
		$query .= "service_type='".$new_zone_data["service_type"]."' ";
		$query .= "WHERE id_table=".$old_zone_data->id_table;

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't update table: ".$this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function remove_zone($id) {

		$zone_data = $this->fetch_zone($id);
		if ($zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}
/*
		$result = $this->HC->connect();
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->remove_dns_zone($zone_data->name);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		$result = $this->WC->remove_redirector_by_domain($zone_data->name);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] .= "Can't remove redirector: ";
			return FALSE;
		}
*/
		$this->SQL->connect();

		$result = $this->SQL->exec_query("UPDATE domain_zone SET deleted=true,  deleted_date='".date("F j, Y, G:i:s")."', deleted_by_admin=".$_SESSION["adminpanel_id_table"]." WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function really_remove_zone($id) {

		$zone_data = $this->fetch_zone($id);
		if ($zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}
/*
		$result = $this->HC->connect();
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->remove_dns_zone($zone_data->name);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		$result = $this->WC->remove_redirector_by_domain($zone_data->name);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] .= "Can't remove redirector: ";
			return FALSE;
		}
*/
		$this->SQL->connect();

		$result = $this->SQL->exec_query("DELETE FROM domain_zone WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function fetch_zone($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			dz.*
			FROM domain_zone dz
			WHERE dz.deleted = false
			AND dz.id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_zone_config($id_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			dzc.*
			FROM domain_zone_cfg dzc
			WHERE dzc.id_domain_zone=".$id_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_zone_by_fqdn($fqdn) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			dz.*
			FROM domain_zone dz
			WHERE dz.deleted = false
			AND dz.fqdn='".$fqdn."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_all_zones() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			dz.*, c.login AS client
			FROM domain_zone dz, client c
			WHERE dz.deleted = false
			AND c.id_table = dz.id_client
			ORDER BY dz.fqdn");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_all_zones_of_client($id_client) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			dz.*, c.login AS client
			FROM domain_zone dz, client c
			WHERE dz.deleted = false
			AND c.id_table = dz.id_client
			AND c.id_table = ".$id_client."
			ORDER BY dz.fqdn");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_all_zones_for_mail() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			zt.*, ct.login AS client, (
					SELECT COUNT(mt.id_table)
					FROM mail_table mt
					WHERE mt.id_zone_table = zt.id_table
				) AS mail_count
			FROM zone_table zt, client_table ct
			WHERE ct.id_table = zt.id_client_table
			ORDER BY name");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_all_zones_of_client_for_mail($id_client) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			zt.*, ct.login AS client, (
					SELECT COUNT(mt.id_table)
					FROM mail_table mt
					WHERE mt.id_zone_table = zt.id_table
				) AS mail_count
			FROM zone_table zt, client_table ct
			WHERE ct.id_table = zt.id_client_table
			AND ct.id_table=".$id_client."
			ORDER BY name");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}


	function fetch_all_zones_for_redirect() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			zt.*, ct.login AS client, (
					SELECT COUNT(rt.id_table)
					FROM redirect_table rt
					WHERE rt.id_zone_table = zt.id_table
				) AS redirector_count
			FROM zone_table zt, client_table ct
			WHERE ct.id_table = zt.id_client_table
			ORDER BY name");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_all_zones_of_client_for_redirect($id_client) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			zt.*, ct.login AS client, (
					SELECT COUNT(rt.id_table)
					FROM redirect_table rt
					WHERE rt.id_zone_table = zt.id_table
				) AS redirector_count
			FROM zone_table zt, client_table ct
			WHERE ct.id_table = zt.id_client_table
			AND ct.id_table=".$id_client."
			ORDER BY name");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

//////////////////////////////////////////////////
//		NEXT IS OUR ZONES
//////////////////////////////////////////////////

	function add_our_zone($name) {

		$name = strtolower($name);

		// Есть ли такая зона уже ?
		// Если есть - тревога !
		$tmp_data = $this->fetch_our_zone_by_name($name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such our zone already exist";
			return FALSE;
		}

		$result = $this->HC->connect();
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->create_dns_zone($name);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		$this->SQL->connect();

		$query = "INSERT INTO our_zone_table (name) VALUES (";
		$query .= "'".$name."' )";

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function change_our_zone($id, $name) {

		$name = strtolower($name);

		// Есть ли такая зона уже ?
		// Если есть и она не старая - тревога !
		$tmp_data = $this->fetch_our_zone_by_name($name);
		if ($tmp_data != FALSE && $tmp_data->id_table != $id) {
			$_SESSION["adminpanel_error"] = "Such our zone already exist";
			return FALSE;
		}

		// Это самый смелый поступок -
		// изменять доменную зону. Ну что ж, прошу...

		$old_zone_data = $this->fetch_our_zone($id);
		if ($old_zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch our zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$result = $this->HC->connect();
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->rename_dns_zone($old_zone_data->name, $name);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		// Теперь обработаем те домены, которые
		// принадлежали этой зоне
/*
		// Возмем все домены
		$domains_result = $this->DC->fetch_all_domains();
		if ($domains_result == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch all domains: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// И если какая-нибудь находится в зоне,
		// которую мы щас будем менять - изменим
		// сначала этот домен
		for ($i = 0; $i < $this->ZC->SQL->get_num_rows($domains_result); $i++) {
			$domain_data = $this->ZC->SQL->get_object($domains_result);

			if ($domain_data->zone_id == $id) {
				// Да ! Это тот самый домен !
				// Обновим его !
				if ($this->DC->change_domain($domain_data->id_table, $name, $domain_data->name, TRUE) != TRUE) {
					echo "Domain: ".$domain_data->name." (id: ".$domain_data->id_table.")<br>Can't change domain: ".$_SESSION["adminpanel_error"]."<br>";
				}
			}
		}
*/
		// Теперь просто обновим таблицу

		$this->SQL->connect();

		$query = "UPDATE our_zone_table SET ";
		$query .= "name='".$name."' ";
		$query .= " WHERE id_table=".$id."";

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function remove_our_zone($id) {

		$data = $this->fetch_our_zone($id);
		if ($data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch our zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$result = $this->HC->connect();
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->remove_dns_zone($data->name);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		$result = $this->WC->remove_redirector_by_domain($data->name);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] .= "Can't remove redirector: ";
			return FALSE;
		}

		$result = $this->WC->remove_webdir_by_domain($data->name);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] .= "Can't remove webdir: ";
			return FALSE;
		}

		$this->SQL->connect();

		$result = $this->SQL->exec_query("DELETE FROM our_zone_table WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function fetch_our_zone_by_name($name) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT * FROM our_zone_table WHERE name='".$name."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_our_zone($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT * FROM our_zone_table WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_all_our_zones() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
				*
				FROM
				our_zone_table ozt,
				(
					(
						SELECT ozt.id_table, COUNT(zt.*)
						FROM our_zone_table ozt, zone_table zt
						WHERE ozt.id_table=zt.id_our_zone_table
						GROUP BY ozt.id_table
					)
					UNION
					(
						SELECT ozt.id_table, 0
						FROM our_zone_table ozt
						WHERE ozt.id_table NOT IN
						(
							SELECT id_our_zone_table FROM zone_table
						)
					)
				) ozt2
				WHERE ozt.id_table= ozt2.id_table ");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_our_zone_hosting_zones($id_our_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			*
			FROM zone_table
			WHERE id_our_zone_table=".$id_our_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}


//////////////////////////////////////////////////
//		NEXT IS MANAGE OUR ZONES
//////////////////////////////////////////////////


	function xxx_fetch_all_subdomains_of_our_zone($id_our_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ozst.*
			FROM our_zone_subdomain_table ozst
			WHERE ozst.id_our_zone_table = ".$id_our_zone."
			ORDER BY f_name");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_fetch_our_zone_info($id_our_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ozt.name
			FROM our_zone_table ozt
			WHERE ozt.id_table = ".$id_our_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		$zone_data = $this->SQL->get_object($result);

		$this->HC->connect();
		$result2 = $this->HC->info_zone_info($zone_data->name, "MX");
		$this->HC->disconnect();

		if ($result2["error"]) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result2["mess"];
			return FALSE;
		}

		$res = Array();

		$s1 = explode("\n", $result2["result"]);
		for($i = 0; $i < count($s1); $i++) {
			if ($s1[$i] == "")
				continue;
			$s2 = explode(" ", $s1[$i]);
			$res2["prior"] = $s2[0];
			$res2["name"] = $s2[1];
			array_push($res, $res2);
		}

		return $res;
	}

	function xxx_fetch_our_zone_plain_text($id_our_zone) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ozt.name
			FROM our_zone_table ozt
			WHERE ozt.id_table = ".$id_our_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		$zone_data = $this->SQL->get_object($result);

		$this->HC->connect();
		$result2 = $this->HC->info_zone($zone_data->name);
		$this->HC->disconnect();

		if ($result2["error"]) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result2["mess"];
			return FALSE;
		}

		return $result2["result"];
	}

	// if we update zone manually
	function xxx_add_our_zone_subdomain_in_db($id_our_zone, $domain, $type, $prior, $record) {

		$domain = strtolower($domain);
		$record = strtolower($record);

		if (eregi("[^a-zA-Z0-9\\.]", $domain, $regs)) {
			$_SESSION["adminpanel_error"] = "Restricted symbols in domain. Allowed only A-Z, a-z, 0-9 and dot(.)";
			return FALSE;
		}

		$this->SQL->connect();

		$zone_data = $this->PARENT_ZC->fetch_our_zone($id_our_zone);
		if ($zone_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch our zone: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// Проверяем, есть ли такой redirect ?
		$tmp_data = $this->WC->fetch_redirector_by_domain($domain.".".$zone_data->name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Subdomain '".$domain.".".$zone_data->name."': Such redirect already exist !";
			return FALSE;
		}

		$tmp_data = $this->WC->fetch_webdir_by_domain($domain.".".$zone_data->name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Subdomain '".$domain.".".$zone_data->name."': Such webdir already exist !";
			return FALSE;
		}

		$result = $this->SQL->exec_query("INSERT INTO
			our_zone_subdomain_table (id_zone_table, f_name, name, type,".($type=="MX"?" prior,":"")." record)
			VALUES (
			".$id_zone.",
			'".$domain.".".$zone_data->name."',
			'".$domain."',
			'".$type."',
			".($type=="MX"?$prior.",":"")."
			'".$record."')");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function xxx_change_our_zone_subdomain_manually($zone_name, $zone_content) {

		$zone_name = strtolower($zone_name);

		$zone_content = base64_encode($zone_content);

		$result = $this->HC->connect();
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->update_zone_manual($zone_name, $zone_content);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		return TRUE;
	}

	// if we update zone manually
	function xxx_remove_all_subdomains_of_our_zone_in_db($id_our_zone) {

		$this->SQL->connect();

		// first, remove all redirectors and webdirs what refer to this domain
		$subdomains = $this->fetch_all_subdomains_of_our_zone($id_our_zone);
		if ($subdomains == FALSE) {
			echo "Can't fetch all subdomains of our zone: ".$_SESSION["adminpanel_error"];
			$this->SQL->disconnect();
			return FALSE;
		}

		for ($i = 0; $i < $this->SQL->get_num_rows($subdomains); $i++) {
			$data = $this->SQL->get_object($subdomains);

			$result = $this->WC->remove_redirector_by_domain($data->f_name);
			if ($result == FALSE) {
				$_SESSION["adminpanel_error"] .= "Can't remove redirector: ";
				return FALSE;
			}

			$result = $this->WC->remove_webdir_by_domain($data->f_name);
			if ($result == FALSE) {
				$_SESSION["adminpanel_error"] .= "Can't remove webdir: ";
				return FALSE;
			}
		}

		// now, flush table
		$result = $this->SQL->exec_query("DELETE FROM our_zone_subdomain_table WHERE id_our_zone_table=".$id_our_zone);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function create_mx_record_in_dns($zone_name, $prior, $record) {

		$zone_name = strtolower($zone_name);
		$record = strtolower($record);

		$this->HC->connect();

		$result = $this->HC->create_zone_info($zone_name, "MX", $prior, $record);

		$this->HC->disconnect();

		if ($result["error"]) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		return TRUE;
	}

	function update_mx_record_in_dns($zone_name, $old_prior, $prior, $record) {

		$zone_name = strtolower($zone_name);
		$record = strtolower($record);

		$this->HC->connect();

		$result = $this->HC->update_zone_info($zone_name, "MX", $old_prior, "MX", $prior, $record);

		$this->HC->disconnect();

		if ($result["error"]) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		return TRUE;
	}

	function remove_mx_record_in_dns($zone_name, $prior) {

		$this->HC->connect();

		$result = $this->HC->remove_zone_info($zone_name, "MX", $prior, "");

		$this->HC->disconnect();

		if ($result["error"]) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		return TRUE;
	}

	function xxx_create_domain_record($id_our_zone, $zone_name, $domain, $type, $prior, $record) {

		$zone_name = strtolower($zone_name);
		$record = strtolower($record);

		// Есть ли  уже такой домен ?
		$tmp_data = $this->fetch_detailed_our_zone_subdomain($id_our_zone, $domain, $type, $prior, $record);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such domain already exists";
			return FALSE;
		}

		// Проверяем, есть ли такой redirect ?
		$tmp_data = $this->PARENT_ZC->WC->fetch_redirector_by_domain($domain.".".$zone_name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Subdomain '".$domain.".".$zone_name."': Redirect for such domain already exist !";
			return FALSE;
		}

		// Проверяем, есть ли такой webdir ?
		$tmp_data = $this->PARENT_ZC->WC->fetch_webdir_by_domain($domain.".".$zone_name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Subdomain '".$domain.".".$zone_name."': Webdir for such domain already exist !";
			return FALSE;
		}

		$result = $this->HC->connect();

		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->create_dns_domain($zone_name, $domain, $type, $prior, $record);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		$this->SQL->connect();

		$query = "INSERT INTO our_zone_subdomain_table (id_our_zone_table, f_name, name, type, prior, record) VALUES (";
		$query .= "".$id_our_zone.", ";
		$query .= "'".$domain.".".$zone_name."', ";
		$query .= "'".$domain."', ";
		$query .= "'".$type."', ";
		$query .= "".$prior.", ";
		$query .= "'".$record."' ";
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

	function xxx_update_domain_record($id_our_zone, $zone_name, $id_domain, $old_domain, $old_type, $old_prior, $domain, $type, $prior, $record) {

		$zone_name = strtolower($zone_name);
		$old_domain = strtolower($old_domain);
		$domain = strtolower($domain);
		$record = strtolower($record);

		// Есть ли  уже такой домен ?
		// Если есть и он не старый - тревога !
		$tmp_data = $this->fetch_detailed_our_zone_subdomain($id_our_zone, $domain, $type, $prior, $record);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such domain already exists";
			return FALSE;
		}

		// Проверяем, есть ли такой redirect ?
		$tmp_data = $this->PARENT_ZC->WC->fetch_redirector_by_domain($domain.".".$zone_name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Subdomain '".$domain.".".$zone_name."': Redirect for such domain already exist !";
			return FALSE;
		}

		// Проверяем, есть ли такой webdir ?
		$tmp_data = $this->PARENT_ZC->WC->fetch_webdir_by_domain($domain.".".$zone_name);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Subdomain '".$domain.".".$zone_name."': Webdir for such domain already exist !";
			return FALSE;
		}

		$result = $this->HC->connect();

		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->rename_dns_domain($zone_name, $old_domain, $old_type, $old_prior, $domain, $type, $prior, $record);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		$this->SQL->connect();

		$query = "UPDATE our_zone_subdomain_table SET ";
		$query .= "id_our_zone_table=".$id_our_zone.", ";
		$query .= "f_name='".$domain.".".$zone_name."', ";
		$query .= "name='".$domain."', ";
		$query .= "type='".$type."', ";
		$query .= "prior=".$prior.", ";
		$query .= "record='".$record."' ";
		$query .= " WHERE id_table=".$id_domain."";

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function xxx_remove_domain_record($zone_name, $id_domain, $domain, $type, $prior) {

		$zone_name = strtolower($zone_name);
		$domain = strtolower($domain);

		$result = $this->HC->connect();

		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			return FALSE;
		}

		$result = $this->HC->remove_dns_domain($zone_name, $domain, $type, $prior);
		if ($result["error"] == TRUE) {
			$_SESSION["adminpanel_error"] = "Remote control: ".$result["mess"];
			$this->HC->disconnect();
			return FALSE;
		}

		$this->HC->disconnect();

		$result = $this->WC->remove_redirector_by_domain($zone_name.".".$domain);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] .= "Can't remove redirector: ";
			return FALSE;
		}

		$result = $this->WC->remove_webdir_by_domain($zone_name.".".$domain);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] .= "Can't remove webdir: ";
			return FALSE;
		}

		$this->SQL->connect();

		$query = "DELETE FROM our_zone_subdomain_table ";
		$query .= "WHERE id_table=".$id_domain;

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function xxx_fetch_detailed_our_zone_subdomain($id_our_zone, $domain, $type, $prior, $record) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ozdt.*
			FROM our_zone_subdomain_table ozdt
			WHERE ozdt.id_our_zone_table = ".$id_our_zone."
			AND ozdt.name='".$domain."'
			AND ozdt.type='".$type."'
			AND ozdt.prior=".$prior."
			AND ozdt.record='".$record."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function xxx_fetch_our_zone_subdomain_by_domain($subdomain) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ozdt.*
			FROM our_zone_subdomain_table ozdt
			WHERE ozdt.f_name='".$subdomain."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

}

?>