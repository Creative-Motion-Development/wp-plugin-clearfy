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
		}

		/**
		 * [MAGIC] Magic method that configures assets for a page.
		 */
		public function assets($scripts, $styles)
		{
			parent::assets($scripts, $styles);

			$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/license-manager.css');
			//$this->scripts->add(WCL_PLUGIN_DIR . '/adminassets/js/license-manager.js');
		}

		/**
		 * Метод печатает html содержимое страницы
		 * @return void
		 */
		public function showPageContent()
		{
			// Тип лицензии, цветовое оформление для формы лицензирования
			// free - бесплатная
			// gift - пожизненная лицензия, лицензия на особых условиях
			// trial - красный цвет, применяется для триалов, если лиценизия истекла или заблокирована
			// paid - обычная оплаченная лицензия, в данный момент активна.
			$license_type = 'free';
			// Лицензионный ключ
			$license_key = 'sk_qH0!vBo=}j)T5%u25z*p)(Pw75!V{';
			// Тарифный план
			$plan = 'free';
			$premium = false;
			$has_key = false;
			// Сколько осталось дней до истечения лицензии
			$remained = '9999';

			if( $remained < 1 ) {
				$license_type = 'trial';
			}

			$is_show_notices = true;

			$scope = 'delete-key';
			$error_type = 'API';
			$message = 'The license key is not found. Please check the key correctness.';

			?>

			<div class="factory-bootstrap-000 onp-page-wrap <?= $license_type ?>-license-manager-content" id="license-manager">
				<?php if( $is_show_notices ) {
					?>
					<div class="license-message <?= $license_type ?>-license-message">
						<?php $this->showError($error_type, $message, $scope) ?>
						<?php //if( $scope == 'delete-key' ) {
						?>
						<!--<div class="alert alert-normal">
						<strong><?php _e('The key has been deleted successfully.', 'onp_licensing_000') ?></strong>

						<p><?php _e('Please check the <a href="plugins.php">Plugins</a> page and update the plugin to complete deletion if it\'s needed.', 'onp_licensing_000') ?></p>
					</div>-->
						<?php //}
						?>

						<?php // if( $scope == 'reset-license' ) {
						?>
						<div class="alert alert-success">
							<?php _e('Your license details have been reset successfully.', 'onp_licensing_000') ?>
						</div>
						<?php // }
						?>

						<?php //if( $scope == 'check-updates' ) {
						?>
						<div class="alert alert-normal">
							<strong><?php _e('The updates have been checked successfully.', 'onp_licensing_000') ?></strong>
						</div>
						<?php //}
						?>
					</div>
				<?php }
				?>

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
								<a href="<?php $this->actionUrl('deleteKey') ?>" class="btn btn-default btn-small license-delete-button"><i class="icon-remove-sign"></i> <?php _e('Delete Key', 'onp_licensing_000') ?>
								</a>
								<a href="<?php $this->actionUrl('deleteKey') ?>" class="btn btn-default btn-small license-synchronization-button"><i class="icon-remove-sign"></i> <?php _e('Synchronization', 'onp_licensing_000') ?>
								</a>
							<?php } ?>

							<h3>
								<?= ucfirst($plan); ?>
								<?php if( $premium ) { ?>
									(Automatic renewal, every year)
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
										<span class="license-value">domain.com</span>
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
											<?php if( $remained < 1 ) { ?>
												<span class="license-value"><?php _e('EXPIRED!', 'onp_licensing_000') ?></span>
												<span class="license-value-name"><?php _e('please update the key', 'onp_licensing_000') ?></span>
											<?php } else { ?>
												<span class="license-value">
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
						<form action="<?php $this->actionUrl("index") ?>" method="post">
							<?php if ($premium) { ?>
						<p><?php _e('Have a key to activate the premium version? Paste it here:', 'onp_licensing_000') ?><p>
						<?php } else { ?>
						<p><?php _e('Have a key to activate the plugin? Paste it here:', 'onp_licensing_000') ?><p>
								<?php } ?>

								<a href="#" class="btn btn-default" id="license-submit">
									<?php _e('Submit Key', 'onp_licensing_000') ?>
								</a>

							<div class="license-key-wrap">
								<input type="text" id="license-key" name="licensekey" value="<?php echo $license_key; ?>" class="form-control"/>
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

		/**
		 * Show error from got from the License Server.
		 *
		 * @since 1.0.5
		 * @param WP_Error $error An error to show.
		 */
		private function showError($errorSource, $message, $action = null)
		{
			if( empty($errorSource) ) {
				return;
			}


			?>

			<?php if( $errorSource == 'API' ) { ?>
			<div class="alert alert-danger">
				<h4 class="alert-heading"><?php _e('The request has been rejected by the Licensing Server', 'onp_licensing_000') ?></h4>

				<p><?php echo $message ?></p>
			</div>
		<?php } elseif( $errorSource == 'HTTP' ) { ?>
			<div class="alert alert-danger">
				<h4 class="alert-heading"><?php _e('Unable to connect to the Licensing Server', 'onp_licensing_000') ?></h4>

				<p><?php echo $message ?></p>

				<p>
					<?php if( $action == 'submit-key' ) {
						printf(__('Please <a href="%s">click here</a> for trying to activate your key manualy.', 'onp_licensing_000'), $this->getActionUrl('activateKeyManualy', array('key' => $_POST['licensekey'])));
					} elseif( $action == 'trial' ) {
						printf(__('Please <a href="%s">click here</a> for trying to activate your trial manualy.', 'onp_licensing_000'), $this->getActionUrl('activateTrialManualy'));
					} elseif( $action == 'delete-key' ) {
						printf(__('Please <a href="%s">click here</a> for trying to delete key manualy.', 'onp_licensing_000'), $this->getActionUrl('deleteKeyManualy'));
					} else {
						?>
						<i><?php _e('Please contact OnePress support if you need to perform this action.', 'onp_licensing_000') ?></i>
					<?php
					} ?>
				</p>
			</div>
		<?php } else { ?>
			<div class="alert alert-danger">
				<h4 class="alert-heading"><?php _e('Unable to apply the specified key', 'onp_licensing_000') ?></h4>

				<p><?php echo $message ?></p>
			</div>
		<?php } ?>

		<?php
		}
	}