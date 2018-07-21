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

	class WCL_InstallPluginsButton {

		protected $type;
		protected $plugin_slug;
		protected $classes = array(
			'button',
			'wbcr-clr-proccess-button',
			'wbcr-clr-update-component-button'
		);
		protected $data = array();
		protected $base_path;

		protected $action;

		/**
		 * @param string $group_name
		 * @throws Exception
		 */
		public function __construct($type, $plugin_slug)
		{
			if( empty($type) || !is_string($plugin_slug) ) {
				throw new Exception('Empty type or plugin_slug attribute.');
			}
			$this->type = $type;
			$this->plugin_slug = $plugin_slug;

			if( $this->type == 'wordpress' ) {
				if( strpos(rtrim(trim($this->plugin_slug)), '/') !== false ) {
					$this->base_path = $this->plugin_slug;
					$base_path_parts = explode('/', $this->base_path);
					if( sizeof($base_path_parts) === 2 ) {
						$this->plugin_slug = $base_path_parts[0];
					}
				} else {
					$this->base_path = $this->getPluginBasePathBySlug($this->plugin_slug);
				}

				$this->buildWordpress();
			} else if( $this->type == 'internal' ) {
				$this->buildInternal();
			} else if( $this->type == 'freemius' ) {
				$this->buildFreemius();
			} else {
				throw new Exception('Invalid button type.');
			}

			// Set default data
			$this->addData('storage', $this->type);
			$this->addData('i18n', WCL_Helper::getEscapeJson($this->getI18n()));
			$this->addData('wpnonce', wp_create_nonce('updates'));
		}

		/**
		 * @return bool
		 */
		public function isPluginActivate()
		{
			if( $this->type == 'wordpress' && $this->isPluginInstall() ) {
				return is_plugin_active($this->base_path);
			} else if( $this->type == 'internal' || ($this->type == 'freemius' && $this->isPluginInstall()) ) {
				$preinsatall_components = WCL_Plugin::app()->getOption('deactive_preinstall_components', array());

				return !in_array($this->plugin_slug, $preinsatall_components);
			}

			return false;
		}

		/**
		 * @return bool
		 */
		public function isPluginInstall()
		{
			if( $this->type == 'wordpress' ) {
				if( empty($this->base_path) ) {
					return false;
				}

				// Check if the function get_plugins() is registered. It is necessary for the front-end
				// usually get_plugins() only works in the admin panel.
				if( !function_exists('get_plugins') ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$plugins = get_plugins();

				if( isset($plugins[$this->base_path]) ) {
					return true;
				}
			} else if( $this->type == 'internal' ) {
				return true;
			} else if( $this->type == 'freemius' ) {
				$freemius_installed_addons = WCL_Plugin::app()->getOption('freemius_installed_addons', array());

				if( in_array($this->plugin_slug, $freemius_installed_addons) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * @param $class
		 * @throws Exception
		 */
		public function addClass($class)
		{
			if( !is_string($class) ) {
				throw new Exception('Attribute class must be a string.');
			}
			$this->classes[] = $class;
		}

		/**
		 * @param $class
		 * @return bool
		 * @throws Exception
		 */
		public function removeClass($class)
		{
			if( !is_string($class) ) {
				throw new Exception('Attribute class must be a string.');
			}
			$key = array_search($class, $this->classes);
			if( isset($this->classes[$key]) ) {
				unset($this->classes[$key]);

				return true;
			}

			return false;
		}

		/**
		 * @param $name
		 * @param $value
		 * @throws Exception
		 */
		public function addData($name, $value)
		{
			if( !is_string($name) || !is_string($value) ) {
				throw new Exception('Attributes name and value must be a string.');
			}

			$this->data[$name] = $value;
		}

		/**
		 * @param $name
		 * @return bool
		 * @throws Exception
		 */
		public function removeData($name)
		{
			if( !is_string($name) ) {
				throw new Exception('Attribute name must be a string.');
			}

			if( isset($this->data[$name]) ) {
				unset($this->data[$name]);

				return true;
			}

			return false;
		}

		/**
		 * @param bool $echo
		 * @return string|void
		 */
		public function render($echo = true)
		{
			$i18n = $this->getI18n();

			$button = '<a href="#" class="' . implode(' ', $this->getClasses()) . '" ' . implode(' ', $this->getData()) . '>' . $i18n[$this->action] . '</a>';

			if( $echo ) {
				echo $button;
			} else {
				return $button;
			}
		}

		/**
		 * @return array
		 */
		protected function getData()
		{
			$data_to_print = array();

			foreach((array)$this->data as $key => $value) {
				$data_to_print[$key] = 'data-' . esc_attr($key) . '="' . esc_attr($value) . '"';
			}

			return $data_to_print;
		}

		/**
		 * @return array
		 */
		protected function getClasses()
		{
			return array_map('esc_attr', $this->classes);
		}

		protected function buildWordpress()
		{
			if( $this->type != 'wordpress' || empty($this->base_path) ) {
				return;
			}

			$this->action = 'install';

			if( $this->isPluginInstall() ) {
				$this->action = 'deactivate';
				if( !$this->isPluginActivate() ) {
					$this->action = 'activate';
				}
			}

			$this->addData('plugin-action', $this->action);
			$this->addData('slug', $this->plugin_slug);
			$this->addData('plugin', $this->base_path);

			if( $this->action == 'activate' ) {
				$this->addClass('button-primary');
			} else {
				$this->addClass('button-default');
			}
		}

		protected function buildInternal()
		{
			if( $this->type != 'internal' ) {
				return;
			}

			$this->action = 'activate';

			if( $this->isPluginActivate() ) {
				$this->action = 'deactivate';
			}

			$this->addData('plugin-action', $this->action);
			$this->addData('plugin', $this->plugin_slug);

			if( $this->action == 'activate' ) {
				$this->addClass('button-primary');
			} else {
				$this->addClass('button-default');
			}
		}

		protected function buildFreemius()
		{
			if( $this->type != 'freemius' ) {
				return;
			}

			$this->action = 'install';

			require_once WCL_PLUGIN_DIR . '/includes/classes/class.licensing.php';

			$licensing = WCL_Licensing::instance();

			if( $this->isPluginInstall() ) {
				$this->action = 'deactivate';
				if( !$this->isPluginActivate() ) {
					$this->action = 'activate';
				}
			} else {
				if( $licensing->isLicenseValid() ) {
					$this->action = 'install';
				} else {
					$this->action = 'read';
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

		protected function getI18n()
		{
			return array(
				'activate' => __('Activate', 'clearfy'),
				'install' => __('Install', 'clearfy'),
				'deactivate' => __('Deactivate', 'clearfy'),
				'delete' => __('Delete', 'clearfy'),
				'loading' => __('Please wait...', 'clearfy'),
				'read' => __('Read more', 'clearfy')
			);
		}


		/**
		 * Allows you to get the base path to the plugin in the directory wp-content/plugins/
		 *
		 * @param $slug - slug for example "clearfy", "hide-login-page"
		 * @return int|null|string - "clearfy/clearfy.php"
		 */
		protected function getPluginBasePathBySlug($slug)
		{
			// Check if the function get_plugins() is registered. It is necessary for the front-end
			// usually get_plugins() only works in the admin panel.
			if( !function_exists('get_plugins') ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugins = get_plugins();

			foreach($plugins as $base_path => $plugin) {
				if( strpos($base_path, rtrim(trim($slug))) !== false ) {
					return $base_path;
				}
			}

			return null;
		}
	}

