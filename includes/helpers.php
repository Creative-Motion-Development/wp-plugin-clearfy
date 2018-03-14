<?php

	/**
	 * Helpers functions
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_Helper {

		/**
		 * Is permalink enabled?
		 * @global WP_Rewrite $wp_rewrite
		 * @since 1.0.0
		 * @return bool
		 */
		public static function isPermalink()
		{
			global $wp_rewrite;

			if( !isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks() ) {
				return false;
			}

			return true;
		}

		/**
		 * Получает и возвращает все опции разрешенные для экспорта
		 *
		 * @param string $return
		 * @return array|string
		 */
		public static function getExportOptions($return = 'json')
		{
			global $wpdb;

			$export_options = array();

			$options = WCL_Option::getAllOptions();

			$allow_export_options = array();

			foreach((array)$options as $option_class) {
				$option_name = $option_class->getName();
				$allow_export_options[] = WCL_Plugin::app()->getOptionName($option_name);
			}

			$request = $wpdb->get_results($wpdb->prepare("
				SELECT option_name, option_value
				FROM {$wpdb->prefix}options
				WHERE option_name
				LIKE '%s'", WCL_Plugin::app()->getPrefix() . "_%"));

			if( !empty($request) && !empty($allow_export_options) ) {
				foreach($request as $option) {
					if( in_array($option->option_name, $allow_export_options) ) {
						$export_options[$option->option_name] = $option->option_value;
					}
				}
			}

			if( $return == 'array' ) {
				return $export_options;
			}

			return WCL_Helper::getEscapeJson($export_options);
		}

		/**
		 * Merge arrays, inserting $arr2 into $arr1 before/after certain key
		 *
		 * @param array $arr Modifyed array
		 * @param array $inserted Inserted array
		 * @param string $position 'before' / 'after' / 'top' / 'bottom'
		 * @param string $key Associative key of $arr1 for before/after insertion
		 *
		 * @return array
		 */
		public static function arrayMergeInsert(array $arr, array $inserted, $position = 'bottom', $key = null)
		{
			if( $position == 'top' ) {
				return array_merge($inserted, $arr);
			}
			$key_position = ($key === null)
				? false
				: array_search($key, array_keys($arr));
			if( $key_position === false OR ($position != 'before' AND $position != 'after') ) {
				return array_merge($arr, $inserted);
			}
			if( $position == 'after' ) {
				$key_position++;
			}

			return array_merge(array_slice($arr, 0, $key_position, true), $inserted, array_slice($arr, $key_position, null, true));
		}

		/**
		 * Try to get variable from JSON-encoded post variable
		 *
		 * Note: we pass some params via json-encoded variables, as via pure post some data (ex empty array) will be absent
		 *
		 * @param string $name $_POST's variable name
		 *
		 * @return array
		 */
		public static function maybeGetPostJson($name)
		{
			if( isset($_POST[$name]) AND is_string($_POST[$name]) ) {
				$result = json_decode(stripslashes($_POST[$name]), true);
				if( !is_array($result) ) {
					$result = array();
				}

				return $result;
			} else {
				return array();
			}
		}

		/**
		 * Escape json data
		 * @param  array $data
		 * @return string escaped json string
		 */
		public static function getEscapeJson(array $data)
		{
			return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
		}

		/**
		 * Html minify function by Tim Eckel<tim@leethost.com>
		 * @param $buffer
		 * @return mixed
		 */

		public static function minifyHtml($buffer)
		{
			if( substr(ltrim($buffer), 0, 5) == '<?xml' ) {
				return ($buffer);
			}

			$minify_javascript = WCL_Plugin::app()->getOption('minify_javascript');
			$minify_html_comments = WCL_Plugin::app()->getOption('minify_html_comments');
			$minify_html_utf8 = WCL_Plugin::app()->getOption('minify_html_utf8');

			if( $minify_html_utf8 && mb_detect_encoding($buffer, 'UTF-8', true) ) {
				$mod = '/u';
			} else {
				$mod = '/s';
			}
			$buffer = str_replace(array(chr(13) . chr(10), chr(9)), array(chr(10), ''), $buffer);
			$buffer = str_ireplace(array(
				'<script',
				'/script>',
				'<pre',
				'/pre>',
				'<textarea',
				'/textarea>',
				'<style',
				'/style>'
			), array(
				'M1N1FY-ST4RT<script',
				'/script>M1N1FY-3ND',
				'M1N1FY-ST4RT<pre',
				'/pre>M1N1FY-3ND',
				'M1N1FY-ST4RT<textarea',
				'/textarea>M1N1FY-3ND',
				'M1N1FY-ST4RT<style',
				'/style>M1N1FY-3ND'
			), $buffer);
			$split = explode('M1N1FY-3ND', $buffer);
			$buffer = '';
			for($i = 0; $i < count($split); $i++) {
				$ii = strpos($split[$i], 'M1N1FY-ST4RT');
				if( $ii !== false ) {
					$process = substr($split[$i], 0, $ii);
					$asis = substr($split[$i], $ii + 12);
					if( substr($asis, 0, 7) == '<script' ) {
						$split2 = explode(chr(10), $asis);
						$asis = '';
						for($iii = 0; $iii < count($split2); $iii++) {
							if( $split2[$iii] ) {
								$asis .= trim($split2[$iii]) . chr(10);
							}
							if( $minify_javascript != 'no' ) {
								if( strpos($split2[$iii], '//') !== false && substr(trim($split2[$iii]), -1) == ';' ) {
									$asis .= chr(10);
								}
							}
						}
						if( $asis ) {
							$asis = substr($asis, 0, -1);
						}
						if( $minify_html_comments ) {
							$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
						}
						if( $minify_javascript != 'no' ) {
							$asis = str_replace(array(
								';' . chr(10),
								'>' . chr(10),
								'{' . chr(10),
								'}' . chr(10),
								',' . chr(10)
							), array(';', '>', '{', '}', ','), $asis);
						}
					} else if( substr($asis, 0, 6) == '<style' ) {
						$asis = preg_replace(array('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array(
							'>',
							'<',
							'\\1'
						), $asis);
						if( $minify_html_comments ) {
							$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
						}
						$asis = str_replace(array(
							chr(10),
							' {',
							'{ ',
							' }',
							'} ',
							'( ',
							' )',
							' :',
							': ',
							' ;',
							'; ',
							' ,',
							', ',
							';}'
						), array('', '{', '{', '}', '}', '(', ')', ':', ':', ';', ';', ',', ',', '}'), $asis);
					}
				} else {
					$process = $split[$i];
					$asis = '';
				}
				$process = preg_replace(array('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array(
					'>',
					'<',
					'\\1'
				), $process);
				if( $minify_html_comments ) {
					$process = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|\s?ngg_resource|>))(?:(?!-->).)*-->' . $mod, '', $process);
				}
				$buffer .= $process . $asis;
			}
			$buffer = str_replace(array(
				chr(10) . '<script',
				chr(10) . '<style',
				'*/' . chr(10),
				'M1N1FY-ST4RT'
			), array('<script', '<style', '*/', ''), $buffer);

			$minify_html_xhtml = WCL_Plugin::app()->getOption('minify_html_xhtml');
			$minify_html_relative = WCL_Plugin::app()->getOption('minify_html_relative');
			$minify_html_scheme = WCL_Plugin::app()->getOption('minify_html_scheme');

			if( $minify_html_xhtml && strtolower(substr(ltrim($buffer), 0, 15)) == '<!doctype html>' ) {
				$buffer = str_replace(' />', '>', $buffer);
			}
			if( $minify_html_relative ) {
				$buffer = str_replace(array(
					'https://' . $_SERVER['HTTP_HOST'] . '/',
					'http://' . $_SERVER['HTTP_HOST'] . '/',
					'//' . $_SERVER['HTTP_HOST'] . '/'
				), array('/', '/', '/'), $buffer);
			}
			if( $minify_html_scheme ) {
				$buffer = str_replace(array('http://', 'https://'), '//', $buffer);
			}

			return ($buffer);
		}

		/**
		 * Componate content for robot.txt
		 * @return string
		 */
		public static function getRightRobotTxt()
		{
			$cache_output = WCL_Plugin::app()->getOption('robots_txt_text_cache');

			if( $cache_output ) {
				return $cache_output;
			}
			
			$site_url = get_home_url();
			$dir_host = preg_replace("(^https?://)", "", $site_url);

			if( is_ssl() ) {
				$dir_host = 'https://' . $dir_host;
			}

			$file_path = WCL_PLUGIN_DIR . '/templates/robots.txt';
			$file = fopen($file_path, 'r');
			$robot_default_content = fread($file, filesize($file_path));
			fclose($file);

			$output = $robot_default_content;
			$output .= 'Host: ' . $dir_host . PHP_EOL;

			$headers = @get_headers($site_url . '/sitemap.xml', 1);

			if( strpos($headers[0], '200 OK') !== false ) {
				$output .= 'Sitemap: ' . $site_url . '/sitemap.xml' . PHP_EOL;
			} else if( isset($headers['Location']) && !empty($headers['Location']) ) {
				$output .= 'Sitemap: ' . $headers['Location'] . PHP_EOL;
			}

			WCL_Plugin::app()->updateOption('robots_txt_text_cache', $output);

			return $output;
		}
	}
