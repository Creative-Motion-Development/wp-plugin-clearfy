<?php
	
	/**
	 * Класс для работы с системой лицензирования
	 * @author Webcraftic <jokerov@gmail.com>
	 * @copyright (c) 2018 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_Licensing {
		
		private $plugin_id = 2245;
		
		private $plugin_public_key = 'pk_a269e86ca40026b56ab3bfec16502';
		
		private $plugin_slug = 'jwp-test';
		
		private static $_instance;
		
		private $_storage = array();
		
		private $_user_api;
		
		private $_site_api;
		
		static function instance() {
            if ( ! isset( self::$_instance ) ) {
                self::$_instance = new WCL_Licensing;
            }

            return self::$_instance;
        }
        
        private function __construct() {
			$this->include_files();
			$this->_storage = new WCL_Licensing_Storage;
		}
		
		private function include_files() {
			require_once( WCL_PLUGIN_DIR . '/includes/freemius/class.storage.php' );
			
			require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-entity.php' );
			require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-scope-entity.php' );
			require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-user.php' );
			require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-site.php' );
			require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-plugin-license.php' );
			
			
			require_once( WCL_PLUGIN_DIR . '/includes/freemius/sdk/FreemiusWordPress.php' );
		}
		
		public function getStorage() {
			return $this->_storage;
		}
		
		public function getSiteApi() {
			if ( isset( $this->_site_api ) ) {
				return $this->_site_api;
			}
			$site = $this->_storage->get( 'site' );
			$this->_site_api = new WCL_FreemiusWPApi(
				'install',  // scope
				$site->id, // element_id
				$site->public_key, //public key
				$site->secret_key,
				false
			);
			return $this->_site_api;
		}
		
		public function getUserApi() {
			if ( isset( $this->_user_api ) ) {
				return $this->_user_api;
			}
			$user = $this->_storage->get( 'user' );
			$this->_user_api = new WCL_FreemiusWPApi(
				'user',  // scope
				$user->id, // element_id
				$user->public_key, //public key
				$user->secret_key,
				false
			);
			return $this->_user_api;
		}
		
		public function uninstall() {
			$site = $this->_storage->get( 'site' );
			$current_license = $this->_storage->get( 'license' );
			$api_install = $this->getSiteApi();
			$api_user = $this->getUserApi();
			
			
			$responce = $api_install->Api(
				'/licenses/' . $current_license->id . '.json?license_key=' . $current_license->secret_key, 
				'DELETE'
			);
			
			$responce = $api_user->Api(
				'/plugins/' . $this->plugin_id . '/installs.json?ids=' . $site->id, 
				'DELETE'
			);
			$this->_storage->delete( 'site' );
			$this->_storage->delete( 'license' );
			$this->_storage->save();
			return new WP_Error( 'alert-success', 'Лицензия деактивирована.' );
		}
		
		public function sync() {
			$site = $this->_storage->get( 'site' );
			$current_license = $this->_storage->get( 'license' );
			$api_install = $this->getSiteApi();
			$api_user = $this->getUserApi();
			
			$license = $api_install->Api(
				'/licenses/' . $current_license->id . '.json?license_key=' . $current_license->secret_key, 
				'GET'
			);
			$install = $api_user->Api(
				'/plugins/' . $this->plugin_id . '/installs.json?ids=' . $site->id, 
				'GET'
			);
			if ( $install->installs[0]->license_id !== $current_license->id ) {
				$this->uninstall();
				return new WP_Error( 'alert-success', 'Лицензия обновлена.' );
			}

			$current_license->sync( $license );
			$this->_storage->set( 'license', $current_license );
			$this->_storage->save();
			
			return new WP_Error( 'alert-success', 'Лицензия обновлена.' );
		}
		
		public function activate( $license_key ) {
			$site = $this->_storage->get( 'site' );
			$current_license = $this->_storage->get( 'license' );
			if ( isset( $current_license->id ) ) {
				if ( $current_license->secret_key == $license_key ) {
					$this->sync();
					return new WP_Error( 'alert-success', 'Лицензия обновлена.' );
				}
				$this->uninstall();
			}
			$url = 'https://wp.freemius.com/action/service/user/install/';
			$unique_id = md5( get_site_url() . SECURE_AUTH_KEY );
			$request_body = array(
				'plugin_slug'       => $this->plugin_slug,
				'plugin_id'         => $this->plugin_id,
				'plugin_public_key' => $this->plugin_public_key,
				'plugin_version'    => '0.3',
				'is_active'         => true,
				'is_premium'        => true,
				'format'            => 'json',
				'is_disconnected'   => false,
				'license_key'       => $license_key,
				'site_url'          => get_home_url(), //site_uid
				'site_uid'          => $unique_id,
				'language'          => get_bloginfo( 'language' ),
                'charset'           => get_bloginfo( 'charset' ),
				'platform_version'  => get_bloginfo( 'version' ),
                'sdk_version'       => '2.1.1',
                'programming_language_version' => phpversion(),
			);
			$responce = wp_remote_post( $url, array(
				'body'    => $request_body,
				'timeout' => 7,
			) );
			if ( is_wp_error( $responce ) ) {
				return new WP_Error( 'alert-danger', $responce->error );
			}
			if ( isset( $responce['response']['code'] ) and $responce['response']['code'] == 403 ) {
				return new WP_Error( 'alert-danger', 'http error' );
			}

			//$responce = array();
			//$responce['body'] = '{"user_id":"632840","user_secret_key":"sk_cgxA*c0])LM:6}2PLw0&u^E$%gEUf","user_public_key":"pk_0f09d464d7bcf64e40c8f994b0e60","is_marketing_allowed":true,"install_id":"1721729","install_secret_key":"sk_>)w.a_O<-8yl!.mtq6#qEQG1Mu+cR","install_public_key":"pk_dc6be6e82c4f68aa0e51badae7168"}';
			//$responce['body'] = '{"error":"Invalid license key."}';
			$responce_data = json_decode( $responce['body'] );
			if ( isset( $responce_data->error ) ) {
				return new WP_Error( 'alert-danger', $responce_data->error );
			}
			$user = new WCL_FS_User( $responce_data );
			$site = new WCL_FS_Site( $responce_data );
			$this->_storage->set( 'user', $user );
			$this->_storage->set( 'site', $site );
			$api_user = $this->getUserApi();
			$api_install = $this->getSiteApi();
			$user_licensies = $api_user->Api(
				'/plugins/' . $this->plugin_id . '/licenses.json', 
				'GET'
			);
			foreach ( $user_licensies->licenses as $user_license ) {
				if ( $user_license->secret_key == $license_key ) {
					$current_license = new WCL_FS_Plugin_License( $user_license );
				}
			}
			
			$plan = $api_user->Api(
				'/plugins/' . $this->plugin_id . '/plans/' . $current_license->plan_id . '.json', 
				'GET'
			);
			
			$subscriptions = $api_install->Api(
				'/licenses/' . $current_license->id . '/subscriptions.json', 
				'GET'
			);
			$current_license->plan_title = $plan->title;
			$current_license->billing_cycle = $subscriptions->subscriptions[0]->billing_cycle;
			
			$this->_storage->set( 'license', $current_license );
			$this->_storage->save();

			return new WP_Error( 'alert-success', 'Ваша лицензия успешно активирована.' );
		}
		
	}

	
