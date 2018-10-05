<?php #comp-page builds: premium

	/**
	 * Updates for altering the table used to store statistics data.
	 * Adds new columns and renames existing ones in order to add support for the new social buttons.
	 */
	class WCLUpdate010400 extends Wbcr_Factory000_Update {

		public function install()
		{
			//WCL_Plugin::app()->deleteOption('factory_000_plugin_activated_');
		}
	}