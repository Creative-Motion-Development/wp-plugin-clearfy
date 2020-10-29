<?php

class WCL_Cache {

	public function __construct()
	{
		if( is_admin() ) {
			return;
		}

		if( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] ) {
			include_once('inc/cache.php');
			$wpfc = new WCL_Create_Cache();
			$wpfc->createCache();
		}
	}

	public static function activate()
	{
		/*if( $options = get_option("WpFastestCache") ) {
			$post = json_decode($options, true);

			include_once('inc/admin.php');
			$wpfc = new WpFastestCacheAdmin();
			$wpfc->modifyHtaccess($post);
		}*/
	}

	public static function deactivate()
	{
		//$wpfc = new WpFastestCache();

		$path = ABSPATH;

		//if( $wpfc->is_subdirectory_install() ) {
		//$path = $wpfc->getABSPATH();
		//}

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