<?php
	/**
	 * Admin boot
	 * @author Webcraftic <alex.kovalevv@gmail.com>
	 * @copyright Webcraftic 25.05.2017
	 * @version 1.0
	 */

	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/includes/classes/class.option.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/includes/classes/class.group.php');

	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/activation.php');
	/*require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/quick-start.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/code-clean.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/privacy.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/seo.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/double-pages.php');
	require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/defence.php');*/

	//$preinsatall_components = (array)$wbcr_clearfy_plugin->options['deactive_preinstall_components'];

	//============================================================
	//                 WIDGETS TOOLS COMPONENT
	//============================================================
	/*if( empty($preinsatall_components) || !in_array('widget_tools', $preinsatall_components) ) {
		require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/widgets.php');
	}*/

	//require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/additionally.php');
	//require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/components.php');

	if( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_clearfy_configurate' ) {
		require(WBCR_CLR_PLUGIN_DIR . '/admin/ajax/configurate.php');
	}

	if( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_clearfy_import_settings' ) {
		require(WBCR_CLR_PLUGIN_DIR . '/admin/ajax/import-settings.php');
	}

	/**
	 * Welcome guid
	 * @param string $hook_suffix
	 */
	function wbcr_enqueue_pointer_script_style($hook_suffix)
	{
		$enqueue_pointer_script_style = false;
		$dismissed_pointers = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));

		if( !in_array('wbcr_clearfy_settings_pointer', $dismissed_pointers) || !in_array('wbcr_clearfy_components_pointer', $dismissed_pointers) ) {
			$enqueue_pointer_script_style = true;
			add_action('admin_print_footer_scripts', 'wbcr_pointer_print_scripts');
		}

		if( $enqueue_pointer_script_style ) {
			wp_enqueue_style('wp-pointer');
			wp_enqueue_script('wp-pointer');
		}
	}

	// todo: remove in 1.2.0
	function wbcr_pointer_print_scripts()
	{
		$dismissed_pointers = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));

		$pointer_setting_content = "<h3>" . __('Welcome to Clearfy (1.1.9)', 'clearfy') . "</h3>";
		$pointer_setting_content .= "<p>" . __('We have moved the plugins menu to the general settings for your comfort. There are new plugin features. Please go to the plugin page to learn more!', 'clearfy') . "</p>";

		$pointer_components_content = "<h3>" . __('Remove the excess from Clearfy (1.1.9)', 'clearfy') . "</h3>";
		$pointer_components_content .= "<p>" . __('We have divided plugin features into components. You can turn off unused plugin functions.', 'clearfy') . "</p>";

		?>

		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function($) {
				<?php if(!in_array('wbcr_clearfy_settings_pointer', $dismissed_pointers)): ?>
				$('#menu-settings').pointer({
					content: '<?php echo $pointer_setting_content; ?>',
					position: {
						edge: 'left', // arrow direction
						align: 'center' // vertical alignment
					},
					pointerWidth: 350,
					close: function() {
						$.post(ajaxurl, {
							pointer: 'wbcr_clearfy_settings_pointer', // pointer ID
							action: 'dismiss-wp-pointer'
						});
					}
				}).pointer('open');
				<?php endif; ?>
				<?php if(!in_array('wbcr_clearfy_components_pointer', $dismissed_pointers)): ?>
				$('#components-wbcr_clearfy-tab').pointer({
					content: '<?php echo $pointer_components_content; ?>',
					position: {
						edge: 'left', // arrow direction
						align: 'center' // vertical alignment
					},
					pointerWidth: 350,
					close: function() {
						$.post(ajaxurl, {
							pointer: 'wbcr_clearfy_components_pointer', // pointer ID
							action: 'dismiss-wp-pointer'
						});
					}
				}).pointer('open');
				<?php endif; ?>
			});
			//]]>
		</script>
	<?php
	}

	add_action('admin_enqueue_scripts', 'wbcr_enqueue_pointer_script_style');
