<?php

	include_once ($INCLUDE_PATH."define.php");

	include_once ($INCLUDE_PATH."sql_core.php");
	include_once ($INCLUDE_PATH."control.php");

	include_once ($INCLUDE_PATH."zone_core.php");

class Client_Control {

	var $SQL;
	var $HC;

	var $ZC;
	var $SDC;

	var $PARENT_PM;

	function Client_Control($parent) {

		$this->PARENT_PM = &$parent;

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

		$this->ZC = new Zone_Control(&$this);
		$this->SDC = $this->ZC->SDC;
	}

	function add_client($login, $passwd, $email, $firstname, $lastname, $company, $country, $region, $postal, $city, $address, $phone, $fax, $egrpou, $userhdl, $active, $cp_access, $add_info) {

		if (!check_login_syntax($login)) {
			$_SESSION["adminpanel_error"] = "Wrong Login";
			return FALSE;
		}

		if (!check_email_syntax($email)) {
			$_SESSION["adminpanel_error"] = "Wrong E-Mail";
			return FALSE;
		}

		if (!is_numeric($postal)) {
			$_SESSION["adminpanel_error"] = "Wrong Postal";
			return FALSE;
		}

		if ($egrpou != "" && !is_numeric($egrpou)) {
			$_SESSION["adminpanel_error"] = "Wrong EGRPOU";
			return FALSE;
		}

		$this->SQL->connect();

		// Проверяем, есть ли такой логин ?
		// Если есть - тревога !
		$tmp_data = $this->fetch_client_by_login($login);
		if ($tmp_data != FALSE) {
			$_SESSION["adminpanel_error"] = "Such login already exist !";
			return FALSE;
		}

		// Теперь создаем нового клиента
		$query = "INSERT INTO client (
				login, passwd, register_date, active, cp_access ) VALUES (";
		$query .= "'".$login."', ";
		$query .= "'".$passwd."', ";
		$query .= "'".date("F j, Y, G:i:s")."', ";
		$query .= "".($active?"true":"false").", ";
		$query .= "".($cp_access?"true":"false")." ";
		$query .= ")";

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$client_data = $this->fetch_client_by_login($login);
		if ($client_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Cent' fetch newly created client: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$query = "INSERT INTO client_info (
				id_client, firstname, lastname, country, company, region, postal,
				city, address, phone, fax, email, egrpou, userhdl, add_info) VALUES (";
		$query .= "".$client_data->id_table.", ";
		$query .= "'".$firstname."', ";
		$query .= "'".$lastname."', ";
		$query .= "'".$country."', ";
		$query .= "'".$company."', ";
		$query .= "'".$region."', ";
		$query .= "".$postal.", ";
		$query .= "'".$city."', ";
		$query .= "'".$address."', ";
		$query .= "'".$phone."', ";
		$query .= "'".$fax."', ";
		$query .= "'".$email."', ";
		$query .= "".($egrpou!=""?$egrpou:"0").", ";
		$query .= "'".$userhdl."', ";
		$query .= "'".$add_info."' ";
		$query .= ")";

		$result = $this->SQL->exec_query($query);
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();

			$result = $this->really_remove_client($client_data->id_table);
			if ($result == FALSE) {
				$_SESSION["adminpanel_error"] = $_SESSION["adminpanel_error"]."<br>Additionally, can't remove data from client table: ".$this->SQL->get_error();
			}

			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function change_client($id, $login, $passwd, $email, $firstname, $lastname, $company, $country, $region, $postal, $city, $address, $phone, $fax, $egrpou, $userhdl, $active, $cp_access, $add_info) {

		if (!check_login_syntax($login)) {
			$_SESSION["adminpanel_error"] = "Wrong Login";
			return FALSE;
		}

		if (!check_email_syntax($email)) {
			$_SESSION["adminpanel_error"] = "Wrong E-Mail";
			return FALSE;
		}

		if (!is_numeric($postal)) {
			$_SESSION["adminpanel_error"] = "Wrong Postal";
			return FALSE;
		}

		if ($egrpou != "" && !is_numeric($egrpou)) {
			$_SESSION["adminpanel_error"] = "Wrong EGRPOU";
			return FALSE;
		}

		// Проверяем, есть ли такой логин ?
		// Если есть и он не старый - тревога !
		$tmp_data = $this->fetch_client_by_login($login);
		if ($tmp_data != FALSE && $tmp_data->id_table != $id) {
			$_SESSION["adminpanel_error"] = "Such login already exist !";
			return FALSE;
		}

		// Извлекаем старые данные из таблицы клиента
		$old_client_data = $this->fetch_client($id);
		if ($old_client_data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch client: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		$this->SQL->connect();
		// Теперь надо изменить основные данные клиента.

		$query = "UPDATE client SET ";
		$query .= "login='".$login."', ";
		if ($passwd != "")
			$query .= "passwd='".$passwd."', ";
		$query .= "active=".($active?"true":"false").", ";
		$query .= "cp_access=".($cp_access?"true":"false")." ";
		$query .= " WHERE id_table=".$id."";

		$result = $this->SQL->exec_query($query);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$query = "UPDATE client_info SET ";
		$query .= "firstname='".$firstname."', ";
		$query .= "lastname='".$lastname."', ";
		$query .= "company='".$company."', ";
		$query .= "country='".$country."', ";
		$query .= "region='".$region."', ";
		$query .= "city='".$city."', ";
		$query .= "postal='".$postal."', ";
		$query .= "address='".$address."', ";
		$query .= "phone='".$phone."', ";
		$query .= "fax='".$fax."', ";
		$query .= "add_info='".$add_info."' ";
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

	function remove_client($id) {

		$this->SQL->connect();

		$data = $this->fetch_client($id);
		if ($data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch client: ".$_SESSION["adminpanel_error"];
			$this->SQL->disconnect();
			return FALSE;
		}

		$result = $this->SQL->exec_query("UPDATE client SET active=false, cp_access=false, deleted=true, deleted_date='".date("F j, Y, G:i:s")."', deleted_by_admin=".$_SESSION["adminpanel_id_table"]." WHERE id_table=".$id."");
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function really_remove_client($id) {

		$this->SQL->connect();

		$data = $this->fetch_client($id);
		if ($data == FALSE) {
			$_SESSION["adminpanel_error"] = "Can't fetch client: ".$_SESSION["adminpanel_error"];
			$this->SQL->disconnect();
			return FALSE;
		}

		$result = $this->SQL->exec_query("DELETE FROM client WHERE id_table=".$id."");
		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();
		return TRUE;
	}

	function fetch_all_client() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
				c.*, (
						SELECT COUNT(dz.id_table)
						FROM domain_zone dz
						WHERE dz.deleted = false
						AND dz.id_client = c.id_table
					) AS zone_count
				FROM client c
				WHERE c.deleted = false
				ORDER BY c.login");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_all_client_light() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
				c.*
				FROM client c
				WHERE c.deleted = false
				ORDER BY c.login");


		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function fetch_client($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			c.*
			FROM client c
			WHERE c.deleted = false
			AND c.id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_client_info($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ci.*
			FROM client_info ci
			WHERE ci.id_client = ".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_clients_zones($id_client) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			dz.*
			FROM domain_zone dz
			WHERE deleted = false
			AND dz.id_client=".$id_client."
			ORDER BY dz.fqdn");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_fetch_client_by_domain($domain) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
		ct.*, zt.name AS domain
		FROM client_table ct, zone_table zt
		WHERE zt.name='".$domain."'
		AND zt.id_client_table=ct.id_table");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_client_by_login($login) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
				c.* FROM client c
				WHERE c.deleted = false
				AND c.login='".$login."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function xxx_is_client_exist_by_domain($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			ct.*, zt.name AS domain
			FROM client_table ct, zone_table zt
			WHERE ct.id_zone_table=".$id."
			AND zt.id_client_table=ct.id_table");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function fetch_countries($lang_id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
				ci.*
				FROM country_info ci
				WHERE ci.id_lang='".$lang_id."'
				ORDER BY ci.country");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;

	}

// ********************************************************************************************
// ********************************************************************************************
//
//	HERE GOES CODE FOR UNPAYED CLIENTS
//
// ********************************************************************************************
// ********************************************************************************************

	function xxx_candidate_activate_account($id) {

		// Check clear
		$data = $this->candidate_fetch_client($id);
		if ($data == FALSE) {
			echo "Can't fetch client: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// Now, send data to client_create()
		if ($this->add_client($data->service_type, $data->domain_type, $data->fdname, "1", $data->login, $data->passwd, $data->email, $data->firstname, $data->lastname, $data->company, $data->region, $data->postal, $data->city, $data->address, $data->phone, $data->fax, $data->add_info, "system") != TRUE) {
			echo "Can't create client: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		//Delete this account from database...
		if ($this->candidate_remove_client($id) != TRUE) {
			echo "Can't remove activated account: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		// Now, we should delete all other queries fo this domain
		$others = $this->candidate_fetch_client_by_fdname($data->fdname);
		if ($others == FALSE) {
			echo "Can't fetch other accounts: ".$_SESSION["adminpanel_error"];
			return FALSE;
		}

		for ($i = 0; $i < $this->SQL->get_num_rows($others); $i++) {
			$data = $this->SQL->get_object($others);

			if ($this->candidate_remove_client($data->id_table) != TRUE) {
				echo "Can't remove others account: ".$_SESSION["adminpanel_error"];
				return FALSE;
			}
		}

		return TRUE;
	}

	function xxx_candidate_remove_client($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("DELETE FROM candidate_client_table WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return TRUE;
	}

	function xxx_candidate_fetch_client($id) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			*
			FROM candidate_client_table
			WHERE id_table=".$id);

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$data = $this->SQL->get_object($result);
		$this->SQL->disconnect();

		return $data;
	}

	function xxx_candidate_fetch_client_by_fdname($fdname) {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			*
			FROM candidate_client_table
			WHERE fdname='".$fdname."'");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

	function xxx_candidate_fetch_all_client() {

		$this->SQL->connect();

		$result = $this->SQL->exec_query("SELECT
			id_table, fdname, service_type, query_time, firstname, lastname
			FROM candidate_client_table
			ORDER BY fdname, query_time");

		if ($result == FALSE) {
			$_SESSION["adminpanel_error"] = $this->SQL->get_error();
			$this->SQL->disconnect();
			return FALSE;
		}

		$this->SQL->disconnect();

		return $result;
	}

}

?>