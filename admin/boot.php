<?php
	/**
	 * Admin boot
	 * @author Webcraftic <alex.kovalevv@gmail.com>
	 * @copyright Webcraftic 25.05.2017
	 * @version 1.0
	 */

	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/includes/classes/class.option.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/includes/classes/class.group.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/includes/classes/class.pages.php');

	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/activation.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/quick-start.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/code-clean.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/privacy.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/seo.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/double-pages.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/defence.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/widgets.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/additionally.php');
	//require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/components.php');

	if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_clearfy_configurate' ) {
		require(WBCR_CLR_PLUGIN_DIR . '/admin/ajax/configurate.php');
	}

	if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_clearfy_import_settings' ) {
		require(WBCR_CLR_PLUGIN_DIR . '/admin/ajax/import-settings.php');
	}

	/**
	 * Checking and using additional import
	 */
	function wbcr_clearfy_admin_init()
	{
		wbcr_clearfy_import_old_options();
	}

	add_action('init', 'wbcr_clearfy_admin_init');

	function wbcr_clearfy_before_save_options($form)
	{
		global $wbcr_clearfy_plugin;

		$get_modes = get_option($wbcr_clearfy_plugin->pluginName . '_quick_modes', array());
		$mods_count = sizeof($get_modes);

		if( !empty($get_modes) ) {
			$group_options = WbcrClr_Option::getAllOptions();
			$controls = $form->getControls();

			foreach($controls as $control) {
				$values = $control->getValuesToSave();
				foreach($values as $keyToSave => $valueToSave) {
					foreach($group_options as $option) {
						if( $keyToSave == $option->getName() ) {
							foreach($get_modes as $mode_key => $mode_name) {
								if( $option->hasGroup($mode_name) ) {
									$option_value = $option->getValue($mode_name);
									if( !empty($option_value) ) {
										if( $option_value != $valueToSave ) {
											unset($get_modes[$mode_key]);
										}
									} else {
										if( is_numeric($valueToSave) && 0 === intval($valueToSave) ) {
											unset($get_modes[$mode_key]);
										}
									}
								}
							}
						}
					}
				}
			}

			if( $mods_count != sizeof($get_modes) ) {
				if( empty($get_modes) ) {
					delete_option($wbcr_clearfy_plugin->pluginName . '_quick_modes');
				} else {
					update_option($wbcr_clearfy_plugin->pluginName . '_quick_modes', $get_modes);
				}
			}
		}
	}

	add_action('wbcr_factory_imppage_before_save', 'wbcr_clearfy_before_save_options');




