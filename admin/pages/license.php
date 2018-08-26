<?php
	/**
	 * The page License page class.
	 *
	 * @since 1.0.0
	 */
	
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	class WCL_LicensePage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see Wbcr_FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "license";

		/**
		 * Тип страницы - произвольный контент
		 * @var string
		 */
		public $type = "page";

		/**
		 * Иконка страницы
		 * Полный список иконок смотреть тут:
		 * https://developer.wordpress.org/resource/dashicons/#admin-network
		 * @var string
		 */
		public $page_menu_dashicon = 'dashicons-admin-network';

		/**
		 * Позиция закладки в меню плагина.
		 * 0 - в самом конце, 100 - в самом начале
		 * @var int
		 */
		public $page_menu_position = 0;

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('License', 'clearfy');

			parent::__construct($plugin);

			$this->plugin = $plugin;
			$this->hooks();
		}

		/**
		 * [MAGIC] Magic method that configures assets for a page.
		 */
		public function assets($scripts, $styles)
		{
			parent::assets($scripts, $styles);

			$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/license-manager.css');
			$this->scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/license-manager.js');
		}
		
		public function hooks()
		{
			add_action('wp_ajax_wbcr_clr_licensing', array($this, 'ajax'));
			add_action('wbcr_clr_license_autosync', array($this, 'autoSync'));
			if( !wp_next_scheduled('wbcr_clr_license_autosync') ) {
				wp_schedule_event(time(), 'twicedaily', 'wbcr_clr_license_autosync');
			}
			add_filter('site_transient_update_plugins', array($this, 'updateFreemiusAddons'));
			add_action('wbcr_factory_pages_000_imppage_print_all_notices', array($this, 'printUpdateNotice'), 10, 2);
			add_action('after_plugin_row_clearfy/clearfy.php', array($this, 'addonsUpdateMessage'), 100, 3);
		}

		/**
		 * @param WCL_Plugin $plugin
		 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
		 * @return bool
		 */
		public function printUpdateNotice($plugin, $obj)
		{
			// выводим уведомление везде, кроме страницы компонентов. Там выводится отдельно.
			if( ($this->plugin->getPluginName() != $plugin->getPluginName()) || ($obj->id == 'components') ) {
				return false;
			}
			$package_plugin = WCL_Package::instance();
			$package_update_notice = $package_plugin->getUpdateNotice();
			
			if( $package_update_notice ) {
				if( $obj->id != 'quick_start' ) {
					$obj->scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/update-package.js');
				}
				$obj->printWarningNotice($package_update_notice);
			}
		}
		
		public function updateFreemiusAddons($transient)
		{
			if( empty($transient->checked) ) {
				return $transient;
			}
			
			$package_plugin = WCL_Package::instance();
			if( !$package_plugin->isActive() ) {
				return $transient;
			}
			$need_update_package = $package_plugin->isNeedUpdate();
			$need_update_addons = $package_plugin->isNeedUpdateAddons();
			$info = $package_plugin->info();
			if( $need_update_package and $need_update_addons ) {
				$update_data = new stdClass();
				$update_data->slug = $info['plugin_slug'];
				$update_data->plugin = $info['plugin_basename'];
				$update_data->new_version = '1.1';
				$update_data->package = $package_plugin->downloadUrl();
				//$res->compatibility = new stdClass();
				$transient->response[$update_data->plugin] = $update_data;
			}

			return $transient;
		}
		
		public function addonsUpdateMessage($plugin_file, $plugin_data, $status)
		{
			$package_plugin = WCL_Package::instance();
			$need_update_package = $package_plugin->isNeedUpdate();

			if ( $need_update_package ) {
				if ( $package_plugin->isNeedUpdateAddons() ) {
					$package_plugin_info = $package_plugin->info();
					$update_link = ' <a href="#" data-wpnonce="' . wp_create_nonce( 'package' ) . '" data-loading="'. __( 'Update in progress...', 'clearfy' ) .'" data-ok="'. __( 'Components have been successfully updated!', 'clearfy' ) .'" class="wbcr-clr-plugin-update-link">' . __( 'update now', 'clearfy' ) . '</a>';
					printf(
							'<tr class="plugin-update-tr active update">

								
								<td colspan="3" class="plugin-update colspanchange">
									<div class="update-message notice inline notice-warning notice-alt" style="background-color:#f5e9f5;border-color: #dab9da;">
										<p>%s</p>
									</div>
								</td>

							</tr>',
							__( 'Updates are available for one of the components.', 'clearfy' ) . $update_link
					);
				}
			}
		}
		
		public function ajax()
		{
			check_admin_referer('license');
			$license_action = isset($_POST['license_action'])
				? $_POST['license_action']
				: false;
			$actions = array(
				'activate',
				'deactivate',
				'sync',
				'unsubscribe'
			);
			if( in_array($license_action, $actions) ) {
				$method_name = $license_action . 'AjaxHandler';
				$this->{$method_name}();
			}
			die();
		}
		
		public function autoSync()
		{
			$licensing = WCL_Licensing::instance();
			$notice = $licensing->sync();
		}

		/**
		 * Метод печатает html содержимое страницы
		 * @return void
		 */
		public function showPageContent()
		{
			?>
			<?php wp_nonce_field('license'); ?>
			<div id="wcl-license-wrapper" data-loader="<?php echo WCL_PLUGIN_URL . '/admin/assets/img/loader.gif'; ?>">
				<?php $this->showLicenseForm(); ?>
			</div>

		<?php
		}
		
		public function showLicenseForm($notice = false)
		{
			$licensing = WCL_Licensing::instance();
			$storage = $licensing->getStorage();
			$license = $storage->get('license');
			// Тип лицензии, цветовое оформление для формы лицензирования
			// free - бесплатная
			// gift - пожизненная лицензия, лицензия на особых условиях
			// trial - красный цвет, применяется для триалов, если лиценизия истекла или заблокирована
			// paid - обычная оплаченная лицензия, в данный момент активна.
			$license_type = 'free';
			// Лицензионный ключ
			$license_key = '';
			// Тарифный план
			$plan = 'free';
			$premium = false;
			$has_key = false;
			// Сколько осталось дней до истечения лицензии
			$remained = 999;
			$subscribe = false;
			if( isset($license->id) ) {
				$subscribe = true;
				$license_type = 'paid';
				$activated = $license->activated;
				$quota = $license->quota;
				// Лицензионный ключ
				$license_key = substr_replace($license->secret_key, '******', 15, 6);
				// Тарифный план
				$plan = $license->plan_title;
				$premium = true;
				$has_key = true;
				// Сколько осталось дней до истечения лицензии
				$remained = $license->remainingDays();
				if( 1 == $license->billing_cycle ) {
					$billing = 'month';
				}
				if( 12 == $license->billing_cycle ) {
					$billing = 'year';
				}
				if( 0 == $license->billing_cycle ) {
					$billing = 'lifetime';
				}
				if( $license->is_lifetime() ) {
					$billing = 'lifetime';
					$license_type = 'gift';
					$quota = 999;
				}
				if( is_null($license->billing_cycle) ) {
					$billing = 'month';
					$subscribe = false;
				}
			}
			
			if( $remained < 1 ) {
				$license_type = 'trial';
			}

			
			?>
			<div class="factory-bootstrap-000 onp-page-wrap <?= $license_type ?>-license-manager-content" id="license-manager">
				<div>
					<h3><?php _e('Activation Clearfy Business', 'clearfy') ?></h3>

					<p style="font-size: 16px;"><?php _e('<b>Clearfy Business</b> is a paid package of components for the popular free WordPress plugin named Clearfy. You get access to all paid components at one price.', 'clearfy')?></p>

					<p style="font-size: 16px;"><?php _e('Paid license guarantees that you can download and update existing and future paid components of the plugin.', 'clearfy')?></p>
				</div>
				<br>
				<?php if( is_wp_error($notice) ) : ?>
					<div class="license-message <?= $license_type ?>-license-message">
						<div class="alert <?php echo esc_attr($notice->get_error_code()); ?>">
							<h4 class="alert-heading"><?php _e($notice->get_error_message(), 'clearfy') ?></h4>
						</div>
					</div>
				<?php endif; ?>

				<div class="onp-container">
					<div class="license-details">
						<a href="<?= $this->plugin->getAuthorSitePageUrl('pricing', 'license_page') ?>" class="purchase-premium" target="_blank" rel="noopener">
                            <span class="btn btn-gold btn-inner-wrap">
                            <i class="fa fa-star"></i> <?php printf(__('Upgrade to Premium for $%s', 'clearfy'), '19') ?>
	                            <i class="fa fa-star"></i>
                            </span>
						</a>

						<p><?php printf(__('Your current license for %1$s:', 'clearfy'), $this->plugin->getPluginTitle()) ?></p>

						<div class="license-details-block <?= $license_type ?>-details-block">
							<?php if( $has_key ) { ?>
								<a data-action="deactivate" href="#" class="btn btn-default btn-small license-delete-button wcl-control-btn"><i class="icon-remove-sign"></i> <?php _e('Delete Key', 'clearfy') ?>
								</a>
								<a data-action="sync" href="#" class="btn btn-default btn-small license-synchronization-button wcl-control-btn"><i class="icon-remove-sign"></i> <?php _e('Synchronization', 'clearfy') ?>
								</a>
							<?php } ?>

							<h3>
								<?= ucfirst($plan); ?>
								<?php if( $premium and $subscribe ) { ?>
									<span style="font-size: 15px;"><?php printf(__('(Automatic renewal, every %s',''), esc_attr($billing)); ?>)</span>
								<?php } ?>
							</h3>

							<?php if( $has_key ) { ?>
								<div class="license-key-identity"><code><?= esc_attr($license_key) ?></code></div>
							<?php } ?>

							<div class="license-key-description">
								<p><?php _e('Public License is a GPLv2 compatible license allowing you to change and use this version of the plugin for free. Please keep in mind this license covers only free edition of the plugin. Premium versions are distributed with other type of a license.', 'clearfy') ?>
								</p>
								<?php if( $premium and $subscribe and $license->billing_cycle ) { ?>
									<p class="activate-trial-hint">
										<?php _e('You use a paid subscription for the plugin updates. In case you don’t want to receive paid updates, please, click <a data-action="unsubscribe" class="wcl-control-btn" href="#">cancel subscription</a>', 'clearfy') ?>
									</p>
								<?php } ?>
								<?php if( $remained < 1 ) { ?>
									<p class="activate-error-hint">
										<?php printf(__('Your license has expired, please extend the license to get updates and support.', 'clearfy'), '') ?>
									</p>
								<?php } ?>
							</div>
							<table class="license-params" colspacing="0" colpadding="0">
								<tr>
									<!--<td class="license-param license-param-domain">
										<span class="license-value"><?php echo esc_attr($_SERVER['SERVER_NAME']); ?></span>
										<span class="license-value-name"><?php _e('domain', 'clearfy') ?></span>
									</td>-->
									<td class="license-param license-param-days">
										<span class="license-value"><?= $plan ?></span>
										<span class="license-value-name"><?php _e('plan', 'clearfy') ?></span>
									</td>
									<?php if( $premium ) : ?>
										<td class="license-param license-param-sites">
											<span class="license-value"><?php echo esc_attr($activated); ?> <?php _e('of', 'clearfy') ?> <?php echo esc_attr($quota); ?></span>
											<span class="license-value-name"><?php _e('active sites', 'clearfy') ?></span>
										</td>
									<?php endif; ?>
									<td class="license-param license-param-version">
										<span class="license-value"><?= $this->plugin->getPluginVersion() ?>
											<small><?php _e('version', 'clearfy') ?></small></span>
										<span class="license-value-name"><span><?php _e('up-to-date', 'clearfy') ?></span></span>
									</td>
									<?php if( $premium ) { ?>
										<td class="license-param license-param-days">
											<?php if( $remained < 1 ) { ?>
												<span class="license-value"><?php _e('EXPIRED!', 'clearfy') ?></span>
												<span class="license-value-name"><?php _e('please update the key', 'clearfy') ?></span>
											<?php } else { ?>
												<span class="license-value">
													<?php
														if( $billing == 'lifetime' ) {
															$remained = 'infiniate';
														}
													?>
													<?= $remained ?>
													<small> <?php _e('day(s)', 'clearfy') ?></small>
                                             </span>
												<span class="license-value-name"><?php _e('remained', 'clearfy') ?></span>
											<?php } ?>
										</td>
									<?php } ?>
								</tr>
							</table>
						</div>
					</div>
					<div class="license-input">
						<form action="" method="post">
							<?php if ($premium) { ?>
						<p><?php _e('Have a key to activate the premium version? Paste it here:', 'clearfy') ?><p>
						<?php } else { ?>
							<p><?php _e('Have a key to activate the plugin? Paste it here:', 'clearfy') ?>

							<p>
								<?php } ?>

								<button data-action="activate" class="btn btn-default wcl-control-btn" type="button" id="license-submit">
									<?php _e('Submit Key', 'clearfy') ?>
								</button>
							<div class="license-key-wrap">
								<input type="text" id="license-key" name="licensekey" value="" class="form-control"/>
							</div>

							<?php if( $premium ) { ?>
								<p style="margin-top: 10px;">
									<?php printf(__('<a href="%s" target="_blank" rel="noopener">Lean more</a> about the premium version and get the license key to activate it now!', 'clearfy'), $this->plugin->getAuthorSitePageUrl('pricing', 'license_page')) ?>
								</p>
							<?php } else { ?>
								<p style="margin-top: 10px;">
									<?php printf(__('Can’t find your key? Go to <a href="%s" target="_blank" rel="noopener">this page</a> and login using the e-mail address associated with your purchase.', 'clearfy'), $this->plugin->getAuthorSitePageUrl('contact-us', 'license_page')) ?>
								</p>
							<?php } ?>
						</form>
					</div>
				</div>
			</div>

		<?php
		}

		
		public function activateAjaxHandler()
		{
			$license_key = $_POST['licensekey'];
			if( !$license_key ) {
				$this->showLicenseForm();
			} else {
				$licensing = WCL_Licensing::instance();
				$notice = $licensing->activate($license_key);
				$this->showLicenseForm($notice);
			}
		}
		
		public function deactivateAjaxHandler()
		{
			$licensing = WCL_Licensing::instance();
			$notice = $licensing->uninstall();
			$this->showLicenseForm($notice);
		}
		
		public function syncAjaxHandler()
		{
			$licensing = WCL_Licensing::instance();
			$notice = $licensing->sync();
			$this->showLicenseForm($notice);
		}
		
		public function unsubscribeAjaxHandler()
		{
			$licensing = WCL_Licensing::instance();
			$notice = $licensing->unsubscribe();
			$this->showLicenseForm($notice);
		}
	}
