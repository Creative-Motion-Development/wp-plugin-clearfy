<?php
/**
 * This class configures the parameters advanced
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 2017 Webraftic Ltd
 * @version 1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCL_CDN {

	/**
	 * Home URL host
	 *
	 * @var string
	 */
	private $home_host;


	public function __construct()
	{
		if( !is_admin() && WCL_Plugin::app()->getPopulateOption('enable_cdn') ) {
			add_action("template_redirect", [$this, "do_rewrite"]);
			add_action("wp_head", [$this, "dns_prefetch"], 0);
		}

		add_action('wmac_action_cachepurged', [$this, 'clear_cdn_cache']);
		add_action('wclearfy_delete_cache', [$this, 'clear_cdn_cache']);
	}


	public function do_rewrite()
	{
		ob_start(array($this, 'rewrite'));
	}

	public function dns_prefetch()
	{
		$cdn_url = untrailingslashit(trim(WCL_Plugin::app()->getPopulateOption('cdn_cname')));
		if( !empty($cdn_url) ) {
			echo "<link rel='dns-prefetch' href='//{$cdn_url}' />";
		}
	}

	/**
	 * Search & Replace URLs with the CDN URLs in the provided content
	 *
	 * @param string $html HTML content.
	 * @return string
	 *
	 *
	 */
	public function rewrite($html)
	{
		$pattern = '#[("\']\s*(?<url>(?:(?:https?:|)' . preg_quote($this->get_base_url(), '#') . ')\/(?:(?:(?:' . $this->get_allowed_paths() . ')[^"\',)]+))|\/[^/](?:[^"\')\s>]+\.[[:alnum:]]+))\s*["\')]#i';

		return preg_replace_callback($pattern, function ($matches) {
			return str_replace($matches['url'], $this->rewrite_url($matches['url']), $matches[0]);
		}, $html);
	}

	/**
	 * Rewrites URLs in a srcset attribute using the CDN URL
	 *
	 * @param string $html HTML content.
	 * @return string
	 *
	 *
	 */
	public function rewrite_srcset($html)
	{
		$pattern = '#\s+(?:data-lazy-|data-)?srcset\s*=\s*["\']\s*(?<sources>[^"\',\s]+\.[^"\',\s]+(?:\s+\d+[wx])?(?:\s*,\s*[^"\',\s]+\.[^"\',\s]+\s+\d+[wx])*)\s*["\']#i';

		if( !preg_match_all($pattern, $html, $srcsets, PREG_SET_ORDER) ) {
			return $html;
		}
		foreach($srcsets as $srcset) {
			$sources = explode(',', $srcset['sources']);
			$sources = array_unique(array_map('trim', $sources));
			$cdn_srcset = $srcset['sources'];
			foreach($sources as $source) {
				$url = preg_split('#\s+#', trim($source));
				$cdn_srcset = str_replace($url[0], $this->rewrite_url($url[0]), $cdn_srcset);
			}

			$cdn_srcsets = str_replace($srcset['sources'], $cdn_srcset, $srcset[0]);
			$html = str_replace($srcset[0], $cdn_srcsets, $html);
		}

		return $html;
	}

	/**
	 * Rewrites an URL with the CDN URL
	 *
	 * @param string $url Original URL.
	 * @return string
	 *
	 *
	 */
	public function rewrite_url($url)
	{
		$cdn_zone = WCL_Plugin::app()->getPopulateOption('cdn_zone');

		if( !$this->is_excluded($url) && "all" === $cdn_zone || in_array($cdn_zone, $this->get_zones_for_url($url)) ) {
			$parsed_url = wp_parse_url($url);
			$cdn_url = untrailingslashit(trim(WCL_Plugin::app()->getPopulateOption('cdn_cname')));

			if( empty($cdn_url) ) {
				return $url;
			}

			if( !isset($parsed_url['host']) ) {
				return $this->add_url_protocol($cdn_url . '/' . ltrim($url, '/'));
			}

			$home_host = $this->get_home_host();

			if( !isset($parsed_url['scheme']) ) {
				return str_replace($home_host, $this->remove_url_protocol($cdn_url), $url);
			}

			$home_url = [
				'http://' . $home_host,
				'https://' . $home_host,
			];

			return str_replace($home_url, $this->add_url_protocol($cdn_url), $url);
		}

		return $url;
	}

	/**
	 * Rewrites URLs to CDN URLs in CSS content
	 *
	 * @param string $content CSS content.
	 * @return string
	 *
	 *
	 */
	public function rewrite_css_properties($content)
	{
		if( !preg_match_all('#url\(\s*(\'|")?\s*(?![\'"]?data)(?<url>(?:https?:|)' . preg_quote($this->get_base_url(), '#') . '\/[^"|\'|\)|\s]+)\s*#i', $content, $matches, PREG_SET_ORDER) ) {
			return $content;
		}

		foreach($matches as $property) {
			/**
			 * Filters the URL of the CSS property
			 *
			 * @param string $url URL of the CSS property.
			 *
			 *
			 */
			$cdn_url = $this->rewrite_url(apply_filters('wclearfy/performance/cdn/css_properties_url', $property['url']));
			$replacement = str_replace($property['url'], $cdn_url, $property[0]);
			$content = str_replace($property[0], $replacement, $content);
		}

		return $content;
	}

	/**
	 * Gets the base URL for the website
	 *
	 * @return string
	 * @author Remy Perona
	 *
	 *
	 */
	private function get_base_url()
	{
		return '//' . $this->get_home_host();
	}

	/**
	 * Gets the allowed paths as a regex pattern for the CDN rewrite
	 *
	 * @return string
	 *
	 */
	private function get_allowed_paths()
	{
		$wp_content_dirname = ltrim(trailingslashit(wp_parse_url(content_url(), PHP_URL_PATH)), '/');
		$wp_includes_dirname = ltrim(trailingslashit(wp_parse_url(includes_url(), PHP_URL_PATH)), '/');

		$upload_dirname = '';
		$uploads_info = wp_upload_dir();

		if( !empty($uploads_info['baseurl']) ) {
			$upload_dirname = '|' . ltrim(trailingslashit(wp_parse_url($uploads_info['baseurl'], PHP_URL_PATH)), '/');
		}

		return $wp_content_dirname . $upload_dirname . '|' . $wp_includes_dirname;
	}

	/**
	 * Checks if the provided URL can be rewritten with the CDN URL
	 *
	 * @param string $url URL to check.
	 * @return boolean
	 *
	 */
	public function is_excluded($url)
	{
		$path = wp_parse_url($url, PHP_URL_PATH);

		$excluded_extensions = [
			'php',
			'html',
			'htm',
		];

		if( in_array(pathinfo($path, PATHINFO_EXTENSION), $excluded_extensions, true) ) {
			return true;
		}

		if( !$path ) {
			return true;
		}

		if( '/' === $path ) {
			return true;
		}

		if( preg_match('#^(' . $this->get_excluded_files('#') . ')$#', $path) ) {
			return true;
		}

		return false;
	}

	/**
	 * Gets the home URL host
	 *
	 * @return string
	 *
	 */
	private function get_home_host()
	{
		if( empty($this->home_host) ) {
			$this->home_host = wp_parse_url(home_url(), PHP_URL_HOST);
		}

		return $this->home_host;
	}

	/**
	 * Gets the CDN zones for the provided URL
	 *
	 * @param string $url URL to check.
	 * @return array
	 *
	 */
	private function get_zones_for_url($url)
	{
		$zones = ['all'];

		$ext = pathinfo(wp_parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

		$image_types = [
			'jpg',
			'jpeg',
			'jpe',
			'png',
			'gif',
			'webp',
			'bmp',
			'tiff',
			'svg',
		];

		if( 'css' === $ext || 'js' === $ext ) {
			$zones[] = 'css_and_js';
		}

		if( 'css' === $ext ) {
			$zones[] = 'css';
		}

		if( 'js' === $ext ) {
			$zones[] = 'js';
		}

		if( in_array($ext, $image_types, true) ) {
			$zones[] = 'images';
		}

		return $zones;
	}

	/**
	 * Get all files we don't allow to get in CDN.
	 *
	 * @param string $delimiter RegEx delimiter.
	 * @return string A pipe-separated list of excluded files.
	 *
	 */
	private function get_excluded_files($delimiter)
	{
		//$files = $this->options->get('cdn_reject_files', []);
		$files = [];

		/**
		 * Filter the excluded files.
		 *
		 * @param array $files List of excluded files.
		 *
		 */
		$files = (array)apply_filters('wclearfy/performance/cdn/reject_files', $files);
		$files = array_filter($files);

		if( !$files ) {
			return '';
		}

		$files = array_flip(array_flip($files));
		$files = array_map(function ($file) use ($delimiter) {
			return str_replace($delimiter, '\\' . $delimiter, $file);
		}, $files);

		return implode('|', $files);
	}

	/**
	 * Add HTTP protocol to an url that does not have it.
	 *
	 * @param string $url The URL to parse.
	 *
	 * @return string $url The URL with protocol.
	 *
	 *
	 */
	public function add_url_protocol($url)
	{
		// Bail out if the URL starts with http:// or https://.
		if( strpos($url, 'http://') !== false || strpos($url, 'https://') !== false ) {
			return $url;
		}

		if( substr($url, 0, 2) !== '//' ) {
			$url = '//' . $url;
		}

		return set_url_scheme($url);
	}

	/**
	 * Get an url without HTTP protocol
	 *
	 * @param string $url The URL to parse.
	 * @param bool $no_dots (default: false).
	 * @return string $url The URL without protocol
	 *
	 *
	 */
	public function remove_url_protocol($url, $no_dots = false)
	{
		$url = preg_replace('#^(https?:)?\/\/#im', '', $url);

		/** This filter is documented in inc/front/htaccess.php */
		if( apply_filters('wclearfy/performance/cdn/url_no_dots', $no_dots) ) {
			$url = str_replace('.', '_', $url);
		}

		return $url;
	}


	public function clear_cdn_cache()
	{
		if( current_user_can('manage_options') && WCL_Plugin::app()->getPopulateOption('enable_cdn') ) {
			$cdn_url = untrailingslashit(trim(WCL_Plugin::app()->getPopulateOption('cdn_cname')));
			if( false !== strpos($cdn_url, 'b-cdn.net') ) {
				$this->bunny_cdn_clear_cache($cdn_url);
			} else if( false !== strpos($cdn_url, 'cdn77.org') ) {
				$this->cdn77_cdn_clear_cache($cdn_url);
			}
		}
	}

	public function bunny_cdn_clear_cache($cdn_url)
	{
		$api_key = WCL_Plugin::app()->getPopulateOption('bunny_cdn_api_key');

		if( empty($api_key) || empty($cdn_url) ) {
			return;
		}

		$header = array(
			"method" => "POST",
			'headers' => array(
				"AccessKey" => $api_key,
				"Content-Type" => "application/json"
			),
			"body" => ''
		);

		$response = wp_remote_request('https://bunnycdn.com/api/pullzone/purgeCacheByHostname?hostname=' . urlencode($cdn_url), $header);

		if( is_wp_error($response) ) {
			wp_die("Failed to purge cache in Bunny cdn due to error: " . $response->get_error_message());
		}

		if( 200 !== wp_remote_retrieve_response_code($response) ) {
			wp_die("Failed to purge cache in Bunny cdn due to unknow error!");
		}
	}

	public function cdn77_cdn_clear_cache($cdn_url)
	{
		$api_login = WCL_Plugin::app()->getPopulateOption('cdn77_api_login');
		$api_key = WCL_Plugin::app()->getPopulateOption('cdn77_api_key');
		$api_cdnid = WCL_Plugin::app()->getPopulateOption('cdn77_api_cdnid');

		if( empty($api_login) || empty($api_key) || empty($api_cdnid) || empty($cdn_url) ) {
			return;
		}

		$header = array(
			"method" => "POST",
			"body" => ['cdn_id' => $api_cdnid, 'login' => $api_login, 'passwd' => $api_key]
		);

		$response = wp_remote_request('https://api.cdn77.com/v2.0/data/purge-all', $header);
		if( is_wp_error($response) ) {
			wp_die("Failed to purge cache in Bunny cdn due to error: " . $response->get_error_message());
		}

		$data = @json_decode(wp_remote_retrieve_body($response), ARRAY_A);

		if( empty($data) || "ok" !== $data['status'] || 200 !== wp_remote_retrieve_response_code($response) ) {
			if( empty($data['description']) ) {
				wp_die("Failed to purge cache in Bunny cdn due to unknow error!");
			} else {
				wp_die("Failed to purge cache in Bunny cdn due to error: " . $data['description']);
			}
		}
	}
}

new WCL_CDN();



