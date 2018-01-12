<?php
	
	/**
	 * This class configures the code cleanup settings
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */
	class WbcrClearfy_ConfigPrivacy extends WbcrFactoryClearfy_Configurate {

		public function registerActionsAndFilters()
		{
			if( $this->getOption('remove_meta_generator') ) {
				remove_action('wp_head', 'wp_generator');
				add_filter('the_generator', '__return_empty_string');
			}

			/**
			 * Priority set to 9999. Higher numbers correspond with later execution.
			 * Hook into the style loader and remove the version information.
			 */

			if( $this->getOption('remove_style_version') ) {
				add_filter('style_loader_src', array($this, 'hideWordpressVersionInScript'), 9999, 2);
			}

			/**
			 * Hook into the script loader and remove the version information.
			 */

			if( $this->getOption('remove_js_version') ) {
				add_filter('script_loader_src', array($this, 'hideWordpressVersionInScript'), 9999, 2);
			}
		}

		/**
		 * Remove wp version from any enqueued scripts
		 * @param string $target_url
		 * @return string
		 */
		public function hideWordpressVersionInScript($src, $handle)
		{
			if( is_admin() ) {
				return $src;
			}

			$filename_arr = explode('?', basename($src));
			$exclude_file_list = $this->getOption('remove_version_exclude', '');
			$exclude_files_arr = array_map('trim', explode(PHP_EOL, $exclude_file_list));

			if( strpos($src, 'ver=') && !in_array(str_replace('?' . $filename_arr[1], '', $src), $exclude_files_arr, true) ) {
				$src = remove_query_arg('ver', $src);
			}

			return $src;
		}
	}