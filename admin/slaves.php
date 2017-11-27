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

class EQdkpSSOAdminSlaves extends page_generic {

	private $sso;
	
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('eqdkp_sso', PLUGIN_INSTALLED))
			message_die($this->user->lang('es_plugin_not_installed'));
		
		$this->user->check_auth('a_eqdkp_sso_manage');
		
		$handler = array(
			'save' => array('process' => 'save', 'csrf' => true),
				
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
		$form->lang_prefix = 'es_sl_';
		$form->add_fieldsets($arrFields);
		
		$arrValues = $form->return_values();
		$form->reset_fields();
		//Check Connection
		$blnResult = $this->sso->check_connection($arrValues['db_type'], $arrValues['db_host'], $arrValues['db_user'], $arrValues['db_password'], $arrValues['db_database'], $arrValues['db_prefix']);
		if ($blnResult === true){
			//Save
			$blnResult = $this->pdh->put('eqdkp_sso', 'save_slave', array($this->in->get('slaveid', 0), $this->sso->get_own_master_key(), $arrValues));
			$this->pdh->process_hook_queue();
			if ($blnResult){
				$this->tpl->add_js('$.FrameDialog.closeDialog();', 'docready');
			}
		} else {
			$this->core->message($blnResult, $this->user->lang('error'), 'red');
		}
		
		$this->display($arrValues);
	}
	
	private function fields(){
		$arrFields = array(
			'general' => array(
				'name' => array(
					'type' => 'text',
					'size' => 40,
					'required' => true,
				),
				'uniqueid' => array(
					'type' => 'text',
					'size' => 40,
				),
				'domain' => array(
							'type' => 'text',
							'size' => 40,
							'required' => true,
				),
				'cookie_name' => array(
					'type' => 'text',
					'size' => 40,
				),
			),
			'db_infos' => array(
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
						'required' => true,
				),
			),
		);
		
		unset($arrFields['db_infos']['db_type']['options'][2]);
		
		return $arrFields;
	}

	
	public function display($arrValues = false){
		registry::load("form");
		
		$arrFields = $this->fields();
		$form = register('form', array('sso_settings'));
		$form->use_fieldsets = true;
		$form->use_dependency = true;
		$form->lang_prefix = 'es_sl_';
		$form->add_fieldsets($arrFields);
		
		if(!$arrValues && $this->in->get('slaveid', 0)){
			$arrValues = $this->pdh->get('eqdkp_sso', 'data', array($this->in->get('slaveid', 0)));
			//Encrypt some Values
			$crypt = register('encrypt', array($this->sso->get_own_master_key()));
			
			$arrValues['db_host'] 		= $crypt->decrypt($arrValues['db_host']);
			$arrValues['db_user'] 		= $crypt->decrypt($arrValues['db_user']);
			$arrValues['db_password']	= $crypt->decrypt($arrValues['db_password']);
			$arrValues['db_database']	= $crypt->decrypt($arrValues['db_database']);
			$arrValues['db_prefix']		= $crypt->decrypt($arrValues['db_prefix']);
		}

		$form->output($arrValues);
		
		$this->tpl->assign_var('SLAVEID', $this->in->get('slaveid', 0));
				
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('settings'),
				'template_path'		=> $this->pm->get_data('eqdkp_sso', 'template_path'),
				'template_file'		=> 'admin/slaves.html',
				'header_format'		=> 'simple',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('eqdkp_sso').': '.$this->user->lang('settings'), 'url'=>' '],
				],
				'display'			=> true)
		);
	}
	
}
registry::register('EQdkpSSOAdminSlaves');
?>