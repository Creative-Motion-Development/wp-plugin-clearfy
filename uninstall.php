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
	
	$package_plugin_basename = 'clearfy_package/clearfy-package.php';
	deactivate_plugins( $package_plugin_basename );
	uninstall_plugin( $package_plugin_basename );
	delete_plugins( array( $package_plugin_basename ) );
