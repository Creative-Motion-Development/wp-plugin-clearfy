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

		$slug = WCL_Plugin::app()->request->post('plugin', null, true);
		$action = WCL_Plugin::app()->request->post('plugin_action', null, true);
		$storage = WCL_Plugin::app()->request->post('storage', null, true);

		if( !current_user_can('activate_plugins') ) {
			wp_send_json_error(array('errorMessage' => __('You don\'t have enough capability to edit this information.', 'clearfy')), 403);
		}

		if( empty($slug) || empty($action) ) {
			wp_send_json_error(array('errorMessage' => __('Required attributes are not passed or empty.', 'clearfy')));
		}
		$success = false;
		$send_data = array();

		if( $storage == 'freemius' ) {
			$licensing = WCL_Licensing::instance();
			$result = false;

			switch( $action ) {
				case 'install':
					$result = $licensing->installAddon($slug);
					break;
				case 'delete':
					$result = $licensing->deleteAddon($slug);
					break;
				case 'deactivate':
					$result = $licensing->deactivateAddon($slug);
					break;
				case 'activate':
					$result = $licensing->activateAddon($slug);
					break;
				default:
					wp_send_json_error(array('errorMessage' => __('You are trying to perform an invalid action.', 'clearfy')));
					break;
			}

			if( is_wp_error($result) ) {
				wp_send_json_error(array('errorMessage' => $result->get_error_message()));
			} else {
				$success = true;
			}
		} else if( $storage == 'internal' ) {

			if( $action == 'activate' ) {
				if( WCL_Plugin::app()->activateComponent($slug) ) {
					$success = true;
				}
			} else if( $action == 'deactivate' ) {
				if( WCL_Plugin::app()->deactivateComponent($slug) ) {
					$success = true;
				}
			} else {
				wp_send_json_error(array('errorMessage' => __('You are trying to perform an invalid action.', 'clearfy')));
			}
		} else if( $storage == 'wordpress' ) {
			if( !empty($slug) ) {
				if( $action == 'activate' ) {
					$result = activate_plugin($slug);
					if( is_wp_error($result) ) {
						wp_send_json_error(array('errorMessage' => $result->get_error_message()));
					}
				} elseif( $action == 'deactivate' ) {
					deactivate_plugins($slug);
				}

				$success = true;
			}
		}

		if( $action == 'install' || $action == 'deactivate' ) {
			require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.delete-plugins-button.php';

			try {
				// Delete button
				$delete_button = new WCL_DeletePluginsButton($storage, $slug);
				$send_data['delete_button'] = $delete_button->render(false);
			} catch( Exception $e ) {
				wp_send_json_error(array('errorMessage' => $e->getMessage()));
			}
		}

		if($success) {
			wp_send_json_success($send_data);
		}

		wp_send_json_error(array('errorMessage' => __('An unknown error occurred during the activation of the component.', 'clearfy')));
	}

	add_action('wp_ajax_wbcr-clearfy-update-component', 'wbcr_clearfy_update_external_addon');
