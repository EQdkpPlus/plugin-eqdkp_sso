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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');

class EQdkpSSOAdminSettings extends page_generic {

	private $sso;
	private $blnMasterTest = false;
	
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('eqdkp_sso', PLUGIN_INSTALLED))
			message_die($this->user->lang('es_plugin_not_installed'));
		
		$this->user->check_auth('a_eqdkp_sso_manage');
		
		$handler = array(
			'save' => array('process' => 'save', 'csrf' => true),
			'tomaster' => array('process' => 'sendToMaster', 'csrf' => true),
		);
		parent::__construct(false, $handler);
		
		include_once $this->root_path.'plugins/eqdkp_sso/includes/eqdkp_sso.class.php';
		$this->sso = register('eqdkp_sso_class');
		
		$this->process();
	}
	
	public function save(){
		$arrFields = $this->fields();
		$form = register('form', array('sso_settings'));
		$form->use_fieldsets = true;
		$form->use_dependency = true;
		$form->lang_prefix = 'es_';
		$form->add_fieldsets($arrFields);
		$arrValues = $form->return_values();
		
		//Check Connection to Master
		$blnResult = $this->sso->check_connection($arrValues['db_type'], $arrValues['db_host'], $arrValues['db_user'], $arrValues['db_password'], $arrValues['db_database'], $arrValues['db_prefix']);
		if ($blnResult === true){
			$this->core->message($this->user->lang('es_master_conn_true'), $this->user->lang('success'), 'green');
			$this->blnMasterTest = true;
		} else {
			$this->core->message($blnResult, $this->user->lang('error'), 'red');
		}
		
		//Encrypt some values
		$arrValues['master_key']	= $this->encrypt->encrypt($arrValues['master_key']);
		$arrValues['db_host']		= $this->encrypt->encrypt($arrValues['db_host']);
		$arrValues['db_user']		= $this->encrypt->encrypt($arrValues['db_user']);
		$arrValues['db_password']	= $this->encrypt->encrypt($arrValues['db_password']);
		$arrValues['db_database']	= $this->encrypt->encrypt($arrValues['db_database']);
		$arrValues['db_prefix']		= $this->encrypt->encrypt($arrValues['db_prefix']);
		
		unset($arrValues['own_uniqueid']);
		unset($arrValues['own_master_key']);
		
		$this->config->set($arrValues, '', 'eqdkp_sso');
		$this->pdc->del('eqdkp_sso_masterdata');
	}
	
	public function sendToMaster(){
		$arrValues = $this->config->get_config('eqdkp_sso');
		
		//Decrypt some values;
		if(isset($arrValues['master_key']))  $arrValues['master_key']	= $this->encrypt->decrypt($arrValues['master_key']);
		if(isset($arrValues['db_host'])) 	 $arrValues['db_host']		= $this->encrypt->decrypt($arrValues['db_host']);
		if(isset($arrValues['db_user']))	 $arrValues['db_user']		= $this->encrypt->decrypt($arrValues['db_user']);
		if(isset($arrValues['db_password'])) $arrValues['db_password']	= $this->encrypt->decrypt($arrValues['db_password']);
		if(isset($arrValues['db_database'])) $arrValues['db_database']	= $this->encrypt->decrypt($arrValues['db_database']);
		if(isset($arrValues['db_prefix']))   $arrValues['db_prefix']		= $this->encrypt->decrypt($arrValues['db_prefix']);
		
		//And now encrypt them with MasterKey
		if (!$arrValues['master_key'] || $arrValues['master_key'] == "") return false;
		
		$dbtype = ($arrValues['db_type'] == 0) ? 0 : 1;
		
		$crypt = register('encrypt', array($arrValues['master_key']));
		$arrQuery = array(
				'name' 				=> $this->config->get('main_title'),
				'domain' 			=> $this->env->server_name,
				'uniqueid'			=> $this->sso->get_uniqueid(),
				'db_type'		 	=> $dbtype,
				'db_host'			=> $crypt->encrypt(registry::get_const("dbhost")),
				'db_user' 			=> $crypt->encrypt(registry::get_const("dbuser")),
				'db_password'		=> $crypt->encrypt(registry::get_const("dbpass")),
				'db_database'		=> $crypt->encrypt(registry::get_const("dbname")),
				'db_prefix'			=> $crypt->encrypt(registry::get_const("table_prefix")),
				'cookie_name'		=> $this->config->get('cookie_name'),
		);
		
		//Connection to Master
		$mydb = $this->sso->createConnection($arrValues['db_type'], $arrValues['db_host'], $arrValues['db_user'], $arrValues['db_password'], $arrValues['db_database'], $arrValues['db_prefix']);
		if ($mydb){
			$objQuery = $mydb->prepare("SELECT * FROM __plugin_sso WHERE uniqueid=?")->execute($this->sso->get_uniqueid());
			if($objQuery){
				if ($objQuery->numRows === 0){
					$mydb->prepare("INSERT INTO __plugin_sso :p")->set($arrQuery)->execute();
					$this->core->message($this->user->lang('es_sendto_master_success'), $this->user->lang('success'), 'green');
					$this->display();
					return;
				} else {
					$mydb->prepare("UPDATE __plugin_sso :p WHERE uniqueid=?")->set($arrQuery)->execute($this->sso->get_uniqueid());
					$this->core->message($this->user->lang('es_sendto_master_success'), $this->user->lang('success'), 'green');
					$this->display();
					return;
				}
			}
		}
		$this->core->message($this->user->lang('es_sendto_master_error'), $this->user->lang('error'), 'red');
	}
	
	public function delete(){
		$intDeleteID = $this->in->get('del', 0);
		if ($intDeleteID){
			$this->pdh->put('eqdkp_sso', 'delete', array($intDeleteID));
			$this->pdh->process_hook_queue();
		}
	}
	
	private function fields(){
		$arrFields = array(
			'general' => array(
				'own_sso_type' => array(
					'type' => 'dropdown',
					'options' => array('master' => 'Master', 'slave' => 'Slave'),
					'default' => 'slave',
				),
				'own_master_key' => array(
					'text' => $this->sso->get_own_master_key(),
				),
				'own_uniqueid' => array(
					'text' => $this->sso->get_uniqueid(),
				),
			),
			'master' => array(
				'master_key' => array(
						'type' => 'text',
						'size' => 40,
				),
				'db_type' => array(
					'type'		=> 'radio',
					'options'	=> $this->user->lang('es_db_types'),
				),
				'db_host' => array(
					'type' => 'text',
					'size' => 30,
				),
				'db_user' => array(
					'type' => 'text',
						'size' => 30,
				),
				'db_password' => array(
						'type' => 'password',
						'size' => 30,
						'set_value' => true,
				),
				'db_database' => array(
						'type' => 'text',
						'size' => 30,
				),
				'db_prefix' => array(
						'type' => 'text',
						'size' => 30,
				),
			),
		);
		
		if ((int)$this->config->get('cmsbridge_active') == 0 || $this->config->get('cmsbridge_type') != 'eqdkp'){
			unset($arrFields['master']['db_type']['options'][2]);
		}
		
		
		return $arrFields;
	}

	
	public function display(){
		registry::load("form");
		
		$arrFields = $this->fields();
		$form = register('form', array('sso_settings'));
		$form->reset_fields();
		$form->use_fieldsets = true;
		$form->use_dependency = true;
		$form->lang_prefix = 'es_';
		$form->add_fieldsets($arrFields);
		
		$arrValues = $this->config->get_config('eqdkp_sso');
		
		//Decrypt some values;
		if(isset($arrValues['master_key']))  $arrValues['master_key']	= $this->encrypt->decrypt($arrValues['master_key']);
		if(isset($arrValues['db_host'])) 	 $arrValues['db_host']		= $this->encrypt->decrypt($arrValues['db_host']);
		if(isset($arrValues['db_user']))	 $arrValues['db_user']		= $this->encrypt->decrypt($arrValues['db_user']);
		if(isset($arrValues['db_password'])) $arrValues['db_password']	= $this->encrypt->decrypt($arrValues['db_password']);
		if(isset($arrValues['db_database'])) $arrValues['db_database']	= $this->encrypt->decrypt($arrValues['db_database']);
		if(isset($arrValues['db_prefix']))   $arrValues['db_prefix']		= $this->encrypt->decrypt($arrValues['db_prefix']);

		$form->output($arrValues);
		
		
		$arrIDList = $this->pdh->get('eqdkp_sso', 'id_list');
		foreach($arrIDList as $intID){
			$data = $this->pdh->get('eqdkp_sso', 'data', array($intID));
			$this->tpl->assign_block_vars('field_row', array(
					'ID' 		=> $intID,
					'NAME'		=> $data['name'],
					'DOMAIN'	=> $data['domain'],
					'UNIQUEID'	=> $data['uniqueid'],
					'COOKIE_NAME'=> $data['cookie_name'],
					'DEL_HASH'	=> $this->CSRFGetToken('del'),
			));
		}
		
		$this->jquery->Dialog('editSlave', $this->user->lang('es_edit_slave'), array('url' => 'slaves.php'.$this->SID."&slaveid='+id+'", 'withid' => 'id', 'width' => 920, 'height' => 740, 'onclose'=> 'settings.php'.$this->SID));

		$this->tpl->assign_vars(array(
			'S_INSERT_MASTER' => $this->blnMasterTest,
		));
		
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('settings'),
				'template_path'		=> $this->pm->get_data('eqdkp_sso', 'template_path'),
				'template_file'		=> 'admin/settings.html',
				'display'			=> true)
		);
	}
	
}
registry::register('EQdkpSSOAdminSettings');
?>