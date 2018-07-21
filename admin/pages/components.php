<?php
	/**
	 * This file is the add-ons page.
	 *
	 * @author Alex Kovalev <alex@byonepress.com>
	 * @copyright (c) 2017, OnePress Ltd
	 *
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_ComponentsPage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "components";

		public $page_menu_position = 0;

		public $page_menu_dashicon = 'dashicons-admin-plugins';

		public $type = 'page';

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Components', 'clearfy');

			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		/**
		 * Requests assets (js and css) for the page.
		 *
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function assets($scripts, $styles)
		{
			parent::assets($scripts, $styles);

			$this->styles->add(WCL_PLUGIN_URL . '/admin/assets/css/components.css');
		}

		public function showPageContent()
		{
			$preinsatall_components = $this->plugin->getOption('deactive_preinstall_components', array());
			$freemius_activated_addons = WCL_Plugin::app()->getOption( 'freemius_activated_addons', array() );

			$default_image = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzNjAiIGhlaWdodD0iMzYwIiB2aWV3Ym94PSIwIDAgMzYwIDM2MCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0icmdiKDcwLCA4MSwgOTMpIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjE1IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLCAwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wNTQ2NjY2NjY2NjY2NjciIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDYwLCAwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4wNDYiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEyMCwgMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDIiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDU0NjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgyNDAsIDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjAyODY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAwLCAwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4xMDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCwgNjApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA5OCIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDYwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMTUzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTIwLCA2MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDYzMzMzMzMzMzMzMzMzIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxODAsIDYwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wMzczMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI0MCwgNjApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iI2RkZCIgZmlsbC1vcGFjaXR5PSIwLjE0MTMzMzMzMzMzMzMzIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzMDAsIDYwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wMzczMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAsIDEyMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDg5MzMzMzMzMzMzMzMzIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg2MCwgMTIwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wODkzMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEyMCwgMTIwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4wODA2NjY2NjY2NjY2NjciIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMTIwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMzI2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjQwLCAxMjApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjE1IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzMDAsIDEyMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDk4IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLCAxODApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA2MzMzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDE4MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDIiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEyMCwgMTgwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wMzczMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMTgwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMTUzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjQwLCAxODApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA2MzMzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAwLCAxODApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iI2RkZCIgZmlsbC1vcGFjaXR5PSIwLjA1NDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCwgMjQwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4xMDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDI0MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDcyIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMjAsIDI0MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMTE1MzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMjQwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMzI2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjQwLCAyNDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA4MDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAwLCAyNDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjEzMjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLCAzMDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iI2RkZCIgZmlsbC1vcGFjaXR5PSIwLjAzNzMzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMTI0IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMjAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDI4NjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxODAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDcyIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgyNDAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDI4NjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzMDAsIDMwMCkiIC8+PC9zdmc+';

			$response = array(
				array(
					'name' => 'robin_image_optimizer',
					'title' => __('Robin image optimizer', 'clearfy'),
					'url' => '#',
					'type' => 'external',
					'slug' => 'cyr3lat',
					'base_path' => 'cyr3lat/cyr-to-lat.php',
					'icon' => $default_image,
					'description' => __('Automatic image optimization without any quality loss. No limitations, no paid plans. The best Wordpress image optimization plugin allows optimizing any amount of images for free!', 'clearfy')
				),
				array(
					'name' => 'hide_login_page',
					'title' => __('Hide login page', 'clearfy'),
					'url' => '#',
					'type' => 'external',
					'slug' => 'hide-login-page',
					'base_path' => 'hide-login-page/hide-login-page.php',
					'icon' => $default_image,
					'description' => __('Hide Login Page is a very light plugin that lets you easily and safely change the url of the login form page to anything you want.', 'clearfy')
				),
				array(
					'name' => 'html_minify',
					'title' => __('Html minify', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => $default_image,
					'description' => __('Ever look at the HTML markup of your website and notice how sloppy and amateurish it looks? The Minify HTML options cleans up sloppy looking markup and minifies, which also speeds up downloa', 'clearfy')
				),
				array(
					'name' => 'minify_and_combine',
					'title' => __('Minify and combine (JS, CSS)', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => $default_image,
					'description' => __('Improve your speed score on GTmetrix, Pingdom Tools and Google PageSpeed Insights by merging and minifying CSS, JavaScript.', 'clearfy')
				),
				array(
					'name' => 'ga_cache',
					'title' => __('Google Analytics Cache', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => $default_image,
					'description' => __('To improve Google Page Speed indicators Analytics caching is needed. However, it can also slightly increase your website loading speed, because Analytics js files will load locally.', 'clearfy')
				),
				array(
					'name' => 'updates_manager',
					'title' => __('Updates manager', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => WCL_PLUGIN_URL . '/admin/assets/img/upm-icon-128x128.png',
					'description' => __('Disable updates enable auto updates for themes, plugins and WordPress.', 'clearfy')
				),
				array(
					'name' => 'comments_tools',
					'title' => __('Comments tools', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => WCL_PLUGIN_URL . '/admin/assets/img/dic-icon-128x128.png',
					'description' => __('Bulk disable and remove comments, disable “Website” field, hides external links, disable XML-RPC.', 'clearfy')
				),
				array(
					'name' => 'widget_tools',
					'title' => __('Widgets tools', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => $default_image,
					'description' => __('Disable unused widgets such as tag cloud, links, calendar etc.', 'clearfy')
				),
				array(
					'name' => 'asset_manager',
					'title' => __('Asset manager', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => WCL_PLUGIN_URL . '/admin/assets/img/asm-icon-128x128.png',
					'description' => __('Selectively disable unused scripts and styles on the pages of your website.', 'clearfy')
				),
				array(
					'name' => 'disable_notices',
					'title' => __('Disable admin notices', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => WCL_PLUGIN_URL . '/admin/assets/img/dan-icon-128x128.png',
					'description' => __('Disables admin notices bulk or individually. Collects notices into the admin bar.', 'clearfy')
				),
				array(
					'name' => 'adminbar_manager',
					'title' => __('Admin bar manager', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => $default_image,
					'description' => __('Disables admin bar. Allows to change and remove admin bar elements.', 'clearfy')
				),
				array(
					'name' => 'post_tools',
					'title' => __('Posts tools', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => $default_image,
					'description' => __('Disable revisions, disable posts autosave, disable smart quotes and disable auto paragraphs.', 'clearfy')
				),
				array(
					'name' => 'yoast_seo',
					'title' => __('Yoast SEO optimization', 'clearfy'),
					'url' => '#',
					'type' => 'internal',
					'icon' => $default_image,
					'description' => __('Set of optimization functions for the popular Yoast SEO plugin.', 'clearfy')
				)
			);

			$response[] = array(
				'name' => 'cyrlitera',
				'title' => __('Transliteration of Cyrillic alphabet', 'clearfy'),
				'type' => 'internal',
				'url' => '#',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/ctr-icon-128x128.png',
				'description' => __('Converts Cyrillic permalinks of post, pages, taxonomies and media files to the Latin alphabet. Supports Russian, Ukrainian, Georgian, Bulgarian languages.', 'clearfy')
			);
			
			$licensing = WCL_Licensing::instance();
			$freemius_addons = array();
			$freemius_addons_data = $licensing->getAddons(); // получаем все аддоны
			//$freemius_installed_addons = WCL_Plugin::app()->getOption( 'freemius_installed_addons', array() );
			if ( isset( $freemius_addons_data->plugins ) ) {
				foreach( $freemius_addons_data->plugins as $freemius_addon ) {
					$is_free_addon = false;
					if ( $freemius_addon->free_releases_count ) {
						$is_free_addon = true;
					}
					$component = array(
						'name'        => $freemius_addon->slug,
						'slug'        => $freemius_addon->slug,
						'title'       => __( $freemius_addon->title, 'clearfy' ),
						'type'        => 'freemius',
						'installed'   => false,
						'is_free'     => $is_free_addon,
						'actived'     => false,
						'url'         => isset( $freemius_addon->info ) ? $freemius_addon->info->url : '#',
						'icon'        => isset( $freemius_addon->icon ) ? $freemius_addon->icon : WCL_PLUGIN_URL . '/admin/assets/img/ctr-icon-128x128.png',
						'description' => isset( $freemius_addon->info ) ? __( $freemius_addon->info->short_description, 'clearfy' ) : '',
					);
					/*
					if ( in_array( $component['name'], $freemius_installed_addons ) ) {
						$component['installed'] = true;
					}
					*/ 
					if ( in_array( $component['name'], $freemius_activated_addons ) ) {
						$component['actived'] = true;
					}
					$response[] = $component;
				}
			}

			?>
			<div class="wbcr-factory-page-group-header"><?php _e('<strong>Plugin Components</strong>.', 'clearfy') ?>
				<p>
					<?php _e('These are components of the plugin bundle. When you activate the plugin, all the components turned on by default. If you don’t need some function, you can easily turn it off on this page.', 'clearfy') ?>
				</p>
			</div>

			<div class="wbcr-clearfy-components">
				<?php foreach($response as $component): ?>
					<?php
					$button_i18n = array(
						'activate' => __('Activate', 'clearfy'),
						'install' => __('Install', 'clearfy'),
						'deactivate' => __('Deactivate', 'clearfy'),
						'delete' => __('Delete', 'clearfy'),
						'loading' => __('Please wait...', 'clearfy'),
						'read' => __('Read more', 'clearfy')
					);
					$link = '#';
					$status_class = '';
					$action = 'deactivate';

					if( ($component['type'] == 'external' && (!WCL_Helper::isPluginInstalled($component['base_path']) || !is_plugin_active($component['base_path']))) || ($component['type'] == 'internal' && in_array($component['name'], $preinsatall_components)) ) {
						$status_class = ' plugin-status-deactive';
						$action = 'activate';
					}

					if( $component['type'] == 'external' && !WCL_Helper::isPluginInstalled($component['base_path']) ) {
						$action = 'install';
					}
					

					$default_classes = array(
						'button',
						'wbcr-clr-proccess-button'
					);

					$data = array();
					$data['i18n'] = WCL_Helper::getEscapeJson($button_i18n);
					$data['plugin-action'] = $action;

					$classes = array();

					if( $component['type'] == 'external' ) {
						$data['plugin-slug'] = $component['slug'];
						$data['plugin'] = $component['base_path'];
						$data['wpnonce'] = wp_create_nonce('updates');
						$classes[] = 'wbcr-clr-update-external-addon-button';
					}
					if( $component['type'] == 'internal' ) {
						$data['component-name'] = $component['name'];
						$data['wpnonce'] = wp_create_nonce('update_component_' . $component['name']);
						$classes[] = 'wbcr-clr-activate-preload-addon-button';
					}
					if( $component['type'] == 'freemius' ) {
						$data['plugin-slug'] = $component['slug'];
						$data['plugin'] = 'freemius';
						$data['wpnonce'] = wp_create_nonce('updates');
						$classes[] = 'wbcr-clr-update-external-addon-button';
					}

					$data_to_print = array();
					foreach((array)$data as $key => $value) {
						$data_to_print[$key] = 'data-' . esc_attr($key) . '="' . esc_attr($value) . '"';
					}
					$process_button_classes = array_merge($default_classes, $classes);

					$delete_button = '';

					if( $component['type'] == 'external' && WCL_Helper::isPluginInstalled($component['base_path']) ) {
						$delete_button_classes = $process_button_classes;
						$delete_button_data_to_print = $data_to_print;

						unset($delete_button_data_to_print['plugin-action']);
						$delete_button_data_to_print[] = 'data-plugin-action="delete"';
						$delete_button_classes[] = 'delete-now';
						$delete_button = '<a href="#" class="' . implode(' ', $delete_button_classes) . '"' . implode(' ', $delete_button_data_to_print) . '><span class="dashicons dashicons-trash"></span></a>';
					}
					
					// если аддон получен из сервиса freemius
					if( $component['type'] == 'freemius' ) {
						/*
						if ( $component['installed'] ) {
							$delete_button_data_to_print = $data_to_print;
							if ( ! $component['actived'] ) {
								$status_class = ' plugin-status-deactive';
								$action = 'activate';
								unset( $data_to_print['plugin-action'] );
								$data_to_print[] = 'data-plugin-action="activate"';
							}
							// если аддон установлен
							$delete_button_classes = $process_button_classes;

							unset($delete_button_data_to_print['plugin-action']);
							$delete_button_data_to_print[] = 'data-plugin-action="delete"';
							$delete_button_classes[] = 'delete-now';
							$delete_button = '<a href="#" class="' . implode(' ', $delete_button_classes) . '"' . implode(' ', $delete_button_data_to_print) . '><span class="dashicons dashicons-trash"></span></a>';
						} else {
							if ( $licensing->isLicenseValid() ) {
								// если лицензия валидна, то аддон можно установить
								$action = 'install';
								$status_class = ' plugin-status-deactive';
								unset( $data_to_print['plugin-action'] );
								$data_to_print[] = 'data-plugin-action="install"';
							} else {
								// если лицензия не валидна, то показываем ссылку на страницу аддона
								$link = $component['url'];
								$status_class = ' plugin-status-deactive';
								$action = 'read';
							}
						}
						*/
						if ( $component['is_free'] ) {
							// если аддон бесплатный
							if ( ! $component['actived'] ) {
								$status_class = ' plugin-status-deactive';
								$action = 'activate';
								unset( $data_to_print['plugin-action'] );
								$data_to_print[] = 'data-plugin-action="activate"';
							}
							
						} else {
							// если аддон НЕ бесплатный
							if ( $licensing->isLicenseValid() ) {
								// если лицензия валидна, то аддон можно установить
								if ( ! $component['actived'] ) {
									$status_class = ' plugin-status-deactive';
									$action = 'activate';
									unset( $data_to_print['plugin-action'] );
									$data_to_print[] = 'data-plugin-action="activate"';
								}
							} else {
								if ( $component['actived'] ) {
									// если лицензия не валидна, но аддон уже был активирован
									$action = 'deactivate';
									unset( $data_to_print['plugin-action'] );
									$data_to_print[] = 'data-plugin-action="deactivate"';
								} else {
									// если лицензия не валидна, то показываем ссылку на страницу аддона
									$link = $component['url'];
									$status_class = ' plugin-status-deactive';
									$action = 'read';
								}
							}
						}
						
						$delete_button = ''; // кнопка удаления не используется для фримиус аддонов
					}

					$process_button_classes[] = 'install-now';

					if( $action == 'activate' ) {
						$process_button_classes[] = 'button-primary';
					} else {
						$process_button_classes[] = 'button-default';
					}

					$proccess_button = '<a href="' . esc_attr( $link ) . '" class="' . implode(' ', $process_button_classes) . '"' . implode(' ', $data_to_print) . '>' . $button_i18n[$action] . '</a>';

					?>

					<div class="plugin-card<?= $status_class ?>">
						<div class="plugin-card-top">
							<div class="name column-name">
								<h3>
									<a href="<?= $component['url'] ?>" class="thickbox open-plugin-details-modal">
										<?= $component['title'] ?>
										<img src="<?= $component['icon'] ?>" class="plugin-icon" alt="">
									</a>
								</h3>
							</div>
							<div class="desc column-description">
								<p><?= $component['description']; ?></p>
							</div>
						</div>
						<div class="plugin-card-bottom">
							<?= $delete_button . ' ' . $proccess_button ?>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="clearfix"></div>
			</div>
		<?php
		}

	}


