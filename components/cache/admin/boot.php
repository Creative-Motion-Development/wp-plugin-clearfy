<?php
/**
 * Clearfy quick start options
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 03.06.2020, Webcraftic
 * @version       1.0
 */

add_filter("wbcr_clearfy_group_options", function ($options) {
	$options[] = [
		'name' => 'enable_cache',
		'title' => __('Enable cache', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'dont_cache_for_logged_in_users',
		'title' => __('Don\'t cache for logged-in users', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'cache_reject_uri',
		'title' => __('Never Cache URL(s)', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'preload_cache',
		'title' => __('Preload cache', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'clear_cache_for_newpost',
		'title' => __('Clear cache for new post', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'exclude_files',
		'title' => __('Filenames that can be cached', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'exclude_pages',
		'title' => __('Rejected User Agents', 'clearfy'),
		'tags' => []
	];

	return $options;
});

add_filter('wclearfy/setup_wizard/speed_optimize_step/form_options', function ($options) {
	array_unshift($options, [
		'type' => 'checkbox',
		'way' => 'buttons',
		'name' => 'enable_cache',
		'title' => __('Enable cache', 'clearfy'),
		'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
		'hint' => __('This option enable cache to generates static html files from your dynamic WordPress blog. After a html file is generated your webserver will serve that file instead of processing the comparatively heavier and more expensive WordPress PHP scripts.', 'clearfy'),
		'default' => true
	]);

	return $options;
});

add_action('wclearfy/setup_wizard/speed_optimize_step/continue_step', function () {
	require_once WCACHE_PLUGIN_DIR . '/includes/includes/helpers.php';
	try {
		\WCL_Cache_Helpers::modifyHtaccess();
	} catch( \Exception $e ) {

	}
});

add_action('wfactory/activated_' . WCL_Plugin::app()->getPluginName() . '_component', function ($component_name) {
	//todo: Проверить совместимость с плагинами, включить или отключить компонент в зависимсти от результатов
});