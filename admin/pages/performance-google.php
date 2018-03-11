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
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Шрифты и карты</strong>.', 'clearfy') . '<p>' . __('Google шрифты и карты очень сильно влияют на скорость загрузки вашего сайта, используйте настройки ниже, чтобы отключить или оптимизировать Google шрифты и карты.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'lazy_load_google_fonts',
				'title' => __('Google Fonts asynchronous', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('По умолчанию Wordpress загружает google шрифты синхронно, то есть ваша страница не будет полностью загружена, пока не будут загружены google шрифты. Такой алгоритм работы замедляет загрузку вашей страницы и создает ошибки в google page speed. Используя эту опцию, ваши google шрифты будут загружаться после полной загрузки вашей страницы, но есть и минусы этой функции, вы будете визуально видеть подмену шрифтов по умолчанию, на ваш подгружаемый шрифт.', 'clearfy'),
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
				'hint' => __('По умолчанию, опция "Отключить google карты" удаляет теги scripts из исходного кода страницы. Но вы также можете вырезать все iframe вставки включив эту опцию.', 'clearfy'),
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
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Кеширование Google Аналитики</strong>.', 'clearfy') . '<p>' . __('В основном кеширование аналитики нужно для улучшения показателей Google Page speed, но также это может немного улучшить скорость загрузки вашей страницы, так как js файлы аналитики будут подгружаться из локального хранилища. Второй случай, когда вам могут понадобиться эти настройки, типично подключение Google аналитики на страницы вашего сайта, то есть вам не нужно это делать с помощью других плагинов или грубо вставть код аналитики в ваш шаблон темы.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'ga_cache',
				'title' => __('Google analytic cache', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Если включить эту опцию, плагин начнет сохранять локальную копию google аналитики, чтобы ускорить загрузку вашей страницы и улучшить показатели Google Page speed', 'clearfy') . '<br>--<br><span class="hint-warnign-color">' . __('Предупреждение! Перед использованием этой опции, пожалуйста удалите ранее установленный код Google аналитики в вашей теме или связанные с этой настройкой плагины!', 'clearfy') . '</span>',
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
						'hint' => __('Установите код отслеживания Google Аналитики.', 'clearfy'),
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
						'hint' => __('Выберите место размещения кода Google Аналитики.', 'clearfy'),
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
						'hint' => __('По умолчанию код Google Аналитики загружается раньше остальных скриптов и javasscript кода, но если вы установите к примеру значение 100, то код аналитики будет загружен после всех остальных скриптов. Изменяя приоритет, вы можете сортировать положение кода на странице.', 'clearfy')
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
						'hint' => __('Плагин создает cron задание, чтобы ежедневно обновлять кеш скриптов Google аналитики, включив эту опцию, плагин не будет обновлять файл Google аналитики. Не используйте эту опцию, если вы не понимаете, для чего вам это нужно!', 'clearfy'),
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