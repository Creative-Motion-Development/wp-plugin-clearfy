<?php

	/**
	 * The page Settings.
	 *
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	class WCL_ClearfySettingsPage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "clearfy_settings";

		/**
		 * @var string
		 */
		public $page_parent_page = 'none';

		/**
		 * @var string
		 */
		public $page_menu_dashicon = 'dashicons-list-view';

		/**
		 * @var int
		 */
		public $page_menu_position = 1;

		/**
		 * @var bool
		 */
		public $available_for_multisite = true;

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Clearfy Settings', 'clearfy');
			$this->page_menu_short_description = __('Useful tweaks', 'clearfy');

			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		/**
		 * Permalinks options.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getOptions()
		{

			$options = array();

			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Clearfy options', 'clearfy') . '</strong>' . '<p>' . __('This group of settings allows you to configure the work of the Clearfy plugin.', 'clearfy') . '</p>' . '</div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_clearfy_extra_menu',
				'title' => __('Disable menu in adminbar', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'red'),
				'hint' => __('This setting allows you to disable the additional menu of the Clearfy plugin, in the admin bar. This menu is required to work with the Minify and Combine and Assets Manager components.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'save_all_settings_after_uninstall',
				'title' => __('Save all settings', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'green'),
				'hint' => __('Если Вкл, настройки плагина будет хранится в базе данных, даже если вы удалите плагин. Это полезно, если у вас возникла проблема с плагинов и вы решели вернуться к предидущей версии. Отключите эту опцию, если вы хотите, чтобы после удаления плагины все настройки были удалены.', 'clearfy'),
				'default' => false
			);

			$formOptions = array();

			$formOptions[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr/clearfy/settings_form_options', $formOptions, $this);
		}
	}
