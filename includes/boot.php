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
	 * Подключает скрипты для дополнительного меню Clearfy, на всех страницах сайта.
	 * Скрипты могут быть добавлены только, если пользователь администратор и в настройках Clearfy
	 * не отключено дополнительное меню.
	 */
	function wbcr_clr_enqueue_admin_bar_scripts()
	{
		$disable_menu = WCL_Plugin::app()->getOption('disable_clearfy_extra_menu', false);

		if( !WCL_Plugin::app()->currentUserCan() || $disable_menu ) {
			return;
		}

		wp_enqueue_style('wbcr-clearfy-adminbar-styles', WCL_PLUGIN_URL . '/assets/css/admin-bar.css', array(), WCL_Plugin::app()
			->getPluginVersion());
	}

	add_action('admin_enqueue_scripts', 'wbcr_clr_enqueue_admin_bar_scripts');
	add_action('wp_enqueue_scripts', 'wbcr_clr_enqueue_admin_bar_scripts');

	/**
	 * Создает дополнительное меню Clearfy на верхней панели администратора.
	 * По умолчанию выводятся пункты:
	 * - Документация
	 * - Нравится ли вам плагин?
	 * - Обновится до премиум
	 * Все остальные пункты меню могут быть получены с помощью фильтра wbcr_clearfy_admin_bar_menu_items.
	 * Меню может быть отключено опционально или если не имеет ни одного пукнта.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	function wbcr_clr_admin_bar_menu($wp_admin_bar)
	{
		$disable_menu = WCL_Plugin::app()->getOption('disable_clearfy_extra_menu', false);

		if( !WCL_Plugin::app()->currentUserCan() || $disable_menu ) {
			return;
		}

		$menu_items = array();
		// todo: переименовать фильтр "рефакторинг"
		$menu_items = apply_filters('wbcr_clearfy_admin_bar_menu_items', $menu_items);

		$menu_items['clearfy-docs'] = array(
			'id' => 'clearfy-docs',
			'title' => '<span class="dashicons dashicons-book"></span> ' . __('Documentation', 'gonzales'),
			'href' => WCL_Plugin::app()->getAuthorSitePageUrl('docs', 'adminbar_menu')
		);

		$menu_items['clearfy-rating'] = array(
			'id' => 'clearfy-rating',
			'title' => '<span class="dashicons dashicons-heart"></span> ' . __('Do you like our plugin?', 'gonzales'),
			'href' => 'https://wordpress.org/support/plugin/clearfy/reviews/'
		);

		require_once(WCL_PLUGIN_DIR . '/includes/classes/class.licensing.php');
		$licensing = WCL_Licensing::instance();

		if( !$licensing->isLicenseValid() ) {
			$menu_items['clearfy-premium'] = array(
				'id' => 'clearfy-premium',
				'title' => '<span class="dashicons dashicons-star-filled"></span> ' . __('Upgrade to premium', 'gonzales'),
				'href' => WCL_Plugin::app()->getAuthorSitePageUrl('pricing', 'adminbar_menu')
			);
		}

		if( empty($menu_items) ) {
			return;
		}

		$wp_admin_bar->add_menu(array(
			'id' => 'clearfy-menu',
			//'parent' => 'top-secondary',
			'title' => '<span class="wbcr-clearfy-admin-bar-menu-icon"></span><span class="wbcr-clearfy-admin-bar-menu-title">' . __('Clearfy', 'clearfy') . ' <span class="dashicons dashicons-arrow-down"></span></span>',
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