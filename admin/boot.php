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
				->getPluginTitle() . ': ' . __('Мы обнаружили у вас установленный плагин %s, функции этого плагина уже есть в %s. Пожалуйста, деактивируйте плагин %s, чтобы это не вызвало конфликт плагинов.', 'webcraftic-updates-manager');
		$default_notice .= ' ' . __('Если вы по какой-то причине не хотите деактивировать плагин %s, то пожалуйста, не используйте похожие функции плагинов одновременно!', 'webcraftic-updates-manager');;

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
		$pointer_setting_content .= "<p>" . __('В плагине появились новые функции оптимизации и защиты. Мы рекомендумаем вам ознакомится с ними, возможно, они будут для вас полезными!', 'clearfy') . "</p>";

		$pointer_performance_content = "<h3>" . __('Переименован раздел "Очистка кода"', 'clearfy') . "</h3>";
		$pointer_performance_content .= "<p>" . __('Добавлена асинхронная загрузка шрифтов, оптимизация Google аналитики, отключение шрифтов и карт, отключени граваторов, иконок.', 'clearfy') . "</p>";

		$pointer_defence_content = "<h3>" . __('Добавлена защита страницы логина', 'clearfy') . "</h3>";
		$pointer_defence_content .= "<p>" . __('В новой версии Clearfy, вы можете использовать функции скрытия страницы логина. Никто не узнает адрес вашей страницы логина, а значит не будет переборов пароля и уменьшатся попытки взлома вашего сайта.', 'clearfy') . "</p>";

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
