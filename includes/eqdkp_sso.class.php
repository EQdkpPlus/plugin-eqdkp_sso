<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp SSO Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class eqdkp_sso_class extends gen_class {
	
	public function get_own_master_key(){
		include($this->pfh->FolderPath('config', 'eqdkp_sso').'config.php');
		return $eqdkp_sso_masterKey;
	}
	
	public function get_uniqueid(){
		include($this->pfh->FolderPath('config', 'eqdkp_sso').'config.php');
		return $eqdkp_sso_uniqueID;
	}
	
	public function get_master_key(){
		$arrValues = $this->config->get_config('eqdkp_sso');
		if(isset($arrValues['master_key']))  $masterKey	= $this->encrypt->decrypt($arrValues['master_key']);

		return $masterKey;
	}
	
	public function check_connection($db_type, $db_host, $db_user, $db_password, $db_database, $db_prefix){
		if((int)$db_type == 0){
			//Same DB
			try {
				$mydb = dbal::factory(array('dbtype' => 'mysqli', 'open'=>true, 'debug_prefix' => 'sso_connector_', 'table_prefix' => trim($db_prefix)));
			} catch(DBALException $e){
				return $e->getMessage();
				$mydb = false;
			}
		} elseif((int)$db_type == 1){
			//Other DB
			try {
				$mydb = dbal::factory(array('dbtype' => 'mysqli', 'debug_prefix' => 'sso_connector_', 'table_prefix' => trim($db_prefix)));
				$mydb->connect($db_host, $db_database, $db_user, $db_password);
			} catch(DBALException $e){
				return $e->getMessage();
				$mydb = false;
			}
		} elseif((int)$db_type == 2){
			//Bridge
			$mydb = $this->bridge->db;
		}
		
		if ($mydb){
			$objQuery = $mydb->query("SELECT * FROM __users LIMIT 1");
			if ($objQuery){
				return true;
			}
		}
		return false;
	}
	
	public function createConnection($db_type, $db_host, $db_user, $db_password, $db_database, $db_prefix){
		if((int)$db_type == 0){
			//Same DB
			try {
				$mydb = dbal::factory(array('dbtype' => 'mysqli', 'open'=>true, 'debug_prefix' => 'sso_connector_', 'table_prefix' => trim($db_prefix)));
			} catch(DBALException $e){
				$mydb = false;
			}
		} elseif((int)$db_type == 1){
			//Other DB
			try {
				$mydb = dbal::factory(array('dbtype' => 'mysqli', 'debug_prefix' => 'sso_connector_', 'table_prefix' => trim($db_prefix)));
				$mydb->connect($db_host, $db_database, $db_user, $db_password);
			} catch(DBALException $e){
				$mydb = false;
			}
		} elseif((int)$db_type == 2){
			//Bridge
			$mydb = $this->bridge->db;
		}
		
		return $mydb;
	}
	
	public function getMasterConnection(){
		$arrValues = $this->config->get_config('eqdkp_sso');
		
		if(isset($arrValues['master_key']))  $arrValues['master_key']	= $this->encrypt->decrypt($arrValues['master_key']);
		if(isset($arrValues['db_host'])) 	 $arrValues['db_host']		= $this->encrypt->decrypt($arrValues['db_host']);
		if(isset($arrValues['db_user']))	 $arrValues['db_user']		= $this->encrypt->decrypt($arrValues['db_user']);
		if(isset($arrValues['db_password'])) $arrValues['db_password']	= $this->encrypt->decrypt($arrValues['db_password']);
		if(isset($arrValues['db_database'])) $arrValues['db_database']	= $this->encrypt->decrypt($arrValues['db_database']);
		if(isset($arrValues['db_prefix']))   $arrValues['db_prefix']	= $this->encrypt->decrypt($arrValues['db_prefix']);
		
		$objMasterDB = $this->createConnection($arrValues['db_type'], $arrValues['db_host'], $arrValues['db_user'], $arrValues['db_password'], $arrValues['db_database'], $arrValues['db_prefix']);
		return $objMasterDB;
	}
	
}
?>