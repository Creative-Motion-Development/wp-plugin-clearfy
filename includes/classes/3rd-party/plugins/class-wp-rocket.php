<?php
/**
 * Thirdparty Forms
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 12.01.2021, CreativeMotion
 * @version 1.0
 */

namespace Clearfy\ThirdParty;

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

final class Wp_Rocket extends Base {

	private $wp_rocket_options;

	protected $defualt_disabled_options = [
		'disable_emoji',
		'remove_js_version',
		'remove_style_version',
		//todo: Starting WP Rocket V 3.7 (August 2020), HTML Minify has been removed.
		//'html_optimize',
		'move_js_to_footer',
		'dont_move_jquery_to_footer'
	];

	protected $map_options = [
		'css_optimize' => 'minify_css',
		'js_optimize' => 'minify_js',
		'ajs_enabled' => 'defer_all_js',
		'disable_embeds' => 'embeds'
	];

	public function __construct()
	{
		$this->wp_rocket_options = get_option('wp_rocket_settings');
	}

	public function get_3rd_plugin_option($option_name)
	{
		return isset($this->wp_rocket_options[$option_name]) ? $this->wp_rocket_options[$option_name] : null;
	}
}