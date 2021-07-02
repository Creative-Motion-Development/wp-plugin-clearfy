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

class WCL_Setup extends WBCR\Factory_Templates_000\Pages\Setup {

	/**
	 * @param \Wbcr_Factory000_Plugin $plugin
	 */
	public function __construct(\Wbcr_Factory000_Plugin $plugin)
	{
		parent::__construct($plugin);

		$path = WCL_PLUGIN_DIR . '/admin/pages/setup/steps';

		#Step 1
		$this->register_step($path . '/class-step-default.php', '\WBCR\Clearfy\Pages\Step_Default');

		#Step 2
		$this->register_step($path . '/class-step-google-page-speed-before.php', '\WBCR\Clearfy\Pages\Step_Google_Page_Speed_Before');

		#Step 3
		$this->register_step($path . '/class-step-plugins.php', '\WBCR\Clearfy\Pages\Step_Plugins');

		#Step 4
		$this->register_step($path . '/class-step-setting-speed-optimize.php', '\WBCR\Clearfy\Pages\Step_Setting_Speed_Optimize');

		#Step 5
		$this->register_step($path . '/class-step-setting-seo-optimize.php', '\WBCR\Clearfy\Pages\Step_Setting_Seo');

		#Step 6
		$this->register_step($path . '/class-step-optimize-images.php', '\WBCR\Clearfy\Pages\Step_Optimize_Images');

		#Step 7
		$this->register_step($path . '/class-step-google-page-speed-after.php', '\WBCR\Clearfy\Pages\Step_Google_Page_Speed_After');

		#Step 8
		$this->register_step($path . '/class-step-congratulation.php', '\WBCR\Clearfy\Pages\Step_Congratulation');
	}


	/**
	 * Requests assets (js and css) for the page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @see   FactoryPages000_AdminPage
	 *
	 */
	public function assets($scripts, $styles)
	{
		parent::assets($scripts, $styles);

		$this->scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/circular-progress.js');
		$this->scripts->add(WCL_PLUGIN_URL . '/admin/assets/js/setup.js');

		$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/setup/page-setup.css');
	}

}
