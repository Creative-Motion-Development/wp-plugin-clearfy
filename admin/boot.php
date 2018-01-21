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
