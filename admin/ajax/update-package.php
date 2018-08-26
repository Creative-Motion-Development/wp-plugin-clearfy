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
	function wbcr_clearfy_update_package()
	{

		check_ajax_referer('package');

		$licensing = WCL_Licensing::instance();
		$licensing->getAddons(true); // обновляем список аддонов

		$package_plugin = WCL_Package::instance();
		if( !$licensing->isLicenseValid() and $licensing->isActivePaidAddons() ) {
			wp_send_json_error(array('msg' => __('To use premium components, you need activate a license!', 'clearfy') . '<a href="admin.php?page=license-wbcr_clearfy" class="btn btn-gold">' . __('Activate license', 'clearfy') . '</a>'));
		}

		try {
			$result = $package_plugin->update();
		} catch( Exception $e ) {
			wp_send_json_error(array(
				'msg' => $e->getMessage(),
				'code' => $e->getCode(),
			));
		}

		if( is_wp_error($result) ) {
			wp_send_json_error(array(
				'msg' => __($result->get_error_message(), 'clearfy'),
				'code' => __($result->get_error_code(), 'clearfy'),
			));
		}
		$success = true;
		$data = array();
		$data['msg'] = __('Configuration updated.', 'clearfy');
		$data['result'] = $result;
		if( $success ) {
			do_action('wbcr_clearfy_package_updated', $package_plugin->getSlugs());

			wp_send_json_success($data);
		}

		wp_send_json_error(array('errorMessage' => __('An unknown error occurred during the activation of the component.', 'clearfy')));
	}

	add_action('wp_ajax_wbcr-clearfy-update-package', 'wbcr_clearfy_update_package');
