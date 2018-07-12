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
			'cyr3lat' => array(
				'base_path' => 'cyr3lat/cyr-to-lat.php',
				'title' => 'Robin image optimizer',
				'description' => '<span>Мы создали полностью бесплатный компонент оптимизации изображений, у компонента нет лимитов, нет никаких ограничений на оптимизацию изображений.
					 Вам не нужно больше тратить деньги, установите наш оптимизатор изображений в один клик и оптимизируйте свои изображения бесплатно!</span><br>'
			),
			'hide-login-page' => array(
				'base_path' => 'hide-login-page/hide-login-page.php',
				'title' => 'Hide login page (Reloaded)',
				'description' => '<span>Это не новый компонент, но </span><br>'
			)
		);

		$need_show_new_components_notice = false;

		$new_component_notice_text = '<div>';
		$new_component_notice_text .= '<h3>Вас приветствует Clearfy!</h3>';
		$new_component_notice_text .= '<p>Мы приносим свои извинения за задержку обновлений! ';
		$new_component_notice_text .= 'Наша команда потратила много времени на создание новых, полезных, а главное бесплатных функций для плагина Clearfy! ';
		$new_component_notice_text .= 'И вот наступил момент, когда вы можете их попробовать.</p>';

		foreach($new_external_componetns as $slug => $new_component) {
			if( !isset($plugins[$new_component['base_path']]) || !is_plugin_active($new_component['base_path']) ) {
				$new_component_notice_text .= '<div class="wbcr-clr-new-component">';
				$new_component_notice_text .= '<h4>' . $new_component['title'] . '</h4> - ';
				$new_component_notice_text .= $new_component['description'];

				$button_i18n = array(
					'activate' => __('Activate', 'clearfy'),
					'install' => __('Install', 'clearfy'),
					'deactivate' => __('Deactivate', 'clearfy'),
					'delete' => __('Delete', 'clearfy'),
					'loading' => __('Please wait...', 'clearfy')
				);

				$action = 'activate';

				if( !isset($plugins[$new_component['base_path']]) ) {
					$action = 'install';
				}

				$new_component_notice_text .= '<a href="#" class="button button-default wbcr-clr-proccess-button wbcr-clr-update-external-addon" data-plugin-slug="' . $slug . '" data-plugin-action="' . $action . '" data-wpnonce="' . wp_create_nonce('updates') . '" data-i18n="' . WCL_Helper::getEscapeJson($button_i18n) . '">' . $button_i18n[$action] . '</a>';
				$new_component_notice_text .= '</div>';

				$need_show_new_components_notice = true;
			}
		}

		if( !WCL_Plugin::app()->isActivateComponent('minify_and_combine') ) {
			$new_component_notice_text .= '<div class="wbcr-clr-new-component">';
			$new_component_notice_text .= '<h4>Minify and Combine (JS, CSS)</h4> - ';
			$new_component_notice_text .= '<span> Этот компонент позволяет сжимать и комбинировать js и css файлы.';
			$new_component_notice_text .= '</span><br>';
			$new_component_notice_text .= '<a href="#" class="button button-default wbcr-clr-proccess-button wbcr-clr-activate-preload-addon" data-component-name="minify_and_combine" data-wpnonce="' . wp_create_nonce('wbcr_clearfy_activate_interal_component') . '">Активировать</a>';
			$new_component_notice_text .= '</div>';

			$need_show_new_components_notice = true;
		}

		if( !WCL_Plugin::app()->isActivateComponent('html_minify') ) {
			$new_component_notice_text .= '<div class="wbcr-clr-new-component">';
			$new_component_notice_text .= '<h4>Html minify (Reloaded)</h4> - ';
			$new_component_notice_text .= '<span> Этот компонент позволяет сжимать html код.';
			$new_component_notice_text .= '</span><br>';
			$new_component_notice_text .= '<a href="#" class="button button-default wbcr-clr-proccess-button wbcr-clr-activate-preload-addon" data-component-name="html_minify" data-wpnonce="' . wp_create_nonce('wbcr_clearfy_activate_interal_component') . '">Активировать</a>';
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
