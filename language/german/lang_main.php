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

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'eqdkp_sso'			=> 'EQdkp Plus SSO',

	// Description
	'eqdkp_sso_short_desc'	=> 'EQdkp Plus SSO',
	'eqdkp_sso_long_desc'	=> 'Melde dich an mehreren EQdkp Plus Systemen gleichzeitig an',

	'es_plugin_not_installed'=> 'Das EQdkp Plus SSO-Plugin ist nicht installiert.',
	'es_fs_general' => 'Allgemein',
	'es_fs_master'	=> 'Master-Einstellungen',
	'es_fs_slave'	=> 'Slave-Einstellungen',
	'es_f_own_sso_type' => 'System-Typ',
	'es_f_help_own_sso_type' => 'In einem EQdkp-Verbund darf es nur einen Master geben, alle anderen Systeme müssen als Slaves konfiguriert sein.',
	'es_f_own_master_key' => 'Eigener Master-Key',
	'es_f_own_uniqueid'	=> 'Eigene Installations-ID',
	'es_f_master_key'	=> 'Master-Key',
	'es_f_help_master_key'	=> 'Trage hier den Master-Key des Masters ein.',
	'es_f_db_type'		=> 'Datenbankverbindung',
	'es_f_help_db_type'		=> 'Wähle aus, wie der Master zu erreichen ist.',
	'es_db_types'	=> array(
		'Selbe Datenbank', 'Andere Datenbank', 'Bridge-Verbindung verwenden'
	),
	'es_f_db_host'	=> 'Datenbank-Host',
	'es_f_db_user'	=> 'Datenbank-Benutzer',
	'es_f_db_password' => 'Datenbank-Passwort',
	'es_f_db_database' => 'Datenbank-Name',
	'es_f_db_prefix'	=> 'Installations-Prefix',
	'es_f_help_db_prefix'	=> 'Das Prefix der Master EQdkp Plus Installation, z.B. "eqdkp20_"',
	'es_slaves'	=> 'Slaves',
	'es_add_slave' => 'Slave hinzufügen',
	'es_edit_slave' => 'Slave bearbeiten',
	'es_fs_db_infos' => 'Verbindungsinformationen',
	'es_f_name'	=> 'Name des Slaves',
	'es_f_uniqueid' => 'Eindeutige ID des Slaves',
	'es_f_cookie_name' => 'Cookiename des Slaves',
	'es_f_domain' => 'Domain des Slaves',
	'es_f_help_domain' => 'Trage hier die Domain (inkl. Subdomain) ein, z.B. mydomain.com, oder eqdkp.yourdomain.eu',
	'es_master_conn_true' => 'Die Verbindung mit dem Master war erfolgreich. Du kannst deinen Slave nun beim Master eintragen lassen, in dem du die Schaltfläche dafür verwendest.',
	'es_sendto_master' => 'Diesen Slave an den Master senden',
	'es_sendto_master_error' => 'Der Slave konnte nicht im Master eingetragen werden. Bitte überprüfe die Verbindung zum Master.',
	'es_sendto_master_success' => 'Dieser Slave wurde erfolgreich im Master eingetragen.',
	'es_domain' => 'Domain',
	'es_uniqueid' => 'Eindeutige ID',
	'es_cookie_name' => 'Cookie-Name',
);

?>