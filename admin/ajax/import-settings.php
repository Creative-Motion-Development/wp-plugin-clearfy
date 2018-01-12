<?php
	/**
	 * Ajax plugin configuration
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	function wbcr_clearfy_import_settings()
	{
		global $wpdb, $wbcr_clearfy_plugin;

		check_ajax_referer('wbcr_clearfy_ajax_quick_start_nonce', 'security');

		if( !current_user_can('manage_options') ) {
			echo json_encode(array('error' => __('You don\'t have enough capability to edit this information.', 'clearfy')));
			exit;
		}

		$settings = wbcr_maybe_get_post_json('settings');

		if( empty($settings) ) {
			echo json_encode(array('error' => __('Settings are not defined or do not exist.', 'clearfy')));
			exit;
		}

		$values = array();
		$place_holders = array();
		$query = "INSERT INTO {$wpdb->prefix}options (option_name, option_value) VALUES ";

		foreach($settings as $key => $value) {
			array_push($values, sanitize_text_field($key), wp_kses_post($value));
			$place_holders[] = "('%s', '%s')";/* In my case, i know they will always be integers */
		}

		$query .= implode(', ', $place_holders);

		$wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%" . $wbcr_clearfy_plugin->pluginName . "_%';");
		$wpdb->query($wpdb->prepare("$query ", $values));

		echo json_encode(array('status' => 'success'));
		exit;
	}

	add_action('wp_ajax_wbcr_clearfy_import_settings', 'wbcr_clearfy_import_settings');

