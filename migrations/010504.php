<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCLUpdate010504 extends Wbcr_Factory000_Update {

	public function install() {
		$deactivate_components = $this->plugin->getPopulateOption( 'deactive_preinstall_components', [] );

		if ( empty( $deactivate_components ) || ! in_array( 'assets_manager', $deactivate_components ) ) {
			require_once( WCL_PLUGIN_DIR . '/components/assets-manager/migrations/010108.php' );
			$am_updates = new WGZUpdate010108( $this->plugin );
			$am_updates->install();
		}
	}
}