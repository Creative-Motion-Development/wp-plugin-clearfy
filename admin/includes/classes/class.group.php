<?php

	/**
	 * This file groups the settings for quick setup
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 16.09.2017, Webcraftic
	 * @version 1.0
	 */
	class WbcrClr_Group {

		private $group_name;

		public function __construct($group_name)
		{
			if( empty($group_name) || !is_string($group_name) ) {
				throw new Exception('Empty group_name attribute.');
			}
			$this->group_name = $group_name;
		}

		public static function getInstance($group_name)
		{
			return new WbcrClr_Group($group_name);
		}

		public function getName()
		{
			return $this->group_name;
		}

		public function getOptions()
		{
			$options = WbcrClr_Option::getAllOptions();
			$filter = array();

			foreach($options as $option) {
				if( $option->hasGroup($this->group_name) ) {
					$filter[] = $option;
				}
			}

			return $filter;
		}
	}

