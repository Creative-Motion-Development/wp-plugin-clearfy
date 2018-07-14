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
	function wbcr_clearfy_update_external_addon()
	{
		check_ajax_referer('updates');

		$plugin_slug = WCL_Plugin::app()->request->post('slug', null, true);
		$plugin_action = WCL_Plugin::app()->request->post('plugin_action', null, true);

		if( !current_user_can('activate_plugins') ) {
			wp_send_json_error(array('errorMessage' => __('You don\'t have enough capability to edit this information.', 'clearfy')), 403);
		}

		if( empty($plugin_slug) || empty($plugin_action) ) {
			wp_send_json_error(array('errorMessage' => __('Required attributes are not passed or empty.', 'clearfy')));
		}
		
		$plugin = WCL_Plugin::app()->request->post('plugin', null, true);
		if ( $plugin == 'freemius' ) {
			$result = wbcr_clearfy_process_freemius_addon();
			if( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'errorMessage' => $result->get_error_message() ) );
			} else {
				wp_send_json_success();
			}
		}

		$plugins = get_plugins();

		$addon_base_path = null;

		foreach($plugins as $base_path => $plugin) {
			if( strpos($base_path, rtrim(trim($plugin_slug))) !== false ) {
				$addon_base_path = $base_path;
			}
		}

		if( !empty($addon_base_path) ) {
			if( $plugin_action == 'activate' ) {
				$result = activate_plugin($addon_base_path);
				if( is_wp_error($result) ) {
					wp_send_json_error(array('errorMessage' => $result->get_error_message()));
				}
			} elseif( $plugin_action == 'deactivate' ) {
				deactivate_plugins($addon_base_path);
			}

			wp_send_json_success();
		}

		wp_send_json_error(array('errorMessage' => __('An unknown error occurred during the activation of the component.', 'clearfy')));
	}

	add_action('wp_ajax_wbcr-clearfy-update-external-addon', 'wbcr_clearfy_update_external_addon');

	/**
	 * This action allows you to process Ajax requests to activate the internal components of Clearfy
	 */
	function wbcr_clearfy_activate_preload_addon()
	{
		$component_name = WCL_Plugin::app()->request->post('component_name', null, true);
		$component_action = WCL_Plugin::app()->request->post('component_action', null, true);

		check_ajax_referer('update_component_' . $component_name);

		if( !WCL_Plugin::app()->currentUserCan() ) {
			wp_send_json_error(array('errorMessage' => __('You don\'t have enough capability to edit this information.', 'clearfy')), 403);
		}

		if( empty($component_name) || empty($component_action) ) {
			wp_send_json_error(array('errorMessage' => __('Required attributes are not passed or empty.', 'clearfy')));
		}

		if( $component_action == 'activate' ) {
			if( WCL_Plugin::app()->activateComponent($component_name) ) {
				wp_send_json_success();
			}
		} else if( $component_action == 'deactivate' ) {
			if( WCL_Plugin::app()->deactivateComponent($component_name) ) {
				wp_send_json_success();
			}
		}

		wp_send_json_error(array('errorMessage' => sprintf(__('An unknown error occurred during the %s of the component.', 'clearfy'), $component_action)));
	}

	add_action('wp_ajax_wbcr-clearfy-activate-preload-addon', 'wbcr_clearfy_activate_preload_addon');
	
	/**
	 * This action allows you to process Ajax requests to activate the freemius components of Clearfy
	 */
	function wbcr_clearfy_process_freemius_addon() {
		$plugin_slug = WCL_Plugin::app()->request->post('slug', null, true);
		$action = WCL_Plugin::app()->request->post('plugin_action', null, true);
		$licensing = WCL_Licensing::instance();
		$result = false;
		
		if( $action == 'install' ) {
			$result = $licensing->installAddon( $plugin_slug );
		}
		if( $action == 'delete' ) {
			$result = $licensing->deleteAddon( $plugin_slug );
		}
		if( $action == 'deactivate' ) {
			$result = $licensing->deactivateAddon( $plugin_slug );
		}
		if( $action == 'activate' ) {
			$result = $licensing->activateAddon( $plugin_slug );
		}
		return $result;
	}

