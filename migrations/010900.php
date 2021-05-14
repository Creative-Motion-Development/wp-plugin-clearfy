<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCLUpdate010900 extends Wbcr_Factory000_Update {

	public function install()
	{
		$this->plugin->updatePopulateOption('start_second_google_page_speed_audit', 1);
		wp_schedule_event(time(), 'daily', 'wclearfy/google_page_speed_audit');
	}
}