<?php
	
	/**
	 * This class configures the code cleanup settings
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_ConfigPrivacy extends Wbcr_FactoryClearfy000_Configurate {

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		public function registerActionsAndFilters()
		{
			if( !is_admin() ) {
				if( $this->getOption('remove_meta_generator') ) {
					remove_action('wp_head', 'wp_generator');

					if( class_exists('WooCommerce') ) {
						remove_action('wp_head', 'woo_version');
					}

					if( class_exists('SitePress') ) {
						global $sitepress;
						remove_action('wp_head', array($sitepress, 'meta_generator_tag'));
					}

					add_filter('the_generator', '__return_empty_string');
				}

				if( $this->getOption('remove_html_comments') ) {
					add_action('wp_loaded', array($this, 'removeHtmlComments'));
				}
			}
		}


		public function removeHtmlComments()
		{
			ob_start(array($this, 'removeHtmlCommentsMain'));
		}

		/**
		 * !ngg_resource - can not be deleted, otherwise the plugin nextgen gallery will not work
		 * @param string $data
		 * @return mixed
		 */
		public function removeHtmlCommentsMain($data)
		{
			//CLRF-166 issue fix bug with noindex (\s?\/?noindex)
			return preg_replace('#<!--(?!<!|\s?ngg_resource|\s?\/?noindex)[^\[>].*?-->#s', '', $data);
		}
	}