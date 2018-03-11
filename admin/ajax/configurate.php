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

	function wbcr_clearfy_configurate_plugin()
	{
		check_ajax_referer('wbcr_clearfy_ajax_quick_start_nonce', 'security');

		if( !current_user_can('manage_options') ) {
			echo json_encode(array('error' => __('You don\'t have enough capability to edit this information.', 'clearfy')));
			exit;
		}

		$mode_name = isset($_POST['mode'])
			? sanitize_text_field($_POST['mode'])
			: null;

		if( empty($mode_name) ) {
			echo json_encode(array('error' => __('Undefinded mode.', 'clearfy')));
			exit;
		}

		if( $mode_name != 'reset' ) {

			$update_options = array();
			$group = WCL_Group::getInstance($mode_name);
			$mode_options = $group->getOptions();

			if( empty($mode_options) ) {
				echo json_encode(array('error' => __('Undefinded mode.', 'clearfy')));
				exit;
			}

			foreach($mode_options as $option) {
				$set_value = 1;

				$option_name = $option->getName();
				$option_value = $option->getValue($mode_name);

				if( !empty($option_value) ) {
					$set_value = $option_value;
				}

				$update_options[$option_name] = $set_value;

				WCL_Plugin::app()->updateOptions($update_options);
			}
		} else {
			$delete_options = array();
			$all_options = WCL_Option::getAllOptions();

			if( !empty($all_options) ) {
				foreach($all_options as $option) {
					$delete_options[] = $option->getName();
				}

				WCL_Plugin::app()->deleteOptions($delete_options);
			}
		}

		// todo: test cache control
		if( function_exists('w3tc_pgcache_flush') ) {
			w3tc_pgcache_flush();
		} elseif( function_exists('wp_cache_clear_cache') ) {
			wp_cache_clear_cache();
		} elseif( function_exists('rocket_clean_files') ) {
			rocket_clean_files(esc_url($_SERVER['HTTP_REFERER']));
		} else if( isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache') ) {
			$GLOBALS['wp_fastest_cache']->deleteCache();
		}

		echo json_encode(array('status' => 'success', 'export_options' => WCL_Helper::getExportOptions()));
		exit;
	}

	add_action('wp_ajax_wbcr_clearfy_configurate', 'wbcr_clearfy_configurate_plugin');

