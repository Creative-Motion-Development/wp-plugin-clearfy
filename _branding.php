<?php
	/**
	 * Sample file, to change branding of the plugin
	 *
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 18.10.2018, Webcraftic
	 * @version 1.0
	 */

	if( class_exists('WCL_Plugin') ) {
		/**
		 * Replace plugin name in admin menu
		 */
		add_filter('wbcr/factory/pages/impressive/menu_title', function ($title, $plugin_name, $page_id) {
			if( WCL_Plugin::app()->getPluginName() == $plugin_name && $page_id == 'quick_start' ) {
				return 'Plugin name menu';
			}

			return $title;
		}, 10, 3);

		/**
		 * Replace plugin name in settins button
		 */
		add_filter('wbcr/clearfy/settings_button_title', function ($title) {
			return 'Plugin name settings';
		});

		/**
		 * Replace plugin name in adminbar menu
		 */
		add_filter('wbcr/clearfy/adminbar_menu_title', function ($title) {
			return 'Plugin name';
		});

		/**
		 * Replace plugin name in header
		 */
		add_filter('wbcr/factory/pages/impressive/plugin_title', function ($title, $plugin_name) {
			if( WCL_Plugin::app()->getPluginName() == $plugin_name ) {
				return 'Plugin name <span class="wbcr-clr-logo-label wbcr-clr-free-label-logo">Free</span> ver';
			}

			return $title;
		}, 10, 2);

		/**
		 * Remove logos using styles
		 */
		add_filter('admin_head', function () {
			?>
			<style>
				#wp-admin-bar-clearfy-menu .wbcr-clearfy-admin-bar-menu-icon {
					display: none !important;
				}

				#wp-admin-bar-clearfy-menu .wbcr-clearfy-admin-bar-menu-title {
					padding-left: 10px;
				}

				#WBCR .wbcr-clr-logo-label:before {
					display: none !important;
				}
			</style>
		<?php
		});

		/**
		 * This hook removes our widgets to get premium.
		 */
		add_filter('wbcr/factory/pages/impressive/widgets', function ($widgets, $position) {
			unset($widgets['donate_widget']);
			unset($widgets['business_suggetion']);
			unset($widgets['rating_widget']);

			if( $position == 'bottom' ) {
				return array();
			}

			return $widgets;
		}, 10, 3);

		/**
		 * You can create an arbitrary company widget, for technical support and any other data, so that users can see it on the pages of the plugin.
		 *
		 * @param array $widgets
		 * @param string $position
		 * @param Wbcr_Factory000_Plugin $plugin
		 */
		add_filter('wbcr/factory/pages/impressive/widgets', function ($widgets, $position, $plugin) {
			if( $plugin->getPluginName() == WCL_Plugin::app()->getPluginName() ) {

				if( $position == 'right' ) {
					ob_start();
					?>
					<div id="wbcr-clr-company-widget" class="wbcr-factory-sidebar-widget">
						<p>
							<strong><?php _e('Company widget 1', 'clearfy'); ?></strong>
						</p>

						<div class="wbcr-clr-company-widget-body">
							<p><?php _e('You can create an arbitrary company widget, for technical support and any other data, so that users can see it on the pages of the plugin.', 'clearfy') ?></p>
						</div>
					</div>
					<?php
					$widgets['company_widget_1'] = ob_get_contents();

					ob_end_clean();
				}

				if( $position == 'bottom' ) {
					ob_start();
					?>
					<div id="wbcr-clr-company-widget" class="wbcr-factory-sidebar-widget">
						<p>
							<strong><?php _e('Company widget 2', 'clearfy'); ?></strong>
						</p>

						<div class="wbcr-clr-company-widget-body">
							<p><?php _e('You can create an arbitrary company widget, for technical support and any other data, so that users can see it on the pages of the plugin.', 'clearfy') ?></p>
						</div>
					</div>
					<?php

					$widgets['company_widget_2'] = ob_get_contents();

					ob_end_clean();
				}
			}

			return $widgets;
		}, 10, 3);

		/**
		 * Add a new component, for example, Yoast SEO
		 */
		add_filter('wbcr/clearfy/components/items_list', function ($components) {
			$components[] = array(
				'name' => 'wordpress-seo',
				'title' => __('Yoast SEO', 'clearfy'),
				'url' => 'https://wordpress.org/plugins/wordpress-seo/',
				'type' => 'wordpress',
				'base_path' => 'wordpress-seo/wp-seo.php',
				'icon' => 'https://ps.w.org/wordpress-seo/assets/icon.svg?rev=1946641',
				'description' => __('Improve your WordPress SEO: Write better content and have a fully optimized WordPress site using the Yoast SEO plugin.', 'clearfy')
			);

			return $components;
		});

		/**
		 * Replace plugin info on plugin page. I did not find the hooks I needed, so I think you can use the replacement trick.
		 * However, the best solution is to simply hide the plugin from the client.
		 */
		add_action('wp_loaded', function () {
			if( !is_admin() || !WbcrFactoryClearfy000_Helpers::strContains($_SERVER['REQUEST_URI'], 'plugins.php') ) {
				return;
			}

			ob_start(function ($content) {
				$old_content = $content;

				$content = preg_replace('/<a\shref=\"[^"]+\">\s?Webcraftic\s?<\/a>/', '<a href="http://yoursite.com">Company name</a>', $content);
				$content = str_replace('https://wordpress.org/plugins/clearfy/', 'http://yoursite.com', $content);
				$content = str_replace('Webcraftic Clearfy', 'Plugin name', $content);
				$content = str_replace('Clearfy', 'Plugin name', $content);

				if( empty($content) ) {
					$content = $old_content;
				}

				return $content;
			});
		});
	}