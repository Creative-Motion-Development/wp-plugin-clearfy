<?php
	/**
	 * Helpers functions
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	/**
	 * @param string $return
	 * @return array|string
	 */
	function wbcr_get_export_options($return = 'json')
	{
		global $wpdb, $wbcr_clearfy_plugin;

		$export_options = array();
		$request = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name LIKE '" . $wbcr_clearfy_plugin->pluginName . "_%'");

		if( !empty($request) ) {
			foreach($request as $option) {
				$export_options[$option->option_name] = $option->option_value;
			}
		}

		if( $return == 'array' ) {
			return $export_options;
		}

		return wbcr_get_escape_json($export_options);
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
	function wbcr_array_merge_insert(array $arr, array $inserted, $position = 'bottom', $key = null)
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
	function wbcr_maybe_get_post_json($name)
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
	function wbcr_get_escape_json(array $data)
	{
		return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Html minify function by Tim Eckel<tim@leethost.com>
	 * @param $buffer
	 * @return mixed
	 */
	function wbcr_clearfy_minify_html_output($buffer)
	{
		if( substr(ltrim($buffer), 0, 5) == '<?xml' ) {
			return ($buffer);
		}
		$minify_javascript = get_option('minify_javascript');
		$minify_html_comments = get_option('minify_html_comments');
		$minify_html_utf8 = get_option('minify_html_utf8');
		if( $minify_html_utf8 == 'yes' && mb_detect_encoding($buffer, 'UTF-8', true) ) {
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
					if( $minify_html_comments != 'no' ) {
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
					if( $minify_html_comments != 'no' ) {
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
			if( $minify_html_comments != 'no' ) {
				$process = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->' . $mod, '', $process);
			}
			$buffer .= $process . $asis;
		}
		$buffer = str_replace(array(
			chr(10) . '<script',
			chr(10) . '<style',
			'*/' . chr(10),
			'M1N1FY-ST4RT'
		), array('<script', '<style', '*/', ''), $buffer);
		$minify_html_xhtml = get_option('minify_html_xhtml');
		$minify_html_relative = get_option('minify_html_relative');
		$minify_html_scheme = get_option('minify_html_scheme');
		if( $minify_html_xhtml == 'yes' && strtolower(substr(ltrim($buffer), 0, 15)) == '<!doctype html>' ) {
			$buffer = str_replace(' />', '>', $buffer);
		}
		if( $minify_html_relative == 'yes' ) {
			$buffer = str_replace(array(
				'https://' . $_SERVER['HTTP_HOST'] . '/',
				'http://' . $_SERVER['HTTP_HOST'] . '/',
				'//' . $_SERVER['HTTP_HOST'] . '/'
			), array('/', '/', '/'), $buffer);
		}
		if( $minify_html_scheme == 'yes' ) {
			$buffer = str_replace(array('http://', 'https://'), '//', $buffer);
		}

		return ($buffer);
	}

	/**
	 * Componate content for robot.txt
	 * @return string
	 */
	function wbcr_clearfy_get_right_robot_txt()
	{
		$site_url = get_home_url();
		$dir_host = preg_replace("(^https?://)", "", $site_url);

		if( is_ssl() ) {
			$dir_host = 'https://' . $dir_host;
		}

		$file_path = WBCR_CLR_PLUGIN_DIR . '/templates/robots.txt';
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

		return $output;
	}