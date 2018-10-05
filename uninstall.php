<?php

	// if uninstall.php is not called by WordPress, die
	if( !defined('WP_UNINSTALL_PLUGIN') ) {
		die;
	}

	// remove plugin options
	global $wpdb;

	require_once ABSPATH . '/wp-admin/includes/plugin.php';

	$package_plugin_basename = 'clearfy_package/clearfy-package.php';

	if( is_plugin_active($package_plugin_basename) ) {
		deactivate_plugins($package_plugin_basename);
	}

	delete_plugins(array($package_plugin_basename));

	/**
	 * Удаление кеша и опций
	 */
	function uninstall()
	{
		// remove plugin options
		global $wpdb;

		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = 'factory_plugin_activated_wbcr_clearfy';");
		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wbcr-clearfy_%';");
		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wbcr_clearfy_%';");
		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wbcr_wp_term_%';");
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='wbcr_wp_old_slug';");
	}

	if( is_multisite() ) {
		global $wpdb, $wp_version;

		$blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		if( !empty($blogs) ) {
			foreach($blogs as $id) {

				switch_to_blog($id);

				uninstall();

				restore_current_blog();
			}
		}
	} else {
		uninstall();
	}

	//todo: добавить функции очистки для компонентов