<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCLUpdate010604 extends Wbcr_Factory000_Update {

	public function install() {
		$this->assets_manager_migration();
	}

	/**
	 * Перенос данных из старого менеджера скриптов в новый
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 */
	private function assets_manager_migration() {
		if ( ! defined( 'WGZ_PLUGIN_DIR' ) ) {
			define( 'WGZ_PLUGIN_DIR', WCL_PLUGIN_DIR . '/components/assets-manager' );
		}

		/**
		 * Миграция для аддона Assets manager
		 */
		require_once( WCL_PLUGIN_DIR . '/components/assets-manager/migrations/020005.php' );

		$wupm_updates = new WGZUpdate020005( $this->plugin );
		$wupm_updates->install();
	}
}