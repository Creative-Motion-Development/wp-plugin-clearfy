<?php

	/**
	 * This class provides tools for downloading, installing external add-ons for the Clearfy plugin
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 10.07.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_ExternalAddon {

		/**
		 * wordpress - wordpress.org repo
		 * custom_url - addon will be downloaded through any installed url
		 * @var string
		 */
		//protected $storage_type = 'wordpress';

		/**
		 * Slug in the WordPress repository
		 * Slug: robin-image-optimizer
		 * @var string
		 */
		protected $slug;

		/**
		 * @var string
		 */
		private $addon_base_path;

		/**
		 * @param string $slug slug in the WordPress repository
		 * @throws Exception
		 */
		public function __construct($slug)
		{
			if( empty($slug) ) {
				throw new Exception('Empty slug attribute.');
			}

			$this->slug = $slug;
		}

		/**
		 * @return int|null|string
		 */
		public function getBasePath()
		{
			if( !empty($this->addon_base_path) ) {
				return $this->addon_base_path;
			}

			$plugins = get_plugins();

			foreach($plugins as $base_path => $plugin) {
				if( strpos($base_path, rtrim(trim($this->slug))) !== false ) {
					$this->addon_base_path = $base_path;

					return $this->addon_base_path;
				}
			}

			return null;
		}

		/**
		 * @param $base_path
		 * @return bool
		 */
		public function isActivated()
		{
			$plugin_base_path = $this->getBasePath();

			if( empty($plugin_base_path) ) {
				return false;
			}

			return is_plugin_active($plugin_base_path);
		}

		/**
		 * @param $base_path
		 * @return bool
		 */
		public function isInstalled()
		{
			$plugin_base_path = $this->getBasePath();

			if( !empty($plugin_base_path) ) {
				return true;
			}

			return false;
		}

		/**
		 * @return bool
		 * @throws Exception
		 */
		public function install()
		{
			global $wp_filesystem;

			if( $this->isInstalled() ) {
				return true;
			}

			if( !$wp_filesystem ) {
				if( !function_exists('WP_Filesystem') ) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				WP_Filesystem();
			}

			// @note: Check if plugins root folder is writable.
			if( !WP_Filesystem(false, WP_PLUGIN_DIR) || 'direct' !== $wp_filesystem->method ) {
				throw new Exception('You are not allowed to edt folders/files on this site');
			} else {
				ob_start();

				require_once(ABSPATH . 'wp-admin/includes/file.php');
				require_once(ABSPATH . 'wp-admin/includes/misc.php');
				require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
				require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.upgrader-skin.php');

				add_filter('async_update_translation', '__return_false', 1);

				$upgrader = new Plugin_Upgrader(new WCL_Upgrader_Skin());
				$install = $upgrader->install($this->getDownloadUrl());

				ob_end_clean();

				if( null === $install ) {
					throw new Exception('Could not complete add-on installation');
				}
			}

			return true;
		}

		/**
		 * @return bool
		 * @throws Exception
		 */
		public function activate()
		{
			$plugin_base_path = $this->getBasePath();

			if( empty($plugin_base_path) ) {
				return false;
			}

			$result = activate_plugin($plugin_base_path);

			if( is_wp_error($result) ) {
				$error_string = $result->get_error_message();
				throw new Exception("Activation error:" . $error_string);
			}

			return true;
		}

		/**
		 * @return bool
		 * @throws Exception
		 */
		public function deactivate()
		{
			$plugin_base_path = $this->getBasePath();

			if( empty($plugin_base_path) ) {
				return false;
			}

			$result = deactivate_plugins($plugin_base_path);

			if( is_wp_error($result) ) {
				$error_string = $result->get_error_message();
				throw new Exception("Activation error:" . $error_string);
			}

			return true;
		}

		/**
		 * Return the download link for the plugin in the WordPress.org repository
		 * @return bool|mixed
		 */
		protected function getDownloadUrl()
		{
			$transient_id = WCL_Plugin::app()->getPrefix() . str_replace('-', '_', $this->slug) . '_download_url';

			$link = get_transient($transient_id);

			if( false === $link ) {

				if( !function_exists('plugins_api') ) {
					include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
				}

				$plugin_info = plugins_api('plugin_information', array(
					'slug' => $this->slug,
					'fields' => array(
						'short_description' => false,
						'sections' => false,
						'requires' => false,
						'rating' => false,
						'ratings' => false,
						'downloaded' => false,
						'last_updated' => false,
						'added' => false,
						'tags' => false,
						'compatibility' => false,
						'homepage' => false,
						'donate_link' => false,
					),
				));

				if( !is_wp_error($plugin_info) ) {
					$link = isset($plugin_info->download_link)
						? $plugin_info->download_link
						: false;
				}

				if( $link ) {
					set_transient($transient_id, $link, DAY_IN_SECONDS);
				}
			}

			return $link;
		}
	}

