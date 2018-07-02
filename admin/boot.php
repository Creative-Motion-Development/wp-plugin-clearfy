<?php
	/**
	 * Admin boot
	 * @author Webcraftic <alex.kovalevv@gmail.com>
	 * @copyright Webcraftic 25.05.2017
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * Ошибки совместимости с похожими плагинами
	 */
	function wbcr_clearfy_admin_conflict_notices_error()
	{
		$notices = array();

		$default_notice = WCL_Plugin::app()
				->getPluginTitle() . ': ' . __('We found that you have the plugin %s installed. The functions of this plugin already exist in %s. Please deactivate plugin %s to avoid conflicts between plugins functions.', 'clearfy');
		$default_notice .= ' ' . __('If you do not want to deactivate the plugin %s for some reason, we strongly recommend do not use the same plugins functions at the same time!', 'clearfy');

		if( is_plugin_active('wp-disable/wpperformance.php') ) {
			$notices[] = sprintf($default_notice, 'WP Disable', WCL_Plugin::app()
				->getPluginTitle(), 'WP Disable', 'WP Disable');
		}

		if( empty($notices) ) {
			return;
		}

		?>
		<div id="wbcr-clearfy-conflict-error" class="notice notice-error is-dismissible">
			<?php foreach((array)$notices as $notice): ?>
				<p>
					<?= $notice ?>
				</p>
			<?php endforeach; ?>
		</div>
	<?php
	}

	add_action('admin_notices', 'wbcr_clearfy_admin_conflict_notices_error');
