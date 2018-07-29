<?php

class WCL_Package {
    private static $instance = null;
    private $packages = array();
    
    private $is_need_update_addons = false;
    
    private $plugin_slug = 'clearfy-package';
    
    private $plugin_dir = 'clearfy_package';
    
    private $plugin_basename = ''; // заполняется в конструкторе
    
    private $builder_url = 'https://clearfy.pro/package/assembly-package.php?addons=';

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __clone() {}
    
    private function __construct() {
		$this->plugin_basename = $this->plugin_dir . '/' . $this->plugin_slug . '.php';
	}
    
    public function info() {
		return array(
			'plugin_basename' => $this->plugin_basename,
			'plugin_slug'     => $this->plugin_basename,
		);
	}
    
    public function add( $packages = array() ) {
		if ( ! $packages ) return false;
		foreach( $packages as $package ) {
			$key = $package['slug'];
			$this->packages[ $key ] = $package;
		}
	}
	
	public function getAll() {
		return $this->packages;
	}
	
	public function getSlugs() {
		$slugs = array();
		if ( $this->packages ) {
			foreach ( $this->packages as $package ) {
				$slugs[] = $package['slug'];
			}
		}
		return $slugs;
	}
	
	public function getAddon( $slug ) {
		if ( isset( $this->packages[ $slug ] ) ) {
			return $this->packages[ $slug ];
		}
		return false;
	}
	
	public function isActive() {
		if( is_plugin_active( $this->plugin_basename ) ) {
			return true;
		}
		return false;
	}
	
	public function isInstalled() {
		if( file_exists( WP_PLUGIN_DIR . '/' . $this->plugin_basename ) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Метод проверяет, нужно ли обновлять сами аддоны
	 *
	 * @return bool
	 */
	public function isNeedUpdateAddons() {
		return $this->is_need_update_addons;
	}
	
	/**
	 * Метод проверяет, нужно ли обновлять весь пакет в целом.
	 * Пакет может быть обновлен по двум причинам:
	 * 1) Активирован аддон, которого ещё нет в пакете(или пакет не установлен)
	 * 2) Для аддонов проявились новые версии
	 *
	 * @return bool
	 */
	public function isNeedUpdate() {
		$need_update_package = false;
		$freemius_activated_addons = WCL_Plugin::app()->getOption( 'freemius_activated_addons', array() );
		
		if( $this->isActive() ) {
			// если плагин clearfy-package установлен, то проверяем в нём наличие фримиус аддонов
			$addons = $this->getAll();
			$licensing = WCL_Licensing::instance();
			$freemius_addons_data = $licensing->getAddons();
			foreach ( $freemius_activated_addons as $freemius_active_addon ) {
				if ( isset( $addons[ $freemius_active_addon ] ) ) {
					// проверяем, актуальна ли версия аддона
					foreach( $freemius_addons_data->plugins as $freemius_addon ) {
						if ( $freemius_addon->slug != $freemius_active_addon ) {
							continue;
						}
						// если во фримиусе не указана версия, то делаем её равной текущей версии аддона. Для того, чтобы уведомление об обновлении вечно не висело.
						$actual_version = isset( $freemius_addon->info ) ? $freemius_addon->info->selling_point_0 : '';
						if ( ! $actual_version ) {
							$actual_version = $addons[ $freemius_active_addon ]['current_version'];
						}
						if ( version_compare( $actual_version, $addons[ $freemius_active_addon ]['current_version'] ) ) {
							$this->is_need_update_addons = true;
							$need_update_package = true;
						}
					}
				} else {
					$need_update_package = true;
				}
			}
			
		} else {
			// если плагин clearfy-package НЕ установлен, то любая активация фримиус аддона требует обновления пакета
			if ( count( $freemius_activated_addons ) ) {
				$need_update_package = true;
			}
		}
		return $need_update_package;
	}
	
	public function active() {
		// если плагин установлен и не активирован, то активируем
		if ( $this->isInstalled() and ! $this->isActive() ) {
			activate_plugin( $this->plugin_basename );
		}
	}
	
	public function downloadUrl() {
		$freemius_activated_addons = WCL_Plugin::app()->getOption( 'freemius_activated_addons', array() );
		$package_slugs = array();
		
		if( $this->isActive() ) {
			$package_slugs = $this->getSlugs();
			foreach ( $freemius_activated_addons as $freemius_addon ) {
				if ( ! in_array( $freemius_addon, $package_slugs ) ) {
					$package_slugs[] = $freemius_addon;
				}
			}
		}
		if ( ! $package_slugs ) {
			$package_slugs = $freemius_activated_addons;
		}
		//$package_slugs[] = 'test-addon'; // для тестирования ошибки. Сборщик не отдаст архив
		$url = $this->builder_url . join( ',', $package_slugs );
		
		return $url;
	}
	
	public function update() {
		$url = $this->downloadUrl();
		
		global $wp_filesystem;
		if( !$wp_filesystem ) {
			if( !function_exists('WP_Filesystem') ) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}
			WP_Filesystem();
		}

		if( !WP_Filesystem(false, WP_PLUGIN_DIR) || 'direct' !== $wp_filesystem->method ) {
			throw new Exception('You are not allowed to edt folders/files on this site');
		} else {
			ob_start();

			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/misc.php');
			require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.upgrader-skin.php');
			add_filter('async_update_translation', '__return_false', 1);

			$upgrader = new Plugin_Upgrader(new WCL_Upgrader_Skin);
			if( $this->isInstalled() ) {
				$result = $upgrader->run( array(
					'package'           => $url,
					'destination'       => WP_PLUGIN_DIR,
					'clear_destination' => true,
					'clear_working'     => true,
					'hook_extra'        => array(
						'plugin' => $this->plugin_basename,
						'type'   => 'plugin',
						'action' => 'update',
					),
				) );
			} else {
				$result = $upgrader->install( $url );
			}
			$this->active();
			
			ob_end_clean();

			if( null === $result ) {
				return new WP_Error( 'addon install error', 'addon install error!!!' ); // пока думаю как получать сообщение об ошибке с сервера
			}
			
			return $result;
		}
	}
	
	public function getUpdateNotice() {
		$need_update_package = $this->isNeedUpdate();
		$message = '';
		if ( $need_update_package ) {
			if ( $this->isNeedUpdateAddons() ) {
				// доступны обновления компонентов
				$message = __( 'Для одного из компонентов доступны обновления. Для установки нужно обновить текущую сборку компонентов.', 'clearfy' );
			} else {
				// нужно обновить весь пакет
				$message = __( 'Вы изменили конфигурацию компонентов, для работы плагина нужно обновить текущую сборку компонентов. ', 'clearfy' );
			}
			$message .= '<button class="wbcr-clr-update-package button button-default" type="button" data-wpnonce="' . wp_create_nonce( 'package' ) . '" data-loading="' . __( 'Идёт обновление...', 'clearfy' ) . '">' . __( 'Обновить', 'clearfy' ) . '</button>';
			return $message;
		}
		return false;
	}
	
	public function getActivedAddons() {
		$addons = array();
		if ( $this->isInstalled() ) {
			$package_dir = WP_PLUGIN_DIR . '/' . $this->plugin_dir;
			$package_config = $package_dir . '/config.php';
			if ( file_exists( $package_config ) ) {
				$packages = require( $package_config );
				$this->add( $packages );
			}
			if ( $this->packages ) {
				$freemius_activated_addons = WCL_Plugin::app()->getOption( 'freemius_activated_addons', array() );
				foreach ( $this->packages as $addon ) {
					if ( in_array( $addon['slug'], $freemius_activated_addons ) ) {
						$addons[ $addon['slug'] ] = array(
							$addon['class_name'],
							$package_dir . '/components/' . $addon['base_dir']
						);
					}
				}
			}
		}
		return $addons;
	}
}
