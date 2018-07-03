<?php
	
	/**
	 * Класс работы с данными лицензирования
	 * @author Webcraftic <jokerov@gmail.com>
	 * @copyright (c) 2018 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	class WCL_Licensing_Storage {
		
		private $_storage = array();
		
		public function __construct() {
			$this->load();
		}
		
		public function load() {
			$this->_storage = WCL_Plugin::app()->getOption( 'license_storage', false );
			
			if ( isset( $this->_storage['user']->id ) and $this->_storage['user']->id ) {
				$this->_storage['user'] = new WCL_FS_User( $this->_storage['user'] );
			}
			if ( isset( $this->_storage['site']->id ) and $this->_storage['site']->id ) {
				$this->_storage['site'] = new WCL_FS_Site( $this->_storage['site'] );
			}
			if ( isset( $this->_storage['license']->id ) and $this->_storage['license']->id ) {
				$this->_storage['license'] = new WCL_FS_Plugin_License( $this->_storage['license'] );
			}
		}
		
		public function save() {
			WCL_Plugin::app()->updateOption( 'license_storage', $this->_storage );
		}
		
		public function get( $property ) {
			if ( isset( $this->_storage[ $property ] ) ) {
				return $this->_storage[ $property ];
			}
			return false;
		}
		
		public function getAll() {
			return $this->_storage;
		}
		
		public function set( $property, $value ) {
			$this->_storage[ $property ] = $value;
		}
		
		public function delete( $property ) {
			if ( isset( $this->_storage[ $property ] ) ) {
				$this->_storage[ $property ] = false;
			}
		}
	}
