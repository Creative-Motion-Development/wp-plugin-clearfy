<?php
require_once WCACHE_PLUGIN_DIR . '/includes/helpers.php';
require_once WCACHE_PLUGIN_DIR . '/includes/create-cache.php';

class WCL_Cache {

	public function __construct()
	{
		add_action('transition_post_status', array($this, 'on_all_status_transitions'), 10, 3);

		add_action('init', function () {
			if( current_user_can('manage_options') && isset($_GET['wclearfy_cache_delete']) ) {
				WCL_Cache_Helpers::deleteCache();
				wp_redirect(esc_url_raw(remove_query_arg('wclearfy_cache_delete')));
				die();
			}
		});

		if( !is_admin() ) {
			if( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] ) {
				$cache = new WCL_Create_Cache();
				$cache->createCache();
			}
		}
	}

	public function on_all_status_transitions($new_status, $old_status, $post)
	{
		if( !WCL_Plugin::app()->getPopulateOption('enable_cache') ) {
			return false;
		}

		if( !wp_is_post_revision($post->ID) ) {
			if( isset($post->post_type) ) {
				if( $post->post_type == "nf_sub" ) {
					return false;
				}
			}

			if( WCL_Plugin::app()->getPopulateOption('clear_cache_for_newpost') ) {
				if( $new_status == "publish" && $old_status != "publish" ) {

					WCL_Cache_Helpers::deleteHomePageCache();

					//to clear category cache and tag cache
					WCL_Cache_Helpers::singleDeleteCache(false, $post->ID);

					//to clear widget cache
					WCL_Cache_Helpers::deleteWidgetCache();
				}
			}

			if( WCL_Plugin::app()->getPopulateOption('clear_cache_for_updated_post') ) {
				if( $new_status == "publish" && $old_status == "publish" ) {
					WCL_Cache_Helpers::singleDeleteCache(false, $post->ID);

					//to clear widget cache
					WCL_Cache_Helpers::deleteWidgetCache();
				}
			}

			if( $new_status == "trash" && $old_status == "publish" ) {
				WCL_Cache_Helpers::singleDeleteCache(false, $post->ID);
			} else if( ($new_status == "draft" || $new_status == "pending" || $new_status == "private") && $old_status == "publish" ) {
				WCL_Cache_Helpers::deleteCache();
			}
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
			$htaccess = preg_replace("/#\s?BEGIN\s?WClearfyCache.*?#\s?END\s?WClearfyCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?GzipWClearfyCache.*?#\s?END\s?GzipWClearfyCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?LBCWClearfyCache.*?#\s?END\s?LBCWClearfyCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?WEBPWClearfyCache.*?#\s?END\s?WEBPWClearfyCache/s", "", $htaccess);
			@file_put_contents($path . ".htaccess", $htaccess);
		}
		//$wclearfy->deleteCache();
	}
}

new WCL_Cache();