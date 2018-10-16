<?php
	/**
	 * Admin boot
	 * @author Webcraftic <alex.kovalevv@gmail.com>
	 * @copyright Webcraftic 25.05.2017
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	function wbcr_dan_test1()
	{
		?>
		<div class="notice notice-warning is-dismissible">
			ЭТО ТЕСТОВОЕ УВЕДОМЛЕНИЕ ДЛЯ ТЕСТИРОВАНИЯ DISABLE ADMIN NOTICES
		</div>
	<?php
	}

	add_action('network_admin_notices', 'wbcr_dan_test1');
	add_action('admin_notices', 'wbcr_dan_test1');

	function wbcr_dan_test2()
	{
		?>
		<div class="notice notice-warning is-dismissible">
			ЭТО ВТОРОЕ ТЕСТОВОЕ УВЕДОМЛЕНИЕ ДЛЯ ТЕСТИРОВАНИЯ DISABLE ADMIN NOTICES
		</div>
	<?php
	}

	add_action('network_admin_notices', 'wbcr_dan_test2');
	add_action('admin_notices', 'wbcr_dan_test2');

	/**
	 * Этот код обманывает Wordpress, убеждая его, что плагин имеет новую версию,
	 * из-за чего Wordpress создает уведомление об обновлении плагина. Все это необходимо
	 * для обновления пакета компонентов
	 * @param mixed $transient - value of site transient.
	 */
	add_filter('site_transient_update_plugins', function ($transient) {
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
			$transient->response[$update_data->plugin] = $update_data;
		}

		return $transient;
	});

	/**
	 * Выводит уведомление внутри интерфейса Clearfy, на всех страницах плагина.
	 * Это необходимо, чтоб напомнить пользователю обновить конфигурацию компонентов плагина,
	 * иначе вновь активированные компоненты не будет зайдествованы в работе плагина.
	 *
	 * @param Wbcr_Factory000_Plugin $plugin
	 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
	 * @return bool
	 */
	add_action('wbcr/factory/pages/impressive/print_all_notices', function ($plugin, $obj) {
		// выводим уведомление везде, кроме страницы компонентов. Там выводится отдельно.
		if( (WCL_Plugin::app()->getPluginName() != $plugin->getPluginName()) || ($obj->id == 'components') ) {
			return false;
		}
		$package_plugin = WCL_Package::instance();
		$package_update_notice = $package_plugin->getUpdateNotice();

		if( $package_update_notice ) {
			$obj->printWarningNotice($package_update_notice);
		}
	}, 10, 2);

	/**
	 * Выводит уведомление в стройке плагина Clearfy (на странице плагинов), что нужно обновить пакет компонентов.
	 *
	 * @see WP_Plugins_List_Table
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $status
	 * @return bool
	 */
	add_action('after_plugin_row_clearfy/clearfy.php', function ($plugin_file, $plugin_data, $status) {
		$package_plugin = WCL_Package::instance();
		$need_update_package = $package_plugin->isNeedUpdate();

		if( $need_update_package ) {
			if( $package_plugin->isNeedUpdateAddons() ) {
				$update_link = ' <a href="#" data-wpnonce="' . wp_create_nonce('package') . '" data-loading="' . __('Update in progress...', 'clearfy') . '" data-ok="' . __('Components have been successfully updated!', 'clearfy') . '" class="wbcr-clr-plugin-update-link">' . __('update now', 'clearfy') . '</a>';
				?>
				<tr class="plugin-update-tr active update">
					<td colspan="3" class="plugin-update colspanchange">
						<div class="update-message notice inline notice-warning notice-alt" style="background-color:#f5e9f5;border-color: #dab9da;">
							<p><?= __('Updates are available for one of the components.', 'clearfy') . $update_link; ?></p>
						</div>
					</td>
				</tr>
			<?php
			}
		}
	}, 100, 3);

	/**
	 * @param $form
	 * @param Wbcr_Factory000_Plugin $plugin
	 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
	 */
	function wbcr_clearfy_multisite_before_save($form, $plugin, $obj)
	{
		if( onp_build('premium') ) {
			if( WCL_PLUGIN_DEBUG ) {
				return;
			}
		}

		if( is_multisite() && WCL_Plugin::app()->isNetworkActive() && $plugin->getPluginName() == WCL_Plugin::app()->getPluginName() ) {
			$obj->redirectToAction('multisite-pro');
		}
	}

	add_action('wbcr_factory_000_imppage_before_form_save', 'wbcr_clearfy_multisite_before_save', 10, 3);

	/**
	 * Устанавливает логотип Webcraftic и сборку плагина для Clearfy и всех его компонентов
	 *
	 * @param string $title
	 * @since 1.4.0
	 */
	function wbcr_clearfy_branding($title)
	{
		$licensing = WCL_Licensing::instance();

		return 'Webcraftic Clearfy ' . ($licensing->isLicenseValid() ? '<span class="wbcr-clr-logo-label wbcr-clr-premium-label-logo">' . __('Business', 'clearfy') . '</span>' : '<span class="wbcr-clr-logo-label wbcr-clr-free-label-logo">Free</span>') . ' ver';
	}

	add_action('wbcr/factory/pages/impressive/plugin_title', 'wbcr_clearfy_branding');

	/**
	 * Подключаем скрипты отвественные за обновления пакетов для Clearfy
	 * Скрипты подключа.тся на каждой странице Clearfy и его компонентов
	 *
	 * @param string $page_id
	 * @param Wbcr_Factory000_ScriptList $scripts
	 * @param Wbcr_Factory000_StyleList $styles
	 * @since 1.4.0
	 */
	function wbcr_clearfy_enqueue_global_scripts($page_id, $scripts, $styles)
	{
		$scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/update-package.js', array(
			'jquery',
			'wbcr-clearfy-global'
		));
	}

	add_action('wbcr/clearfy/page_assets', 'wbcr_clearfy_enqueue_global_scripts', 10, 3);

	/**
	 * Выводит уведомление, что нужно сбросить постоянные ссылки.
	 * Уведомление будет показано на всех страницах Clearfy и его компонентах.
	 *
	 * @param WCL_Plugin $plugin
	 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
	 * @return bool
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

	add_action('wbcr_factory_000_imppage_after_form_save', 'wbcr_clearfy_after_form_save', 10, 2);

	/**
	 * This proposal to download new components from the team Webcraftic,
	 * all components are installed without reloading the page, if the components are already installed,
	 * then this notice will be hidden.
	 *
	 * @param $notices
	 * @return mixed|void
	 */
	function wbcr_clearfy_admin_notices($notices, $plugin_name)
	{
		if( $plugin_name != WCL_Plugin::app()->getPluginName() ) {
			return $notices;
		}

		if( is_plugin_active('wp-disable/wpperformance.php') ) {
			$default_notice = WCL_Plugin::app()->getPluginTitle() . ': ' . __('We found that you have the plugin %s installed. The functions of this plugin already exist in %s. Please deactivate plugin %s to avoid conflicts between plugins functions.', 'clearfy');
			$default_notice .= ' ' . __('If you do not want to deactivate the plugin %s for some reason, we strongly recommend do not use the same plugins functions at the same time!', 'clearfy');

			$notices[] = array(
				'id' => 'clearfy_plugin_conflicts_notice',
				'type' => 'warning',
				'dismissible' => true,
				'dismiss_expires' => 0,
				'text' => '<p>' . sprintf($default_notice, 'WP Disable', WCL_Plugin::app()->getPluginTitle(), 'WP Disable', 'WP Disable') . '</p>'
			);
		}

		$new_external_componetns = array(
			array(
				'name' => 'robin-image-optimizer',
				'base_path' => 'robin-image-optimizer/robin-image-optimizer.php',
				'type' => 'wordpress',
				'title' => __('Robin image optimizer – saves your money on image optimization!', 'clearfy'),
				'description' => '<br><span><b>' . __('Our new component!', 'clearfy') . '</b> ' . __('We’ve created a fully free solution for image optimization, which is as good as the paid products. The plugin optimizes your images automatically, reducing their weight with no quality loss.', 'clearfy') . '</span><br>'
			),
			array(
				'name' => 'hide_login_page',
				'base_path' => 'hide-login-page/hide-login-page.php',
				'type' => 'wordpress',
				'title' => __('Hide login page (Reloaded) – hides your login page!', 'clearfy'),
				'description' => '<br><span> <b style="color:red;">' . __('Attention! If you’ve ever used features associated with hiding login page, then, please, re-activate this component.', 'clearfy') . '</b><br> ' . __('This simple module changes the login page URL to a custom link quickly and safely. The plugin requires installation.', 'clearfy') . '</span><br>'
			),
			array(
				'name' => 'webcraftic-hide-my-wp',
				'type' => 'freemius',
				'title' => __('Hide my wp (Premium) – hides your WordPress from hackers and bots!', 'clearfy'),
				'description' => '<br><span><b>' . __('Our new component! ', 'clearfy') . '</b>' . __('This premium component helps in hiding your WordPress from hackers and bots. Basically, it disables identification of your CMS by changing directories and files names, removing meta data and replacing HTML content which can provide all information about the platform you use.
Most websites can be hacked easily, as hackers and bots know all security flaws in plugins, themes and the WordPress core. You can secure the website from the attack by hiding the information the hackers will need.
', 'clearfy') . '</span><br>'
			),
			/*array(
				'name' => 'minify_and_combine',
				'type' => 'internal',
				'title' => __('Minify and Combine (JS, CSS) – optimizes your scripts and styles!', 'clearfy'),
				'description' => '<br><span><b>' . __('Our new component! ', 'clearfy') . '</b> ' . __('This component combines all your scripts and styles in one file, compresses & caches it. ', 'clearfy') . '
</span><br>'
			),
			array(
				'name' => 'html_minify',
				'type' => 'internal',
				'title' => __('Html minify (Reloaded) – reduces the amount of code on your pages!', 'clearfy'),
				'description' => '<br><span><b>' . __('Our new component! ', 'clearfy') . '</b> ' . __('We’ve completely redesigned HTML compression of the pages and added these features to another component. It’s more stable and reliable solution for HTML code optimization of your pages.', 'clearfy') . '</span><br>'
			),*/
		);

		$need_show_new_components_notice = false;

		$new_component_notice_text = '<div>';
		$new_component_notice_text .= '<h3>' . __('Welcome to Clearfy!', 'clearfy') . '</h3>';
		$new_component_notice_text .= '<p>' . __('We apologize for the delay in updates!', 'clearfy') . ' ';
		$new_component_notice_text .= __('Our team has spent a lot of time designing new, useful, and the most important – free! – features of the Clearfy plugin! ', 'clearfy') . ' ';
		$new_component_notice_text .= __('Now it is time to try it.', 'clearfy') . '</p>';

		foreach($new_external_componetns as $new_component) {
			$slug = $new_component['name'];

			if( $new_component['type'] == 'wordpress' ) {
				$slug = $new_component['base_path'];
			}
			$install_button = WCL_Plugin::app()->getInstallComponentsButton($new_component['type'], $slug);

			if( $install_button->isPluginActivate() ) {
				continue;
			}

			$premium_class = $new_component['name'] == 'webcraftic-hide-my-wp' ? ' wbcr-clr-premium' : '';

			$new_component_notice_text .= '<div class="wbcr-clr-new-component' . $premium_class . '">';
			$new_component_notice_text .= '<h4>' . $new_component['title'] . '</h4>';
			$new_component_notice_text .= $new_component['description'];
			$new_component_notice_text .= $install_button->getButton();
			$new_component_notice_text .= '</div>';

			$need_show_new_components_notice = true;
		}

		$new_component_notice_text .= '</div>';

		if( $need_show_new_components_notice ) {
			$notices[] = array(
				'id' => 'clearfy_plugin_install_new_components_notice',
				'type' => 'warning',
				'dismissible' => true,
				'dismiss_expires' => 0,
				'text' => $new_component_notice_text
			);
		}

		return apply_filters('wbcr_clearfy_admin_notices', $notices);
	}

	add_filter('wbcr_factory_notices_000_list', 'wbcr_clearfy_admin_notices', 10, 2);

	/**
	 * Fake stubs for the Clearfy plugin board
	 */
	function wbcr_clearfy_fake_boards()
	{
		if( !defined('WIO_PLUGIN_ACTIVE') ) {
			require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
			$install_button = new WCL_InstallPluginsButton('wordpress', 'robin-image-optimizer/robin-image-optimizer.php');

			?>
			<div class="col-sm-12">
				<div class="wbcr-clearfy-fake-image-optimizer-board wbcr-clearfy-board">
					<h4 class="wio-text-left"><?php _e('Images optimization', 'image-optimizer'); ?></h4>

					<div class="wbcr-clearfy-fake-widget">
						<div class="wbcr-clearfy-widget-overlay">
							<img src="<?= WCL_PLUGIN_URL ?>/admin/assets/img/robin-image-optimizer-fake-board.png" alt=""/>
						</div>
						<?php $install_button->renderButton(); ?>
					</div>
				</div>
			</div>
		<?php
		}
	}

	add_action('wbcr_clearfy_quick_boards', 'wbcr_clearfy_fake_boards');

	/**
	 * Widget with the offer to buy Clearfy Business
	 *
	 * @param array $widgets
	 * @param string $position
	 * @param Wbcr_Factory000_Plugin $plugin
	 */
	function wbcr_clearfy_donate_widget($widgets, $position, $plugin)
	{
		if( $plugin->getPluginName() == WCL_Plugin::app()->getPluginName() ) {

			$licensing = WCL_Licensing::instance();

			if( $licensing->isLicenseValid() ) {
				unset($widgets['donate_widget']);
				unset($widgets['businnes_suggetion']);

				return $widgets;
			}

			if( $position == 'bottom' ) {
				$buy_premium_url = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl(WCL_Plugin::app()->getPluginName(), 'pricing', 'license_page');
				$upgrade_price = WbcrFactoryClearfy000_Helpers::getClearfyBusinessPrice();

				ob_start();
				?>
				<div id="wbcr-clr-go-to-premium-widget" class="wbcr-factory-sidebar-widget">
					<p>
						<strong><?php _e('Activation Clearfy Business', 'clearfy'); ?></strong>
					</p>

					<div class="wbcr-clr-go-to-premium-widget-body">
						<p><?php _e('<b>Clearfy Business</b> is a paid package of components for the popular free WordPress plugin named Clearfy. You get access to all paid components at one price.', 'clearfy') ?></p>

						<p><?php _e('Paid license guarantees that you can download and update existing and future paid components of the plugin.', 'clearfy') ?></p>
						<a href="<?= $buy_premium_url ?>" class="wbcr-clr-purchase-premium" target="_blank" rel="noopener">
                        <span class="btn btn-gold btn-inner-wrap">
                        <i class="fa fa-star"></i> <?php printf(__('Upgrade to Clearfy Business for $%s', 'clearfy'), $upgrade_price) ?>
	                        <i class="fa fa-star"></i>
                        </span>
						</a>
					</div>
				</div>
				<?php

				$widgets['donate_widget'] = ob_get_contents();

				ob_end_clean();
			}
		}

		return $widgets;
	}

	add_filter('wbcr/factory/pages/impressive/widgets', 'wbcr_clearfy_donate_widget', 10, 3);

