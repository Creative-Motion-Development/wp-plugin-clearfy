<?php

class WCL_Package {
    private static $instance = null;
    private $packages = array();
    
    private $plugin_basename = 'clearfy-package/clearfy-package.php';

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __clone() {}
    private function __construct() {}
    
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
	
	public function isNeedUpdate() {
		$need_update_package = false;
		$freemius_activated_addons = WCL_Plugin::app()->getOption( 'freemius_activated_addons', array() );
		
		if( $this->isActive() ) {
			// если плагин clearfy-package установлен, то проверяем в нём наличие фримиус аддонов
			$addons = $this->getAll();
			foreach ( $freemius_activated_addons as $freemius_addon ) {
				if ( isset( $addons[ $freemius_addon ] ) ) {
					// проверяем, актуальна ли версия аддона
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
		activate_plugin( $this->plugin_basename );
	}
	
	public function update() {
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
		
		$url = '/package/assembly-package.php?addons=' . join( ',', $package_slugs );
		if ( count( $package_slugs ) > 1 ) {
			$url = 'http://u16313p6h.ha002.t.justns.ru/clearfy-package2.zip';
		} else {
			$url = 'http://u16313p6h.ha002.t.justns.ru/clearfy-package1.zip';
		}
		
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
			if( $this->isActive() ) {
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
				$this->active();
			}
			
			ob_end_clean();

			if( null === $result ) {
				throw new Exception('Could not complete add-on installation');
			}
			
			return $result;
		}
	}
}
