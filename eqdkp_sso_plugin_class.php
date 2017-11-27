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

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found'); exit;
}


/*+----------------------------------------------------------------------------
  | siggenerator
  +--------------------------------------------------------------------------*/
class eqdkp_sso extends plugin_generic{

	public $version		= '0.1.4';
	public $build		= '1';
	public $copyright	= 'GodMod';
	public $vstatus		= 'Beta';

	protected static $apiLevel = 23;

	/**
	* Constructor
	* Initialize all informations for installing/uninstalling plugin
	*/
	public function __construct(){
		parent::__construct();

		$this->add_data(array (
			'name'				=> 'EQdkp SSO',
			'code'				=> 'eqdkp_sso',
			'path'				=> 'eqdkp_sso',
			'template_path'		=> 'plugins/eqdkp_sso/templates/',
			'icon'				=> 'fa-chain',
			'version'			=> $this->version,
			'author'			=> $this->copyright,
			'description'		=> $this->user->lang('eqdkp_sso_short_desc'),
			'long_description'	=> $this->user->lang('eqdkp_sso_long_desc'),
			'homepage'			=> EQDKP_PROJECT_URL,
			'manuallink'		=> false,
			'plus_version'		=> '2.0',
			'build'				=> $this->build,
		));

		$this->add_dependency(array(
			'plus_version'		=> '2.0'
		));

		// -- Register our permissions ------------------------
		// permissions: 'a'=admins, 'u'=user
		// ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
		// Groups: 2 = Super-Admin, 3 = Admin, 4 = Member
		$this->add_permission('a', 'manage',	'N', $this->user->lang('manage'),	array(2));

		// -- Menu --------------------------------------------
		$this->add_menu('admin', $this->gen_admin_menu());

		// -- PDH Modules -------------------------------------
		$this->add_pdh_read_module('eqdkp_sso');
		$this->add_pdh_write_module('eqdkp_sso');
		
		$this->add_hook('user_login_successful', 'sso_user_login_successful_hook', 'user_login_successful');
	}

	/**
	* pre_install
	* Define Installation
	*/
	public function pre_install(){
		// include SQL and default configuration data for installation
		include($this->root_path.'plugins/eqdkp_sso/includes/sql.php');

		// define installation
		for ($i = 1; $i <= count($eqdkpSSOSQL['install']); $i++)
			$this->db->query($eqdkpSSOSQL['install'][$i]);
		
		
		//Create uniqueID and masterKey
		$masterKey = sha1(generateRandomBytes(48));
		$uniqueID = sha1(generateRandomBytes(48));
		$data = "<?php 
if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

\$eqdkp_sso_uniqueID = '".$uniqueID."';
\$eqdkp_sso_masterKey = '".$masterKey."';

?>";
		$this->pfh->secure_folder('config', 'eqdkp_sso');
		$this->pfh->putContent($this->pfh->FolderPath('config', 'eqdkp_sso').'config.php', $data);
		
		//Insert this master as slave;
		$crypt = register('encrypt', array($masterKey));
		$arrQuery = array(
				'name' 				=> ($this->config->get('main_title') != "") ? $this->config->get('main_title') : "This Master",
				'domain' 			=> $this->env->server_name,
				'uniqueid'			=> $uniqueID,
				'db_type'		 	=> 0,
				'db_host'			=> '',
				'db_user' 			=> '',
				'db_password'		=> '',
				'db_database'		=> '',
				'db_prefix'			=> $crypt->encrypt(registry::get_const("table_prefix")),
				'cookie_name'		=> $this->config->get('cookie_name'),
		);
		$this->db->prepare("INSERT INTO __plugin_sso :p")->set($arrQuery)->execute();
	}

	/**
	* pre_uninstall
	* Define uninstallation
	*/
	public function pre_uninstall(){
		// include SQL data for uninstallation
		include($this->root_path.'plugins/eqdkp_sso/includes/sql.php');

		for ($i = 1; $i <= count($eqdkpSSOSQL['uninstall']); $i++)
			$this->add_sql(SQL_UNINSTALL, $eqdkpSSOSQL['uninstall'][$i]);
	}

	/**
	* post_uninstall
	* Define Post Uninstall
	*/
	public function post_uninstall(){
		$arrConf = $this->config->get_config('eqdkp_sso');
		$this->config->del(array_keys($arrConf), 'eqdkp_sso');
	}

	/**
	* gen_admin_menu
	* Generate the Admin Menu
	*/
	private function gen_admin_menu(){
		$admin_menu = array (array(
			'text' => $this->user->lang('eqdkp_sso'),
			'icon' => 'fa-chain',
			'link'	=> 'plugins/eqdkp_sso/admin/settings.php'.$this->SID,
			'check'	=> 'a_eqdkp_sso_manage',
		));

		return $admin_menu;
	}
}
?>
