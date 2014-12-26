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
				
if ( !class_exists( "pdh_r_eqdkp_sso" ) ) {
	class pdh_r_eqdkp_sso extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}				
	
	public $default_lang = 'english';
	public $eqdkp_sso = null;

	public $hooks = array(
		'eqdkp_sso_update',
	);		
			
	public $presets = array(
	);
				
	public function reset(){
			$this->pdc->del('pdh_eqdkp_sso_table');
			
			$this->eqdkp_sso = NULL;
	}
					
	public function init(){
			$this->eqdkp_sso	= $this->pdc->get('pdh_eqdkp_sso_table');				
					
			if($this->eqdkp_sso !== NULL){
				return true;
			}		

			$objQuery = $this->db->query('SELECT * FROM __plugin_sso');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){

					$this->eqdkp_sso[(int)$drow['id']] = array(
						'id'				=> (int)$drow['id'],
						'name'				=> $drow['name'],
						'domain'			=> $drow['domain'],
						'uniqueid'			=> $drow['uniqueid'],
						'db_type'			=> (int)$drow['db_type'],
						'db_host'			=> $drow['db_host'],
						'db_user'			=> $drow['db_user'],
						'db_password'		=> $drow['db_password'],
						'db_database'		=> $drow['db_database'],
						'db_prefix'			=> $drow['db_prefix'],
						'cookie_name'		=> $drow['cookie_name'],
					);
				}
				
				$this->pdc->put('pdh_eqdkp_sso_table', $this->eqdkp_sso, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */				
		public function get_id_list(){
			if ($this->eqdkp_sso === null) return array();
			return array_keys($this->eqdkp_sso);
		}
		
		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */				
		public function get_data($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID];
			}
			return false;
		}
				
		/**
		 * Returns id for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype id
		 */
		 public function get_id($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['id'];
			}
			return false;
		}

		/**
		 * Returns name for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype name
		 */
		 public function get_name($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['name'];
			}
			return false;
		}

		/**
		 * Returns domain for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype domain
		 */
		 public function get_domain($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['domain'];
			}
			return false;
		}

		/**
		 * Returns uniqueid for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype uniqueid
		 */
		 public function get_uniqueid($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['uniqueid'];
			}
			return false;
		}

		/**
		 * Returns db_type for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype db_type
		 */
		 public function get_db_type($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['db_type'];
			}
			return false;
		}

		/**
		 * Returns db_host for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype db_host
		 */
		 public function get_db_host($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['db_host'];
			}
			return false;
		}

		/**
		 * Returns db_user for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype db_user
		 */
		 public function get_db_user($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['db_user'];
			}
			return false;
		}

		/**
		 * Returns db_password for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype db_password
		 */
		 public function get_db_password($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['db_password'];
			}
			return false;
		}

		/**
		 * Returns db_database for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype db_database
		 */
		 public function get_db_database($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['db_database'];
			}
			return false;
		}

		/**
		 * Returns db_prefix for $intEQdkpID				
		 * @param integer $intEQdkpID
		 * @return multitype db_prefix
		 */
		 public function get_db_prefix($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['db_prefix'];
			}
			return false;
		}
		
		/**
		 * Returns cookie_name for $intEQdkpID
		 * @param integer $intEQdkpID
		 * @return multitype cookie_name
		 */
		public function get_cookie_name($intEQdkpID){
			if (isset($this->eqdkp_sso[$intEQdkpID])){
				return $this->eqdkp_sso[$intEQdkpID]['cookie_name'];
			}
			return false;
		}

	}//end class
}//end if
?>