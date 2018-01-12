<?php
	
	/**
	 * This class configures security settings
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */
	class WbcrClearfy_ConfigSecurity extends WbcrFactoryClearfy_Configurate {
		
		public function registerActionsAndFilters()
		{
			if( $this->getOption('change_login_errors') ) {
				add_filter('login_errors', array($this, 'changeLoginErrors'));
			}

			if( $this->getOption('protect_author_get') ) {
				add_action('wp', array($this, 'protectAuthorGet'));
			}
		}

		/**
		 * Change login error message
		 *
		 * @return string
		 */

		public function changeLoginErrors()
		{
			return __('<strong>ERROR</strong>: Wrong login or password', 'clearfy');
		}

		/**
		 * Protect author get
		 */

		public function protectAuthorGet()
		{
			if( isset($_GET['author']) && !is_admin() ) {
				wp_redirect(home_url(), 301);

				die();
			}
		}
	}