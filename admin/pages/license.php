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
		
		public function hooks() {
			add_action( 'wp_ajax_wcl_licensing', array( $this, 'ajax' ) );
			add_action( 'wcl_license_autosync', array( $this, 'autoSync' ) );
			if ( ! wp_next_scheduled( 'wcl_license_autosync' ) ) {
				wp_schedule_event( time(), 'twicedaily', 'wcl_license_autosync' );
			}
		}
		
		public function ajax() {
			check_admin_referer( 'license' );
			$license_action = isset( $_POST['license_action'] ) ? $_POST['license_action'] : false;
			$actions = array(
				'activate',
				'deactivate',
				'sync'
			);
			if ( in_array( $license_action, $actions ) ) {
				$method_name = $license_action . 'AjaxHandler';
				$this->{$method_name}();
			}
			die();
		}
		
		public function autoSync() {
			$licensing = WCL_Licensing::instance();
			$notice = $licensing->sync();
		}

		/**
		 * Метод печатает html содержимое страницы
		 * @return void
		 */
		public function showPageContent() {
			?>
				<?php wp_nonce_field( 'license' ); ?>
				<div id="wcl-license-wrapper" data-loader="<?php echo WCL_PLUGIN_URL . '/admin/assets/img/loader.gif'; ?>">
					<?php $this->showLicenseForm(); ?>
				</div>
			
			<?php
		}
		
		public function showLicenseForm( $notice = false )
		{
			$licensing = WCL_Licensing::instance();
			$storage = $licensing->getStorage();
			$license = $storage->get( 'license' );
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
			if ( isset( $license->id ) ) {
				$subscribe = true;
				$license_type = 'paid';
				// Лицензионный ключ
				$license_key = substr_replace( $license->secret_key, '******', 15, 6 );
				// Тарифный план
				$plan = $license->plan_title;
				$premium = true;
				$has_key = true;
				// Сколько осталось дней до истечения лицензии
				$remained = $license->remainingDays();
				if ( 1 == $license->billing_cycle ) {
					$billing = 'month';
				}
				if ( 12 == $license->billing_cycle ) {
					$billing = 'year';
				}
				if ( 0 == $license->billing_cycle ) {
					$billing = 'lifetime';
				}
				if ( $license->is_lifetime() ) {
					$billing = 'lifetime';
					$license_type = 'gift';
				}
				if ( is_null( $license->billing_cycle ) ) {
					$billing = 'month';
					$subscribe = false;
				}
			}
			

			if( $remained < 1 ) {
				$license_type = 'trial';
			}

			
			?>
			<div class="factory-bootstrap-000 onp-page-wrap <?= $license_type ?>-license-manager-content" id="license-manager">
				<?php if ( is_wp_error( $notice ) ) : ?>
				<div class="license-message <?= $license_type ?>-license-message">
					<div class="alert <?php echo esc_attr( $notice->get_error_code() ); ?>">
						<h4 class="alert-heading"><?php _e( $notice->get_error_message(), 'onp_licensing_000' ) ?></h4>
					</div>
				</div>
				<?php endif; ?>

				<div class="onp-container">
					<div class="license-details">
						<a href="" id="purchase-premium">
                            <span class="btn btn-gold btn-inner-wrap">
                            <i class="fa fa-star"></i> <?php _e('Upgrade to Premium', 'onp_licensing_000') ?>
	                            <i class="fa fa-star"></i>
                            </span>
						</a>

						<p><?php printf(__('Your current license for %1$s:', 'onp_licensing_000'), $this->plugin->getPluginTitle()) ?></p>

						<div class="license-details-block <?= $license_type ?>-details-block">
							<?php if( $has_key ) { ?>
								<a data-action="deactivate" href="#" class="btn btn-default btn-small license-delete-button wcl-control-btn"><i class="icon-remove-sign"></i> <?php _e('Delete Key', 'onp_licensing_000') ?>
								</a>
								<a data-action="sync" href="#" class="btn btn-default btn-small license-synchronization-button wcl-control-btn"><i class="icon-remove-sign"></i> <?php _e('Synchronization', 'onp_licensing_000') ?>
								</a>
							<?php } ?>

							<h3>
								<?= ucfirst($plan); ?>
								<?php if( $premium and $subscribe ) { ?>
									(Automatic renewal, every <?php echo esc_attr( $billing ); ?>)
								<?php } ?>
							</h3>
							<?php if( $has_key ) { ?>
								<div class="licanse-key-identity"><?= $license_key ?></div>
							<?php } ?>

							<div class="licanse-key-description">
								<p><?php _e('Public License is a GPLv2 compatible license allowing you to change and use this version of the plugin for free. Please keep in mind this license covers only free edition of the plugin. Premium versions are distributed with other type of a license.', 'onp_licensing_000') ?>
								</p>
								<?php if( $premium ) { ?>
									<p class="activate-trial-hint">
										<?php printf(__('Вы используете платную подписку на обновления плагина, нажмите <a href="%s">отменить подписку</a>, если вы не хотите больше получать платные обновления.', 'onp_licensing_000'), '') ?>
									</p>
								<?php } ?>
								<?php if( $remained < 1 ) { ?>
									<p class="activate-error-hint">
										<?php printf(__('Ваша лицензия истекла, пожалуйста, продлите лицензию, чтобы получать обновления и поддержку.', 'onp_licensing_000'), '') ?>
									</p>
								<?php } ?>
							</div>
							<table class="license-params" colspacing="0" colpadding="0">
								<tr>
									<td class="license-param license-param-domain">
										<span class="license-value"><?php echo esc_attr( $_SERVER['SERVER_NAME'] ); ?></span>
										<span class="license-value-name"><?php _e('domain', 'onp_licensing_000') ?></span>
									</td>
									<td class="license-param license-param-version">
										<span class="license-value"><?= $this->plugin->getPluginVersion() ?>
											<small><?php _e('version', 'onp_licensing_000') ?></small></span>
										<span class="license-value-name"><span><?php _e('up-to-date', 'onp_licensing_000') ?></span></span>
									</td>
									<td class="license-param license-param-days">
										<span class="license-value"><?= $plan ?></span>
										<span class="license-value-name"><?php _e('plan', 'onp_licensing_000') ?></span>
									</td>

									<?php if( $premium ) { ?>
										<td class="license-param license-param-days">
											<?php if( $remained < 1) { ?>
												<span class="license-value"><?php _e('EXPIRED!', 'onp_licensing_000') ?></span>
												<span class="license-value-name"><?php _e('please update the key', 'onp_licensing_000') ?></span>
											<?php } else { ?>
												<span class="license-value">
													<?php
														if( $billing == 'lifetime' ) {
															$remained = 'infiniate';
														}
													?>
	                                            <?= $remained ?>
													<small> <?php _e('day(s)', 'onp_licensing_000') ?></small>
                                             </span>
												<span class="license-value-name"><?php _e('remained', 'onp_licensing_000') ?></span>
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
						<p><?php _e('Have a key to activate the premium version? Paste it here:', 'onp_licensing_000') ?><p>
						<?php } else { ?>
						<p><?php _e('Have a key to activate the plugin? Paste it here:', 'onp_licensing_000') ?><p>
								<?php } ?>

								<button data-action="activate" class="btn btn-default wcl-control-btn" type="button" id="license-submit">
									<?php _e('Submit Key', 'onp_licensing_000') ?>
								</button>

							<div class="license-key-wrap">
								<input type="text" id="license-key" name="licensekey" value="" class="form-control"/>
							</div>

							<?php if( $premium ) { ?>
								<p style="margin-top: 10px;">
									<?php printf(__('<a href="%1$s">Lean more</a> about the premium version and get the license key to activate it now!', 'onp_licensing_000'), '') ?>
								</p>
							<?php } else { ?>
								<p style="margin-top: 10px;">
									<?php printf(__('Не можете найти свой ключ? Перейдите на <a href="%1$s">эту страницу</a> и авторизуйтесь используя свой email, на который вы делали покупку.', 'onp_licensing_000'), '') ?>
								</p>
							<?php } ?>
						</form>
					</div>
				</div>
			</div>

		<?php
		}

		
		public function activateAjaxHandler() {
			$license_key = $_POST['licensekey'];
			if ( ! $license_key ) {
				$this->showLicenseForm();
			} else {
				$licensing = WCL_Licensing::instance();
				$notice = $licensing->activate( $license_key );
				$this->showLicenseForm( $notice );
			}
		}
		
		public function deactivateAjaxHandler() {
			$licensing = WCL_Licensing::instance();
			$notice = $licensing->uninstall();
			$this->showLicenseForm( $notice );
		}
		
		public function syncAjaxHandler() {
			$licensing = WCL_Licensing::instance();
			$notice = $licensing->sync();
			$this->showLicenseForm( $notice );
		}
	}
