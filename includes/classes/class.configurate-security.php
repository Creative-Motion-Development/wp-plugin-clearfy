<?php
	/**
	 * This class configures security settings
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_ConfigSecurity extends Wbcr_FactoryClearfy000_Configurate {

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		public function registerActionsAndFilters()
		{
			if( !is_admin() ) {
				if( $this->getPopulateOption('change_login_errors') ) {
					add_filter('login_errors', array($this, 'changeLoginErrors'));
				}

				if( $this->getPopulateOption('protect_author_get') ) {
					add_action('wp', array($this, 'protectAuthorGet'));
				}
			}
		}

		/**
		 * Change login error message
		 *
		 * @return string
		 */

		public function changeLoginErrors($errors)
		{
			if( !in_array($GLOBALS['pagenow'], array('wp-login.php')) ) {
				return $errors;
			}

			return __('<strong>ERROR</strong>: Wrong login or password', 'clearfy');
		}

		/**
		 * Protect author get
		 */

		public function protectAuthorGet()
		{
			if( isset($_GET['author']) ) {
				wp_redirect(home_url(), 301);

				die();
			}
		}
	}