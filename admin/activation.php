<?php
/**
 * Activator for the clearfy
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 09.09.2017, Webcraftic
 * @see           Factory000_Activator
 * @version       1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCL_Activation extends Wbcr_Factory000_Activator {

	/**
	 * Runs activation actions.
	 *
	 * @since 1.0.0
	 */
	public function activate()
	{
		if( !function_exists('is_plugin_active') ) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}
		// Deactivate components for code minification, if alternative plugins are installed
		// -------------
		$minify_js_plugins = [
			'autoptimize/autoptimize.php',
			'fast-velocity-minify/fvm.php',
			'js-css-script-optimizer/js-css-script-optimizer.php',
			'merge-minify-refresh/merge-minify-refresh.php',
			'wp-super-minify/wp-super-minify.php'
		];

		$is_activate_minify_js = true;
		foreach($minify_js_plugins as $m_plugin) {

			if( is_plugin_active($m_plugin) ) {
				$is_activate_minify_js = false;
			}
		}

		if( !$is_activate_minify_js ) {
			WCL_Plugin::app()->deactivateComponent('minify_and_combine');
			WCL_Plugin::app()->deactivateComponent('html_minify');
		}

		// -------------
		// Deactivate yoast component features if it is not activated
		// -------------

		if( !defined('WPSEO_VERSION') ) {
			WCL_Plugin::app()->deactivateComponent('yoast_seo');
		}

		// Deactivate cyrlitera component for all languages except selected
		if( !in_array(get_locale(), ['ru_RU', 'bel', 'kk', 'uk', 'bg', 'bg_BG', 'ka_GE']) ) {
			WCL_Plugin::app()->deactivateComponent('cyrlitera');
		}

		if( !WCL_Plugin::app()->getPopulateOption('deactivated_unused_modules') ) {
			if( !WCL_Plugin::app()->getPopulateOption('disable_comments') || "enable_comments" == WCL_Plugin::app()->getPopulateOption('disable_comments') ) {
				WCL_Plugin::app()->deactivateComponent('comments_tools');
			}

			$plugin_updates = !WCL_Plugin::app()->getPopulateOption('plugin_updates') || "enable_plugin_monual_updates" == WCL_Plugin::app()->getPopulateOption('plugin_updates');
			$theme_updates = !WCL_Plugin::app()->getPopulateOption('theme_updates') || "enable_theme_monual_updates" == WCL_Plugin::app()->getPopulateOption('theme_updates');

			if( $plugin_updates || $theme_updates ) {
				WCL_Plugin::app()->deactivateComponent('updates_manager');
			}
			WCL_Plugin::app()->updatePopulateOption('deactivated_unused_modules', 1);
		}

		if( !get_option($this->plugin->getOptionName('plugin_activated'), false) ) {
			//WCL_Plugin::app()->updatePopulateOption('start_first_google_page_speed_audit', 1);
			update_option($this->plugin->getOptionName('setup_wizard'), 1);
		}

		/*if( !wp_next_scheduled('wclearfy/google_page_speed_audit') ) {
			wp_schedule_event(time(), 'daily', 'wclearfy/google_page_speed_audit');
		}*/

		/**
		 * @since 1.4.1
		 */
		do_action('wbcr/clearfy/activated');
	}

	/**
	 * Runs activation actions.
	 *
	 * @since 1.0.0
	 */
	public function deactivate()
	{

		if( wp_next_scheduled('wclearfy/google_page_speed_audit') ) {
			wp_clear_scheduled_hook('wclearfy/google_page_speed_audit');
		}

		if( wp_next_scheduled('wclearfy/google_page_speed_audit') ) {
			wp_clear_scheduled_hook('wclearfy/google_page_speed_audit');
		}

		/*$dependent = 'clearfy_package/clearfy-package.php';

		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		if ( is_plugin_active( $dependent ) ) {
			add_action( 'update_option_active_plugins', [ $this, 'deactivateDependent' ] );
		}
		add_action( 'update_site_option_active_sitewide_plugins', [ $this, 'deactivateDependent' ] );*/

		/**
		 * @since 1.4.1
		 */
		do_action('wbcr/clearfy/deactivated');
	}

	/**
	 * Deactivate clearfy package
	 */
	/*public function deactivateDependent() {
		$package_plugin = WCL_Package::instance();
		$package_plugin->deactive();
	}*/
}
