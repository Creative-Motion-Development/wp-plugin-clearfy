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
	 * Handle options
	 *
	 * @param $mode_name
	 */
	function wbcr_clearfy_handle_options( $mode_name ) {
		if( $mode_name != 'reset' ) {

			$update_options = array();
			$group = WCL_Group::getInstance($mode_name);
			$mode_options = $group->getOptions();

			if( empty($mode_options) ) {
				wp_send_json(array('error' => __('Undefinded mode.', 'clearfy')));
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
	}

	function wbcr_clearfy_configurate_plugin()
	{
		check_ajax_referer('wbcr_clearfy_ajax_quick_start_nonce', 'security');

		if( !current_user_can('manage_options') ) {
			wp_send_json(array('error' => __('You don\'t have enough capability to edit this information.', 'clearfy')));
		}

		$mode_name = WCL_Plugin::app()->request->post('mode', false, true);
		$flush_redirect = WCL_Plugin::app()->request->post('flush_redirect', false, true);
		$all_sites = WCL_Plugin::app()->request->post('all_sites', false, true);

		if( empty($mode_name) ) {
			wp_send_json(array('error' => __('Undefinded mode.', 'clearfy')));
		}

		if ( $all_sites ) {
			foreach ( WCL_Plugin::app()->getActiveSites() as $site ) {
				switch_to_blog( $site->blog_id );
				wbcr_clearfy_handle_options( $mode_name );
				restore_current_blog();
			}
		} else {
			wbcr_clearfy_handle_options( $mode_name );
		}

		if( !$flush_redirect ) {
			// todo: создать отдельный файл для сброса кеша и перенести этот код туда
			if( function_exists('w3tc_pgcache_flush') ) {
				w3tc_pgcache_flush();
			} elseif( function_exists('wp_cache_clear_cache') ) {
				wp_cache_clear_cache();
			} elseif( function_exists('rocket_clean_files') ) {
				rocket_clean_files(esc_url($_SERVER['HTTP_REFERER']));
			} else if( isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache') ) {
				$GLOBALS['wp_fastest_cache']->deleteCache();
			}
		}

		do_action('wbcr_clearfy_configurated_quick_mode', $mode_name);

		// wbcr_clearfy/configurate_quick_mode_success_args
		// @since 1.3.188
		wp_send_json(apply_filters('wbcr_clearfy/configurate_quick_mode_success_args', array(
			'status' => 'success',
			'export_options' => WCL_Helper::getExportOptions()
		), $mode_name));
	}

	add_action('wp_ajax_wbcr_clearfy_configurate', 'wbcr_clearfy_configurate_plugin');

