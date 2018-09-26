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
	 * Handle settings
	 *
	 * @param $settings
	 */
	function wbcr_clearfy_handle_settings($settings)
	{
		global $wpdb;

		$values = array();
		$place_holders = array();
		$query = "INSERT INTO {$wpdb->prefix}options (option_name, option_value) VALUES ";

		foreach($settings as $option_name => $option_value) {
			$option_name = sanitize_text_field($option_name);
			$option_value = wp_kses_post($option_value);

			if( WCL_Plugin::app()->getOptionName('robots_txt_text') == $option_name ) {
				$site_url = get_home_url();
				$dir_host_without_scheme = preg_replace("(^https?://)", "", $site_url);
				$dir_host = $dir_host_without_scheme;

				if( is_ssl() ) {
					$dir_host = 'https://' . $dir_host_without_scheme;
				}

				$replace_host_value = preg_replace('/(Host:\s?)(.*)/', '$1' . $dir_host, $option_value);

				if( !empty($replace_host_value) ) {
					$option_value = $replace_host_value;
				}

				if( preg_match('/Sitemap:\s?(.*)/', $option_value, $matches) ) {
					$site_map_url = $matches[1];

					if( filter_var($site_map_url, FILTER_VALIDATE_URL) ) {
						$url_parts = parse_url($site_map_url);
						$replace_sitemap_value = preg_replace('/(Sitemap:\s?)(.*)/', '$1' . $url_parts['scheme'] . '://' . $dir_host_without_scheme . $url_parts['path'] . PHP_EOL, $option_value);

						if( !empty($replace_sitemap_value) ) {
							$option_value = $replace_sitemap_value;
						}
					}
				}
			}

			if( WCL_Plugin::app()->getPrefix() . 'freemius_activated_addons' == $option_name ) {
				$option_value = serialize( $option_value );
			}


			array_push($values, $option_name, $option_value);
			$place_holders[] = "('%s', '%s')";/* In my case, i know they will always be integers */
		}

		$query .= implode(', ', $place_holders);

		// Очищаем все опции
		$wpdb->query(
			"DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%" . WCL_Plugin::app()->getPrefix() . "_%';"
		);

		// Сбрасываем кеш опций
		WCL_Plugin::app()->flushOptionsCache();
		wp_cache_flush(); // сброс объектного кеша WP

		// Импортируем опции
		$wpdb->query($wpdb->prepare("$query ", $values));
	}

	function wbcr_clearfy_import_settings()
	{
		check_ajax_referer('wbcr_clearfy_ajax_quick_start_nonce', 'security');

		if( !current_user_can('manage_options') ) {
			echo json_encode(array('error' => __('You don\'t have enough capability to edit this information.', 'clearfy')));
			exit;
		}

		$settings = WCL_Helper::maybeGetPostJson('settings');
		$all_sites = WCL_Plugin::app()->request->post('all_sites', false, true);

		if( empty($settings) ) {
			echo json_encode(array('error' => __('Settings are not defined or do not exist.', 'clearfy')));
			exit;
		}

		if ( $all_sites ) {
			foreach ( WCL_Plugin::app()->getActiveSites() as $site ) {
				switch_to_blog( $site->blog_id );
				wbcr_clearfy_handle_settings($settings);
				restore_current_blog();
			}
		} else {
			wbcr_clearfy_handle_settings($settings);
		}

		$send_data = array( 'status' => 'success' );
		
		$package_plugin = WCL_Package::instance();
		$send_data['updateNotice'] = $package_plugin->getUpdateNotice();

		do_action('wbcr_clearfy_imported_settings');
		
		wp_send_json( $send_data );
		exit;
	}

	add_action('wp_ajax_wbcr_clearfy_import_settings', 'wbcr_clearfy_import_settings');

