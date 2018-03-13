<?php
	/**
	 * The page Settings.
	 *
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_PerformanceGooglePage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "performance_google";

		/**
		 * @var string
		 */
		public $page_parent_page = 'performance';

		/**
		 * @var string
		 */
		public $page_menu_dashicon = 'dashicons-performance';

		/**
		 * @var int
		 */
		public $page_menu_position = 20;

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Google services', 'clearfy');

			parent::__construct($plugin);

			add_action('wbcr_factory_000_imppage_flush_cache', array($this, 'afterSave'), 10, 2);

			$this->plugin = $plugin;
		}

		public function afterSave($plugin_name, $result_id)
		{
			if( $plugin_name == WCL_Plugin::app()->getPluginName() && $result_id == $this->getResultId() ) {

				$ga_cache = $this->getOption('ga_cache');
				$ga_caos_remove_wp_cron = $this->getOption('ga_caos_remove_wp_cron');

				if( $ga_cache ) {
					if( !$ga_caos_remove_wp_cron ) {
						if( !wp_next_scheduled('wbcr_clearfy_update_local_ga') ) {
							wp_schedule_event(time(), 'daily', 'wbcr_clearfy_update_local_ga');
						}

						return;
					}
				}

				if( (!$ga_cache || $ga_caos_remove_wp_cron) && wp_next_scheduled('wbcr_clearfy_update_local_ga') ) {
					wp_clear_scheduled_hook('wbcr_clearfy_update_local_ga');
				}
			}
		}

		/**
		 * Permalinks options.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getOptions()
		{
			$options = array();

			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Fonts and Maps</strong>.', 'clearfy') . '<p>' . __('Google Fonts and Maps strongly affect your website loading speed. Use settings below to disable or optimize Google fonts and Maps.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'lazy_load_google_fonts',
				'title' => __('Google Fonts asynchronous', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('By default, WordPress loads Google fonts synchronously, that is, your page will not be fully loaded until Google Fonts are loaded. This algorithm slows down the loading of your page and leads to errors when checking the site in Google Page Speed. Using this option, your Google Fonts will be loaded after your page is fully loaded. This method has a negative — you and visitors of your site will see how the font changes while loading a page, from the system to the downloadable one.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_google_fonts',
				'title' => __('Disable Google Fonts', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('This function stops loading of Open Sans and other fonts used by WordPress and bundled themes (Twenty Twelve, Twenty Thirteen, Twenty Fourteen, Twenty Fifteen, Twenty Sixteen, Twenty Seventeen) from Google Fonts.
Reasons for not using Google Fonts might be privacy and security, local development or production, blocking of Google’s servers, characters not supported by font, performance.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_google_maps',
				'title' => __('Disable Google maps', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('This function stops loading of Google Maps used by some themes or plugins.
Reasons for not using Google Maps might be privacy and security, local development or production, blocking of Google’s servers, performance, not necessary, etc.', 'clearfy'),
				'default' => false,
				'eventsOn' => array(
					'show' => '.factory-control-exclude_from_disable_google_maps,.factory-control-remove_iframe_google_maps'
				),
				'eventsOff' => array(
					'hide' => '.factory-control-exclude_from_disable_google_maps,.factory-control-remove_iframe_google_maps'
				)
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_iframe_google_maps',
				'title' => __('Remove iframe Google maps', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('By default, the "Disable Google Maps" option removes maps inserted with the SCRIPT tag from the page source code. However, you can also cut out all maps inserted via the iframe by enabling this option.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'textbox',
				'way' => 'buttons',
				'name' => 'exclude_from_disable_google_maps',
				'title' => __('Exclude pages from Disable Google Maps filter', 'clearfy'),
				'hint' => __('Posts or Pages IDs separated by a ,', 'clearfy')
			);

			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Google Analytics cache</strong>.', 'clearfy') . '<p>' . __('To improve Google Page Speed indicators Analytics caching is needed. However, it can also slightly increase your website loading speed, because Analytics js files will load locally. The second case that you might need these settings is the usual Google Analytics connection to your website. You do not need to do this with other plugins or insert the tracking code into your theme.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'ga_cache',
				'title' => __('Google analytic cache', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('If you enable this option, the plugin will begin to save a local copy of Google Analytics to speed up the loading of your website and improve Google Page Speed.', 'clearfy') . '<br>--<br><span class="hint-warnign-color">' . __('ATTENTION! Before using this option, remove the previously installed Google Analytics code inside your theme or plugins associated with this feature!', 'clearfy') . '</span>',
				'default' => false,
				'eventsOn' => array(
					'show' => '#wbcr-clearfy-performance-ga-block'
				),
				'eventsOff' => array(
					'hide' => '#wbcr-clearfy-performance-ga-block'
				)

			);

			$options[] = array(
				'type' => 'div',
				'id' => 'wbcr-clearfy-performance-ga-block',
				'items' => array(
					array(
						'type' => 'textbox',
						'way' => 'buttons',
						'name' => 'ga_tracking_id',
						'title' => __('Google analytic Code', 'clearfy'),
						'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'hint' => __('Set the Google Analytics tracking code.', 'clearfy'),
						'placeholder' => 'UA-XXXXX-Y'
					),
					array(
						'type' => 'dropdown',
						'way' => 'buttons',
						'name' => 'ga_script_position',
						'data' => array(
							array('header', 'Header'),
							array('footer', 'Footer'),
						),
						'title' => __('Save GA in', 'clearfy'),
						'hint' => __('Select location for the Google Analytics code.', 'clearfy'),
						'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'default' => 'footer'
					),
					array(
						'type' => 'integer',
						'name' => 'ga_adjusted_bounce_rate',
						'title' => __('Use adjusted bounce rate?', 'clearfy'),
						'default' => 0,
						'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'hint' => __('Essentially, you set up an event which is triggered after a user spends a certain amount of time on the landing page, telling Google Analytics not to count these users as bounces. A user may come to your website, find all of the information they need (a phone number, for example) and then leave the site without visiting another page. Without adjusted bounce rate, such a user would be considered a bounce, even though they had a successful experience. By defining a time limit after which you can consider a user to be "engaged," that user would no longer count as a bounce, and you\'d get a more accurate idea of whether they found what they were looking for.', 'clearfy')
					),
					array(
						'type' => 'integer',
						'way' => 'buttons',
						'name' => 'ga_enqueue_order',
						'title' => __('Change enqueue order?', 'clearfy'),
						'default' => 0,
						'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'hint' => __('By default, Google Analytics code is loaded before other scripts and javasscript code, but if you set the value to 100, the GA code will be loaded after all other scripts. By changing the priority, you can set code position on the page.', 'clearfy')
					),
					array(
						'type' => 'checkbox',
						'way' => 'buttons',
						'name' => 'ga_caos_disable_display_features',
						'title' => __('Disable all display features functionality?', 'clearfy'),
						//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'hint' => sprintf(__('Disable all <a href="%s">display features functionality?</a>', 'clearfy'), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features'),
						'default' => false
					),
					array(
						'type' => 'checkbox',
						'way' => 'buttons',
						'name' => 'ga_anonymize_ip',
						'title' => __('Use Anonymize IP? (Required by law for some countries)', 'clearfy'),
						//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'hint' => sprintf(__('Use <a href="%s">Anonymize IP?</a> (Required by law for some countries)', 'clearfy'), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features'),
						'default' => false
					),
					array(
						'type' => 'checkbox',
						'way' => 'buttons',
						'name' => 'ga_track_admin',
						'title' => __('Track logged in Administrators?', 'clearfy'),
						'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'hint' => __('Track logged in Administrators?', 'clearfy'),
						'default' => false
					),
					array(
						'type' => 'checkbox',
						'way' => 'buttons',
						'name' => 'ga_caos_remove_wp_cron',
						'title' => __('Remove script from wp-cron?', 'clearfy'),
						'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
						'hint' => __('Clearfy creates a cron job to daily update Google Analytics cache scripts. After enabling this option, the plugin will not update Google Analytics cache file. Do not use this option if you do not understand why you need it!', 'clearfy'),
						'default' => false
					)
				)
			);

			$form_options = array();

			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_code_clean_form_options', $form_options, $this);
		}
	}