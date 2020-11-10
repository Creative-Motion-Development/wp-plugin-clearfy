<?php
require_once 'includes/helpers.php';
include_once('includes/cache.php');

class WCL_Cache {

	public function __construct()
	{
		add_action('init', function () {
			if( current_user_can('manage_options') && isset($_GET['wclearfy_cache_delete']) ) {
				WCL_Cache_Helpers::deleteCache();
				wp_redirect(remove_query_arg('wclearfy_cache_delete'));
				die();
			}
		});

		if( is_admin() ) {
			return;
		}

		if( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] ) {
			$wpfc = new WCL_Create_Cache();
			$wpfc->createCache();
		}
	}

	public static function activate()
	{
		if( WCL_Plugin::app()->getPopulateOption('enable_cache') ) {
			WCL_Cache_Helpers::modifyHtaccess();
		}
	}

	public static function deactivate()
	{
		$path = ABSPATH;

		if( WCL_Cache_Helpers::is_subdirectory_install() ) {
			$path = WCL_Cache_Helpers::getABSPATH();
		}

		if( is_file($path . ".htaccess") && is_writable($path . ".htaccess") ) {
			$htaccess = file_get_contents($path . ".htaccess");
			$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?LBCWpFastestCache.*?#\s?END\s?LBCWpFastestCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?WEBPWpFastestCache.*?#\s?END\s?WEBPWpFastestCache/s", "", $htaccess);
			@file_put_contents($path . ".htaccess", $htaccess);
		}
		//$wpfc->deleteCache();
	}
}

new WCL_Cache();