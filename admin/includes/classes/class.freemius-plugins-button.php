<?php

	/**
	 * This file groups the settings for quick setup
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 16.09.2017, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';

	class WCL_FreemiusPluginsButton extends WCL_InstallPluginsButton {
		
		protected $addon_data;
		
		protected $action;
		
		protected $url;
		
		public function __construct( $type, $plugin_slug ) {
			if( empty( $type ) || ! is_string( $plugin_slug ) ) {
				throw new Exception('Empty type or plugin_slug attribute.');
			}
			$this->type = $type;
			$this->plugin_slug = $plugin_slug;


			// Set default data
			$this->addData('storage', $this->type);
			$this->addData('i18n', WCL_Helper::getEscapeJson($this->getI18n()));
			$this->addData('wpnonce', wp_create_nonce('updates'));
		}

		
		
		public function setAddonData( $addon_data ) {
			$this->addon_data = $addon_data;
		}
		
		public function getAddonData() {
			return $this->addon_data;
		}

		public function build() {
			if( $this->type != 'freemius' ) {
				return;
			}

			$this->action = 'activate';


			$licensing = WCL_Licensing::instance();
			
			$component = $this->getAddonData();
			

			if ( $component['is_free'] ) {
				// если аддон бесплатный
				if ( $component['actived'] ) {
					$this->action = 'deactivate';
				}
			} else {
				// если аддон НЕ бесплатный
				if ( $licensing->isLicenseValid() ) {
					// если лицензия валидна, то аддон можно установить
					if ( $component['actived'] ) {
						$this->action = 'deactivate';
					}
				} else {
					if ( $component['actived'] ) {
						// если лицензия не валидна, но аддон уже был активирован
						$this->action = 'deactivate';
					} else {
						// если лицензия не валидна, то показываем ссылку на страницу аддона
						$this->action = 'read';
						$this->url = $component['url'];
					}
				}
			}


			$this->addData('plugin-action', $this->action);
			$this->addData('plugin', $this->plugin_slug);

			if( $this->action == 'activate' ) {
				$this->addClass('button-primary');
			} else {
				$this->addClass('button-default');
			}
		}

		/**
		 * @param bool $echo
		 * @return string|void
		 */
		public function render($echo = true)
		{
			$component = $this->getAddonData();
			$i18n = parent::getI18n();
			if ( $this->action == 'read' and isset( $this->url ) ) {
				$button = '<a target="_blank" href="' .esc_attr( $this->url ) . '" class="button button-default install-now">' . $i18n[$this->action] . '</a>';
			} else {
				$button = '<a href="#" class="' . implode(' ', parent::getClasses()) . '" ' . implode(' ', parent::getData()) . '>' . $i18n[$this->action] . '</a>';
			}
			

			if( $echo ) {
				echo $button;
			} else {
				return $button;
			}
		}
	}

