<?php
/**
 * Global boot file
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 01.07.2018, Webcraftic
 * @version       1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/*add_action('wclearfy/google_page_speed_audit', function () {
	$results = WCL_Helper::fetch_google_page_speed_audit();

	if( !empty($results) ) {
		if( WCL_Plugin::app()->getPopulateOption('start_first_google_page_speed_audit') && !WCL_Plugin::app()->getPopulateOption('google_page_speed_audit_before') ) {
			WCL_Plugin::app()->updatePopulateOption('google_page_speed_audit_before', $results);
			WCL_Plugin::app()->deletePopulateOption('start_first_google_page_speed_audit');
		} else if( WCL_Plugin::app()->getPopulateOption('start_second_google_page_speed_audit') ) {
			WCL_Plugin::app()->updatePopulateOption('google_page_speed_audit_after', $results);
		}
	}
});*/

/*function wclearfy_refresh_google_page_widgets()
{
	WCL_Plugin::app()->updatePopulateOption('start_second_google_page_speed_audit', 1);
	WCL_Plugin::app()->deletePopulateOption('google_page_speed_audit_after');
	wp_schedule_event(time(), 'daily', 'wclearfy/google_page_speed_audit');
}*/

//add_action('wbcr/factory/clearfy/setup_wizard/saved_options', 'wclearfy_refresh_google_page_widgets');
//add_action('wbcr_clearfy_configurated_quick_mode', 'wclearfy_refresh_google_page_widgets');
//add_action('wbcr/factory/pages/impressive/after_form_save', 'wclearfy_refresh_google_page_widgets');

/**
 * Подключает скрипты для дополнительного меню Clearfy, на всех страницах сайта.
 * Скрипты могут быть добавлены только, если пользователь администратор и в настройках Clearfy
 * не отключено дополнительное меню.
 */
function wbcr_clr_enqueue_admin_bar_scripts()
{
	$disable_menu = WCL_Plugin::app()->getPopulateOption('disable_clearfy_extra_menu', false);

	if( !WCL_Plugin::app()->currentUserCan() || $disable_menu ) {
		return;
	}

	wp_enqueue_style('wbcr-clearfy-adminbar-styles', WCL_PLUGIN_URL . '/assets/css/admin-bar.css', [], WCL_Plugin::app()->getPluginVersion());
}

add_action('admin_enqueue_scripts', 'wbcr_clr_enqueue_admin_bar_scripts');
add_action('wp_enqueue_scripts', 'wbcr_clr_enqueue_admin_bar_scripts');

/**
 * Создает дополнительное меню Clearfy на верхней панели администратора.
 * По умолчанию выводятся пункты:
 * - Документация
 * - Нравится ли вам плагин?
 * - Обновится до премиум
 * Все остальные пункты меню могут быть получены с помощью фильтра wbcr/clearfy/adminbar_menu_items.
 * Меню может быть отключено опционально или если не имеет ни одного пукнта.
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function wbcr_clr_admin_bar_menu($wp_admin_bar)
{
	$disable_menu = WCL_Plugin::app()->getPopulateOption('disable_clearfy_extra_menu', false);

	if( !WCL_Plugin::app()->currentUserCan() || $disable_menu ) {
		return;
	}

	$menu_items = [];

	$menu_items['clearfy-docs'] = [
		'id' => 'clearfy-docs',
		'title' => '<span class="dashicons dashicons-book"></span> ' . __('Documentation', 'clearfy'),
		'href' => WCL_Plugin::app()->get_support()->get_docs_url(true, 'adminbar_menu')
	];

	$menu_items['clearfy-rating'] = [
		'id' => 'clearfy-rating',
		'title' => '<span class="dashicons dashicons-heart"></span> ' . __('Do you like our plugin?', 'clearfy'),
		'href' => 'https://wordpress.org/support/plugin/clearfy/reviews/'
	];
	$menu_items['clearfy-support'] = [
		'id' => 'clearfy-rating',
		'title' => '<span class="dashicons dashicons-sos"></span> ' . __('Getting started free support', 'clearfy'),
		'href' => WCL_Plugin::app()->get_support()->get_contacts_url(true, 'support', 'adminbar_menu')
	];

	if( !WCL_Plugin::app()->premium->is_activate() ) {
		$menu_items['clearfy-premium'] = [
			'id' => 'clearfy-premium',
			'title' => '<span class="dashicons dashicons-star-filled"></span> ' . __('Upgrade to premium', 'clearfy'),
			'href' => WCL_Plugin::app()->get_support()->get_pricing_url(true, 'adminbar_menu')
		];
	}

	/**
	 * @since 1.1.3 - добавлен
	 * @since 1.1.4 - является устаревшим
	 */
	$menu_items = wbcr_factory_000_apply_filters_deprecated('wbcr_clearfy_admin_bar_menu_items', [$menu_items], '1.4.0', 'wbcr/clearfy/adminbar_menu_items');

	/**
	 * @since 1.1.3 - добавлен
	 * @since 1.1.4 - изменено имя
	 */
	$menu_items = array_reverse(apply_filters('wbcr/clearfy/adminbar_menu_items', $menu_items));

	if( empty($menu_items) ) {
		return;
	}

	if( WCL_Plugin::app()->isNetworkActive() ) {
		$clearfy_settings_url = network_admin_url('settings.php');
	} else {
		$clearfy_settings_url = admin_url('options-general.php');
	}

	$clearfy_settings_url = $clearfy_settings_url . '?page=quick_start-' . WCL_Plugin::app()->getPluginName();
	$clearfy_extra_menu_title = apply_filters('wbcr/clearfy/adminbar_menu_title', __('Clearfy', 'clearfy'));

	$wp_admin_bar->add_menu([
		'id' => 'clearfy-menu',
		//'parent' => 'top-secondary',
		'title' => '<span class="wbcr-clearfy-admin-bar-menu-icon"></span><span class="wbcr-clearfy-admin-bar-menu-title">' . $clearfy_extra_menu_title . ' <span class="dashicons dashicons-arrow-down"></span></span>',
		'href' => $clearfy_settings_url
	]);

	foreach((array)$menu_items as $id => $item) {
		$wp_admin_bar->add_menu([
			'id' => $id,
			'parent' => 'clearfy-menu',
			'title' => $item['title'],
			'href' => $item['href'],
			'meta' => [
				'class' => isset($item['class']) ? $item['class'] : ''
			]
		]);
	}
}

add_action('admin_bar_menu', 'wbcr_clr_admin_bar_menu', 80);