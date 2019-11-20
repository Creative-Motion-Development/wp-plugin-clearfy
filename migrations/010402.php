<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCLUpdate010402 extends Wbcr_Factory000_Update {

	public function install() {
		/**
		 * Миграция для аддона Disable admin notices
		 */
		require_once( WCL_PLUGIN_DIR . '/components/disable-admin-notices/migrations/010007.php' );
		$dan_updates = new WDNUpdate010007( $this->plugin );
		$dan_updates->install();

		/**
		 * Миграция для аддона Updates manager
		 */
		require_once( WCL_PLUGIN_DIR . '/components/updates-manager/migrations/010008.php' );

		$wupm_updates = new WUPMUpdate010008( $this->plugin );
		$wupm_updates->install();

		/**
		 * Удаляем старый cron хук
		 */
		if ( wp_next_scheduled( 'wbcr_clr_license_autosync' ) ) {
			wp_clear_scheduled_hook( 'wbcr_clr_license_autosync' );
		}

		/**
		 * Добавляем новый cron хук
		 */
		if ( ! wp_next_scheduled( 'wbcr_clearfy_license_autosync' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'wbcr_clearfy_license_autosync' );
		}

		if ( $this->plugin->getPopulateOption( 'remove_style_version' ) ) {
			$this->plugin->updatePopulateOption( 'disable_remove_style_version_for_auth_users', 1 );
		}
	}
}