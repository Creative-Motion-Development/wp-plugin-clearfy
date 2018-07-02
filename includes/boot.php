<?php
	/**
	 * Global boot file
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 01.07.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * Assets admin bar scripts
	 */
	function wbcr_clr_enqueue_admin_bar_scripts()
	{
		if( !WCL_Plugin::app()->currentUserCan() ) {
			return;
		}

		wp_enqueue_style('wbcr-clearfy-adminbar-styles', WCL_PLUGIN_URL . '/assets/css/admin-bar.css', array(), WCL_Plugin::app()
			->getPluginVersion());
	}

	add_action('admin_enqueue_scripts', 'wbcr_clr_enqueue_admin_bar_scripts');
	add_action('wp_enqueue_scripts', 'wbcr_clr_enqueue_admin_bar_scripts');

	/**
	 * Cleate admin bar menu
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	function wbcr_clr_admin_bar_menu($wp_admin_bar)
	{
		if( !WCL_Plugin::app()->currentUserCan() ) {
			return;
		}
		$menu_items = array();
		$menu_items = apply_filters('wbcr_clearfy_admin_bar_menu_items', $menu_items);

		if( empty($menu_items) ) {
			return;
		}

		$wp_admin_bar->add_menu(array(
			'id' => 'clearfy-menu',
			'parent' => 'top-secondary',
			'title' => '<span class="wbcr-clearfy-admin-bar-menu-icon"></span><span class="wbcr-clearfy-admin-bar-menu-title">' . __('Clearfy', 'clearfy') . '</span>',
			'href' => admin_url('options-general.php?page=quick_start-' . WCL_Plugin::app()->getPluginName())
		));

		foreach((array)$menu_items as $id => $item) {
			$wp_admin_bar->add_menu(array(
				'id' => $id,
				'parent' => 'clearfy-menu',
				'title' => $item['title'],
				'href' => $item['href'],
				'meta' => array(
					'class' => isset($item['class'])
						? $item['class']
						: ''
				)
			));
		}
	}

	add_action('admin_bar_menu', 'wbcr_clr_admin_bar_menu', 80);