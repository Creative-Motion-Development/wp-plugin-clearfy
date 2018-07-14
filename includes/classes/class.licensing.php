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

		// плагин для отладки
		//private $plugin_id = 2245;
		//private $plugin_id = 1323;
		private $plugin_id = 2315; // prod

		// ключ для отладки
		//private $plugin_public_key = 'pk_a269e86ca40026b56ab3bfec16502';

		//private $plugin_public_key = 'pk_ad48458b9f12efca6be7818ead5d2';
		private $plugin_public_key = 'pk_70e226af07d37d2b9a69720e0952c'; // prod

		// слаг для отладки
		//private $plugin_slug = 'jwp-test';

		private $plugin_slug = 'clearfy'; // prod
		
		private static $_instance;
		
		private $_storage = array();
		
		private $_user_api;
		
		private $_site_api;


		static function instance()
		{
			if( !isset(self::$_instance) ) {
				self::$_instance = new WCL_Licensing;
			}

			return self::$_instance;
		}

		private function __construct()
		{
			$this->include_files();
			$this->_storage = new WCL_Licensing_Storage;
		}
		
		private function include_files()
		{
			require_once(WCL_PLUGIN_DIR . '/includes/freemius/class.storage.php');
			
			require_once(WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-entity.php');
			require_once(WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-scope-entity.php');
			require_once(WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-user.php');
			require_once(WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-site.php');
			require_once(WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-plugin-license.php');
			
			require_once(WCL_PLUGIN_DIR . '/includes/freemius/sdk/FreemiusWordPress.php');
		}
		
		public function getStorage()
		{
			return $this->_storage;
		}
		
		public function getPluginApi() {
			return new WCL_FreemiusWPApi(
				'plugin',  // scope
				$this->plugin_id, // element_id
				$this->plugin_public_key, //public key
				$this->plugin_public_key,
				false
			);
		}
		
		public function getAddonApi( $addon ) {
			return new WCL_FreemiusWPApi(
				'plugin',  // scope
				$addon->id, // element_id
				$addon->public_key, //public key
				false,
				false
			);
		}
		
		public function getSiteApi()
		{
			if( isset($this->_site_api) ) {
				return $this->_site_api;
			}
			$site = $this->_storage->get('site');
			$this->_site_api = new WCL_FreemiusWPApi('install',  // scope
				$site->id, // element_id
				$site->public_key, //public key
				$site->secret_key, false);

			return $this->_site_api;
		}
		
		public function getUserApi()
		{
			if( isset($this->_user_api) ) {
				return $this->_user_api;
			}
			$user = $this->_storage->get('user');
			$this->_user_api = new WCL_FreemiusWPApi('user',  // scope
				$user->id, // element_id
				$user->public_key, //public key
				$user->secret_key, false);

			return $this->_user_api;
		}

		public function deactivate() {
			$site = $this->_storage->get( 'site' );
			$current_license = $this->_storage->get( 'license' );

			$api_install = $this->getSiteApi();
			$api_user = $this->getUserApi();
			
			$responce = $api_install->Api('/licenses/' . $current_license->id . '.json?license_key=' . $current_license->secret_key, 'DELETE');
			
			$responce = $api_user->Api('/plugins/' . $this->plugin_id . '/installs.json?ids=' . $site->id, 'DELETE');
			$this->_storage->delete('site');
			$this->_storage->delete('license');
			$this->_storage->save();

			$this->_user_api = null;
			$this->_site_api = null;
		}
		
		public function uninstall() {
			$this->deactivate();
			return new WP_Error( 'alert-success', 'Лицензия деактивирована.' );
		}
		
		public function sync()
		{
			$site = $this->_storage->get('site');
			$current_license = $this->_storage->get('license');
			$api_install = $this->getSiteApi();
			$api_user = $this->getUserApi();
			
			$license = $api_install->Api('/licenses/' . $current_license->id . '.json?license_key=' . urlencode($current_license->secret_key), 'GET');
			$install = $api_user->Api('/plugins/' . $this->plugin_id . '/installs.json?ids=' . $site->id, 'GET');
			if( $install->installs[0]->license_id !== $current_license->id ) {
				$this->uninstall();

				return new WP_Error('alert-success', 'Лицензия обновлена.');
			}
			
			$subscriptions = $api_install->Api(
				'/licenses/' . $current_license->id . '/subscriptions.json',
				'GET'
			);
			$plan = $api_user->Api(
				'/plugins/' . $this->plugin_id . '/plans/' . $current_license->plan_id . '.json',
				'GET'
			);
			$current_license->plan_title = $plan->title;

			if ( isset( $subscriptions->subscriptions ) and isset( $subscriptions->subscriptions[0] ) ) {
				if ( ! is_null( $subscriptions->subscriptions[0]->next_payment ) ) {
					$current_license->billing_cycle = $subscriptions->subscriptions[0]->billing_cycle;
				}
			}

			$current_license->sync($license);
			$this->_storage->set('license', $current_license);
			$this->_storage->save();
			
			return new WP_Error('alert-success', 'Лицензия обновлена.');
		}


		public function unsubscribe() {
			$site = $this->_storage->get( 'site' );
			$current_license = $this->_storage->get( 'license' );
			$api_install = $this->getSiteApi();
			$api_user = $this->getUserApi();
			$subscriptions = $api_install->Api(
				'/licenses/' . $current_license->id . '/subscriptions.json',
				'GET'
			);
			if ( isset( $subscriptions->subscriptions ) and isset( $subscriptions->subscriptions[0] ) ) {
				$subscriptions = $api_install->Api(
					'downgrade.json',
					'PUT'
				);
				$current_license->billing_cycle = null;
				$this->_storage->set( 'license', $current_license );
				$this->_storage->save();
			}
			return new WP_Error( 'alert-success', 'Подписка удалена' );
		}
		
		public function activate( $license_key ) {
			$site = $this->_storage->get( 'site' );
			$current_license = $this->_storage->get( 'license' );
			if ( isset( $current_license->id ) ) {
				if ( $current_license->secret_key == $license_key ) {
					$this->sync();

					return new WP_Error('alert-success', 'Лицензия обновлена.');
				}
				$this->deactivate();
			}

			$url = 'https://wp.freemius.com/action/service/user/install/';
			$unique_id = md5(get_site_url() . SECURE_AUTH_KEY);
			$request_body = array(
				'plugin_slug' => $this->plugin_slug,
				'plugin_id' => $this->plugin_id,
				'plugin_public_key' => $this->plugin_public_key,
				'plugin_version' => '0.3',
				'is_active' => true,
				'is_premium' => true,
				'format' => 'json',
				'is_disconnected' => false,
				'license_key' => $license_key,
				'site_url' => get_home_url(), //site_uid
				'site_uid' => $unique_id,
				'language' => get_bloginfo('language'),
				'charset' => get_bloginfo('charset'),
				'platform_version' => get_bloginfo('version'),
				'sdk_version' => '2.1.1',
				'programming_language_version' => phpversion(),
			);
			$responce = wp_remote_post($url, array(
				'body' => $request_body,
				'timeout' => 7,
			));
			if( is_wp_error($responce) ) {
				return new WP_Error('alert-danger', $responce->error);
			}
			if( isset($responce['response']['code']) and $responce['response']['code'] == 403 ) {
				return new WP_Error('alert-danger', 'http error');
			}

			$responce_data = json_decode($responce['body']);
			if( isset($responce_data->error) ) {
				return new WP_Error('alert-danger', $responce_data->error);
			}
			$user = new WCL_FS_User($responce_data);
			$site = new WCL_FS_Site($responce_data);
			$this->_storage->set('user', $user);
			$this->_storage->set('site', $site);
			$api_user = $this->getUserApi();
			$api_install = $this->getSiteApi();
			$user_licensies = $api_user->Api('/plugins/' . $this->plugin_id . '/licenses.json', 'GET');
			foreach($user_licensies->licenses as $user_license) {
				if( $user_license->secret_key == $license_key ) {
					$current_license = new WCL_FS_Plugin_License($user_license);
				}
			}
			
			$plan = $api_user->Api('/plugins/' . $this->plugin_id . '/plans/' . $current_license->plan_id . '.json', 'GET');
			
			$subscriptions = $api_install->Api('/licenses/' . $current_license->id . '/subscriptions.json', 'GET');
			$current_license->plan_title = $plan->title;
			if( isset($subscriptions->subscriptions) and isset($subscriptions->subscriptions[0]) ) {
				$current_license->billing_cycle = $subscriptions->subscriptions[0]->billing_cycle;
			}
			
			$this->_storage->set('license', $current_license);
			$this->_storage->save();

			return new WP_Error('alert-success', 'Ваша лицензия успешно активирована.');
		}
		
		public function isLicenseValid() {
			$current_license = $this->_storage->get('license');
			if ( ! $current_license ) return false;
			return $current_license->is_valid();
		}
		
		public function getAddons( $flush_cache = false ) {
			$api_plugin = $this->getPluginApi();

			// Debug
			//WCL_Plugin::app()->deleteOption('freemius_addons');
			//WCL_Plugin::app()->deleteOption('freemius_addons_last_update');

			$addons = WCL_Plugin::app()->getOption( 'freemius_addons', array() );
			$addons_last_update = WCL_Plugin::app()->getOption( 'freemius_addons_last_update', 0 );
			
			$next_update = $addons_last_update + DAY_IN_SECONDS;
			if ( date('U') > $next_update ) {
				$addons = $api_plugin->Api( '/addons.json?enriched=true' );
				WCL_Plugin::app()->updateOption( 'freemius_addons_last_update', date('U') );
				if ( $addons and isset( $addons->plugins ) ) {
					WCL_Plugin::app()->updateOption( 'freemius_addons', $addons );
				}
			}
			return $addons;
		}
		
		public function installAddon( $slug ) {
			$installed_addons = WCL_Plugin::app()->getOption( 'freemius_installed_addons', array() );
			if ( in_array( $slug, $installed_addons ) ) {
				return new WP_Error( 'addon_exist', 'Аддон уже установлен' );
			}
			$installed_addons[] = $slug;
			
			$components_dir = WCL_PLUGIN_DIR . '/components/';
			$tmp_file = $components_dir . date( 'U' ) . '.zip';
			
			$url = 'http://www.u16313p6h.ha002.t.justns.ru/zip/test-addon-premium.0.3.zip';
			$zip = file_get_contents( $url );
			file_put_contents( $tmp_file, $zip );
			
			global $wp_filesystem;
			if( ! $wp_filesystem ) {
				if( ! function_exists( 'WP_Filesystem' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				}
				WP_Filesystem();
			}
			unzip_file( $tmp_file, $components_dir );
			unlink($tmp_file);
			// удаляем папку libs если она есть
			$addon_dir = $components_dir . $slug . '/';
			if ( ! is_dir( $addon_dir ) ) {
				$addon_dir = $components_dir . $slug . '-premium/';
			}
			$libs_dir = $addon_dir . 'libs/';
			if ( is_dir( $addon_dir ) and is_dir( $libs_dir ) ) {
				$wp_filesystem->rmdir( $libs_dir, true );
			}
			WCL_Plugin::app()->updateOption( 'freemius_installed_addons', $installed_addons );
			return true;
		}
		
		public function deleteAddon( $slug ) {
			$installed_addons = WCL_Plugin::app()->getOption( 'freemius_installed_addons', array() );
			if ( in_array( $slug, $installed_addons ) ) {
				foreach( $installed_addons as $key => $addon ) {
					if( $slug == $addon ) {
						unset( $installed_addons[$key] );
						global $wp_filesystem;
						if( ! $wp_filesystem ) {
							if( ! function_exists( 'WP_Filesystem' ) ) {
								require_once( ABSPATH . 'wp-admin/includes/file.php' );
							}
							WP_Filesystem();
						}
						$addon_dir = WCL_PLUGIN_DIR . '/components/' . $slug . '/';
						if ( ! is_dir( $addon_dir ) ) {
							$addon_dir = WCL_PLUGIN_DIR . '/components/' . $slug . '-premium/';
						}
						if ( is_dir( $addon_dir ) ) {
							$wp_filesystem->rmdir( $addon_dir, true );
						}
					}
				}
				WCL_Plugin::app()->updateOption( 'freemius_installed_addons', $installed_addons );
			}
			return true;
		}
		
		public function activateAddon( $slug ) {
			$preinsatall_components = WCL_Plugin::app()->getOption( 'deactive_preinstall_components', array() );

			if( in_array( $slug, $preinsatall_components ) ) {
				foreach( $preinsatall_components as $key => $component ) {
					if( $component == $slug ) {
						unset( $preinsatall_components[$key] );
					}
				}
			}

			WCL_Plugin::app()->updateOption( 'deactive_preinstall_components', $preinsatall_components );
			return true;
		}
		
		public function deactivateAddon( $slug ) {
			$preinsatall_components = WCL_Plugin::app()->getOption( 'deactive_preinstall_components', array() );

			if( ! in_array( $slug, $preinsatall_components ) ) {
				$preinsatall_components[] = $slug;
			}
			WCL_Plugin::app()->updateOption( 'deactive_preinstall_components', $preinsatall_components );
			
			return true;
		}
	}

	
