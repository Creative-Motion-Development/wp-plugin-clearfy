<?php
	/**
	 * Class for working with the licensing system
	 *
	 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018 Webraftic Ltd
	 * @version       1.0
	 */
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	class WCL_Licensing {
		
		public $id;
		public $secret_key;
		
		/**
		 * @since  1.0
		 * @var WCL_Licensing
		 */
		private static $instance;
		
		/**
		 * Initialization of the licensing system
		 *
		 */
		private function __construct() {
			if ( WCL_Plugin::app()->premium->is_activate() ) {
				$this->id         = 1;
				$this->secret_key = WCL_Plugin::app()->premium->get_license()->get_key();
			}
		}
		
		/**
		 * Getting a licensing system
		 *
		 * @return WCL_Licensing
		 */
		public static function instance() {
			if ( self::$instance ) {
				return self::$instance;
			}
			
			self::$instance = new self();
			
			return self::$instance;
		}
		
		/**
		 * Returns a storage object
		 *
		 * @return WCL_Licensing
		 */
		public function getStorage() {
			return self::instance();
		}
		
		/**
		 * @return \WCL_Licensing
		 * @since  1.1
		 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
		 */
		public function getLicense() {
			return self::instance();
		}
		
		/**
		 * Checks if current license has expired
		 *
		 * @return bool
		 */
		public function isLicenseValid() {
			return WCL_Plugin::app()->premium->is_activate();
		}
	}

	
