<?php
/**
 * Helpers methods
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 10.11.2020, Webcraftic
 * @version       1.0
 */

class WCL_Cache_Helpers {

	/**
	 * Return if the server run on nginx
	 *
	 * @return bool
	 */
	public static function is_nginx()
	{
		$is_nginx = (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);

		return $is_nginx;
	}

	public static function is_anymobilethemeswitcher_mobile()
	{
		// https://plugins.svn.wordpress.org/any-mobile-theme-switcher/tags/1.9/any-mobile-theme-switcher.php
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		switch( true ) {
			case (preg_match('/ipad/i', $user_agent));
				return true;
				break;

			case (preg_match('/ipod/i', $user_agent) || preg_match('/iphone/i', $user_agent));
				return true;
				break;

			case (preg_match('/android/i', $user_agent) && preg_match('/mobile/i', $user_agent));
				return true;
				break;

			case (preg_match('/opera mini/i', $user_agent));
				return true;
				break;

			case (preg_match('/blackberry/i', $user_agent));
				return true;
				break;

			case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i', $user_agent));
				return true;
				break;

			case (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i', $user_agent));
				return true;
				break;

			case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent));
				return true;
				break;
		}

		return false;
	}

	/**
	 * @param string $plugin
	 *
	 * @return bool
	 */
	public static function isPluginActive($plugin)
	{
		return in_array($plugin, (array)get_option('active_plugins', [])) || self::isPluginActiveForNetwork($plugin);
	}

	/**
	 * @param string $plugin
	 *
	 * @return bool
	 */
	public static function isPluginActiveForNetwork($plugin)
	{
		if( !is_multisite() ) {
			return false;
		}

		$plugins = get_site_option('active_sitewide_plugins');
		if( isset($plugins[$plugin]) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param false $path
	 *
	 * @return string
	 */
	public static function getWpContentDir($path = false)
	{
		/*
		Sample Paths;

		/cache/

		/cache/all/
		/cache/all
		/cache/all/page
		/cache/all/index.html

		/cache/wclearfy-minified

		/cache/wclearfy-widget-cache

		/cache/wclearfy-mobile-cache/
		/cache/wclearfy-mobile-cache/page
		/cache/wclearfy-mobile-cache/index.html

		/cache/tmpWclearfy
		/cache/tmpWclearfy/
		/cache/tmpWclearfy/mobile_
		/cache/tmpWclearfy/m
		/cache/tmpWclearfy/w


		/cache/testWpFc/

		/cache/all/testWpFc/

		/cache/wclearfy-widget-cache/
		/cache/wclearfy-widget-cache
		/cache/wclearfy-widget-cache/".$args["widget_id"].".html
		*/

		if( $path ) {
			if( preg_match("/\/cache\/(all|wclearfy-minified|wclearfy-widget-cache|wclearfy-mobile-cache)/", $path) ) {
				//WPML language switch
				//https://wpml.org/forums/topic/wpml-language-switch-wp-fastest-cache-issue/
				$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
				if( ($language_negotiation_type == 2) && self::isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
					$my_home_url = apply_filters('wpml_home_url', get_option('home'));
					$my_home_url = preg_replace("/https?\:\/\//i", "", $my_home_url);
					$my_home_url = trim($my_home_url, "/");

					$path = preg_replace("/\/cache\/(all|wclearfy-minified|wclearfy-widget-cache|wclearfy-mobile-cache)/", "/cache/" . $my_home_url . "/$1", $path);
				} else if( ($language_negotiation_type == 1) && self::isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
					$my_current_lang = apply_filters('wpml_current_language', null);

					if( $my_current_lang ) {
						$path = preg_replace("/\/cache\/wclearfy-widget-cache\/(.+)/", "/cache/wclearfy-widget-cache/" . $my_current_lang . "-" . "$1", $path);
					}
				}

				if( self::isPluginActive('multiple-domain-mapping-on-single-site/multidomainmapping.php') ) {
					$path = preg_replace("/\/cache\/(all|wclearfy-minified|wclearfy-widget-cache|wclearfy-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( self::isPluginActive('polylang/polylang.php') ) {
					$path = preg_replace("/\/cache\/(all|wclearfy-minified|wclearfy-widget-cache|wclearfy-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( self::isPluginActive('multiple-domain/multiple-domain.php') ) {
					$path = preg_replace("/\/cache\/(all|wclearfy-minified|wclearfy-widget-cache|wclearfy-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( is_multisite() ) {
					$path = preg_replace("/\/cache\/(all|wclearfy-minified|wclearfy-widget-cache|wclearfy-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}
			}

			return WP_CONTENT_DIR . $path;
		} else {
			return WP_CONTENT_DIR;
		}
	}

	public static function is_trailing_slash()
	{
		// no need to check if Custom Permalinks plugin is active (https://tr.wordpress.org/plugins/custom-permalinks/)
		if( self::isPluginActive("custom-permalinks/custom-permalinks.php") ) {
			return false;
		}

		if( $permalink_structure = get_option('permalink_structure') ) {
			if( preg_match("/\/$/", $permalink_structure) ) {
				return true;
			}
		}

		return false;
	}

	public static function get_excluded_useragent()
	{
		$excluded_user_agents = WCL_Plugin::app()->getPopulateOption('cache_reject_user_agents');

		if( !empty($excluded_user_agents) ) {
			$excluded_user_agents = array_map(function ($value) {
				$value = trim(rtrim($value));
				return $value;
			}, preg_split('/\r\n|\n|\r/', $excluded_user_agents));
		} else {
			$excluded_user_agents = [];
		}

		$agents = implode("|", $excluded_user_agents);

		return $agents;
	}

	public static function get_mobile_browsers()
	{
		$mobile_browsers = [
			'\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?',
			'\bDolfin\b',
			'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+',
			'Skyfire',
			'Mobile\sSafari\/[.0-9]*\sEdge',
			'IEMobile|MSIEMobile',
			// |Trident/[.0-9]+
			'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS',
			'bolt',
			'teashark',
			'Blazer',
			'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
			'Tizen',
			'UC.*Browser|UCWEB',
			'baiduboxapp',
			'baidubrowser',
			'DiigoBrowser',
			'Puffin',
			'\bMercury\b',
			'Obigo',
			'NF-Browser',
			'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
			'Android.*PaleMoon|Mobile.*PaleMoon'
		];

		return $mobile_browsers;
	}

	public static function get_operating_systems()
	{
		$operating_systems = [
			'Android',
			'blackberry|\bBB10\b|rim\stablet\sos',
			'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
			'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
			'Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;',
			'Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;',
			'\biPhone.*Mobile|\biPod|\biPad',
			'Apple-iPhone7C2',
			'MeeGo',
			'Maemo',
			'J2ME\/|\bMIDP\b|\bCLDC\b',
			// '|Java/' produces bug #135
			'webOS|hpwOS',
			'\bBada\b',
			'BREW'
		];

		return $operating_systems;
	}

	public function is_amp($content)
	{
		$request_uri = trim($_SERVER["REQUEST_URI"], "/");

		if( preg_match("/^amp/", $request_uri) || preg_match("/\/amp\//", $request_uri) || preg_match("/amp$/", $request_uri) ) {
			if( preg_match("/<html[^\>]+amp[^\>]*>/i", $content) ) {
				return true;
			}

			if( preg_match("/<html[^\>]+\⚡[^\>]*>/i", $content) ) {
				return true;
			}
		}

		return false;
	}

	public static function isMobile()
	{
		foreach(self::get_mobile_browsers() as $value) {
			if( preg_match("/" . $value . "/i", $_SERVER['HTTP_USER_AGENT']) ) {
				return true;
			}
		}

		foreach(self::get_operating_systems() as $key => $value) {
			if( preg_match("/" . $value . "/i", $_SERVER['HTTP_USER_AGENT']) ) {
				return true;
			}
		}

		if( isset($_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER']) && "true" === $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] ) {
			return true;
		}

		if( isset($_SERVER['HTTP_CLOUDFRONT_IS_TABLET_VIEWER']) && "true" === $_SERVER['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'] ) {
			return true;
		}

		return false;
	}

	public static function isWpLogin($buffer)
	{
		// if(preg_match("/<form[^\>]+loginform[^\>]+>((?:(?!<\/form).)+)user_login((?:(?!<\/form).)+)user_pass((?:(?!<\/form).)+)<\/form>/si", $buffer)){
		// 	return true;
		// }
		if( $GLOBALS["pagenow"] == "wp-login.php" ) {
			return true;
		}

		return false;
	}

	public static function hasContactForm7WithCaptcha($buffer)
	{
		if( is_single() || is_page() ) {
			if( preg_match("/<input[^\>]+_wpcf7_captcha[^\>]+>/i", $buffer) ) {
				return true;
			}
		}

		return false;
	}

	public static function is_wptouch_smartphone()
	{
		// https://plugins.svn.wordpress.org/wptouch/tags/4.0.4/core/mobile-user-agents.php
		// wptouch: ipad is accepted as a desktop so no need to create cache if user agent is ipad
		// https://wordpress.org/support/topic/plugin-wptouch-wptouch-wont-display-mobile-version-on-ipad?replies=12
		if( preg_match("/ipad/i", $_SERVER['HTTP_USER_AGENT']) ) {
			return false;
		}

		$wptouch_smartphone_list = [];

		$wptouch_smartphone_list[] = ['iPhone'];                      // iPhone
		$wptouch_smartphone_list[] = ['Android', 'Mobile'];           // Android devices
		$wptouch_smartphone_list[] = ['BB', 'Mobile Safari'];         // BB10 devices
		$wptouch_smartphone_list[] = ['BlackBerry', 'Mobile Safari']; // BB 6, 7 devices
		$wptouch_smartphone_list[] = ['Firefox', 'Mobile'];           // Firefox OS devices
		$wptouch_smartphone_list[] = ['IEMobile/11', 'Touch'];        // Windows IE 11 touch devices
		$wptouch_smartphone_list[] = ['IEMobile/10', 'Touch'];        // Windows IE 10 touch devices
		$wptouch_smartphone_list[] = ['IEMobile/9.0'];                // Windows Phone OS 9
		$wptouch_smartphone_list[] = ['IEMobile/8.0'];                // Windows Phone OS 8
		$wptouch_smartphone_list[] = ['IEMobile/7.0'];                // Windows Phone OS 7
		$wptouch_smartphone_list[] = ['OPiOS', 'Mobile'];             // Opera Mini iOS
		$wptouch_smartphone_list[] = ['Coast', 'Mobile'];             // Opera Coast iOS

		foreach($wptouch_smartphone_list as $key => $value) {
			if( isset($value[0]) && isset($value[1]) ) {
				if( preg_match("/" . preg_quote($value[0], "/") . "/i", $_SERVER['HTTP_USER_AGENT']) ) {
					if( preg_match("/" . preg_quote($value[1], "/") . "/i", $_SERVER['HTTP_USER_AGENT']) ) {
						return true;
					}
				}
			} else if( isset($value[0]) ) {
				if( preg_match("/" . preg_quote($value[0], "/") . "/i", $_SERVER['HTTP_USER_AGENT']) ) {
					return true;
				}
			}
		}

		return false;
	}

	public static function is_subdirectory_install()
	{
		if( strlen(site_url()) > strlen(home_url()) ) {
			return true;
		}

		return false;
	}

	public static function getABSPATH()
	{
		$path = ABSPATH;
		$siteUrl = site_url();
		$homeUrl = home_url();
		$diff = str_replace($homeUrl, "", $siteUrl);
		$diff = trim($diff, "/");

		$pos = strrpos($path, $diff);

		if( $pos !== false ) {
			$path = substr_replace($path, "", $pos, strlen($diff));
			$path = trim($path, "/");
			$path = "/" . $path . "/";
		}

		return $path;
	}

	public static function checkSuperCache($path, $htaccess)
	{
		if( self::isPluginActive('wp-super-cache/wp-cache.php') ) {
			return ["WP Super Cache needs to be deactive", "error"];
		} else {
			@unlink($path . "wp-content/wp-cache-config.php");

			$message = "";

			if( is_file($path . "wp-content/wp-cache-config.php") ) {
				$message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
			}

			if( preg_match("/supercache/", $htaccess) ) {
				$message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
			}

			return $message ? [
				"WP Super Cache cannot remove its own remnants so please follow the steps below" . $message,
				"error"
			] : "";
		}

		return "";
	}

	public static function warningIncompatible($incompatible, $alternative = false)
	{
		if( $alternative ) {
			throw new Exception($incompatible . " <label>needs to be deactive</label><br><label>We advise</label> <a id='alternative-plugin' target='_blank' href='" . $alternative["url"] . "'>" . $alternative["name"] . "</a>");
		} else {
			throw new Exception($incompatible . " <label>needs to be deactive</label>");
		}
	}

	public static function insertRewriteRule($htaccess)
	{
		$enable_cache = WCL_Plugin::app()->getPopulateOption('enable_cache');

		if( $enable_cache ) {
			$htaccess = preg_replace("/#\s?BEGIN\s?WClearfyCache.*?#\s?END\s?WClearfyCache/s", "", $htaccess);
			$htaccess = self::getHtaccess() . $htaccess;
		} else {
			$htaccess = preg_replace("/#\s?BEGIN\s?WClearfyCache.*?#\s?END\s?WClearfyCache/s", "", $htaccess);
			self::deleteCache();
		}

		return $htaccess;
	}

	public static function prefixRedirect()
	{
		$forceTo = "";

		if( defined("WCLEARFY_DISABLE_REDIRECTION") && WCLEARFY_DISABLE_REDIRECTION ) {
			return $forceTo;
		}

		if( preg_match("/^https:\/\//", home_url()) ) {
			if( preg_match("/^https:\/\/www\./", home_url()) ) {
				$forceTo = "\nRewriteCond %{HTTPS} =on" . "\n" . "RewriteCond %{HTTP_HOST} ^www." . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . "\n";
			} else {
				$forceTo = "\nRewriteCond %{HTTPS} =on" . "\n" . "RewriteCond %{HTTP_HOST} ^" . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . "\n";
			}
		} else {
			if( preg_match("/^http:\/\/www\./", home_url()) ) {
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^" . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . "\n" . "RewriteRule ^(.*)$ " . preg_quote(home_url(), "/") . "\/$1 [R=301,L]" . "\n";
			} else {
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^www." . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . " [NC]" . "\n" . "RewriteRule ^(.*)$ " . preg_quote(home_url(), "/") . "\/$1 [R=301,L]" . "\n";
			}
		}

		return $forceTo;
	}

	public static function getHtaccess()
	{
		$mobile = "";
		$loggedInUser = "";
		$ifIsNotSecure = "";
		$trailing_slash_rule = "";
		$consent_cookie = "";

		$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
		if( ($language_negotiation_type == 2) && self::isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
			$cache_path = '/cache/all/%{HTTP_HOST}/';
			$disable_condition = true;
		} else {
			$cache_path = '/cache/all/';
			$disable_condition = false;
		}

		if( isset($_POST["wpFastestCacheMobile"]) && $_POST["wpFastestCacheMobile"] == "on" ) {
			$mobile = "RewriteCond %{HTTP_USER_AGENT} !^.*(" . self::getMobileUserAgents() . ").*$ [NC]" . "\n";

			if( isset($_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER']) ) {
				$mobile = $mobile . "RewriteCond %{HTTP_CLOUDFRONT_IS_MOBILE_VIEWER} false [NC]" . "\n";
				$mobile = $mobile . "RewriteCond %{HTTP_CLOUDFRONT_IS_TABLET_VIEWER} false [NC]" . "\n";
			}
		}

		if( isset($_POST["wpFastestCacheLoggedInUser"]) && $_POST["wpFastestCacheLoggedInUser"] == "on" ) {
			$loggedInUser = "RewriteCond %{HTTP:Cookie} !wordpress_logged_in" . "\n";
		}

		if( !preg_match("/^https/i", get_option("home")) ) {
			$ifIsNotSecure = "RewriteCond %{HTTPS} !=on";
		}

		if( self::is_trailing_slash() ) {
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} \/$" . "\n";
		} else {
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} ![^\/]+\/$" . "\n";
		}

		$data = "# BEGIN WClearfyCache" . "\n" . "# Modified Time: " . date("d-m-y G:i:s", current_time('timestamp')) . "\n" . "<IfModule mod_rewrite.c>" . "\n" . "RewriteEngine On" . "\n" . "RewriteBase /" . "\n" . self::ruleForWpContent() . "\n" . self::prefixRedirect() . self::excludeRules() . "\n" . self::excludeAdminCookie() . "\n" . self::http_condition_rule() . "\n" . "RewriteCond %{HTTP_USER_AGENT} !(" . self::get_excluded_useragent() . ")" . "\n" . "RewriteCond %{HTTP_USER_AGENT} !(WP\sFastest\sCache\sPreload(\siPhone\sMobile)?\s*Bot)" . "\n" . "RewriteCond %{REQUEST_METHOD} !POST" . "\n" . $ifIsNotSecure . "\n" . "RewriteCond %{REQUEST_URI} !(\/){2}$" . "\n" . $trailing_slash_rule . "RewriteCond %{QUERY_STRING} !.+" . "\n" . $loggedInUser . $consent_cookie . "RewriteCond %{HTTP:Cookie} !comment_author_" . "\n" . //"RewriteCond %{HTTP:Cookie} !woocommerce_items_in_cart"."\n".
			"RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil" . "\n" . 'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]' . "\n" . $mobile;

		if( ABSPATH == "//" ) {
			$data = $data . "RewriteCond %{DOCUMENT_ROOT}/" . basename(WP_CONTENT_DIR) . $cache_path . "$1/index.html -f" . "\n";
		} else {
			//WARNING: If you change the following lines, you need to update webp as well
			$data = $data . "RewriteCond %{DOCUMENT_ROOT}/" . basename(WP_CONTENT_DIR) . $cache_path . "$1/index.html -f [or]" . "\n";
			// to escape spaces
			$tmp_WCLEARFY_WP_CONTENT_DIR = str_replace(" ", "\ ", WP_CONTENT_DIR);

			$data = $data . "RewriteCond " . $tmp_WCLEARFY_WP_CONTENT_DIR . $cache_path . self::getRewriteBase(true) . "$1/index.html -f" . "\n";
		}

		$data = $data . 'RewriteRule ^(.*) "/' . self::getRewriteBase() . basename(WP_CONTENT_DIR) . $cache_path . self::getRewriteBase(true) . '$1/index.html" [L]' . "\n";

		//RewriteRule !/  "/wp-content/cache/all/index.html" [L]

		/*if( class_exists("WCL_MobileCache") && isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme ) {
			$wclearfy_mobile = new WCL_MobileCache();

			if( $this->isPluginActive('wptouch/wptouch.php') || $this->isPluginActive('wptouch-pro/wptouch-pro.php') ) {
				$wclearfy_mobile->set_wptouch(true);
			} else {
				$wclearfy_mobile->set_wptouch(false);
			}

			$data = $data . "\n\n\n" . $wclearfy_mobile->update_htaccess($data);
		}*/

		$data = $data . "</IfModule>" . "\n" . "<FilesMatch \"index\.(html|htm)$\">" . "\n" . "AddDefaultCharset UTF-8" . "\n" . "<ifModule mod_headers.c>" . "\n" . "FileETag None" . "\n" . "Header unset ETag" . "\n" . "Header set Cache-Control \"max-age=0, no-cache, no-store, must-revalidate\"" . "\n" . "Header set Pragma \"no-cache\"" . "\n" . "Header set Expires \"Mon, 29 Oct 1923 20:30:00 GMT\"" . "\n" . "</ifModule>" . "\n" . "</FilesMatch>" . "\n" . "# END WClearfyCache" . "\n";

		if( is_multisite() ) {
			return "";
		} else {
			return preg_replace("/\n+/", "\n", $data);
		}
	}

	public static function http_condition_rule()
	{
		$http_host = preg_replace("/(http(s?)\:)?\/\/(www\d*\.)?/i", "", trim(home_url(), "/"));

		if( preg_match("/\//", $http_host) ) {
			$http_host = strstr($http_host, '/', true);
		}

		if( preg_match("/www\./", home_url()) ) {
			$http_host = "www." . $http_host;
		}

		return "RewriteCond %{HTTP_HOST} ^" . $http_host;
	}

	public static function ruleForWpContent()
	{
		$newContentPath = str_replace(home_url(), "", content_url());
		if( !preg_match("/wp-content/", $newContentPath) ) {
			$newContentPath = trim($newContentPath, "/");

			return "RewriteRule ^" . $newContentPath . "/cache/(.*) " . WP_CONTENT_DIR . "/cache/$1 [L]" . "\n";
		}

		return "";
	}

	public static function getRewriteBase($sub = "")
	{
		if( $sub && self::is_subdirectory_install() ) {
			$trimedProtocol = preg_replace("/http:\/\/|https:\/\//", "", trim(home_url(), "/"));
			$path = strstr($trimedProtocol, '/');

			if( $path ) {
				return trim($path, "/") . "/";
			} else {
				return "";
			}
		}

		$url = rtrim(site_url(), "/");
		preg_match("/https?:\/\/[^\/]+(.*)/", $url, $out);

		if( isset($out[1]) && $out[1] ) {
			$out[1] = trim($out[1], "/");

			if( preg_match("/\/" . preg_quote($out[1], "/") . "\//", WP_CONTENT_DIR) ) {
				return $out[1] . "/";
			} else {
				return "";
			}
		} else {
			return "";
		}
	}

	public static function singleDeleteCache($comment_id = false, $post_id = false)
	{

		$to_clear_parents = true;
		$to_clear_feed = true;

		// not to clear cache of homepage/cats/tags after ajax request by other plugins
		if( isset($_POST) && isset($_POST["action"]) ) {
			// kk Star Rating
			if( $_POST["action"] == "kksr_ajax" ) {
				$to_clear_parents = false;
			}

			// All In One Schema.org Rich Snippets
			if( preg_match("/bsf_(update|submit)_rating/i", $_POST["action"]) ) {
				$to_clear_parents = false;
			}

			// Yet Another Stars Rating
			if( $_POST["action"] == "yasr_send_visitor_rating" ) {
				$to_clear_parents = false;
				$post_id = $_POST["post_id"];
			}

			// All In One Schema.org Rich Snippets
			if( preg_match("/bsf_(update|submit)_rating/i", $_POST["action"]) ) {
				$to_clear_feed = false;
			}
		}

		if( $comment_id ) {
			$comment_id = intval($comment_id);

			$comment = get_comment($comment_id);

			if( $comment && $comment->comment_post_ID ) {
				$post_id = $comment->comment_post_ID;
			}
		}

		if( $post_id ) {
			$post_id = intval($post_id);

			$permalink = urldecode(get_permalink($post_id));

			//for trash contents
			$permalink = rtrim($permalink, "/");
			$permalink = preg_replace("/__trashed$/", "", $permalink);
			//for /%postname%/%post_id% : sample-url__trashed/57595
			$permalink = preg_replace("/__trashed\/(\d+)$/", "/$1", $permalink);

			if( preg_match("/https?:\/\/[^\/]+\/(.+)/", $permalink, $out) ) {
				$path = self::getWpContentDir("/cache/all/") . $out[1];
				$mobile_path = self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $out[1];

				$files = [];

				if( is_dir($path) ) {
					array_push($files, $path);
				}

				if( is_dir($mobile_path) ) {
					array_push($files, $mobile_path);
				}

				if( defined('WCACHE_QUERYSTRING') && WCACHE_QUERYSTRING ) {
					$files_with_query_string = glob($path . "\?*");
					$mobile_files_with_query_string = glob($mobile_path . "\?*");

					if( is_array($files_with_query_string) && (count($files_with_query_string) > 0) ) {
						$files = array_merge($files, $files_with_query_string);
					}

					if( is_array($mobile_files_with_query_string) && (count($mobile_files_with_query_string) > 0) ) {
						$files = array_merge($files, $mobile_files_with_query_string);
					}
				}

				if( $to_clear_feed ) {
					// to clear cache of /feed
					if( preg_match("/https?:\/\/[^\/]+\/(.+)/", get_feed_link(), $feed_out) ) {
						array_push($files, self::getWpContentDir("/cache/all/") . $feed_out[1]);
					}

					// to clear cache of /comments/feed/
					if( preg_match("/https?:\/\/[^\/]+\/(.+)/", get_feed_link("comments_"), $comment_feed_out) ) {
						array_push($files, self::getWpContentDir("/cache/all/") . $comment_feed_out[1]);
					}
				}

				foreach((array)$files as $file) {
					self::rm_folder_recursively($file);
				}
			}

			if( $to_clear_parents ) {
				// to clear cache of homepage
				self::deleteHomePageCache();

				// to clear cache of author page
				self::delete_author_page_cache($post_id);

				// to clear sitemap cache
				self::delete_sitemap_cache();

				// to clear cache of cats and  tags which contains the post (only first page)
				global $wpdb;
				$terms = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "term_relationships` WHERE `object_id`=" . $post_id, ARRAY_A);

				foreach($terms as $term_key => $term_val) {
					self::delete_cache_of_term($term_val["term_taxonomy_id"]);
				}
			}

			self::delete_multiple_domain_mapping_cache();
		}
	}

	public static function delete_cache_of_term($term_taxonomy_id)
	{
		$term = get_term_by("term_taxonomy_id", $term_taxonomy_id);

		if( !$term || is_wp_error($term) ) {
			return false;
		}

		//if(preg_match("/cat|tag|store|listing/", $term->taxonomy)){}

		$url = get_term_link($term->term_id, $term->taxonomy);

		if( preg_match("/^http/", $url) ) {
			$path = preg_replace("/https?\:\/\/[^\/]+/i", "", $url);
			$path = trim($path, "/");
			$path = urldecode($path);

			// to remove the cache of tag/cat
			if( file_exists(self::getWpContentDir("/cache/all/") . $path . "/index.html") ) {
				@unlink(self::getWpContentDir("/cache/all/") . $path . "/index.html");
			}

			if( file_exists(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $path . "/index.html") ) {
				@unlink(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $path . "/index.html");
			}

			// to remove the cache of the pages
			self::rm_folder_recursively(self::getWpContentDir("/cache/all/") . $path . "/page");
			self::rm_folder_recursively(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $path . "/page");

			// to remove the cache of the feeds
			self::rm_folder_recursively(self::getWpContentDir("/cache/all/") . $path . "/feed");
			self::rm_folder_recursively(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $path . "/feed");
		}

		if( $term->parent > 0 ) {
			$parent = get_term_by("id", $term->parent, $term->taxonomy);
			self::delete_cache_of_term($parent->term_taxonomy_id);
		}
	}

	public static function delete_author_page_cache($post_id)
	{
		$author_id = get_post_field('post_author', $post_id);
		$permalink = get_author_posts_url($author_id);

		if( preg_match("/https?:\/\/[^\/]+\/(.+)/", $permalink, $out) ) {
			$path = self::getWpContentDir("/cache/all/") . $out[1];
			$mobile_path = self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $out[1];

			self::rm_folder_recursively($path);
			self::rm_folder_recursively($mobile_path);
		}
	}

	public static function delete_sitemap_cache()
	{
		//to clear sitemap.xml and sitemap-(.+).xml
		$files = array_merge(glob(self::getWpContentDir("/cache/all/") . "sitemap*.xml"), glob(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . "sitemap*.xml"));

		foreach((array)$files as $file) {
			self::rm_folder_recursively($file);
		}
	}

	public static function deleteHomePageCache()
	{
		$site_url_path = preg_replace("/https?\:\/\/[^\/]+/i", "", site_url());
		$home_url_path = preg_replace("/https?\:\/\/[^\/]+/i", "", home_url());

		if( $site_url_path ) {
			$site_url_path = trim($site_url_path, "/");

			if( $site_url_path ) {
				@unlink(self::getWpContentDir("/cache/all/") . $site_url_path . "/index.html");
				@unlink(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $site_url_path . "/index.html");

				//to clear pagination of homepage cache
				self::rm_folder_recursively(self::getWpContentDir("/cache/all/") . $site_url_path . "/page");
				self::rm_folder_recursively(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $site_url_path . "/page");
			}
		}

		if( $home_url_path ) {
			$home_url_path = trim($home_url_path, "/");

			if( $home_url_path ) {
				@unlink(self::getWpContentDir("/cache/all/") . $home_url_path . "/index.html");
				@unlink(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $home_url_path . "/index.html");

				//to clear pagination of homepage cache
				self::rm_folder_recursively(self::getWpContentDir("/cache/all/") . $home_url_path . "/page");
				self::rm_folder_recursively(self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $home_url_path . "/page");
			}
		}

		if( file_exists(self::getWpContentDir("/cache/all/index.html")) ) {
			@unlink(self::getWpContentDir("/cache/all/index.html"));
		}

		if( file_exists(self::getWpContentDir("/cache/wclearfy-mobile-cache/index.html")) ) {
			@unlink(self::getWpContentDir("/cache/wclearfy-mobile-cache/index.html"));
		}

		//to clear pagination of homepage cache
		self::rm_folder_recursively(self::getWpContentDir("/cache/all/page"));
		self::rm_folder_recursively(self::getWpContentDir("/cache/wclearfy-mobile-cache/page"));

		// options-reading.php - static posts page
		if( $page_for_posts_id = get_option('page_for_posts') ) {
			$page_for_posts_permalink = urldecode(get_permalink($page_for_posts_id));

			$page_for_posts_permalink = rtrim($page_for_posts_permalink, "/");
			$page_for_posts_permalink = preg_replace("/__trashed$/", "", $page_for_posts_permalink);
			//for /%postname%/%post_id% : sample-url__trashed/57595
			$page_for_posts_permalink = preg_replace("/__trashed\/(\d+)$/", "/$1", $page_for_posts_permalink);

			if( preg_match("/https?:\/\/[^\/]+\/(.+)/", $page_for_posts_permalink, $out) ) {
				$page_for_posts_path = self::getWpContentDir("/cache/all/") . $out[1];
				$page_for_posts_mobile_path = self::getWpContentDir("/cache/wclearfy-mobile-cache/") . $out[1];

				self::rm_folder_recursively($page_for_posts_path);
				self::rm_folder_recursively($page_for_posts_mobile_path);
			}
		}
	}

	public static function deleteCache($minified = false)
	{
		$created_tmpWclearfy = false;
		$cache_deleted = false;
		$minifed_deleted = false;

		$cache_path = self::getWpContentDir("/cache/all");
		$minified_cache_path = self::getWpContentDir("/cache/wclearfy-minified");

		if( class_exists("WCL_MobileCache") ) {
			if( is_dir(self::getWpContentDir("/cache/wclearfy-mobile-cache")) ) {
				if( is_dir(self::getWpContentDir("/cache/tmpWclearfy")) ) {
					rename(self::getWpContentDir("/cache/wclearfy-mobile-cache"), self::getWpContentDir("/cache/tmpWclearfy/mobile_") . time());
				} else if( @mkdir(self::getWpContentDir("/cache/tmpWclearfy"), 0755, true) ) {
					rename(self::getWpContentDir("/cache/wclearfy-mobile-cache"), self::getWpContentDir("/cache/tmpWclearfy/mobile_") . time());
				}
			}
		}

		if( !is_dir(self::getWpContentDir("/cache/tmpWclearfy")) ) {
			if( @mkdir(self::getWpContentDir("/cache/tmpWclearfy"), 0755, true) ) {
				$created_tmpWclearfy = true;
			} else {
				$created_tmpWclearfy = false;
				//$this->systemMessage = array("Permission of <strong>/wp-content/cache</strong> must be <strong>755</strong>", "error");
			}
		} else {
			$created_tmpWclearfy = true;
		}

		//to clear widget cache path
		self::deleteWidgetCache();

		self::delete_multiple_domain_mapping_cache($minified);

		if( is_dir($cache_path) ) {
			if( @rename($cache_path, self::getWpContentDir("/cache/tmpWclearfy/") . time()) ) {
				$cache_deleted = true;
			}
		} else {
			$cache_deleted = true;
		}

		if( $minified ) {
			if( is_dir($minified_cache_path) ) {
				if( @rename($minified_cache_path, self::getWpContentDir("/cache/tmpWclearfy/m") . time()) ) {

					$minifed_deleted = true;
				}
			} else {
				$minifed_deleted = true;
			}
		} else {
			$minifed_deleted = true;
		}

		if( $created_tmpWclearfy && $cache_deleted && $minifed_deleted ) {
			do_action('wclearfy_delete_cache');

			self::rm_folder_recursively(self::getWpContentDir("/cache/tmpWclearfy"));

			return true;
		} else {
			// ошибка
		}
	}

	public static function delete_multiple_domain_mapping_cache($minified = false)
	{
		//https://wordpress.org/plugins/multiple-domain-mapping-on-single-site/
		if( self::isPluginActive("multiple-domain-mapping-on-single-site/multidomainmapping.php") ) {
			$multiple_arr = get_option('falke_mdm_mappings');

			if( isset($multiple_arr) && isset($multiple_arr["mappings"]) && isset($multiple_arr["mappings"][0]) ) {
				foreach($multiple_arr["mappings"] as $mapping_key => $mapping_value) {
					if( $minified ) {
						$mapping_domain_path = preg_replace("/(\/cache\/[^\/]+\/all\/)/", "/cache/" . $mapping_value["domain"] . "/", self::getWpContentDir("/cache/all/"));

						if( is_dir($mapping_domain_path) ) {
							if( @rename($mapping_domain_path, self::getWpContentDir("/cache/tmpWclearfy/") . $mapping_value["domain"] . "_" . time()) ) {

							}
						}
					} else {
						$mapping_domain_path = preg_replace("/(\/cache\/[^\/]+\/all)/", "/cache/" . $mapping_value["domain"] . "/all", self::getWpContentDir("/cache/all/index.html"));

						@unlink($mapping_domain_path);
					}
				}
			}
		}
	}

	public static function deleteWidgetCache()
	{
		$widget_cache_path = self::getWpContentDir("/cache/wclearfy-widget-cache");

		if( is_dir($widget_cache_path) ) {
			if( !is_dir(self::getWpContentDir("/cache/tmpWclearfy")) ) {
				if( @mkdir(self::getWpContentDir("/cache/tmpWclearfy"), 0755, true) ) {
					//tmpWclearfy has been created
				}
			}

			if( @rename($widget_cache_path, self::getWpContentDir("/cache/tmpWclearfy/w") . time()) ) {
				//DONE
			}
		}
	}

	public static function to_move_gtranslate_rules($htaccess)
	{
		preg_match("/\#\#\#\s+BEGIN\sGTranslate\sconfig\s\#\#\#[^\#]+\#\#\#\s+END\sGTranslate\sconfig\s\#\#\#/i", $htaccess, $gtranslate);

		if( isset($gtranslate[0]) ) {
			$htaccess = preg_replace("/\#\#\#\s+BEGIN\sGTranslate\sconfig\s\#\#\#[^\#]+\#\#\#\s+END\sGTranslate\sconfig\s\#\#\#/i", "", $htaccess);
			$htaccess = $gtranslate[0] . "\n" . $htaccess;
		}

		return $htaccess;
	}

	public static function getMobileUserAgents()
	{
		return implode("|", self::get_mobile_browsers()) . "|" . implode("|", self::get_operating_systems());
	}

	public static function rm_folder_recursively($dir, $i = 1)
	{
		if( is_dir($dir) ) {
			$files = @scandir($dir);
			foreach((array)$files as $file) {
				if( $i > 50 && !preg_match("/wp-fastest-cache-premium/i", $dir) ) {
					return true;
				} else {
					$i++;
				}
				if( '.' === $file || '..' === $file ) {
					continue;
				}
				if( is_dir("$dir/$file") ) {
					self::rm_folder_recursively("$dir/$file", $i);
				} else {
					if( file_exists("$dir/$file") ) {
						@unlink("$dir/$file");
					}
				}
			}
		}

		if( is_dir($dir) ) {
			$files_tmp = @scandir($dir);

			if( !isset($files_tmp[2]) ) {
				@rmdir($dir);
			}
		}

		return true;
	}

	public static function excludeAdminCookie()
	{
		$rules = "";
		$users_groups = array_chunk(get_users(["role" => "administrator", "fields" => ["user_login"]]), 5);

		foreach($users_groups as $group_key => $group) {
			$tmp_users = "";
			$tmp_rule = "";

			foreach($group as $key => $value) {
				if( $tmp_users ) {
					$tmp_users = $tmp_users . "|" . sanitize_user(wp_unslash($value->user_login), true);
				} else {
					$tmp_users = sanitize_user(wp_unslash($value->user_login), true);
				}

				// to replace spaces with \s
				$tmp_users = preg_replace("/\s/", "\s", $tmp_users);

				if( !next($group) ) {
					$tmp_rule = "RewriteCond %{HTTP:Cookie} !wordpress_logged_in_[^\=]+\=" . $tmp_users;
				}
			}

			if( $rules ) {
				$rules = $rules . "\n" . $tmp_rule;
			} else {
				$rules = $tmp_rule;
			}
		}

		return "# Start_WCLEARFY_Exclude_Admin_Cookie\n" . $rules . "\n# End_WCLEARFY_Exclude_Admin_Cookie\n";
	}

	public static function excludeRules()
	{
		$uris = WCL_Plugin::app()->getPopulateOption('cache_reject_uri');

		if( !empty($uris) ) {
			$uris = array_map(function ($value) {
				$site_url = site_url();

				$value = trim(rtrim($value));
				// http://clearfy.pro/members/ -> /members/
				$value = str_replace($site_url, "", $value);
				// /members/(.*) -> members/(.*)
				$value = preg_replace("/^\//", "", $value);
				// members/ -> members
				$value = untrailingslashit($value);

				if( empty($value) ) {
					return "RewriteCond %{REQUEST_URI} !^/$ [NC]";
				}

				return "RewriteCond %{REQUEST_URI} !^/" . $value . " [NC]";
			}, preg_split('/\r\n|\n|\r/', $uris));
		} else {
			$uris = [];
		}

		return "# Start WCLEARFY Exclude\n" . implode("\n", $uris) . "\n# End WCLEARFY Exclude\n";
	}


	public static function modifyHtaccess()
	{
		$path = ABSPATH;
		if( self::is_subdirectory_install() ) {
			$path = self::getABSPATH();
		}

		// if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"])){
		// 	return array("The plugin does not work with Microsoft IIS. Only with Apache", "error");
		// }

		// if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])){
		// 	return array("The plugin does not work with Nginx. Only with Apache", "error");
		// }

		if( !file_exists($path . ".htaccess") ) {
			if( isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && (preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"]) || preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])) ) {
				//
			} else {
				throw new Exception("<label>.htaccess was not found</label> <a target='_blank' href='http://clearfy.com/docs/htaccess-was-not-found/'>Read More</a>", 98);
			}
		}

		if( self::isPluginActive('wp-postviews/wp-postviews.php') ) {
			$wp_postviews_options = get_option("views_options");
			$wp_postviews_options["use_ajax"] = true;
			update_option("views_options", $wp_postviews_options);

			if( !WP_CACHE ) {
				if( $wp_config = @file_get_contents(ABSPATH . "wp-config.php") ) {
					$wp_config = str_replace("\$table_prefix", "define('WP_CACHE', true);\n\$table_prefix", $wp_config);

					if( !@file_put_contents(ABSPATH . "wp-config.php", $wp_config) ) {
						throw new Exception("define('WP_CACHE', true); is needed to be added into wp-config.php", 99);
					}
				} else {
					throw new Exception("define('WP_CACHE', true); is needed to be added into wp-config.php", 99);
				}
			}
		}

		$htaccess = @file_get_contents($path . ".htaccess");

		// if(defined('DONOTCACHEPAGE')){
		// 	return array("DONOTCACHEPAGE <label>constant is defined as TRUE. It must be FALSE</label>", "error");
		// }else

		if( !get_option('permalink_structure') ) {
			throw new Exception("You have to set <strong><u><a href='" . admin_url() . "options-permalink.php" . "'>permalinks</a></u></strong>", 100);
		} else if( $res = self::checkSuperCache($path, $htaccess) ) {
			return $res;
		} else if( self::isPluginActive('fast-velocity-minify/fvm.php') ) {
			throw new Exception("Fast Velocity Minify needs to be deactivated", 101);
		} else if( self::isPluginActive('far-future-expiration/far-future-expiration.php') ) {
			throw new Exception("Far Future Expiration Plugin needs to be deactivated", 102);
		} else if( self::isPluginActive('sg-cachepress/sg-cachepress.php') ) {
			throw new Exception("SG Optimizer needs to be deactived", 103);
		} else if( self::isPluginActive('adrotate/adrotate.php') || self::isPluginActive('adrotate-pro/adrotate.php') ) {
			//return self::warningIncompatible("AdRotate");
			throw new Exception("AdRotate needs to be deactived", 104);
		} else if( self::isPluginActive('mobilepress/mobilepress.php') ) {
			/*return self::warningIncompatible("MobilePress", array(
				"name" => "WPtouch Mobile",
				"url" => "https://wordpress.org/plugins/wptouch/"
			));*/
			throw new Exception("MobilePress needs to be deactived", 105);
		} else if( self::isPluginActive('speed-booster-pack/speed-booster-pack.php') ) {
			throw new Exception("Speed Booster Pack needs to be deactivated<br>", 106);
		} else if( self::isPluginActive('wp-performance-score-booster/wp-performance-score-booster.php') ) {
			throw new Exception("WP Performance Score Booster needs to be deactivated<br>This plugin has aldready Gzip, Leverage Browser Caching features", 107);
		} else if( self::isPluginActive('bwp-minify/bwp-minify.php') ) {
			throw new Exception("Better WordPress Minify needs to be deactivated<br>This plugin has aldready Minify feature", 108);
		} else if( self::isPluginActive('check-and-enable-gzip-compression/richards-toolbox.php') ) {
			throw new Exception("Check and Enable GZIP compression needs to be deactivated<br>This plugin has aldready Gzip feature", 109);
		} else if( self::isPluginActive('gzippy/gzippy.php') ) {
			throw new Exception("GZippy needs to be deactivated<br>This plugin has aldready Gzip feature", 110);
		} else if( self::isPluginActive('gzip-ninja-speed-compression/gzip-ninja-speed.php') ) {
			throw new Exception("GZip Ninja Speed Compression needs to be deactivated<br>This plugin has aldready Gzip feature", 111);
		} else if( self::isPluginActive('wordpress-gzip-compression/ezgz.php') ) {
			throw new Exception("WordPress Gzip Compression needs to be deactivated<br>This plugin has aldready Gzip feature", 112);
		} else if( self::isPluginActive('filosofo-gzip-compression/filosofo-gzip-compression.php') ) {
			throw new Exception("GZIP Output needs to be deactivated<br>This plugin has aldready Gzip feature", 113);
		} else if( self::isPluginActive('head-cleaner/head-cleaner.php') ) {
			throw new Exception("Head Cleaner needs to be deactivated", 114);
		} else if( self::isPluginActive('far-future-expiry-header/far-future-expiration.php') ) {
			throw new Exception("Far Future Expiration Plugin needs to be deactivated", 115);
		} else if( is_writable($path . ".htaccess") ) {
			$htaccess = apply_filters('wclearfy/cache/htaccess_rules', $htaccess);

			$htaccess = self::insertRewriteRule($htaccess);
			$htaccess = self::to_move_gtranslate_rules($htaccess);

			file_put_contents($path . ".htaccess", $htaccess);
		}

		return false;
	}


	/**
	 * Returns the home URL, without WPML filters if the plugin is active
	 *
	 * @param string $path Path to add to the home URL.
	 *
	 * @return string
	 */
	public static function get_home_url($path = '')
	{
		static $home_url = [];

		if( isset($home_url[$path]) ) {
			return $home_url[$path];
		}

		$home_url[$path] = home_url($path);

		return $home_url[$path];
	}

	/**
	 * Get the name of the "home directory", in case the home URL is not at the domain's root.
	 * It can be seen like the `RewriteBase` from the .htaccess file, but without the trailing slash.
	 *
	 * @return string
	 */
	public static function get_home_dirname()
	{
		static $home_root;

		if( isset($home_root) ) {
			return $home_root;
		}

		$home_root = wp_parse_url(self::get_main_home_url());

		if( !empty($home_root['path']) ) {
			$home_root = '/' . trim($home_root['path'], '/');
			$home_root = rtrim($home_root, '/');
		} else {
			$home_root = '';
		}

		return $home_root;
	}

	/**
	 * Get the URL of the site's root. It corresponds to the main site's home page URL.
	 *
	 * @return string
	 * @author Grégory Viguier
	 *
	 * @since  3.1.1
	 */
	public static function get_main_home_url()
	{
		static $root_url;

		if( isset($root_url) ) {
			return $root_url;
		}

		if( !is_multisite() || is_main_site() ) {
			$root_url = self::get_home_url('/');

			return $root_url;
		}

		$current_network = get_network();

		if( $current_network ) {
			$root_url = set_url_scheme('https://' . $current_network->domain . $current_network->path);
			$root_url = trailingslashit($root_url);
		} else {
			$root_url = self::get_home_url('/');
		}

		return $root_url;
	}
}