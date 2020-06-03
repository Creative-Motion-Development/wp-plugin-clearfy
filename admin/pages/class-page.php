<?php
/**
 * Default page
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, Alexander Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 17.08.2019, Webcraftic
 * @version       1.0
 */

class WCL_Page extends Wbcr_FactoryClearfy000_PageBase {

	/**
	 * Действие выполняется для всех страниц Clearfy и его компонентах.
	 * Это простое предложение перейти на PRO версию.
	 */
	public function multisiteProAction()
	{
		if( is_multisite() && $this->plugin->isNetworkActive() ) {

			$license_page_url = $this->getBaseUrl('clearfy_license');
			$upgrade_url = $this->plugin->get_support()->get_pricing_url(true, 'multisite_save_settings');

			$html = '<div class="wbcr-factory-clearfy-000-multisite-suggetion">';
			$html .= '<div class="wbcr-factory-inner-contanier">';
			$html .= '<h3>' . __('Upgrade to Clearfy Business', 'wbcr_factory_clearfy_000') . '</h3>';
			$html .= '<p>' . __('Oops... Sorry for the inconvenience caused!', 'wbcr_factory_clearfy_000') . '</p>';
			$html .= '<p>' . __('Complete multisite support is available in Clearfy Business and Clearfy Business Revolution packages only!', 'wbcr_factory_clearfy_000') . '</p>';
			$html .= '<p>' . __('You can activate the plugin on each website and use it with zero limitations. But you can’t save the plugin’s settings under the Super Administrator role!', 'wbcr_factory_clearfy_000') . '</p>';
			$html .= '<p style="margin-top:20px">';
			$html .= '<a href="' . $license_page_url . '" class="wbcr-factory-activate-premium" rel="noopener">' . __('Activate license ', 'wbcr_factory_clearfy_000') . '</a> ';
			$html .= '<a href="' . $upgrade_url . '" class="wbcr-factory-purchase-premium" target="_blank" rel="noopener">' . __('Upgrade to Clearfy Business', 'wbcr_factory_clearfy_000') . '</a>';
			$html .= '</p>';
			$html .= '</div>';
			$html .= '</div>';

			$this->showPage($html);

			return;
		}

		$this->redirectToAction('index');
	}


}