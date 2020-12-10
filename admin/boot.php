<?php
/**
 * The boot file is needed to connect backend files, as well as register hooks.
 * Some hooks are so small that it does not make sense to put them into a file
 * or put them into a specific group of code.
 *
 * I usually register administrator notifications, create handlers before saving
 * plugin settings or after, register options in the Clearfy plugin.
 *
 * @author    Webcraftic <wordpress.webraftic@gmail.com>, Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright Webcraftic
 * @version   1.1
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Этот хук реализует условную логику перенаправления на страницу мастера настроек,
 * сразу после активации плагина.
 */
add_action('admin_init', function () {

	$plugin = WCL_Plugin::app();

	// If the user has updated the plugin or activated it for the first time,
	// you need to show the page "What's new?"
	if( !WCL_Plugin::app()->isNetworkAdmin() ) {
		$setup_page_viewed = WCL_Plugin::app()->request->get('wclearfy_setup_page_viewed', null);
		if( is_null($setup_page_viewed) ) {
			if( WCL_Helper::is_need_show_setup_page() ) {
				try {
					$redirect_url = '';
					if( class_exists('Wbcr_FactoryPages000') ) {
						$redirect_url = WCL_Plugin::app()->getPluginPageUrl('setup', ['wclearfy_setup_page_viewed' => 1]);
					}
					if( $redirect_url ) {
						wp_safe_redirect($redirect_url);
						die();
					}
				} catch( Exception $e ) {
				}
			}
		} else {
			if( WCL_Helper::is_need_show_setup_page() ) {
				delete_option($plugin->getOptionName('setup_wizard'));
			}
		}
	}
});

/**
 * Выводит кнопку настроек Clearfy в шапке интерфейса плагина
 */
add_action('wbcr/factory/pages/impressive/header', function ($plugin_name) {
	if( $plugin_name != WCL_Plugin::app()->getPluginName() ) {
		return;
	}
	?>
	<a href="<?php echo WCL_Plugin::app()->getPluginPageUrl('clearfy_settings') ?>" class="wbcr-factory-button wbcr-factory-type-settings">
		<?php echo apply_filters('wbcr/clearfy/settings_button_title', __('Clearfy settings', 'clearfy')); ?>
	</a>
	<?php
});

/**
 * @param                                          $form
 * @param Wbcr_Factory000_Plugin $plugin
 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
 */
function wbcr_clearfy_multisite_before_save($form, $plugin, $obj)
{
	if( $plugin->getPluginName() !== WCL_Plugin::app()->getPluginName() ) {
		return;
	}

	if( onp_build('premium') ) {
		if( WCL_PLUGIN_DEBUG ) {
			return;
		}
	}

	if( $plugin->isNetworkAdmin() ) {
		if( !$plugin->premium->is_activate() && $plugin->isNetworkActive() ) {
			$obj->redirectToAction('multisite-pro');
		}
	}
}

add_action('wbcr/factory/pages/impressive/before_form_save', 'wbcr_clearfy_multisite_before_save', 10, 3);

/**
 * Устанавливает логотип Webcraftic и сборку плагина для Clearfy и всех его компонентов
 *
 * @param string $title
 *
 * @since 1.4.0
 *
 */
function wbcr_clearfy_branding($title)
{
	$is_premium = WCL_Plugin::app()->premium->is_activate();

	return 'Webcraftic Clearfy ' . ($is_premium ? '<span class="wbcr-clr-logo-label wbcr-clr-premium-label-logo">' . __('Business', 'clearfy') . '</span>' : '<span class="wbcr-clr-logo-label wbcr-clr-free-label-logo">Free</span>') . ' ver';
}

add_action('wbcr/factory/pages/impressive/plugin_title', 'wbcr_clearfy_branding');

/**
 * Подключаем скрипты для установки компонентов Clearfy
 * на все страницы админпанели
 */
/*add_action('admin_enqueue_scripts', function () {
	wp_enqueue_style('wbcr-clearfy-install-components', WCL_PLUGIN_URL . '/admin/assets/css/install-addons.css', [], WCL_Plugin::app()->getPluginVersion());
	wp_enqueue_script('wbcr-clearfy-install-components', WCL_PLUGIN_URL . '/admin/assets/js/install-addons.js', [
		'jquery',
		'wbcr-factory-clearfy-000-global'
	], WCL_Plugin::app()->getPluginVersion());
});*/

/**
 * Выводит уведомление, что нужно сбросить постоянные ссылки.
 * Уведомление будет показано на всех страницах Clearfy и его компонентах.
 *
 * @param WCL_Plugin $plugin
 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
 */
function wbcr_clearfy_print_notice_rewrite_rules($plugin, $obj)
{
	if( WCL_Plugin::app()->getPopulateOption('need_rewrite_rules') ) {
		$obj->printWarningNotice(sprintf('<span class="wbcr-clr-need-rewrite-rules-message">' . __('When you deactivate some components, permanent links may work incorrectly. If this happens, please, <a href="%s">update the permalinks</a>, so you could complete the deactivation.', 'clearfy'), admin_url('options-permalink.php')) . '</span>');
	}
}

add_action('wbcr/factory/pages/impressive/print_all_notices', 'wbcr_clearfy_print_notice_rewrite_rules', 10, 2);

/**
 * Удалем уведомление Clearfy о том, что нужно перезаписать постоянные ссылоки.s
 */
function wbcr_clearfy_flush_rewrite_rules()
{
	WCL_Plugin::app()->deletePopulateOption('need_rewrite_rules', 1);
}

add_action('flush_rewrite_rules_hard', 'wbcr_clearfy_flush_rewrite_rules');

/**
 * Обновить постоынные ссылки, после выполнения быстрых настроек
 *
 * @param WHM_Plugin $plugin
 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
 */
function wbcr_clearfy_after_form_save($plugin, $obj)
{
	if( !WCL_Plugin::app()->currentUserCan() ) {
		return;
	}
	$is_clearfy = WCL_Plugin::app()->getPluginName() == $plugin->getPluginName();

	if( $is_clearfy && $obj->id == 'quick_start' && isset($_GET['action']) && $_GET['action'] == 'flush-cache-and-rules' ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		flush_rewrite_rules(true);
	}
}

add_action('wbcr/factory/pages/impressive/after_form_save', 'wbcr_clearfy_after_form_save', 10, 2);

/**
 * Widget with the offer to buy Clearfy Business
 *
 * @param array $widgets
 * @param string $position
 * @param Wbcr_Factory000_Plugin $plugin
 */

add_filter('wbcr/factory/pages/impressive/widgets', function ($widgets, $position, $plugin) {
	$is_current_plugin = $plugin->getPluginName() == WCL_Plugin::app()->getPluginName();

	if( class_exists('WRIO_Plugin') ) {
		$is_current_plugin = $plugin->getPluginName() === WRIO_Plugin::app()->getPluginName() || $plugin->getPluginName() === WCL_Plugin::app()->getPluginName();
	}

	if( $is_current_plugin ) {

		require_once WCL_PLUGIN_DIR . '/admin/includes/sidebar-widgets.php';

		if( WCL_Plugin::app()->premium->is_activate() ) {
			unset($widgets['donate_widget']);

			if( $position == 'right' ) {

				unset($widgets['adverts_widget']);
				unset($widgets['business_suggetion']);
				unset($widgets['rating_widget']);
				unset($widgets['info_widget']);
			}

			return $widgets;
		} else {
			if( $position == 'right' ) {
				$widgets['subscribe'] = wbcr_clearfy_get_sidebar_subscribe_widget();
				unset($widgets['business_suggetion']);
				unset($widgets['info_widget']);
				unset($widgets['rating_widget']);
			}
		}
	
		if( $position == 'bottom' ) {
			unset($widgets['support_widget']);
			$widgets['donate_widget'] = wbcr_clearfy_get_sidebar_premium_widget();
		}
	}

	return $widgets;
}, 9999, 3);

/**
 * Remove adverts notices for premium users
 */
add_action('wbcr/factory/admin_notices', function ($notices, $plugin_name) {
	if( $plugin_name != WCL_Plugin::app()->getPluginName() ) {
		return $notices;
	}

	if( WCL_Plugin::app()->premium->is_activate() ) {
		unset($notices['adverts_notice']);
	}

	return $notices;
}, 9999, 2);

/**
 * Remove adverts widgets for premium users
 */
add_action('wp_dashboard_setup', function () {
	global $wp_meta_boxes;

	if( WCL_Plugin::app()->premium->is_activate() ) {
		if( isset($wp_meta_boxes['dashboard']) ) {
			if( isset($wp_meta_boxes['dashboard']['normal']) && isset($wp_meta_boxes['dashboard']['normal']['core']) && isset($wp_meta_boxes['dashboard']['normal']['core']['wbcr-factory-adverts-widget']) ) {
				unset($wp_meta_boxes['dashboard']['normal']['core']['wbcr-factory-adverts-widget']);
			}
			if( isset($wp_meta_boxes['dashboard']['side']) && isset($wp_meta_boxes['dashboard']['side']['core']) && isset($wp_meta_boxes['dashboard']['normal']['core']['wbcr-factory-adverts-widget']) ) {
				unset($wp_meta_boxes['dashboard']['normal']['core']['wbcr-factory-adverts-widget']);
			}
		}
	}
}, 9999);

// add widget scripts on all clearfy pages
add_action('wbcr/clearfy/page_assets', function ($id, $scripts, $styles) {
	$scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/widgets.js', array('jquery'));
}, 10, 3);
