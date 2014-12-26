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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_w_eqdkp_sso" ) ) {
	class pdh_w_eqdkp_sso extends pdh_w_generic{
		
		public function save_slave($intSlaveID, $strMasterKey, $arrValues){
			if ($strMasterKey == "") return false;
			
			$crypt = register('encrypt', array($strMasterKey));
			
			if ($intSlaveID){
				//Update
				$arrQuery = array(
						'name' 				=> $arrValues['name'],
						'domain' 			=> $arrValues['domain'],
						'uniqueid'			=> $arrValues['uniqueid'],
						'db_type'		 	=> $arrValues['db_type'],
						'db_host'			=> $crypt->encrypt($arrValues['db_host']),
						'db_user' 			=> $crypt->encrypt($arrValues['db_user']),
						'db_password'		=> $crypt->encrypt($arrValues['db_password']),
						'db_database'		=> $crypt->encrypt($arrValues['db_database']),
						'db_prefix'			=> $crypt->encrypt($arrValues['db_prefix']),
						'cookie_name'		=> $arrValues['cookie_name'],
				);
				$objQuery = $this->db->prepare("UPDATE __plugin_sso :p WHERE id=?")->set($arrQuery)->execute($intSlaveID);
				if ($objQuery){
					$id = $intSlaveID;
				} else return false;
				
			} else {
				//Insert
				$arrQuery = array(
						'name' 				=> $arrValues['name'],
						'domain' 			=> $arrValues['domain'],
						'uniqueid'			=> $arrValues['uniqueid'],
						'db_type'		 	=> $arrValues['db_type'],
						'db_host'			=> $crypt->encrypt($arrValues['db_host']),
						'db_user' 			=> $crypt->encrypt($arrValues['db_user']),
						'db_password'		=> $crypt->encrypt($arrValues['db_password']),
						'db_database'		=> $crypt->encrypt($arrValues['db_database']),
						'db_prefix'			=> $crypt->encrypt($arrValues['db_prefix']),
						'cookie_name'		=> $arrValues['cookie_name'],
				);
					
				$objQuery = $this->db->prepare("INSERT INTO __plugin_sso :p")->set($arrQuery)->execute();
				if ($objQuery){
					$id = $objQuery->insertId;
				} else return false;
			}
			
			if ($id){					
				$this->pdh->enqueue_hook('eqdkp_sso_update');
				return $id;
			}
			
			return false;
		}

		
		public function delete($intSlaveID){
			$objQuery = $this->db->prepare("DELETE FROM __plugin_sso WHERE id =?")->execute($intSlaveID);
			$this->pdh->enqueue_hook('eqdkp_sso_update');
			return true;
		}

	}//end class
}//end if
?>