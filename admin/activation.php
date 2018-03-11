<?php

	/**
	 * Activator for the clearfy
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 09.09.2017, Webcraftic
	 * @see Factory000_Activator
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_Activation extends Wbcr_Factory000_Activator {

		/**
		 * Runs activation actions.
		 *
		 * @since 1.0.0
		 */
		public function activate()
		{
			if( !defined('WPSEO_VERSION') ) {
				WCL_Plugin::app()->deactivateComponent('yoast_seo');
			}

			// caching google analytics on a schedule
			//----------------------------------------
			$ga_cache = WCL_Plugin::app()->getOption('ga_cache');

			if( $ga_cache ) {
				wp_clear_scheduled_hook('wbcr_clearfy_update_local_ga');

				if( !wp_next_scheduled('wbcr_clearfy_update_local_ga') ) {
					$ga_caos_remove_wp_cron = WCL_Plugin::app()->getOption('ga_caos_remove_wp_cron');

					if( !$ga_caos_remove_wp_cron ) {
						wp_schedule_event(time(), 'daily', 'wbcr_clearfy_update_local_ga');
					}
				}
			}
		}

		/**
		 * Runs activation actions.
		 *
		 * @since 1.0.0
		 */
		public function deactivate()
		{
			if( wp_next_scheduled('wbcr_clearfy_update_local_ga') ) {
				wp_clear_scheduled_hook('wbcr_clearfy_update_local_ga');
			}
		}
	}
