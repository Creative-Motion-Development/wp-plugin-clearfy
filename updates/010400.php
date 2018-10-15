<?php #comp-page builds: premium

	/**
	 * Updates for altering the table used to store statistics data.
	 * Adds new columns and renames existing ones in order to add support for the new social buttons.
	 */
	class WCLUpdate010400 extends Wbcr_Factory000_Update {

		public function install()
		{
			//WCL_Plugin::app()->deletePopulateOption('factory_000_plugin_activated_');

			if( wp_next_scheduled('wbcr_clr_license_autosync') ) {
				wp_clear_scheduled_hook('wbcr_clr_license_autosync');
			}

			if( !wp_next_scheduled('wbcr_clearfy_license_autosync') ) {
				wp_schedule_event(time(), 'twicedaily', 'wbcr_clearfy_license_autosync');
			}
		}
	}