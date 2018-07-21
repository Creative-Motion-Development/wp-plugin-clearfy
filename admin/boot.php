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

	/**
	 * We assets scripts in the admin panel on each page.
	 * @param $hook
	 */
	function wbcr_clearfy_enqueue_global_scripts($hook)
	{
		wp_enqueue_style('wbcr-clearfy-install-addons', WCL_PLUGIN_URL . '/admin/assets/css/install-addons.css', array(), WCL_Plugin::app()
			->getPluginVersion());
		wp_enqueue_script('wbcr-clearfy-install-addons', WCL_PLUGIN_URL . '/admin/assets/js/install-addons.js', array('jquery'), WCL_Plugin::app()
			->getPluginVersion());
	}

	add_action('admin_enqueue_scripts', 'wbcr_clearfy_enqueue_global_scripts');

	/**
	 * Ошибки совместимости с похожими плагинами
	 */
	function wbcr_clearfy_admin_notices($notices)
	{

		if( is_plugin_active('wp-disable/wpperformance.php') ) {
			$default_notice = WCL_Plugin::app()
					->getPluginTitle() . ': ' . __('We found that you have the plugin %s installed. The functions of this plugin already exist in %s. Please deactivate plugin %s to avoid conflicts between plugins functions.', 'clearfy');
			$default_notice .= ' ' . __('If you do not want to deactivate the plugin %s for some reason, we strongly recommend do not use the same plugins functions at the same time!', 'clearfy');

			$notices[] = array(
				'id' => 'clearfy_plugin_conflicts_notice',
				'type' => 'warning',
				'dismissible' => true,
				'dismiss_expires' => 0,
				'text' => '<p>' . sprintf($default_notice, 'WP Disable', WCL_Plugin::app()
						->getPluginTitle(), 'WP Disable', 'WP Disable') . '</p>'
			);
		}

		$plugins = get_plugins();

		$new_external_componetns = array(
			array(
				'name' => 'cyr3lat',
				'base_path' => 'cyr3lat/cyr-to-lat.php',
				'type' => 'wordpress',
				'title' => 'Robin image optimizer',
				'description' => '<span>Мы создали полностью бесплатный компонент оптимизации изображений, у компонента нет лимитов, нет никаких ограничений на оптимизацию изображений.
					 Вам не нужно больше тратить деньги, установите наш оптимизатор изображений в один клик и оптимизируйте свои изображения бесплатно!</span><br>'
			),
			array(
				'name' => 'hide_login_page',
				'base_path' => 'hide-login-page/hide-login-page.php',
				'type' => 'wordpress',
				'title' => 'Hide login page (Reloaded)',
				'description' => '<span>Это не новый компонент, но </span><br>'
			),
			array(
				'name' => 'html_minify',
				'type' => 'internal',
				'title' => 'Html minify (Reloaded)',
				'description' => '<span>Это не новый компонент, но </span><br>'
			),
			array(
				'name' => 'minify_and_combine',
				'type' => 'internal',
				'title' => 'Minify and Combine (JS, CSS)',
				'description' => '<span>Это не новый компонент, но </span><br>'
			),
		);

		$need_show_new_components_notice = false;

		$new_component_notice_text = '<div>';
		$new_component_notice_text .= '<h3>Вас приветствует Clearfy!</h3>';
		$new_component_notice_text .= '<p>Мы приносим свои извинения за задержку обновлений! ';
		$new_component_notice_text .= 'Наша команда потратила много времени на создание новых, полезных, а главное бесплатных функций для плагина Clearfy! ';
		$new_component_notice_text .= 'И вот наступил момент, когда вы можете их попробовать.</p>';

		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';

		foreach($new_external_componetns as $new_component) {
			$slug = $new_component['name'];

			if( $new_component['type'] == 'wordpress' ) {
				$slug = $new_component['base_path'];
			}

			$install_button = new WCL_InstallPluginsButton($new_component['type'], $slug);

			if( $install_button->isPluginActivate() ) {
				continue;
			}
			$new_component_notice_text .= '<div class="wbcr-clr-new-component">';
			$new_component_notice_text .= '<h4>' . $new_component['title'] . '</h4> - ';
			$new_component_notice_text .= $new_component['description'];
			$new_component_notice_text .= $install_button->render(false);
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

	add_filter('wbcr_factory_admin_notices', 'wbcr_clearfy_admin_notices', 10, 2);

	/**
	 * Fake stubs for the Clearfy plugin board
	 */
	function wbcr_clearfy_fake_boards()
	{
		if( !defined('WIO_PLUGIN_ACTIVE') ) {
			require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
			$install_button = new WCL_InstallPluginsButton('wordpress', 'cyr3lat/cyr-to-lat.php');

			//$install_button->removeClass('button');
			//$install_button->removeClass('button-default');
			//$install_button->removeClass('button-primary');
			?>
			<div class="col-sm-12">
				<div class="wbcr-clearfy-fake-image-optimizer-board wbcr-clearfy-board">
					<h4 class="wio-text-left"><?php _e('Images optimization', 'image-optimizer'); ?></h4>

					<div class="wbcr-clearfy-fake-widget">
						<div class="wbcr-clearfy-widget-overlay">
							<img src="<?= WCL_PLUGIN_URL ?>/admin/assets/img/robin-image-optimizer-fake-board.png" alt=""/>
						</div>
						<?php $install_button->render(); ?>
					</div>
				</div>
			</div>
		<?php
		}
	}

	add_action('wbcr_clearfy_quick_boards', 'wbcr_clearfy_fake_boards');
