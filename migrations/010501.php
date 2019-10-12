<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */
class WCLUpdate010501 extends Wbcr_Factory000_Update {

	public function install() {
		require_once( WCL_PLUGIN_DIR . '/components/ga-cache/migrations/030002.php' );
		$gac_updates = new WGACUpdate030002( $this->plugin );
		$gac_updates->install();
	}
}