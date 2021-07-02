<?php
/**
 * Default page
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, Alexander Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 17.08.2019, Webcraftic
 * @version       1.0
 */

class WCL_Page extends WBCR\Factory_Templates_000\Pages\PageBase {

	/**
	 * Requests assets (js and css) for the page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @see FactoryPages000_AdminPage
	 *
	 */
	public function assets($scripts, $styles)
	{
		parent::assets($scripts, $styles);

		$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/components.css');

		/**
		 * Подгружаем стили для вижета оптимизации изображений, если не установли плагин оптимизации изображений
		 */
		if( !defined('WIO_PLUGIN_ACTIVE') ) {
			$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/base-statistic.css');
		}

		$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/general.css');
		$this->scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/general.js', [], 'wclearfy-general');


		$params = array(
			//'ajaxurl' => admin_url('admin-ajax.php'),
			'flush_cache_url' => $this->getActionUrl('flush-cache-and-rules', array('_wpnonce' => wp_create_nonce('wbcr_factory_' . $this->getResultId() . '_flush_action'))),
			'ajax_nonce' => wp_create_nonce('wbcr_clearfy_ajax_quick_start_nonce'),
			'import_options_nonce' => wp_create_nonce('wbcr_clearfy_import_options'),
			'i18n' => array(
				'success_update_settings' => __('Settings successfully updated!', 'clearfy'),
				'unknown_error' => __('During the setup, an unknown error occurred, please try again or contact the plugin support.', 'clearfy')
			)
		);
		$this->scripts->localize('wbcr_clearfy_ajax', $params);
	}

	/**
	 * Действие выполняется для всех страниц Clearfy и его компонентах.
	 * Это простое предложение перейти на PRO версию.
	 */
	public function multisiteProAction()
	{
		if( is_multisite() && $this->plugin->isNetworkActive() ) {

			$license_page_url = $this->getBaseUrl('clearfy_license');
			$upgrade_url = $this->plugin->get_support()->get_pricing_url(true, 'multisite_save_settings');

			$html = '<div class="wbcr-factory-templates-000-multisite-suggetion">';
			$html .= '<div class="wbcr-factory-inner-contanier">';
			$html .= '<h3>' . __('Upgrade to Clearfy Business', 'wbcr_factory_templates_000') . '</h3>';
			$html .= '<p>' . __('Oops... Sorry for the inconvenience caused!', 'wbcr_factory_templates_000') . '</p>';
			$html .= '<p>' . __('Complete multisite support is available in Clearfy Business and Clearfy Business Revolution packages only!', 'wbcr_factory_templates_000') . '</p>';
			$html .= '<p>' . __('You can activate the plugin on each website and use it with zero limitations. But you can’t save the plugin’s settings under the Super Administrator role!', 'wbcr_factory_templates_000') . '</p>';
			$html .= '<p style="margin-top:20px">';
			$html .= '<a href="' . $license_page_url . '" class="wbcr-factory-activate-premium" rel="noopener">' . __('Activate license ', 'wbcr_factory_templates_000') . '</a> ';
			$html .= '<a href="' . $upgrade_url . '" class="wbcr-factory-purchase-premium" target="_blank" rel="noopener">' . __('Upgrade to Clearfy Business', 'wbcr_factory_templates_000') . '</a>';
			$html .= '</p>';
			$html .= '</div>';
			$html .= '</div>';

			$this->showPage($html);

			return;
		}

		$this->redirectToAction('index');
	}


}