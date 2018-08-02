<?php

	// if uninstall.php is not called by WordPress, die
	if( !defined('WP_UNINSTALL_PLUGIN') ) {
		die;
	}

	// remove plugin options
	global $wpdb;

	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = 'factory_plugin_activated_wbcr_clearfy';");
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wbcr-clearfy_%';");
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wbcr_clearfy_%';");
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wbcr_wp_term_%';");
	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='wbcr_wp_old_slug';");
	
	$package_dir = WP_PLUGIN_DIR . '/clearfy_package/';
	if ( is_dir( $package_dir ) ) {
		global $wp_filesystem;
		if( ! $wp_filesystem ) {
			if( ! function_exists( 'WP_Filesystem' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			WP_Filesystem();
		}		
		$wp_filesystem->rmdir( $package_dir, true );
	}
