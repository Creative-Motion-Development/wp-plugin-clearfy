<?php

	/**
	 * Page template class
	 * Author: Webcraftic <wordpress.webraftic@gmail.com>
	 * Version: 1.0.0
	 */
	class WbcrClr_Page extends FactoryPages000_ImpressiveThemplate {

		public function __construct(Factory000_Plugin $plugin)
		{
			parent::__construct($plugin);
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

			$this->styles->add(WBCR_CLR_PLUGIN_URL . '/admin/assets/css/general.css');
		}
	}

	/*@mix:place*/