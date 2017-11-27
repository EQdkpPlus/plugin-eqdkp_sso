<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp SSO Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
	header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | sso_user_login_successful_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('sso_user_login_successful_hook')){
	class sso_user_login_successful_hook extends gen_class{


		public function user_login_successful($arrOptions){
			$user_id = $arrOptions['user_id'];
			$blnAutologin = $arrOptions['autologin'];
			
			//Include SSO Class
			include_once $this->root_path.'plugins/eqdkp_sso/includes/eqdkp_sso.class.php';
			$this->sso = register('eqdkp_sso_class');
			
			//Hole Daten aus Cache
			$arrMasterData	= $this->pdc->get('eqdkp_sso_masterdata');
				
			if($arrMasterData === NULL){
				//Hole Daten aus Master			
				$objMasterDB = $this->sso->getMasterConnection();
				if ($objMasterDB){
					$objQuery = $objMasterDB->query('SELECT * FROM __plugin_sso');
					if($objQuery){
						while($drow = $objQuery->fetchAssoc()){
					
							$arrMasterData[(int)$drow['id']] = array(
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
					}
					//Und Cache sie
					$this->pdc->put('eqdkp_sso_masterdata', $arrMasterData, 60*10);
				} else {
					//No connection to Master
					return;
				}
			}

			$strUsername = clean_username($this->pdh->get('user', 'name', array($user_id)));
			$strMyDomain = $this->env->server_name;
			$strMyCookiename = $this->config->get('cookie_name');
			$strMyUniqueID = $this->sso->get_uniqueid();

			$crypt = register('encrypt', array($this->sso->get_master_key()));

			foreach($arrMasterData as $arrValue){
				//UniqueID checken
				if($arrValue['uniqueid'] != "" && $arrValue['uniqueid'] == $strMyUniqueID) continue;
				
				//Verbindung aufbauen
				$mydb = false;
				if ((int)$arrValue['db_type'] === 0){
					//Same Connection as Master
					$mydb = (isset($objMasterDB)) ? $objMasterDB : $this->sso->getMasterConnection();
				} elseif((int)$arrValue['db_type'] === 1){

					//External Connection. Decrypt the data
					$arrValue['db_host']		= $crypt->decrypt($arrValue['db_host']);
					$arrValue['db_user']		= $crypt->decrypt($arrValue['db_user']);
					$arrValue['db_password']	= $crypt->decrypt($arrValue['db_password']);
					$arrValue['db_database']	= $crypt->decrypt($arrValue['db_database']);
					$arrValue['db_prefix']		= $crypt->decrypt($arrValue['db_prefix']);
					
					//Check if it's the same connection as ours
					if ($arrValue['db_user'] === registry::get_const('dbuser') && $arrValue['db_database'] === registry::get_const('dbname') && $arrValue['db_password'] === registry::get_const('dbpass') ){
						$mydb = $this->sso->createConnection(0, $arrValue['db_host'], $arrValue['db_user'], $arrValue['db_password'], $arrValue['db_database'], $arrValue['db_prefix']);
					} else {
						$mydb = $this->sso->createConnection(1, $arrValue['db_host'], $arrValue['db_user'], $arrValue['db_password'], $arrValue['db_database'], $arrValue['db_prefix']);
					}
				}
				
				if ($mydb){
				
					//UserID suchen
					$objUserQuery = $mydb->prepare("SELECT * FROM __users WHERE LOWER(username)=?")->execute($strUsername);
					if($objUserQuery){
						$arrUserdata = $objUserQuery->fetchAssoc();
						$intUserID = $arrUserdata['user_id'];
						
						if($intUserID){
							//Session anlegen
							$sid = substr(md5(generateRandomBytes(55)).md5(generateRandomBytes()), 0, 40);
							$strSessionKey = $this->user->generate_session_key();
							$arrData = array(
									'session_id'			=> $sid,
									'session_user_id'		=> $intUserID,
									'session_last_visit'	=> $this->time->time,
									'session_start'			=> $this->time->time,
									'session_current'		=> $this->time->time,
									'session_ip'			=> $this->env->ip,
									'session_browser'		=> $this->env->useragent,
									'session_page'			=> ($this->env->current_page) ? utf8_strtolower($this->env->current_page) : '',
									'session_key'			=> $strSessionKey,
									'session_type'			=> (defined('SESSION_TYPE')) ? SESSION_TYPE : '',
							);
							$mydb->prepare('INSERT INTO __sessions :p')->set($arrData)->execute();
							
							//Cookie Daten auslesen
							$objCookieQuery = $mydb->prepare("SELECT * FROM __config")->execute();
							if($objCookieQuery){
								$lookingFor = array('cookie_name', 'cookie_path', 'cookie_domain');
								while($row = $objCookieQuery->fetchAssoc()){
									if(in_array($row['config_name'], $lookingFor)){
										$arrCookieConf[$row['config_name']] = $row['config_value'];
									}
								}
							}

							//Cookie Domain
							if (!isset($arrCookieConf['cookie_domain'])){
								$strDomain = $arrValue['domain'];
								if (!strpos($strDomain, '://')){
									$strDomain = 'http://'.$strDomain;
								}
								
								$parsedURL = parse_url($strDomain);
								$arrCookieConf['cookie_domain'] = $parsedURL['host'];
							}
							
							//Autologin
							$arrCookieData['user_id'] = $intUserID;
							if($blnAutologin && $arrUserdata['user_login_key'] != ""){
								$arrCookieData['auto_login_id'] = $arrUserdata['user_login_key'];
							}
							
							//Set Cookies
							setcookie( $arrCookieConf['cookie_name'].'_sid', $sid, 0, $arrCookieConf['cookie_path'], $arrCookieConf['cookie_domain']);
							setcookie( $arrCookieConf['cookie_name'].'_data', base64_encode(serialize($arrCookieData)), $this->time->time + 2592000, $arrCookieConf['cookie_path'], $arrCookieConf['cookie_domain']);
						}

					}
				
					
					//Verbindung beenden
					unset($mydb);
				}
			}

		}
	}
}
?>