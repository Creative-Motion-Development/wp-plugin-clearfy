<?php

class WCL_Create_Cache {

	public $options = [];
	public $cdn;
	private $startTime;
	private $blockCache = false;
	private $err = "";
	public $cacheFilePath = "";
	public $exclude_rules = false;
	public $preload_user_agent = false;
	public $current_page_type = false;
	public $current_page_content_type = false;
	public $exclude_current_page_text = false;

	public function __construct()
	{
		//to fix: PHP Notice: Undefined index: HTTP_USER_AGENT
		$_SERVER['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] ? strip_tags($_SERVER['HTTP_USER_AGENT']) : "Empty User Agent";

		if( preg_match("/(WP\sFastest\sCache\sPreload(\siPhone\sMobile)?\s*Bot)/", $_SERVER['HTTP_USER_AGENT']) ) {
			$this->preload_user_agent = true;
		} else {
			$this->preload_user_agent = false;
		}

		//$this->set_cdn();
		$this->set_cache_file_path();
	}

	public function detect_current_page_type()
	{
		if( preg_match("/\?/", $_SERVER["REQUEST_URI"]) ) {
			return true;
		}

		if( preg_match("/^\/wp-json/", $_SERVER["REQUEST_URI"]) ) {
			return true;
		}

		if( is_front_page() ) {
			echo "<!--WCLEARFY_PAGE_TYPE_homepage-->";
		} else if( is_category() ) {
			echo "<!--WCLEARFY_PAGE_TYPE_category-->";
		} else if( is_tag() ) {
			echo "<!--WCLEARFY_PAGE_TYPE_tag-->";
		} else if( is_singular('post') ) {
			echo "<!--WCLEARFY_PAGE_TYPE_post-->";
		} else if( is_page() ) {
			echo "<!--WCLEARFY_PAGE_TYPE_page-->";
		} else if( is_attachment() ) {
			echo "<!--WCLEARFY_PAGE_TYPE_attachment-->";
		} else if( is_archive() ) {
			echo "<!--WCLEARFY_PAGE_TYPE_archive-->";
		}
	}

	public function set_exclude_rules()
	{
		if( $json_data = get_option("WClearfyCacheExclude") ) {
			$this->exclude_rules = json_decode($json_data);
		}
	}

	public function set_cache_file_path()
	{
		$type = "all";

		if( WCL_Cache_Helpers::isMobile() && WCL_Plugin::app()->getPopulateOption('cache_mobile') ) {
			if( class_exists("WCL_MobileCache") && WCL_Plugin::app()->getPopulateOption('cache_mobile_theme') ) {
				$type = "wclearfy-mobile-cache";
			}
		}

		if( WCL_Cache_Helpers::isPluginActive('gtranslate/gtranslate.php') ) {
			if( isset($_SERVER["HTTP_X_GT_LANG"]) ) {
				$this->cacheFilePath = WCL_Cache_Helpers::getWpContentDir("/cache/" . $type . "/") . $_SERVER["HTTP_X_GT_LANG"] . $_SERVER["REQUEST_URI"];
			} else if( isset($_SERVER["REDIRECT_URL"]) && $_SERVER["REDIRECT_URL"] != "/index.php" ) {
				$this->cacheFilePath = WCL_Cache_Helpers::getWpContentDir("/cache/" . $type . "/") . $_SERVER["REDIRECT_URL"];
			} else if( isset($_SERVER["REQUEST_URI"]) ) {
				$this->cacheFilePath = WCL_Cache_Helpers::getWpContentDir("/cache/" . $type . "/") . $_SERVER["REQUEST_URI"];
			}
		} else {
			$this->cacheFilePath = WCL_Cache_Helpers::getWpContentDir("/cache/" . $type . "/") . $_SERVER["REQUEST_URI"];

			// for /?s=
			$this->cacheFilePath = preg_replace("/(\/\?s\=)/", "$1/", $this->cacheFilePath);
		}

		$this->cacheFilePath = $this->cacheFilePath ? rtrim($this->cacheFilePath, "/") . "/" : "";
		$this->cacheFilePath = preg_replace("/\/cache\/(all|wclearfy-mobile-cache)\/\//", "/cache/$1/", $this->cacheFilePath);

		if( strlen($_SERVER["REQUEST_URI"]) > 1 ) { // for the sub-pages
			if( !preg_match("/\.html/i", $_SERVER["REQUEST_URI"]) ) {
				if( WCL_Cache_Helpers::is_trailing_slash() ) {
					if( !preg_match("/\/$/", $_SERVER["REQUEST_URI"]) ) {
						if( defined('WCACHE_QUERYSTRING') && WCACHE_QUERYSTRING ) {

						} else if( preg_match("/gclid\=/i", $this->cacheFilePath) ) {

						} else if( preg_match("/fbclid\=/i", $this->cacheFilePath) ) {

						} else if( preg_match("/utm_(source|medium|campaign|content|term)/i", $this->cacheFilePath) ) {

						} else {
							$this->cacheFilePath = false;
						}
					}
				} else {
					//toDo
				}
			}
		}

		$this->remove_url_paramters();

		// to decode path if it is not utf-8
		if( $this->cacheFilePath ) {
			$this->cacheFilePath = urldecode($this->cacheFilePath);
		}

		// for security
		if( preg_match("/\.{2,}/", $this->cacheFilePath) ) {
			$this->cacheFilePath = false;
		}
		if( WCL_Cache_Helpers::isMobile() ) {
			if( WCL_Plugin::app()->getPopulateOption('cache_mobile') ) {
				if( !class_exists("WCL_MobileCache") ) {
					$this->cacheFilePath = false;
				} else {
					if( !WCL_Plugin::app()->getPopulateOption('cache_mobile_theme') ) {
						$this->cacheFilePath = false;
					}
				}
			}
		}
	}

	public function remove_url_paramters()
	{
		$action = false;

		//to remove query strings for cache if Google Click Identifier are set
		if( preg_match("/gclid\=/i", $this->cacheFilePath) ) {
			$action = true;
		}

		//to remove query strings for cache if facebook parameters are set
		if( preg_match("/fbclid\=/i", $this->cacheFilePath) ) {
			$action = true;
		}

		//to remove query strings for cache if google analytics parameters are set
		if( preg_match("/utm_(source|medium|campaign|content|term)/i", $this->cacheFilePath) ) {
			$action = true;
		}

		if( $action ) {
			if( strlen($_SERVER["REQUEST_URI"]) > 1 ) { // for the sub-pages

				$this->cacheFilePath = preg_replace("/\/*\?.+/", "", $this->cacheFilePath);
				$this->cacheFilePath = $this->cacheFilePath . "/";

				define('WCACHE_QUERYSTRING', true);
			}
		}
	}

	public function set_cdn()
	{
		$cdn_values = get_option("WClearfyCacheCDN");
		if( $cdn_values ) {
			$std_obj = json_decode($cdn_values);
			$arr = [];

			if( is_array($std_obj) ) {
				$arr = $std_obj;
			} else {
				array_push($arr, $std_obj);
			}

			foreach($arr as $key => &$std) {
				$std->originurl = trim($std->originurl);
				$std->originurl = trim($std->originurl, "/");
				$std->originurl = preg_replace("/http(s?)\:\/\/(www\.)?/i", "", $std->originurl);

				$std->cdnurl = trim($std->cdnurl);
				$std->cdnurl = trim($std->cdnurl, "/");

				if( !preg_match("/https\:\/\//", $std->cdnurl) ) {
					$std->cdnurl = "//" . preg_replace("/http(s?)\:\/\/(www\.)?/i", "", $std->cdnurl);
				}
			}

			$this->cdn = $arr;
		}
	}

	public function checkShortCode($content)
	{
		if( preg_match("/\[wclearfyNOT\]/", $content) ) {
			if( !is_home() || !is_archive() ) {
				$this->blockCache = true;
			}
			$content = str_replace("[wclearfyNOT]", "", $content);
		}

		return $content;
	}

	public function createCache()
	{
		if( !WCL_Plugin::app()->getPopulateOption('enable_cache') ) {
			return 0;
		}

		// to exclude static pdf files
		if( preg_match("/\.pdf$/i", $_SERVER["REQUEST_URI"]) ) {
			return 0;
		}

		// to check logged-in user
		if( WCL_Plugin::app()->getPopulateOption('dont_cache_for_logged_in_users') ) {
			foreach((array)$_COOKIE as $cookie_key => $cookie_value) {
				if( preg_match("/wordpress_logged_in/i", $cookie_key) ) {
					ob_start([$this, "cdn_rewrite"]);

					return 0;
				}
			}
		}

		// to exclude admin users
		foreach((array)$_COOKIE as $cookie_key => $cookie_value) {
			if( preg_match("/wordpress_logged_in/i", $cookie_key) ) {
				$users_groups = get_users(["role" => "administrator", "fields" => ["user_login"]]);

				foreach($users_groups as $user_key => $user_value) {
					if( preg_match("/^" . preg_quote($user_value->user_login, "/") . "/", $cookie_value) ) {
						ob_start([$this, "cdn_rewrite"]);

						return 0;
					}
				}
			}
		}

		// to check comment author
		foreach((array)$_COOKIE as $cookie_key => $cookie_value) {
			if( preg_match("/comment_author_/i", $cookie_key) ) {
				ob_start([$this, "cdn_rewrite"]);

				return 0;
			}
		}

		if( isset($_COOKIE) && isset($_COOKIE['safirmobilswitcher']) ) {
			ob_start([$this, "cdn_rewrite"]);

			return 0;
		}

		if( isset($_COOKIE) && isset($_COOKIE["wptouch-pro-view"]) ) {
			if( WCL_Cache_Helpers::is_wptouch_smartphone() ) {
				if( $_COOKIE["wptouch-pro-view"] == "desktop" ) {
					ob_start([$this, "cdn_rewrite"]);

					return 0;
				}
			}
		}

		if( preg_match("/\?/", $_SERVER["REQUEST_URI"]) && !preg_match("/\/\?fdx\_switcher\=true/", $_SERVER["REQUEST_URI"]) ) { // for WP Mobile Edition
			if( preg_match("/\?amp(\=1)?/i", $_SERVER["REQUEST_URI"]) ) {
				//
			} else if( defined('WCACHE_QUERYSTRING') && WCACHE_QUERYSTRING ) {
				//
			} else {
				ob_start([$this, "cdn_rewrite"]);

				return 0;
			}
		}

		if( preg_match("/(" . WCL_Cache_Helpers::get_excluded_useragent() . ")/", $_SERVER['HTTP_USER_AGENT']) ) {
			return 0;
		}

		if( isset($_SERVER['REQUEST_URI']) && preg_match("/(\/){2}$/", $_SERVER['REQUEST_URI']) ) {
			return 0;
		}

		// to check permalink if it does not end with slash
		if( isset($_SERVER['REQUEST_URI']) && preg_match("/[^\/]+\/$/", $_SERVER['REQUEST_URI']) ) {
			if( !preg_match("/\/$/", get_option('permalink_structure')) ) {
				return 0;
			}
		}

		if( isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST" ) {
			return 0;
		}

		if( preg_match("/^https/i", get_option("home")) && !is_ssl() ) {
			//Must be secure connection
			return 0;
		}

		if( !preg_match("/^https/i", get_option("home")) && is_ssl() ) {
			//must be normal connection
			if( !WCL_Cache_Helpers::isPluginActive('really-simple-ssl/rlrsssl-really-simple-ssl.php') ) {
				if( !WCL_Cache_Helpers::isPluginActive('really-simple-ssl-pro/really-simple-ssl-pro.php') ) {
					if( !WCL_Cache_Helpers::isPluginActive('really-simple-ssl-on-specific-pages/really-simple-ssl-on-specific-pages.php') ) {
						if( !WCL_Cache_Helpers::isPluginActive('ssl-insecure-content-fixer/ssl-insecure-content-fixer.php') ) {
							if( !WCL_Cache_Helpers::isPluginActive('https-redirection/https-redirection.php') ) {
								if( !WCL_Cache_Helpers::isPluginActive('better-wp-security/better-wp-security.php') ) {
									return 0;
								}
							}
						}
					}
				}
			}
		}

		if( preg_match("/www\./i", get_option("home")) && !preg_match("/www\./i", $_SERVER['HTTP_HOST']) ) {
			return 0;
		}

		if( !preg_match("/www\./i", get_option("home")) && preg_match("/www\./i", $_SERVER['HTTP_HOST']) ) {
			return 0;
		}

		if( $this->exclude_page() ) {
			echo "<!-- Clearfy Cache: Exclude Page -->"."\n";
			return 0;
		}

		// http://mobiledetect.net/ does not contain the following user-agents
		if( preg_match("/Nokia309|Casper_VIA/i", $_SERVER['HTTP_USER_AGENT']) ) {
			return 0;
		}

		if( preg_match("/Empty\sUser\sAgent/i", $_SERVER['HTTP_USER_AGENT']) ) { // not to show the cache for command line
			return 0;
		}

		//to show cache version via php if htaccess rewrite rule does not work
		if( !$this->preload_user_agent && $this->cacheFilePath && (@file_exists($this->cacheFilePath . "index.html") || @file_exists($this->cacheFilePath . "index.json") || @file_exists($this->cacheFilePath . "index.xml")) ) {

			$via_php = "";
			if( @file_exists($this->cacheFilePath . "index.json") ) {
				$file_extension = "json";

				header('Content-type: application/json');
			} else if( @file_exists($this->cacheFilePath . "index.xml") ) {
				$file_extension = "xml";

				header('Content-type: text/xml');
			} else {
				$file_extension = "html";
				$via_php = "<!-- via php -->";
			}

			if( $content = @file_get_contents($this->cacheFilePath . "index." . $file_extension) ) {
				if( defined('WCLEARFY_REMOVE_VIA_FOOTER_COMMENT') && WCLEARFY_REMOVE_VIA_FOOTER_COMMENT ) {
					$via_php = "";
				}

				$content = $content . $via_php;

				die($content);
			}
		} else {
			if( WCL_Cache_Helpers::isMobile() ) {
				if( class_exists("WCL_MobileCache") && WCL_Plugin::app()->getPopulateOption('cache_mobile_theme') ) {
					if( WCL_Plugin::app()->getPopulateOption('cache_mobile_theme_name') ) {
						$create_cache = true;
					} else if( WCL_Cache_Helpers::isPluginActive('wptouch/wptouch.php') || WCL_Cache_Helpers::isPluginActive('wptouch-pro/wptouch-pro.php') ) {
						//to check that user-agent exists in wp-touch's list or not
						if( WCL_Cache_Helpers::is_wptouch_smartphone() ) {
							$create_cache = true;
						} else {
							$create_cache = false;
						}
					} else if( WCL_Cache_Helpers::isPluginActive('any-mobile-theme-switcher/any-mobile-theme-switcher.php') ) {
						if( WCL_Cache_Helpers::is_anymobilethemeswitcher_mobile() ) {
							$create_cache = true;
						} else {
							$create_cache = false;
						}
					} else {
						if( (preg_match('/iPhone/', $_SERVER['HTTP_USER_AGENT']) && preg_match('/Mobile/', $_SERVER['HTTP_USER_AGENT'])) || (preg_match('/Android/', $_SERVER['HTTP_USER_AGENT']) && preg_match('/Mobile/', $_SERVER['HTTP_USER_AGENT'])) ) {
							$create_cache = true;
						} else {
							$create_cache = false;
						}
					}
				} else if( !WCL_Plugin::app()->getPopulateOption('cache_mobile') && !WCL_Plugin::app()->getPopulateOption('cache_mobile_theme') ) {
					$create_cache = true;
				} else {
					$create_cache = false;
				}
			} else {
				$create_cache = true;
			}

			if( $create_cache ) {
				$this->startTime = microtime(true);

				add_action('wp', [$this, "detect_current_page_type"]);
				add_action('get_footer', [$this, "detect_current_page_type"]);
				add_action('get_footer', [$this, "wp_print_scripts_action"]);

				// to exclude current page hook
				add_action("wclearfy_exclude_current_page", [$this, 'exclude_current_page'], 10, 0);

				ob_start([$this, "callback"]);
			}
		}
	}

	public function exclude_current_page($some = true)
	{
		$via = debug_backtrace();

		if( isset($via) && is_array($via) ) {
			foreach($via as $key => $value) {
				if( $value["function"] == "wclearfy_exclude_current_page" ) {

					if( defined('WCLEARFY_DEBUG') && (WCLEARFY_DEBUG === true) ) {
						if( preg_match("/wp-content\/themes/", $value["file"]) ) {
							$this->exclude_current_page_text = "<!-- This page has been excluded by " . basename($value["file"]) . " of the Theme -->";
						} else if( preg_match("/wp-content\/plugins/", $value["file"]) ) {
							$this->exclude_current_page_text = "<!-- This page has been excluded by " . basename($value["file"]) . " of " . preg_replace("/([^\/]+)\/.+/", "$1", plugin_basename($value["file"])) . " -->";
						}
					} else {
						$this->exclude_current_page_text = "<!-- This page has been excluded -->";
					}

					break;
				}
			}
		}
	}

	public function wp_print_scripts_action()
	{
		echo "<!--WCLEARFY_FOOTER_START-->";
	}

	public function ignored($buffer)
	{
		$list = [
			"\/wp\-comments\-post\.php",
			"\/wp\-login\.php",
			"\/robots\.txt",
			"\/wp\-cron\.php",
			"\/wp\-content",
			"\/wp\-admin",
			"\/wp\-includes",
			"\/index\.php",
			"\/xmlrpc\.php",
			"\/wp\-api\/",
			"leaflet\-geojson\.php",
			"\/clientarea\.php"
		];
		if( WCL_Cache_Helpers::isPluginActive('woocommerce/woocommerce.php') ) {
			if( $this->current_page_type != "homepage" ) {
				global $post;

				if( isset($post->ID) && $post->ID ) {
					if( function_exists("wc_get_page_id") ) {
						$woocommerce_ids = [];
						array_push($woocommerce_ids, wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('receipt'), wc_get_page_id('confirmation'), wc_get_page_id('myaccount'));

						if( in_array($post->ID, $woocommerce_ids) ) {
							return true;
						}
					}
				}

				array_push($list, "\/cart\/?$", "\/checkout", "\/receipt", "\/confirmation", "\/wc-api\/");
			}
		}

		if( WCL_Cache_Helpers::isPluginActive('wp-easycart/wpeasycart.php') ) {
			array_push($list, "\/cart");
		}

		if( WCL_Cache_Helpers::isPluginActive('easy-digital-downloads/easy-digital-downloads.php') ) {
			array_push($list, "\/cart", "\/checkout");
		}

		if( preg_match("/" . implode("|", $list) . "/i", $_SERVER["REQUEST_URI"]) ) {
			return true;
		}

		return false;
	}

	public function exclude_page()
	{
		$preg_match_rule = "";
		$request_url = urldecode(trim($_SERVER["REQUEST_URI"], "/"));

		$excluded_uris = WCL_Plugin::app()->getPopulateOption('cache_reject_uri');

		if( !empty($excluded_uris) ) {
			$excluded_uris = array_map(function ($value) {
				$site_url = site_url();

				$value = trim(rtrim($value));
				// http://clearfy.pro/members/ -> /members/
				$value = str_replace($site_url, "", $value);
				// /members/(.*) -> members/(.*)
				$value = preg_replace("/^\//", "", $value);
				// members/ -> members
				$value = untrailingslashit($value);

				return $value;
			}, preg_split('/\r\n|\n|\r/', $excluded_uris));
		} else {
			$excluded_uris = [];
		}

		foreach($excluded_uris as $uri) {
			if( $uri === $request_url ) {
				return true;
			}

			if( !empty($uri) && @preg_match('/' . $uri . '/i', $request_url) ) {
				return true;
			}
		}

		return false;
	}

	public function is_json()
	{
		return $this->current_page_content_type == "json" ? true : false;
	}

	public function is_xml()
	{
		return $this->current_page_content_type == "xml" ? true : false;
	}

	public function is_html()
	{
		return $this->current_page_content_type == "html" ? true : false;
	}

	public function set_current_page_type($buffer)
	{
		preg_match('/<\!--WCLEARFY_PAGE_TYPE_([a-z]+)-->/i', $buffer, $out);

		$this->current_page_type = isset($out[1]) ? $out[1] : false;
	}

	public function set_current_page_content_type($buffer)
	{
		$content_type = false;
		if( function_exists("headers_list") ) {
			$headers = headers_list();
			foreach($headers as $header) {
				if( preg_match("/Content-Type\:/i", $header) ) {
					$content_type = preg_replace("/Content-Type\:\s(.+)/i", "$1", $header);
				}
			}
		}

		if( preg_match("/xml/i", $content_type) ) {
			$this->current_page_content_type = "xml";
		} else if( preg_match("/json/i", $content_type) ) {
			$this->current_page_content_type = "json";
		} else {
			$this->current_page_content_type = "html";
		}
	}

	public function last_error($buffer = false)
	{
		if( function_exists("http_response_code") && (http_response_code() === 404) ) {
			return true;
		}

		if( is_404() ) {
			return true;
		}

		if( preg_match("/<body id\=\"error-page\">\s*<p>[^\>]+<\/p>\s*<\/body>/i", $buffer) ) {
			return true;
		}
	}

	public function callback($buffer)
	{
		$this->set_current_page_type($buffer);
		$this->set_current_page_content_type($buffer);

		$buffer = $this->checkShortCode($buffer);

		// for Wordfence: not to cache 503 pages
		if( defined('DONOTCACHEPAGE') && WCL_Cache_Helpers::isPluginActive('wordfence/wordfence.php') ) {
			if( function_exists("http_response_code") && http_response_code() == 503 ) {
				return $buffer . "<!-- DONOTCACHEPAGE is defined as TRUE -->";
			}
		}

		// for iThemes Security: not to cache 403 pages
		if( defined('DONOTCACHEPAGE') && WCL_Cache_Helpers::isPluginActive('better-wp-security/better-wp-security.php') ) {
			if( function_exists("http_response_code") && http_response_code() == 403 ) {
				return $buffer . "<!-- DONOTCACHEPAGE is defined as TRUE -->";
			}
		}

		if( $this->exclude_page($buffer) ) {
			$buffer = preg_replace('/<\!--WCLEARFY_PAGE_TYPE_[a-z]+-->/i', '', $buffer);

			return $buffer;
		}

		$buffer = preg_replace('/<\!--WCLEARFY_PAGE_TYPE_[a-z]+-->/i', '', $buffer);

		if( $this->exclude_current_page_text ) {
			return $buffer . $this->exclude_current_page_text;
		} else if( $this->is_json() && (!defined('WCACHE_JSON') || (defined('WCACHE_JSON') && WCACHE_JSON !== true)) ) {
			return $buffer;
		} else if( preg_match("/Mediapartners-Google|Google\sWireless\sTranscoder/i", $_SERVER['HTTP_USER_AGENT']) ) {
			return $buffer;
		} else if( is_user_logged_in() || $this->isCommenter() ) {
			return $buffer;
		} else if( $this->isPasswordProtected($buffer) ) {
			return $buffer . "<!-- Password protected content has been detected -->";
		} else if( WCL_Cache_Helpers::isWpLogin($buffer) ) {
			return $buffer . "<!-- wp-login.php -->";
		} else if( WCL_Cache_Helpers::hasContactForm7WithCaptcha($buffer) ) {
			return $buffer . "<!-- This page was not cached because ContactForm7's captcha -->";
		} else if( $this->last_error($buffer) ) {
			return $buffer;
		} else if( $this->ignored($buffer) ) {
			return $buffer;
		} else if( $this->blockCache === true ) {
			return $buffer . "<!-- wclearfyNOT has been detected -->";
		} else if( isset($_GET["preview"]) ) {
			return $buffer . "<!-- not cached -->";
		} else if( $this->checkHtml($buffer) ) {
			return $buffer . "<!-- html is corrupted -->";
		} else if( (function_exists("http_response_code")) && (http_response_code() == 301 || http_response_code() == 302) ) {
			return $buffer;
		} else if( !$this->cacheFilePath ) {
			return $buffer . "<!-- permalink_structure ends with slash (/) but REQUEST_URI does not end with slash (/) -->";
		} else {
			$content = $buffer;

			if( $this->err ) {
				return $buffer . "<!-- " . $this->err . " -->";
			} else {
				$content = $this->cacheDate($content);
				$content = str_replace("<!--WCLEARFY_FOOTER_START-->", "", $content);

				$content = $this->cdn_rewrite($content);
				$content = $this->fix_pre_tag($content, $buffer);

				if( $this->cacheFilePath ) {
					if( $this->is_html() ) {
						$this->createFolder($this->cacheFilePath, $content);
						do_action('wclearfy_is_cacheable_action');
					} else if( $this->is_xml() ) {
						if( preg_match("/<link><\/link>/", $buffer) ) {
							if( preg_match("/\/feed$/", $_SERVER["REQUEST_URI"]) ) {
								return $buffer . time();
							}
						}

						$this->createFolder($this->cacheFilePath, $buffer, "xml");
						do_action('wclearfy_is_cacheable_action');

						return $buffer;
					} else if( $this->is_json() ) {
						$this->createFolder($this->cacheFilePath, $buffer, "json");
						do_action('wclearfy_is_cacheable_action');

						return $buffer;
					}
				}

				return $content . "<!-- need to refresh to see cached version -->";
			}
		}
	}

	public function fix_pre_tag($content, $buffer)
	{
		if( preg_match("/<pre[^\>]*>/i", $buffer) ) {
			preg_match_all("/<pre[^\>]*>((?!<\/pre>).)+<\/pre>/is", $buffer, $pre_buffer);
			preg_match_all("/<pre[^\>]*>((?!<\/pre>).)+<\/pre>/is", $content, $pre_content);

			if( isset($pre_content[0]) && isset($pre_content[0][0]) ) {
				foreach($pre_content[0] as $key => $value) {
					/*
					location ~ / {
						set $path /path/$1/index.html;
					}
					*/
					if( isset($pre_buffer[0][$key]) ) {
						$pre_buffer[0][$key] = preg_replace('/\$(\d)/', '\\\$$1', $pre_buffer[0][$key]);

						$content = preg_replace("/" . preg_quote($value, "/") . "/", $pre_buffer[0][$key], $content);
					}
				}
			}
		}

		return $content;
	}

	public function cdn_rewrite($content)
	{
		if( $this->cdn ) {
			$content = preg_replace_callback("/(srcset|src|href|data-vc-parallax-image|data-bg|data-fullurl|data-mobileurl|data-img-url|data-cvpsrc|data-cvpset|data-thumb|data-bg-url|data-large_image|data-lazyload|data-lazy|data-source-url|data-srcsmall|data-srclarge|data-srcfull|data-slide-img|data-lazy-original)\s{0,2}\=\s{0,2}[\'\"]([^\'\"]+)[\'\"]/i", [
				$this,
				'cdn_replace_urls'
			], $content);

			//url()
			$content = preg_replace_callback("/(url)\(([^\)\>]+)\)/i", [$this, 'cdn_replace_urls'], $content);

			//{"concatemoji":"http:\/\/your_url.com\/wp-includes\/js\/wp-emoji-release.min.js?ver=4.7"}
			$content = preg_replace_callback("/\{\"concatemoji\"\:\"[^\"]+\"\}/i", [
				$this,
				'cdn_replace_urls'
			], $content);

			//<script>var loaderRandomImages=["https:\/\/www.site.com\/wp-content\/uploads\/2016\/12\/image.jpg"];</script>
			$content = preg_replace_callback("/[\"\']([^\'\"]+)[\"\']\s*\:\s*[\"\']https?\:\\\\\/\\\\\/[^\"\']+[\"\']/i", [
				$this,
				'cdn_replace_urls'
			], $content);

			// <script>
			// jsFileLocation:"//domain.com/wp-content/plugins/revslider/public/assets/js/"
			// </script>
			$content = preg_replace_callback("/(jsFileLocation)\s*\:[\"\']([^\"\']+)[\"\']/i", [
				$this,
				'cdn_replace_urls'
			], $content);

			// <form data-product_variations="[{&quot;src&quot;:&quot;//domain.com\/img.jpg&quot;}]">
			// <div data-siteorigin-parallax="{&quot;backgroundUrl&quot;:&quot;https:\/\/domain.com\/wp-content\/TOR.jpg&quot;,&quot;backgroundSize&quot;:[830,467],&quot;}" data-stretch-type="full">
			$content = preg_replace_callback("/(data-product_variations|data-siteorigin-parallax)\=[\"\'][^\"\']+[\"\']/i", [
				$this,
				'cdn_replace_urls'
			], $content);

			// <object data="https://site.com/source.swf" type="application/x-shockwave-flash"></object>
			$content = preg_replace_callback("/<object[^\>]+(data)\s{0,2}\=[\'\"]([^\'\"]+)[\'\"][^\>]+>/i", [
				$this,
				'cdn_replace_urls'
			], $content);
		}

		return $content;
	}

	public function get_header($content)
	{
		$head_first_index = strpos($content, "<head");
		$head_last_index = strpos($content, "</head>");

		return substr($content, $head_first_index, ($head_last_index - $head_first_index + 1));
	}

	public function checkHtml($buffer)
	{
		if( !$this->is_html() ) {
			return false;
		}

		if( preg_match('/<html[^\>]*>/si', $buffer) && preg_match('/<body[^\>]*>/si', $buffer) ) {
			return false;
		}
		// if(strlen($buffer) > 10){
		// 	return false;
		// }

		return true;
	}

	public function cacheDate($buffer)
	{
		if( WCL_Cache_Helpers::isMobile() && class_exists("WCL_MobileCache") && WCL_Plugin::app()->getPopulateOption('cache_mobile') && WCL_Plugin::app()->getPopulateOption('cache_mobile_theme') ) {
			$comment = "<!-- Mobile: Clearfy Cache file was created in " . $this->creationTime() . " seconds, on " . date("d-m-y G:i:s", current_time('timestamp')) . " -->";
		} else {
			$comment = "<!-- Clearfy Cache file was created in " . $this->creationTime() . " seconds, on " . date("d-m-y G:i:s", current_time('timestamp')) . " -->";
		}

		if( defined('WCLEARFY_REMOVE_FOOTER_COMMENT') && WCLEARFY_REMOVE_FOOTER_COMMENT ) {
			return $buffer;
		} else {
			return $buffer . $comment;
		}
	}

	public function creationTime()
	{
		return microtime(true) - $this->startTime;
	}

	public function isCommenter()
	{
		$commenter = wp_get_current_commenter();

		return isset($commenter["comment_author_email"]) && $commenter["comment_author_email"] ? true : false;
	}

	public function isPasswordProtected($buffer)
	{
		if( preg_match("/action\=[\'\"].+postpass.*[\'\"]/", $buffer) ) {
			return true;
		}

		foreach($_COOKIE as $key => $value) {
			if( preg_match("/wp\-postpass\_/", $key) ) {
				return true;
			}
		}

		return false;
	}

	public function create_name($list)
	{
		$arr = is_array($list) ? $list : [["href" => $list]];
		$name = "";

		foreach($arr as $tag_key => $tag_value) {
			$tmp = preg_replace("/(\.css|\.js)\?.*/", "$1", $tag_value["href"]); //to remove version number
			$name = $name . $tmp;
		}

		return base_convert(crc32($name), 20, 36);
	}

	public function createFolder($cachFilePath, $buffer, $extension = "html", $prefix = false)
	{
		$create = false;
		$file_name = "index.";
		$update_db_statistic = true;

		if( $buffer && strlen($buffer) > 100 && preg_match("/html|xml|json/i", $extension) ) {
			if( !preg_match("/^\<\!\-\-\sMobile\:\sWP\sFastest\sCache/i", $buffer) ) {
				if( !preg_match("/^\<\!\-\-\sWP\sFastest\sCache/i", $buffer) ) {
					$create = true;
				}
			}

			if( $this->preload_user_agent ) {
				if( file_exists($cachFilePath . "/" . "index." . $extension) ) {
					$update_db_statistic = false;
					@unlink($cachFilePath . "/" . "index." . $extension);
				}
			}
		}

		if( ($extension == "svg" || $extension == "woff" || $extension == "css" || $extension == "js") && $buffer && strlen($buffer) > 5 ) {
			$create = true;
			$file_name = base_convert(substr(time(), -6), 20, 36) . ".";
			$buffer = trim($buffer);

			if( $extension == "js" ) {
				if( substr($buffer, -1) != ";" ) {
					$buffer .= ";";
				}
			}
		}

		if( $create ) {
			if( !is_user_logged_in() && !$this->isCommenter() ) {
				if( !is_dir($cachFilePath) ) {
					if( is_writable(WCL_Cache_Helpers::getWpContentDir()) || ((is_dir(WCL_Cache_Helpers::getWpContentDir() . "/cache")) && (is_writable(WCL_Cache_Helpers::getWpContentDir() . "/cache"))) ) {
						if( @mkdir($cachFilePath, 0755, true) ) {

							$buffer = (string)apply_filters('wclearfy_buffer_callback_filter', $buffer, $extension);

							file_put_contents($cachFilePath . "/" . $file_name . $extension, $buffer);

							if( $extension == "html" ) {
								if( !file_exists(WP_CONTENT_DIR . "/cache/index.html") ) {
									@file_put_contents(WP_CONTENT_DIR . "/cache/index.html", "");
								}
							} else {
								if( !file_exists(WP_CONTENT_DIR . "/cache/wclearfy-minified/index.html") ) {
									@file_put_contents(WP_CONTENT_DIR . "/cache/wclearfy-minified/index.html", "");
								}
							}
						} else {
						}
					} else {

					}
				} else {
					if( file_exists($cachFilePath . "/" . $file_name . $extension) ) {

					} else {
						$buffer = (string)apply_filters('wclearfy_buffer_callback_filter', $buffer, $extension);

						file_put_contents($cachFilePath . "/" . $file_name . $extension, $buffer);
					}
				}
			}
		} else if( $extension == "html" ) {
			$this->err = "Buffer is empty so the cache cannot be created";
		}
	}
}

?>