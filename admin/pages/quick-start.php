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

	class WCL_QuickStartPage extends WCL_Page {
		
		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "quick_start";

		/**
		 * @var string
		 */
		public $page_menu_dashicon = 'dashicons-clock';

		/**
		 * @var int
		 */
		public $page_menu_position = 100;

		/**
		 * @var bool
		 */
		public $internal = false;

		/**
		 * @var string
		 */
		public $menu_target = 'options-general.php';

		/**
		 * @var bool
		 */
		public $add_link_to_plugin_actions = true;

		/**
		 * @var string
		 */
		public $type = 'page';

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Clearfy menu', 'clearfy');
			
			parent::__construct($plugin);

			$this->plugin = $plugin;
		}
		
		public function getMenuTitle()
		{
			return __('Quick start', 'clearfy');
		}
		
		/**
		 * Requests assets (js and css) for the page.
		 *
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function assets($scripts, $styles)
		{
			parent::assets($scripts, $styles);
			
			$this->scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/general.js');
			//для импорта натсроек нужен стрипт обновления пакета.
			$this->scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/update-package.js');
			
			$params = array(
				//'ajaxurl' => admin_url('admin-ajax.php'),
				'flush_cache_url' => $this->getActionUrl('flush-cache-and-rules', array('_wpnonce' => wp_create_nonce('wbcr_factory_' . $this->getResultId() . '_flush_action'))),
				'ajax_nonce' => wp_create_nonce('wbcr_clearfy_ajax_quick_start_nonce'),
			);

			wp_localize_script('jquery', 'wbcr_clearfy_ajax', $params);
		}
		
		/**
		 * Shows the description above the options.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function _showHeader()
		{
			?>
			<div class="wbcr-clearfy-header">
				<?php _e('On this page you can quickly configure the plug-in without going into details.', 'clearfy') ?>
			</div>
		<?php
		}
		
		/**
		 * Collects error and system error data
		 * @return string
		 */
		public function getDebugReport()
		{
			$run_time = number_format(microtime(true), 3);
			$pps = number_format(1 / $run_time, 0);
			$memory_avail = ini_get('memory_limit');
			$memory_used = number_format(memory_get_usage(true) / (1024 * 1024), 2);
			$memory_peak = number_format(memory_get_peak_usage(true) / (1024 * 1024), 2);
			
			$debug = '';
			if( PHP_SAPI == 'cli' ) {
				// if run for command line, display some info
				$debug = PHP_EOL . "======================================================================================" . PHP_EOL . " Config: php " . phpversion() . " " . php_sapi_name() . " / zend engine " . zend_version() . PHP_EOL . " Load: {$memory_avail} (avail) / {$memory_used}M (used) / {$memory_peak}M (peak)" . "  | Time: {$run_time}s | {$pps} req/sec" . PHP_EOL . "  | Server Timezone: " . date_default_timezone_get() . "  | Agent: CLI" . PHP_EOL . "======================================================================================" . PHP_EOL;
			} else {
				// if not run from command line, only display if debug is enabled
				$debug = "" //<hr />"
					. "<div style=\"text-align: left;\">" . "<hr />" . " Config: " . "<br />" . " &nbsp;&nbsp; | php " . phpversion() . " " . php_sapi_name() . " / zend engine " . zend_version() . "<br />" . " &nbsp;&nbsp; | Server Timezone: " . date_default_timezone_get() . "<br />" . " Load: " . "<br />" . " &nbsp;&nbsp; | Memory: {$memory_avail} (avail) / {$memory_used}M (used) / {$memory_peak}M (peak)" . "<br />" . " &nbsp;&nbsp; | Time: {$run_time}s &nbsp;&nbsp; | {$pps} req/sec" . "<br />" . "Url: " . "<br />" . " &nbsp;&nbsp; |" . "<br />" . " &nbsp;&nbsp; | Agent: " . (@$_SERVER["HTTP_USER_AGENT"]) . "<br />" . "Version Control: " . "<br />" . " &nbsp;&nbsp; </div>" . "<br />";
			}
			
			$debug .= "Plugins<br>";
			$debug .= "=====================<br>";
			
			$plugins = get_plugins();

			require_once ABSPATH . '/wp-admin/includes/plugin.php';
			foreach($plugins as $path => $plugin) {
				if( is_plugin_active($path) ) {
					$debug .= $plugin['Name'] . '<br>';
				}
			}
			
			return $debug;
		}
		
		/**
		 * Generates a report about the system and plug-in error
		 * @return string
		 */
		public function gererateReportAction()
		{
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.zip-archive.php');
			
			$reposts_dir = WCL_PLUGIN_DIR . '/reports';
			$reports_temp = $reposts_dir . '/temp';
			
			if( !file_exists($reposts_dir) ) {
				mkdir($reposts_dir, 0777, true);
			}
			
			if( !file_exists($reports_temp) ) {
				mkdir($reports_temp, 0777, true);
			}
			
			$file = fopen($reports_temp . '/site-info.html', 'w+');
			fputs($file, $this->getDebugReport());
			fclose($file);
			
			$download_file_name = 'webcraftic-clearfy-report-' . date('Y.m.d-H.i.s') . '.zip';
			$download_file_path = WCL_PLUGIN_DIR . '/reports/' . $download_file_name;
			
			Wbcr_ExtendedZip::zipTree(WCL_PLUGIN_DIR . '/reports/temp', $download_file_path, ZipArchive::CREATE);
			
			array_map('unlink', glob(WCL_PLUGIN_DIR . "/reports/temp/*"));
			
			wp_redirect(WCL_PLUGIN_URL . '/reports/' . $download_file_name);
			exit;
		}

		public function showPageContent()
		{
			$allow_mods = apply_filters('wbcr_clearfy_allow_quick_mods', array(
				'clear_code' => array('title' => __('One click code clearing', 'clearfy'), 'icon' => 'dashicons-yes'),
				'defence' => array('title' => __('One click security', 'clearfy'), 'icon' => 'dashicons-shield'),
				'seo_optimize' => array(
					'title' => __('One click seo optimization', 'clearfy'),
					'icon' => 'dashicons-star-empty'
				),
				'remove_default_widgets' => array(
					'title' => __('One click remove default Widgets', 'clearfy'),
					'icon' => 'dashicons-networking'
				),
			));

			if( !$this->plugin->isActivateComponent('widget_tools') ) {
				unset($allow_mods['remove_default_widgets']);
			}

			$allow_mods['reset'] = array(
				'title' => __('Reset all settings', 'clearfy'),
				'icon' => 'dashicons-backup',
				'args' => array('flush_redirect' => 1)
			);
			?>
			<div class="wbcr-clearfy-layer"></div>
			<div class="wbcr-clearfy-confirm-popup">
				<h3><?php _e('Are you sure you want to enable the this options?', 'clearfy') ?></h3>
				
				<div class="wbcr-clearfy-reset-warning-message">
					<?php _e('After confirmation, all the settings of the plug-in will return to the default state. Make backup settings by copying data from the export field.', 'clearfy') ?>
				</div>
				<ul class="wbcr-clearfy-list-options"></ul>
				<div class="wbcr-clearfy-popup-buttons">
					<button class="wbcr-clearfy-popup-button-ok"><?php _e('Confirm', 'clearfy') ?></button>
					<button class="wbcr-clearfy-popup-button-cancel"><?php _e('Cancel', 'clearfy') ?></button>
				</div>
			</div>
			
			<div class="wbcr-content-section">
				<div id="wbcr-clearfy-quick-mode-board">
					<p><?php _e('These are quick optimization options for your website. You can activate the groups of necessary settings in one click. With the fast optimization mode, we are enable the only safe settings that do not break your website. That is why we recommend you to look at each setting of the plugin individually. The settings with grey and red question mark will not be active, until you do it yourself.', 'clearfy') ?></p>
					<h4><?php _e('Select what you need to do', 'clearfy') ?></h4>

					<p style="color:#9e9e9e"><?php _e('After selecting any optimization case, the plugin will automatically enable the necessary settings in safe mode and one click.', 'clearfy') ?></p>

					<div class="row">
						<?php foreach($allow_mods as $mode_name => $mode): ?>
							<?php
							$mode_title = $mode;
							$mode_icon = '';
							$mode_args = '';

							if( is_array($mode) ) {
								$mode_title = isset($mode['title'])
									? $mode['title']
									: '';
								$mode_icon = isset($mode['icon'])
									? $mode['icon']
									: '';
								$mode_args = isset($mode['args']) && is_array($mode['args'])
									? WCL_Helper::getEscapeJson($mode['args'])
									: '';
							}
							?>

							<div class="col-sm-12">
								<?php
									$group = WCL_Group::getInstance($mode_name);

									$filter_mode_options = array();
									foreach($group->getOptions() as $option) {
										$filter_mode_options[$option->getName()] = $option->getTitle();
									}

									$print_group_options = WCL_Helper::getEscapeJson($filter_mode_options);
								?>
								<?php if( $mode_name == 'reset' ): ?>
									<h4><?php _e('Reset settings', 'clearfy') ?></h4>
									<p style="color:#9e9e9e"><?php _e('After confirmation, all the settings of the plug-in will return to the default state. Make backup settings by copying data from the export field.', 'clearfy') ?></p>

								<?php endif; ?>
								<div class="wbcr-clearfy-switch wbcr-clearfy-switch-mode-<?= $mode_name ?>" data-mode="<?= $mode_name ?>" data-mode-args="<?= $mode_args ?>" data-mode-options="<?= $print_group_options ?>">
									<?php if( !empty($mode_icon) ): ?>
										<i class="dashicons <?= $mode_icon; ?>"></i>
									<?php endif; ?>
									<span><?= $mode_title ?></span>

									<div class="wbcr-clearfy-switch-confirmation">
										<button class="wbcr-clearfy-button-activate-mode">
											<?php if( $mode_name == 'reset' ): ?>
												<?php _e('Reset', 'clearfy'); ?>
											<?php else: ?>
												<?php _e('Do It!', 'clearfy'); ?>
											<?php endif; ?>
										</button>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class="wbcr-right-sidebar-section">
				<div class="row">
					<div class="col-sm-12">
						<div class="wbcr-clearfy-switch-success-message">
							<?php _e('Settings successfully updated!', 'clearfy') ?>
						</div>
						<div class="wbcr-clearfy-switch-error-message">
							<?php _e('During the setup, an unknown error occurred, please try again or contact the plug-in support.', 'clearfy') ?>
						</div>
					</div>

					<?php do_action('wbcr_clearfy_quick_boards'); ?>

					<div class="col-sm-12">
						<div class="wbcr-clearfy-export-import-board wbcr-clearfy-board">
							<p>
								<label for="wbcr-clearfy-import-export">
									<strong><?php _e('Import/Export settings', 'clearfy') ?></strong>
								</label>
								<textarea id="wbcr-clearfy-import-export"><?= WCL_Helper::getExportOptions(); ?></textarea>
								<button class="button wbcr-clearfy-import-options-button"><?php _e('Import options', 'clearfy') ?></button>
							</p>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="wbcr-clearfy-troubleshooting-board wbcr-clearfy-board">
							<h4><?php _e('Support', 'clearfy') ?></h4>

							<p><?php _e('If you faced with any issues, please follow the steps below to get quickly quality support:', 'clearfy') ?></p>
							<ol>
								<li>
									<p><?php _e('Generate a debug report which will contains inforamtion about your configuratin and installed plugins', 'clearfy') ?></p>

									<p>
										<a href="<?= admin_url('options-general.php?page=quick_start-' . $this->plugin->getPluginName() . '&action=gererate_report'); ?>" class="button"><?php _e('Generate Debug Report', 'clearfy') ?></a>
									</p>
								</li>
								<li>
									<p><?php _e('Send a message to <b>wordpress.webraftic@gmail.com</b> include the debug report into the message body.', 'clearfy'); ?></p>
								</li>
							</ol>
							<p style="margin-bottom: 0px;"><?php _e('We guarantee to respond you within 7 business day.', 'clearfy') ?></p>
						</div>
					</div>
				</div>
			</div>

		<?php
		}
	}
