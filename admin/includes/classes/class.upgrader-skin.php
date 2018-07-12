<?php
	/**
	 * Upgrader API: WP_Upgrader_Skin class
	 *
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 11.07.2018, Webcraftic
	 *
	 * @package WordPress
	 * @subpackage Upgrader
	 * @since 4.6.0
	 */

	include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

	class WCL_Upgrader_Skin extends WP_Upgrader_Skin {

		public function feedback($string)
		{
			// @note: Keep it empty.
		}
	}