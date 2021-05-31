<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCLUpdate010903 extends Wbcr_Factory000_Update {

	public function install()
	{
		if( wp_next_scheduled('wclearfy/google_page_speed_audit') ) {
			wp_clear_scheduled_hook('wclearfy/google_page_speed_audit');
		}
	}
}