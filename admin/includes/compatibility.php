<?php
/**
 * Compatibility with Clearfy old components
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 22.10.2018, Webcraftic
 * @version       1.0
 */

add_action( 'plugins_loaded', function () {
	if ( defined( 'WIO_PLUGIN_ACTIVE' ) ) {
		if ( ! file_exists( WP_PLUGIN_DIR . '/robin-image-optimizer/robin-image-optimizer.php' ) ) {
			return;
		}

		$plugin = get_plugin_data( WP_PLUGIN_DIR . '/robin-image-optimizer/robin-image-optimizer.php' );

		if ( isset( $plugin['Version'] ) && version_compare( $plugin['Version'], '1.0.8', '<=' ) ) {
			$notice = __( 'Please update the plugin Robin image Optimizer to the latest version, as it may not work correctly with the new version of Clearfy!', 'clearfy' );
			/**
			 * Выводит уведомление внутри интерфейса Clearfy, на всех страницах плагина.
			 * Это необходимо, чтоб напомнить пользователю обновить конфигурацию компонентов плагина,
			 * иначе вновь активированные компоненты не будет зайдествованы в работе плагина.
			 *
			 * @param Wbcr_Factory000_Plugin                   $plugin
			 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
			 *
			 * @return bool
			 */
			add_action( 'wbcr/factory/pages/impressive/print_all_notices', function ( $plugin, $obj ) use ( $notice ) {
				$obj->printErrorNotice( $notice );
			}, 10, 2 );

			// Специально для преидущей версии фреймворка (407)
			add_action( 'wbcr_factory_pages_407_imppage_print_all_notices', function ( $plugin, $obj ) use ( $notice ) {
				$obj->printErrorNotice( $notice );
			}, 10, 2 );
		}
	}

	if ( defined( 'WHLP_PLUGIN_ACTIVE' ) ) {
		if ( ! file_exists( WP_PLUGIN_DIR . '/hide-login-page/hide-login-page.php' ) ) {
			return;
		}

		$plugin = get_plugin_data( WP_PLUGIN_DIR . '/hide-login-page/hide-login-page.php' );

		if ( isset( $plugin['Version'] ) && version_compare( $plugin['Version'], '1.0.5', '<=' ) ) {
			$notice = __( 'Please update the plugin Hide login page to the latest version, as it may not work correctly with the new version of Clearfy!', 'clearfy' );
			/**
			 * Выводит уведомление внутри интерфейса Clearfy, на всех страницах плагина.
			 * Это необходимо, чтоб напомнить пользователю обновить конфигурацию компонентов плагина,
			 * иначе вновь активированные компоненты не будет зайдествованы в работе плагина.
			 *
			 * @param Wbcr_Factory000_Plugin                   $plugin
			 * @param Wbcr_FactoryPages000_ImpressiveThemplate $obj
			 *
			 * @return bool
			 */
			add_action( 'wbcr/factory/pages/impressive/print_all_notices', function ( $plugin, $obj ) use ( $notice ) {
				$obj->printErrorNotice( $notice );
			}, 10, 2 );

			// Специально для преидущей версии фреймворка (407)
			add_action( 'wbcr_factory_pages_407_imppage_print_all_notices', function ( $plugin, $obj ) use ( $notice ) {
				$obj->printErrorNotice( $notice );
			}, 10, 2 );
		}
	}

	if ( defined( 'WCLRP_PLUGIN_ACTIVE' ) ) {
		add_action( 'wbcr/factory/admin_notices', function ( $notices, $plugin_name ) {
			if ( $plugin_name != WGZ_Plugin::app()->getPluginName() ) {
				return $notices;
			}

			if ( ! current_user_can( 'update_plugins' ) ) {
				return $notices;
			}

			$nonce_action = 'upgrade-plugin_' . WCLRP_PLUGIN_BASE;
			$upgrade_url  = wp_nonce_url( self_admin_url( "update.php?action=upgrade-plugin&plugin=" . urlencode( WCLRP_PLUGIN_BASE ) ), $nonce_action );
			$notice_text  = sprintf( __( 'You must <a href="%s">upgrade the premium version</a> of the Clearfy plugin to version 1.1.2, since the new Clearfy release isn\'t compatible with the previous version of the premium plugin.', 'clearfy' ), $upgrade_url );

			$notices[] = [
				'id'              => 'clearfy-package_-compatibility-113',
				'type'            => 'error',
				'dismissible'     => false,
				'dismiss_expires' => 0,
				'text'            => '<p><b>' . __( 'Clearfy', 'clearfy' ) . ': </b>' . $notice_text . '</p>'
			];

			return $notices;
		}, 10, 2 );
	}
}, 30 );