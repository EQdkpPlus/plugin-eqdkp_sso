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

 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English	
//Created by EQdkp Plus Translation Tool on  2014-12-17 23:17
//File: plugins/siggenerator/language/english/lang_main.php
//Source-Language: german

$lang = array( 
		'eqdkp_sso'			=> 'EQdkp Plus SSO',

	// Description
	'eqdkp_sso_short_desc'	=> 'EQdkp Plus SSO',
	'eqdkp_sso_long_desc'	=> 'Single Sign On for multiple EQdkp Plus Installations',

	'es_plugin_not_installed'=> 'The EQdkp Plus SSO-Plugin is not installed.',
	'es_fs_general' => 'General',
	'es_fs_master'	=> 'Master-Settings',
	'es_fs_slave'	=> 'Slave-Settings',
	'es_f_own_sso_type' => 'System-Type',
	'es_f_help_own_sso_type' => 'In a EQdkp Plus SSO Cluster, there must be only one Master. All others have to be slaves.',
	'es_f_own_master_key' => 'Own Masterkey',
	'es_f_own_uniqueid'	=> 'Own unique Installation-ID',
	'es_f_master_key'	=> 'Masterkey',
	'es_f_help_master_key'	=> 'Insert here the Masterkey of the Master EQdkp Plus.',
	'es_f_db_type'		=> 'Database Connection',
	'es_f_help_db_type'		=> 'Select how to connect to the Master.',
	'es_db_types'	=> array(
		'Same database', 'Other database', 'Use bridge connection'
	),
	'es_f_db_host'	=> 'Database host',
	'es_f_db_user'	=> 'Database user',
	'es_f_db_password' => 'Database password',
	'es_f_db_database' => 'Database name',
	'es_f_db_prefix'	=> 'Installation prefix',
	'es_f_help_db_prefix'	=> 'The prefix of the Master EQdkp Plus Installation, e.g. "eqdkp20_"',
	'es_slaves'	=> 'Slaves',
	'es_add_slave' => 'Add Slave',
	'es_edit_slave' => 'Edit Slave',
	'es_fs_db_infos' => 'Connection information',
	'es_f_name'	=> 'Name of Slave',
	'es_f_uniqueid' => 'Unique Installation-ID of Slave',
	'es_f_cookie_name' => 'Cookiename of Slave',
	'es_f_domain' => 'Domain of Slave',
	'es_f_help_domain' => 'Insert the Domain (incl. subdomains if used) of the slave, e.g. mydomain.com, or eqdkp.yourdomain.eu',
	'es_master_conn_true' => 'The connection with the master was successful. You can now add this slave to the Master by clicking on the button at the bottom.',
	'es_sendto_master' => 'Send slave to master',
	'es_sendto_master_error' => 'Slave could not be sent to master. Please check the master connection.',
	'es_sendto_master_success' => 'Slave was sent successfully to Master.',
	'es_domain' => 'Domain',
	'es_uniqueid' => 'Unique ID',
	'es_cookie_name' => 'Cookie-Name',	
);

?>