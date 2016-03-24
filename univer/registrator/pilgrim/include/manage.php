<?php

	include_once ($INCLUDE_PATH."define.php");

	include_once ($INCLUDE_PATH."sql_core.php");
	include_once ($INCLUDE_PATH."control.php");

	include_once ($INCLUDE_PATH."ns_core.php");
	include_once ($INCLUDE_PATH."mx_core.php");
	include_once ($INCLUDE_PATH."delegation_zone_core.php");

	include_once ($INCLUDE_PATH."client_core.php");
	include_once ($INCLUDE_PATH."zone_core.php");

class Pilgrim_Manage {

	var $SQL;
	var $HC;

	var $NS;
	var $MX;
	var $DZ;

	var $CC;
	var $ZC;
	var $SDC;

	function Pilgrim_Manage() {

		$this->SQL = new PGSQL;
		$this->HC = new Pilgrim_Control;

		$this->NS = new NS_Control(&$this);
		$this->MX = new MX_Control(&$this);
		$this->DZ = new DZ_Control(&$this);

		$this->CC = new Client_Control(&$this);

		$this->ZC = $this->CC->ZC;
		$this->SDC = $this->CC->ZC->SDC;
	}
}

?>