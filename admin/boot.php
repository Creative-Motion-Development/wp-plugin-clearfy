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

	/**
	 * Welcome guid
	 * @param string $hook_suffix
	 */
	function wbcr_enqueue_pointer_script_style($hook_suffix)
	{
		$enqueue_pointer_script_style = false;
		$dismissed_pointers = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));

		if( !in_array('wbcr_clearfy_defence_pointer_1_2_0', $dismissed_pointers) || !in_array('wbcr_clearfy_settings_pointer_1_2_0', $dismissed_pointers) || !in_array('wbcr_clearfy_components_pointer_1_2_0', $dismissed_pointers) ) {
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

		$pointer_setting_content = "<h3>" . sprintf(__('Welcome to Clearfy (%s)', 'clearfy'), '1.2.0') . "</h3>";
		$pointer_setting_content .= "<p>" . __('There are new optimization and protection features in the plugin. We recommend you to look at them maybe they will be useful!', 'clearfy') . "</p>";

		$pointer_performance_content = "<h3>" . __('The section "Code cleaning" was renamed', 'clearfy') . "</h3>";
		$pointer_performance_content .= "<p>" . __('Asynchronous Google fonts loading, Google Analytics optimization, disabling Google Fonts and Maps, disabling of gravatars, Font Awesome icons was added.', 'clearfy') . "</p>";

		$pointer_defence_content = "<h3>" . __('Login page protection added', 'clearfy') . "</h3>";
		$pointer_defence_content .= "<p>" . __('With the new Clearfy version, you can use the hide login page function. Nobody will know the address of your login page, which means there will be no password bruteforce and the attempts of your website hack will decrease.', 'clearfy') . "</p>";

		?>

		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function($) {
				<?php if(!in_array('wbcr_clearfy_settings_pointer_1_2_0', $dismissed_pointers)): ?>
				$('#menu-settings').pointer({
					content: '<?php echo $pointer_setting_content; ?>',
					position: {
						edge: 'left', // arrow direction
						align: 'center' // vertical alignment
					},
					pointerWidth: 350,
					close: function() {
						$.post(ajaxurl, {
							pointer: 'wbcr_clearfy_settings_pointer_1_2_0', // pointer ID
							action: 'dismiss-wp-pointer'
						});
					}
				}).pointer('open');
				<?php endif; ?>
				<?php if(!in_array('wbcr_clearfy_performance_pointer_1_2_0', $dismissed_pointers) && in_array('wbcr_clearfy_settings_pointer_1_2_0', $dismissed_pointers)): ?>
				$('#performance-clearfy-tab').pointer({
					content: '<?php echo $pointer_performance_content; ?>',
					position: {
						edge: 'left', // arrow direction
						align: 'center' // vertical alignment
					},
					pointerWidth: 350,
					close: function() {
						$.post(ajaxurl, {
							pointer: 'wbcr_clearfy_performance_pointer_1_2_0', // pointer ID
							action: 'dismiss-wp-pointer'
						});
					}
				}).pointer('open');
				<?php endif; ?>
				<?php if(!in_array('wbcr_clearfy_defence_pointer_1_2_0', $dismissed_pointers) && in_array('wbcr_clearfy_performance_pointer_1_2_0', $dismissed_pointers)): ?>
				$('#defence-clearfy-tab').pointer({
					content: '<?php echo $pointer_defence_content; ?>',
					position: {
						edge: 'left', // arrow direction
						align: 'center' // vertical alignment
					},
					pointerWidth: 350,
					close: function() {
						$.post(ajaxurl, {
							pointer: 'wbcr_clearfy_defence_pointer_1_2_0', // pointer ID
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
