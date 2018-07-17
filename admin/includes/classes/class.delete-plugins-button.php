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

	class WCL_DeletePluginsButton extends WCL_InstallPluginsButton {

		/**
		 * @throws Exception
		 */
		protected function buildWordpress()
		{
			parent::buildWordpress();

			$this->action = 'delete';
			$this->addData('plugin-action', $this->action);
			$this->removeClass('button-primary');
		}

		protected function buildInternal()
		{
			// nothing
		}

		/**
		 * @throws Exception
		 */
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

			$this->addData('plugin-slug', $this->plugin_slug);
			$this->addData('plugin', 'freemius');
			$this->addData('wpnonce', wp_create_nonce('updates'));

			if( $this->action != 'read' ) {
				$this->addClass('wbcr-clr-update-component-button');
			}
		}

		/**
		 * @param bool $echo
		 * @return string|void
		 */
		public function render($echo = true)
		{
			if( $this->type == 'internal' || !$this->isPluginInstall() || $this->isPluginActivate() ) {
				$button = '';
			} else {
				$button = '<a href="#" class="' . implode(' ', $this->getClasses()) . '" ' . implode(' ', $this->getData()) . '><span class="dashicons dashicons-trash"></span></a>';
			}

			if( $echo ) {
				echo $button;
			} else {
				return $button;
			}
		}
	}

