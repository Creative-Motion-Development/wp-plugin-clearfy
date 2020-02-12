<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCLUpdate010108 extends Wbcr_Factory000_Update {

	public function install()
	{
		delete_option($this->plugin->getPluginName() . '_quick_modes');
		delete_option($this->plugin->getPluginName() . '_disable_admin_notices');
	}
}