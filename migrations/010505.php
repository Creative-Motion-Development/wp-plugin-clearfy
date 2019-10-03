<?php #comp-page builds: premium

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */
class WCLUpdate010505 extends Wbcr_Factory000_Update {

	public function install() {
		$this->update_premium();
		$this->move_freemius_addons();

		/**
		 * Миграция для аддона Updates manager
		 */
		require_once( WCL_PLUGIN_DIR . '/components/assets-manager/migrations/010108.php' );

		$wupm_updates = new WGZUpdate010108( $this->plugin );
		$wupm_updates->install();

		/**
		 * Очищаем старые данных от плагина Hide my wp
		 */
		$this->clean_hide_my_wp_data();
	}

	/**
	 * Безопасно отключаем Hide my wp, так как он будет удален.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 */
	private function clean_hide_my_wp_data() {
		$is_hide_my_wp_active = get_option( $this->plugin->getPrefix() . 'hide_my_wp_activate' );

		if ( is_multisite() || ! $is_hide_my_wp_active ) {
			return;
		}

		if ( $this->is_apache() ) {
			$this->reset_hide_my_wp_settings();

			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/misc.php';
			flush_rewrite_rules( true );
		}
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

		# Вносим данные уже установленного премиум плагина в новую систему лицензирования.
		# Запускаем принудильную проверку обновлений для премиум плагина.
		# ------------------------------------------------------------------------
		if ( $this->plugin->premium->is_activate() && ! $this->plugin->premium->is_install_package() ) {

			# Выполняем синхронизацию лицензии и обновляем версию SDK Freemius для стабильной работы,
			# если лицензия была активирован на SDK ниже 2.2.3, при скачивании премиум плагина
			# сервис freemius будет возвращать ошибку 500.
			$this->plugin->premium->sync();

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

	// For Clean Hide my wp settings
	// ============================================================================================

	public static function is_hide_mode_active() {
		return WCL_Plugin::app()->getPopulateOption( 'hide_my_wp_activate', false );
	}

	/**
	 * Return if the server run Apache
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 *
	 * @return bool
	 */
	private function is_apache() {
		$is_apache = ( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache' ) !== false || strpos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) !== false );

		return $is_apache;
	}

	/**
	 * Get rules marker
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 *
	 * @return string
	 */
	private function get_rules_marker() {
		if ( is_multisite() ) {
			return "Webcraftic Hide My Wp Site" . get_current_blog_id();
		}

		return "Webcraftic Hide My Wp";
	}

	/**
	 * Return the server home path
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 * @return bool|string
	 */
	private function get_home_path() {
		$home    = set_url_scheme( get_option( 'home' ), 'http' );
		$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );

			if ( $pos !== false ) {
				$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
				$home_path = trim( $home_path, '/\\' ) . DIRECTORY_SEPARATOR;;
			} else {
				$wp_path_rel_to_home = DIRECTORY_SEPARATOR . trim( $wp_path_rel_to_home, '/\\' ) . DIRECTORY_SEPARATOR;

				$real_apth = realpath( ABSPATH ) . DIRECTORY_SEPARATOR;

				$pos       = strpos( $real_apth, $wp_path_rel_to_home );
				$home_path = substr( $real_apth, 0, $pos );
				$home_path = trim( $home_path, '/\\' ) . DIRECTORY_SEPARATOR;
			}
		} else {
			$home_path = ABSPATH;
		}

		$home_path = trim( $home_path, '\\/ ' );

		//not for windows
		if ( DIRECTORY_SEPARATOR != '\\' ) {
			$home_path = DIRECTORY_SEPARATOR . $home_path;
		}

		return $home_path;
	}

	/**
	 * Return whatever the htaccess config file is writable
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 * @return bool
	 */
	private function is_writable_htaccess_config_file() {
		$home_path     = $this->get_home_path();
		$htaccess_file = $home_path . DIRECTORY_SEPARATOR . '.htaccess';

		if ( ( ! file_exists( $htaccess_file ) && $this->is_permalink() ) || is_writable( $htaccess_file ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $filename
	 * @param $markers
	 *
	 * @return bool
	 */
	private function clean_rules_in_file( $filename, $markers ) {

		if ( ! file_exists( $filename ) ) {
			if ( ! is_writable( dirname( $filename ) ) ) {
				return false;
			}
			if ( ! touch( $filename ) ) {
				return false;
			}
		} else if ( ! is_writeable( $filename ) ) {
			return false;
		}

		$start_marker = $markers['start'];
		$end_marker   = $markers['end'];

		$fp = fopen( $filename, 'r+' );
		if ( ! $fp ) {
			return false;
		}

		// Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
		flock( $fp, LOCK_EX );

		$lines = [];
		while( ! feof( $fp ) ) {
			$lines[] = rtrim( fgets( $fp ), "\r\n" );
		}

		// Split out the existing file into the preceding lines, and those that appear after the marker
		$pre_lines    = $post_lines = $existing_lines = [];
		$found_marker = $found_end_marker = false;
		foreach ( $lines as $line ) {
			if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
				$found_marker = true;
				continue;
			} else if ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
				$found_end_marker = true;
				continue;
			}
			if ( ! $found_marker ) {
				$pre_lines[] = $line;
			} else if ( $found_marker && $found_end_marker ) {
				$post_lines[] = $line;
			} else {
				$existing_lines[] = $line;
			}
		}

		// Generate the new file data
		if ( $found_marker && $found_end_marker ) {
			$new_file_data = implode( "\n", array_merge( $pre_lines, $post_lines ) );

			// Write to the start of the file, and truncate it to that length
			fseek( $fp, 0 );
			$bytes = fwrite( $fp, $new_file_data );
			if ( $bytes ) {
				ftruncate( $fp, ftell( $fp ) );
			}
			fflush( $fp );
			flock( $fp, LOCK_UN );
			fclose( $fp );

			return (bool) $bytes;
		}

		return false;
	}

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 */
	private function reset_hide_my_wp_settings() {
		WCL_Plugin::app()->deletePopulateOption( 'hide_my_wp_activate' );
		WCL_Plugin::app()->deletePopulateOption( 'replace_content_filters' );
		WCL_Plugin::app()->deletePopulateOption( 'replace_content_patterns' );
		WCL_Plugin::app()->deletePopulateOption( 'recovery_content_patterns' );
		WCL_Plugin::app()->deletePopulateOption( 'recovery_content_filters' );
		WCL_Plugin::app()->deletePopulateOption( 'nginx_rewrite_rules' );
		WCL_Plugin::app()->deletePopulateOption( 'apache_rewrite_rules' );
		WCL_Plugin::app()->deletePopulateOption( 'server_configuration_error' );

		//check if .htaccess file exists and is writable
		if ( $this->is_writable_htaccess_config_file() ) {
			$home_path     = $this->get_home_path();
			$htaccess_file = $home_path . DIRECTORY_SEPARATOR . '.htaccess';

			$markers = [
				'start' => '# BEGIN ' . $this->get_rules_marker(),
				'end'   => '# END ' . $this->get_rules_marker()
			];
			$this->clean_rules_in_file( $htaccess_file, $markers );
		}
	}

	/**
	 * Is permalink enabled?
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 * @return bool
	 * @global WP_Rewrite $wp_rewrite
	 */
	private function is_permalink() {
		global $wp_rewrite;

		if ( ! isset( $wp_rewrite ) || ! is_object( $wp_rewrite ) || ! $wp_rewrite->using_permalinks() ) {
			return false;
		}

		return true;
	}
}