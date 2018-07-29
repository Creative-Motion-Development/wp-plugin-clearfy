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

		$package_plugin = WCL_Package::instance();
		$result = $package_plugin->update();
		
		$success = true;
		$data = array();
		$data['msg'] = __( 'Configuration updated.', 'clearfy' );
		$data['result'] = $result;
		if($success) {
			do_action('wbcr_clearfy_package_updated', $package_plugin->getSlugs());

			wp_send_json_success( $data );
		}

		wp_send_json_error(array('errorMessage' => __('An unknown error occurred during the activation of the component.', 'clearfy')));
	}

	add_action('wp_ajax_wbcr-clearfy-update-package', 'wbcr_clearfy_update_package');
