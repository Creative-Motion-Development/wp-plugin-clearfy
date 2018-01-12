<?php

	/**
	 * This file groups the settings for quick setup
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 16.09.2017, Webcraftic
	 * @version 1.0
	 */
	class WbcrClr_Option {

		private $name;
		private $title;
		private $values;
		private $tags;

		public function __construct(array $option_data)
		{
			if( empty($option_data) ) {
				throw new Exception('Empty group_name attribute.');
			}

			foreach($option_data as $key => $value) {
				$this->$key = $value;
			}
		}

		public function getName()
		{
			return $this->name;
		}

		public function getTitle()
		{
			return $this->title;
		}

		public function getValue($group_name = null)
		{
			if( !empty($group_name) && isset($this->values[$group_name]) ) {
				return $this->values[$group_name];
			}

			return !empty($this->values)
				? $this->values
				: array();
		}

		public function getTags()
		{
			return $this->tags;
		}

		public function hasGroup($group_name)
		{
			if( !empty($this->tags) && in_array($group_name, $this->tags) ) {
				return true;
			}

			return false;
		}

		/**
		 * @return array
		 */
		public static function getAllOptions()
		{
			$all_options = require(WBCR_CLR_PLUGIN_DIR . '/admin/includes/options.php');
			$result = array();

			if( !empty($all_options) ) {
				foreach($all_options as $option_data) {
					$result[] = new WbcrClr_Option($option_data);
				}
			}

			return $result;
		}
	}

