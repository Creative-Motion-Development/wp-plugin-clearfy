<?php

/**
 * Activator for the cyrlitera
 *
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @copyright (c) 09.03.2018, Webcraftic
 * @see           Wbcr_Factory000_Activator
 * @version       1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCACHE_Activation extends Wbcr_Factory000_Activator {

	/**
	 * Runs activation actions.
	 *
	 * @since 1.0.0
	 */
	public function activate()
	{
		require_once WCACHE_PLUGIN_DIR . '/includes/cache.php';
		try {
			WCL_Cache::activate();
		} catch( Exception $e ) {
			//nothing
		}
	}

	public function deactivate()
	{
		require_once WCACHE_PLUGIN_DIR . '/includes/cache.php';
		try {
			WCL_Cache::deactivate();
		} catch( Exception $e ) {
			//nothing
		}
	}
}
