<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */
class WCLUpdate010505 extends Wbcr_Factory000_Update {

	public function install() {
		$this->update_premium();
		$this->move_freemius_addons();
	}

	/**
	 * Миграция из старой модели лицензирования на новую модель
	 *
	 * Чтобы пользователи, у котороых установлен премиум плагина, ничего
	 * не заметили при обновлени плагина и не столкнулись с проблемами в
	 * использовании премиум версии Clearfy, нам нужно создать мягкую
	 * миграцию и обновление премиум версии.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 */
	private function update_premium() {

		// Удаляем крон задачу из старой модели лицензирования
		if ( ! wp_next_scheduled( 'wbcr_clearfy_license_autosync' ) ) {
			wp_clear_scheduled_hook( 'wbcr_clearfy_license_autosync' );
		}

		// Вносим данные уже установленного премиум плагина в новую систему лицензирования.
		// Запускаем принудильную проверку обновлений для премиум плагина.
		// ------------------------------------------------------------------------
		if ( $this->plugin->premium->is_activate() && ! $this->plugin->premium->is_install_package() ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';

			$plugins = get_plugins( $plugin_folder = '' );

			if ( ! empty( $plugins ) ) {
				foreach ( (array) $plugins as $plugin_base => $plugin ) {
					$basename_parts = explode( '/', $plugin_base );
					if ( sizeof( $basename_parts ) == 2 && $basename_parts[0] == 'clearfy_package' ) {

						$plugin_basename  = $plugin_base;
						$plugin_main_file = WP_PLUGIN_DIR . '/' . $plugin_base;

						$default_headers = [
							'Version'          => 'Version',
							'FrameworkVersion' => 'Framework Version'
						];

						$plugin_data = get_file_data( $plugin_main_file, $default_headers, 'plugin' );

						try {
							$this->plugin->premium->update_package_data( [
								'basename'          => $plugin_basename,
								'version'           => $plugin_data['Version'],
								'framework_version' => isset( $plugin_data['FrameworkVersion'] ) ? $plugin_data['FrameworkVersion'] : null,
							] );
						} catch( Exception $e ) {
							// nothing
						}

						$this->plugin->updatePopulateOption( "last_check_premium_update_time", 0 );

						return;
					}
				}
			}
		}
	}

	/**
	 * Удаляем данные от старой системы лицензирования и загрузки пакетов с freemius
	 *
	 * Мы перешли на новую модель установки премиум аддонов, старая модель больше не
	 * нужна, просто удаляем ее.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 */
	private function move_freemius_addons() {
		$freemius_activated_addons = WCL_Plugin::app()->getPopulateOption( 'freemius_activated_addons', [] );

		if ( ! in_array( 'webcraftic-hide-my-wp', $freemius_activated_addons ) ) {
			WCL_Plugin::app()->deactivateComponent( 'hide_my_wp' );
		}

		WCL_Plugin::app()->deletePopulateOption( 'freemius_addons' );
		WCL_Plugin::app()->deletePopulateOption( 'freemius_addons_last_update' );
	}
}