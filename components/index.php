<?php
// Silence is golden.

/**
 * Регистрируем уведомление для администратора.
 *
 * Если установлен похожий плагин, к пример Cyr to lat, мы должны вывести предупреждение, что наш плагин не совместим
 * c плагином Cyr to lat. Один из плагинов должен быть отключен, иначе могут быть проблемы совместимости.
 *
 * @param array  $notices       Массив со списком всех уведомлений, которые будут напечатыны в админ панели
 * @param string $plugin_name   Имя плагина, передано для того, чтобы выводить уведомления условно, только для конкретного плагина
 */
add_action( 'wbcr_factory_notices_000_list', function ( $notices, $plugin_name ) {

	# Если экшен вызывал плагин не Cyrlitera, то не выводим это уведомления
	if ( $plugin_name != WCTR_Plugin::app()->getPluginName() ) {
		return $notices;
	}

	# Получаем заголовок плагина Cyrlitera
	$plugin_title = WCTR_Plugin::app()->getPluginTitle();

	# Задаем текст уведомления
	$notice_text = $plugin_title . ': ' . __( 'We found that you have the plugin %s installed. The functions of this plugin already exist in %s. Please deactivate plugin %s to avoid conflicts between plugins functions.', 'cyrlitera' );
	$notice_text .= ' ' . __( 'If you do not want to deactivate the plugin %s for some reason, we strongly recommend do not use the same plugins functions at the same time!', 'cyrlitera' );

	# Задаем настройки уведомления
	$notices[] = [
		'id'              => 'cyrlitera_plugin_compatibility',
		// уникальный ID уведомления
		'type'            => 'error',
		// Варианты: error, success, warning
		'dismissible'     => true,
		// Нужна ли кнопка закрыть?
		'dismiss_expires' => 0,
		// Через какое время уведомление снова появится? По умолчанию: 0 скрыть навсегда. Значение в сек.
		'text'            => $notice_text
	];

	return $notices;
}, 10, 2 );