<?php
/**
 * Helpers methods
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 10.11.2020, Webcraftic
 * @version 1.0
 */

class WCL_Cache_Helpers {

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
	 * @return bool
	 */
	public static function isPluginActive($plugin)
	{
		return in_array($plugin, (array)get_option('active_plugins', array())) || self::isPluginActiveForNetwork($plugin);
	}

	/**
	 * @param string $plugin
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

		/cache/wpfc-minified

		/cache/wpfc-widget-cache

		/cache/wpfc-mobile-cache/
		/cache/wpfc-mobile-cache/page
		/cache/wpfc-mobile-cache/index.html

		/cache/tmpWpfc
		/cache/tmpWpfc/
		/cache/tmpWpfc/mobile_
		/cache/tmpWpfc/m
		/cache/tmpWpfc/w


		/cache/testWpFc/

		/cache/all/testWpFc/

		/cache/wpfc-widget-cache/
		/cache/wpfc-widget-cache
		/cache/wpfc-widget-cache/".$args["widget_id"].".html
		*/

		if( $path ) {
			if( preg_match("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", $path) ) {
				//WPML language switch
				//https://wpml.org/forums/topic/wpml-language-switch-wp-fastest-cache-issue/
				$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
				if( ($language_negotiation_type == 2) && self::isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
					$my_home_url = apply_filters('wpml_home_url', get_option('home'));
					$my_home_url = preg_replace("/https?\:\/\//i", "", $my_home_url);
					$my_home_url = trim($my_home_url, "/");

					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $my_home_url . "/$1", $path);
				} else if( ($language_negotiation_type == 1) && self::isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
					$my_current_lang = apply_filters('wpml_current_language', null);

					if( $my_current_lang ) {
						$path = preg_replace("/\/cache\/wpfc-widget-cache\/(.+)/", "/cache/wpfc-widget-cache/" . $my_current_lang . "-" . "$1", $path);
					}
				}

				if( self::isPluginActive('multiple-domain-mapping-on-single-site/multidomainmapping.php') ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( self::isPluginActive('polylang/polylang.php') ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( self::isPluginActive('multiple-domain/multiple-domain.php') ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( is_multisite() ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
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
		return "facebookexternalhit|WP_FASTEST_CACHE_CSS_VALIDATOR|Twitterbot|LinkedInBot|WhatsApp|Mediatoolkitbot";
	}

	public static function get_mobile_browsers()
	{
		$mobile_browsers = array(
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
		);

		return $mobile_browsers;
	}

	public static function get_operating_systems()
	{
		$operating_systems = array(
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
		);

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

		$wptouch_smartphone_list = array();

		$wptouch_smartphone_list[] = array('iPhone'); // iPhone
		$wptouch_smartphone_list[] = array('Android', 'Mobile'); // Android devices
		$wptouch_smartphone_list[] = array('BB', 'Mobile Safari'); // BB10 devices
		$wptouch_smartphone_list[] = array('BlackBerry', 'Mobile Safari'); // BB 6, 7 devices
		$wptouch_smartphone_list[] = array('Firefox', 'Mobile'); // Firefox OS devices
		$wptouch_smartphone_list[] = array('IEMobile/11', 'Touch'); // Windows IE 11 touch devices
		$wptouch_smartphone_list[] = array('IEMobile/10', 'Touch'); // Windows IE 10 touch devices
		$wptouch_smartphone_list[] = array('IEMobile/9.0'); // Windows Phone OS 9
		$wptouch_smartphone_list[] = array('IEMobile/8.0'); // Windows Phone OS 8
		$wptouch_smartphone_list[] = array('IEMobile/7.0'); // Windows Phone OS 7
		$wptouch_smartphone_list[] = array('OPiOS', 'Mobile'); // Opera Mini iOS
		$wptouch_smartphone_list[] = array('Coast', 'Mobile'); // Opera Coast iOS

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
			return array("WP Super Cache needs to be deactive", "error");
		} else {
			@unlink($path . "wp-content/wp-cache-config.php");

			$message = "";

			if( is_file($path . "wp-content/wp-cache-config.php") ) {
				$message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
			}

			if( preg_match("/supercache/", $htaccess) ) {
				$message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
			}

			return $message ? array(
				"WP Super Cache cannot remove its own remnants so please follow the steps below" . $message,
				"error"
			) : "";
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

	public static function insertLBCRule($htaccess)
	{
		$browser_caching = WCL_Plugin::app()->getPopulateOption('browser_caching');

		if( $browser_caching ) {

			$data = "# BEGIN LBCWpFastestCache" . "\n" . '<FilesMatch "\.(webm|ogg|mp4|ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|x-html|css|xml|js|woff|woff2|otf|ttf|svg|eot)(\.gz)?$">' . "\n" . '<IfModule mod_expires.c>' . "\n" . 'AddType application/font-woff2 .woff2' . "\n" . 'AddType application/x-font-opentype .otf' . "\n" . 'ExpiresActive On' . "\n" . 'ExpiresDefault A0' . "\n" . 'ExpiresByType video/webm A10368000' . "\n" . 'ExpiresByType video/ogg A10368000' . "\n" . 'ExpiresByType video/mp4 A10368000' . "\n" . 'ExpiresByType image/webp A10368000' . "\n" . 'ExpiresByType image/gif A10368000' . "\n" . 'ExpiresByType image/png A10368000' . "\n" . 'ExpiresByType image/jpg A10368000' . "\n" . 'ExpiresByType image/jpeg A10368000' . "\n" . 'ExpiresByType image/ico A10368000' . "\n" . 'ExpiresByType image/svg+xml A10368000' . "\n" . 'ExpiresByType text/css A10368000' . "\n" . 'ExpiresByType text/javascript A10368000' . "\n" . 'ExpiresByType application/javascript A10368000' . "\n" . 'ExpiresByType application/x-javascript A10368000' . "\n" . 'ExpiresByType application/font-woff2 A10368000' . "\n" . 'ExpiresByType application/x-font-opentype A10368000' . "\n" . 'ExpiresByType application/x-font-truetype A10368000' . "\n" . '</IfModule>' . "\n" . '<IfModule mod_headers.c>' . "\n" . 'Header set Expires "max-age=A10368000, public"' . "\n" . 'Header unset ETag' . "\n" . 'Header set Connection keep-alive' . "\n" . 'FileETag None' . "\n" . '</IfModule>' . "\n" . '</FilesMatch>' . "\n" . "# END LBCWpFastestCache" . "\n";

			if( !preg_match("/BEGIN\s*LBCWpFastestCache/", $htaccess) ) {
				return $data . $htaccess;
			} else {
				return $htaccess;
			}
		} else {
			//delete levere browser caching
			$htaccess = preg_replace("/#\s?BEGIN\s?LBCWpFastestCache.*?#\s?END\s?LBCWpFastestCache/s", "", $htaccess);

			return $htaccess;
		}
	}

	public static function insertGzipRule($htaccess)
	{
		$gzip = WCL_Plugin::app()->getPopulateOption('gzip');

		if( $gzip ) {
			$data = "# BEGIN GzipWpFastestCache" . "\n" . "<IfModule mod_deflate.c>" . "\n" . "AddType x-font/woff .woff" . "\n" . "AddType x-font/ttf .ttf" . "\n" . "AddOutputFilterByType DEFLATE image/svg+xml" . "\n" . "AddOutputFilterByType DEFLATE text/plain" . "\n" . "AddOutputFilterByType DEFLATE text/html" . "\n" . "AddOutputFilterByType DEFLATE text/xml" . "\n" . "AddOutputFilterByType DEFLATE text/css" . "\n" . "AddOutputFilterByType DEFLATE text/javascript" . "\n" . "AddOutputFilterByType DEFLATE application/xml" . "\n" . "AddOutputFilterByType DEFLATE application/xhtml+xml" . "\n" . "AddOutputFilterByType DEFLATE application/rss+xml" . "\n" . "AddOutputFilterByType DEFLATE application/javascript" . "\n" . "AddOutputFilterByType DEFLATE application/x-javascript" . "\n" . "AddOutputFilterByType DEFLATE application/x-font-ttf" . "\n" . "AddOutputFilterByType DEFLATE x-font/ttf" . "\n" . "AddOutputFilterByType DEFLATE application/vnd.ms-fontobject" . "\n" . "AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf" . "\n" . "</IfModule>" . "\n";

			if( defined("WPFC_GZIP_FOR_COMBINED_FILES") && WPFC_GZIP_FOR_COMBINED_FILES ) {
				$data = $data . "\n" . '<FilesMatch "\d+index\.(css|js)(\.gz)?$">' . "\n" . "# to zip the combined css and js files" . "\n\n" . "RewriteEngine On" . "\n" . "RewriteCond %{HTTP:Accept-encoding} gzip" . "\n" . "RewriteCond %{REQUEST_FILENAME}\.gz -s" . "\n" . "RewriteRule ^(.*)\.(css|js) $1\.$2\.gz [QSA]" . "\n\n" . "# to revent double gzip and give the correct mime-type" . "\n\n" . "RewriteRule \.css\.gz$ - [T=text/css,E=no-gzip:1,E=FORCE_GZIP]" . "\n" . "RewriteRule \.js\.gz$ - [T=text/javascript,E=no-gzip:1,E=FORCE_GZIP]" . "\n" . "Header set Content-Encoding gzip env=FORCE_GZIP" . "\n" . "</FilesMatch>" . "\n";
			}

			$data = $data . "# END GzipWpFastestCache" . "\n";

			$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);

			return $data . $htaccess;
		} else {
			//delete gzip rules
			$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);

			return $htaccess;
		}
	}

	public static function insertRewriteRule($htaccess)
	{
		$enable_cache = WCL_Plugin::app()->getPopulateOption('enable_cache');

		if( $enable_cache ) {
			$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
			$htaccess = self::getHtaccess() . $htaccess;
		} else {
			$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
			self::deleteCache();
		}

		return $htaccess;
	}

	public static function prefixRedirect()
	{
		$forceTo = "";

		if( defined("WPFC_DISABLE_REDIRECTION") && WPFC_DISABLE_REDIRECTION ) {
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

		$data = "# BEGIN WpFastestCache" . "\n" . "# Modified Time: " . date("d-m-y G:i:s", current_time('timestamp')) . "\n" . "<IfModule mod_rewrite.c>" . "\n" . "RewriteEngine On" . "\n" . "RewriteBase /" . "\n" . self::ruleForWpContent() . "\n" . self::prefixRedirect() . self::excludeRules() . "\n" . self::excludeAdminCookie() . "\n" . self::http_condition_rule() . "\n" . "RewriteCond %{HTTP_USER_AGENT} !(" . self::get_excluded_useragent() . ")" . "\n" . "RewriteCond %{HTTP_USER_AGENT} !(WP\sFastest\sCache\sPreload(\siPhone\sMobile)?\s*Bot)" . "\n" . "RewriteCond %{REQUEST_METHOD} !POST" . "\n" . $ifIsNotSecure . "\n" . "RewriteCond %{REQUEST_URI} !(\/){2}$" . "\n" . $trailing_slash_rule . "RewriteCond %{QUERY_STRING} !.+" . "\n" . $loggedInUser . $consent_cookie . "RewriteCond %{HTTP:Cookie} !comment_author_" . "\n" . //"RewriteCond %{HTTP:Cookie} !woocommerce_items_in_cart"."\n".
			"RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil" . "\n" . 'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]' . "\n" . $mobile;

		if( ABSPATH == "//" ) {
			$data = $data . "RewriteCond %{DOCUMENT_ROOT}/" . basename(WP_CONTENT_DIR) . $cache_path . "$1/index.html -f" . "\n";
		} else {
			//WARNING: If you change the following lines, you need to update webp as well
			$data = $data . "RewriteCond %{DOCUMENT_ROOT}/" . basename(WP_CONTENT_DIR) . $cache_path . "$1/index.html -f [or]" . "\n";
			// to escape spaces
			$tmp_WPFC_WP_CONTENT_DIR = str_replace(" ", "\ ", WP_CONTENT_DIR);

			$data = $data . "RewriteCond " . $tmp_WPFC_WP_CONTENT_DIR . $cache_path . self::getRewriteBase(true) . "$1/index.html -f" . "\n";
		}

		$data = $data . 'RewriteRule ^(.*) "/' . self::getRewriteBase() . basename(WP_CONTENT_DIR) . $cache_path . self::getRewriteBase(true) . '$1/index.html" [L]' . "\n";

		//RewriteRule !/  "/wp-content/cache/all/index.html" [L]

		/*if( class_exists("WCL_MobileCache") && isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme ) {
			$wpfc_mobile = new WCL_MobileCache();

			if( $this->isPluginActive('wptouch/wptouch.php') || $this->isPluginActive('wptouch-pro/wptouch-pro.php') ) {
				$wpfc_mobile->set_wptouch(true);
			} else {
				$wpfc_mobile->set_wptouch(false);
			}

			$data = $data . "\n\n\n" . $wpfc_mobile->update_htaccess($data);
		}*/

		$data = $data . "</IfModule>" . "\n" . "<FilesMatch \"index\.(html|htm)$\">" . "\n" . "AddDefaultCharset UTF-8" . "\n" . "<ifModule mod_headers.c>" . "\n" . "FileETag None" . "\n" . "Header unset ETag" . "\n" . "Header set Cache-Control \"max-age=0, no-cache, no-store, must-revalidate\"" . "\n" . "Header set Pragma \"no-cache\"" . "\n" . "Header set Expires \"Mon, 29 Oct 1923 20:30:00 GMT\"" . "\n" . "</ifModule>" . "\n" . "</FilesMatch>" . "\n" . "# END WpFastestCache" . "\n";

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

	public static function deleteCache($minified = false)
	{
		//include_once('inc/cdn.php');
		//CdnWPFC::cloudflare_clear_cache();

		//$this->set_preload();

		$created_tmpWpfc = false;
		$cache_deleted = false;
		$minifed_deleted = false;

		$cache_path = self::getWpContentDir("/cache/all");
		$minified_cache_path = self::getWpContentDir("/cache/wpfc-minified");

		if( class_exists("WCL_MobileCache") ) {


			if( is_dir(self::getWpContentDir("/cache/wpfc-mobile-cache")) ) {
				if( is_dir(self::getWpContentDir("/cache/tmpWpfc")) ) {
					rename(self::getWpContentDir("/cache/wpfc-mobile-cache"), self::getWpContentDir("/cache/tmpWpfc/mobile_") . time());
				} else if( @mkdir(self::getWpContentDir("/cache/tmpWpfc"), 0755, true) ) {
					rename(self::getWpContentDir("/cache/wpfc-mobile-cache"), self::getWpContentDir("/cache/tmpWpfc/mobile_") . time());
				}
			}
		}

		if( !is_dir(self::getWpContentDir("/cache/tmpWpfc")) ) {
			if( @mkdir(self::getWpContentDir("/cache/tmpWpfc"), 0755, true) ) {
				$created_tmpWpfc = true;
			} else {
				$created_tmpWpfc = false;
				//$this->systemMessage = array("Permission of <strong>/wp-content/cache</strong> must be <strong>755</strong>", "error");
			}
		} else {
			$created_tmpWpfc = true;
		}

		//to clear widget cache path
		self::deleteWidgetCache();

		self::delete_multiple_domain_mapping_cache($minified);

		if( is_dir($cache_path) ) {
			if( @rename($cache_path, self::getWpContentDir("/cache/tmpWpfc/") . time()) ) {
				delete_option("WpFastestCacheHTML");
				delete_option("WpFastestCacheHTMLSIZE");
				delete_option("WpFastestCacheMOBILE");
				delete_option("WpFastestCacheMOBILESIZE");

				$cache_deleted = true;
			}
		} else {
			$cache_deleted = true;
		}

		if( $minified ) {
			if( is_dir($minified_cache_path) ) {
				if( @rename($minified_cache_path, self::getWpContentDir("/cache/tmpWpfc/m") . time()) ) {
					delete_option("WpFastestCacheCSS");
					delete_option("WpFastestCacheCSSSIZE");
					delete_option("WpFastestCacheJS");
					delete_option("WpFastestCacheJSSIZE");

					$minifed_deleted = true;
				}
			} else {
				$minifed_deleted = true;
			}
		} else {
			$minifed_deleted = true;
		}

		if( $created_tmpWpfc && $cache_deleted && $minifed_deleted ) {
			do_action('wpfc_delete_cache');

			//$this->notify(array("All cache files have been deleted", "updated"));

			/*if( $this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php") ) {
				include_once $this->get_premium_path("logs.php");

				$log = new WpFastestCacheLogs("delete");
				$log->action();
			}*/
			self::rm_folder_recursively(self::getWpContentDir("/cache/tmpWpfc"));

			return true;
		} else {
			// ошибка

			/*$this->notify(array(
				"Permissions Problem: <a href='http://www.wpfastestcache.com/warnings/delete-cache-problem-related-to-permission/' target='_blank'>Read More</a>",
				"error"
			));*/
		}

		// for ajax request
		/*if( isset($_GET["action"]) && in_array($_GET["action"], array(
				"wpfc_delete_cache",
				"wpfc_delete_cache_and_minified"
			)) ) {
			die(json_encode($this->systemMessage));
		}*/
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
							if( @rename($mapping_domain_path, self::getWpContentDir("/cache/tmpWpfc/") . $mapping_value["domain"] . "_" . time()) ) {

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
		$widget_cache_path = self::getWpContentDir("/cache/wpfc-widget-cache");

		if( is_dir($widget_cache_path) ) {
			if( !is_dir(self::getWpContentDir("/cache/tmpWpfc")) ) {
				if( @mkdir(self::getWpContentDir("/cache/tmpWpfc"), 0755, true) ) {
					//tmpWpfc has been created
				}
			}

			if( @rename($widget_cache_path, self::getWpContentDir("/cache/tmpWpfc/w") . time()) ) {
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
		$users_groups = array_chunk(get_users(array("role" => "administrator", "fields" => array("user_login"))), 5);

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

		return "# Start_WPFC_Exclude_Admin_Cookie\n" . $rules . "\n# End_WPFC_Exclude_Admin_Cookie\n";
	}

	public static function excludeRules()
	{
		$htaccess_page_rules = "";
		$htaccess_page_useragent = "";
		$htaccess_page_cookie = "";

		/*if($rules_json = get_option("WpFastestCacheExclude")){
			if($rules_json != "null"){
				$rules_std = json_decode($rules_json);

				foreach ($rules_std as $key => $value) {
					$value->type = isset($value->type) ? $value->type : "page";

					// escape the chars
					$value->content = str_replace("?", "\?", $value->content);

					if($value->type == "page"){
						if($value->prefix == "startwith"){
							$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !^/".$value->content." [NC]\n";
						}

						if($value->prefix == "contain"){
							$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !".$value->content." [NC]\n";
						}

						if($value->prefix == "exact"){
							$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !\/".$value->content." [NC]\n";
						}
					}else if($value->type == "useragent"){
						$htaccess_page_useragent = $htaccess_page_useragent."RewriteCond %{HTTP_USER_AGENT} !".$value->content." [NC]\n";
					}else if($value->type == "cookie"){
						$htaccess_page_cookie = $htaccess_page_cookie."RewriteCond %{HTTP:Cookie} !".$value->content." [NC]\n";
					}
				}
			}
		}*/

		return "# Start WPFC Exclude\n" . $htaccess_page_rules . $htaccess_page_useragent . $htaccess_page_cookie . "# End WPFC Exclude\n";
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
				throw new Exception("<label>.htaccess was not found</label> <a target='_blank' href='http://clearfy.com/docs/htaccess-was-not-found/'>Read More</a>", "server_is_not_support");
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
						throw new Exception("define('WP_CACHE', true); is needed to be added into wp-config.php", "need_constant");
					}
				} else {
					throw new Exception("define('WP_CACHE', true); is needed to be added into wp-config.php", "need_constant");
				}
			}
		}

		$htaccess = @file_get_contents($path . ".htaccess");

		// if(defined('DONOTCACHEPAGE')){
		// 	return array("DONOTCACHEPAGE <label>constant is defined as TRUE. It must be FALSE</label>", "error");
		// }else

		if( !get_option('permalink_structure') ) {
			throw new Exception("You have to set <strong><u><a href='" . admin_url() . "options-permalink.php" . "'>permalinks</a></u></strong>", "need_permalinks_structure");
		} else if( $res = self::checkSuperCache($path, $htaccess) ) {
			return $res;
		} else if( self::isPluginActive('fast-velocity-minify/fvm.php') ) {
			throw new Exception("Fast Velocity Minify needs to be deactivated", "fast_velocity_plugin_needs_deactivated");
		} else if( self::isPluginActive('far-future-expiration/far-future-expiration.php') ) {
			throw new Exception("Far Future Expiration Plugin needs to be deactivated", "future_expiration_plugin_need_be_deactivated");
		} else if( self::isPluginActive('sg-cachepress/sg-cachepress.php') ) {
			throw new Exception("SG Optimizer needs to be deactived", "sg_optimizer_plugin_needs_deactivated");
		} else if( self::isPluginActive('adrotate/adrotate.php') || self::isPluginActive('adrotate-pro/adrotate.php') ) {
			//return self::warningIncompatible("AdRotate");
			throw new Exception("AdRotate needs to be deactived", "adrotate_plugin_needs_deactivated");
		} else if( self::isPluginActive('mobilepress/mobilepress.php') ) {
			/*return self::warningIncompatible("MobilePress", array(
				"name" => "WPtouch Mobile",
				"url" => "https://wordpress.org/plugins/wptouch/"
			));*/
			throw new Exception("MobilePress needs to be deactived", "mobilepress_plugin_need_be_deactivated");
		} else if( self::isPluginActive('speed-booster-pack/speed-booster-pack.php') ) {
			throw new Exception("Speed Booster Pack needs to be deactivated<br>", "speed_booster_pack_plugin_need_be_deactivated");
		} else if( self::isPluginActive('wp-performance-score-booster/wp-performance-score-booster.php') ) {
			throw new Exception("WP Performance Score Booster needs to be deactivated<br>This plugin has aldready Gzip, Leverage Browser Caching features", "wp_performance_score_booster_plugin_need_be_deactivated");
		} else if( self::isPluginActive('bwp-minify/bwp-minify.php') ) {
			throw new Exception("Better WordPress Minify needs to be deactivated<br>This plugin has aldready Minify feature", "error");
		} else if( self::isPluginActive('check-and-enable-gzip-compression/richards-toolbox.php') ) {
			throw new Exception("Check and Enable GZIP compression needs to be deactivated<br>This plugin has aldready Gzip feature", "check_and_enable_gzip_compression_plugin_need_be_deactivated");
		} else if( self::isPluginActive('gzippy/gzippy.php') ) {
			throw new Exception("GZippy needs to be deactivated<br>This plugin has aldready Gzip feature", "gzippy_plugin_need_be_deactivated");
		} else if( self::isPluginActive('gzip-ninja-speed-compression/gzip-ninja-speed.php') ) {
			throw new Exception("GZip Ninja Speed Compression needs to be deactivated<br>This plugin has aldready Gzip feature", "gzip_ninja_speed_compression_plugin_need_be_deactivated");
		} else if( self::isPluginActive('wordpress-gzip-compression/ezgz.php') ) {
			throw new Exception("WordPress Gzip Compression needs to be deactivated<br>This plugin has aldready Gzip feature", "wordpress_gzip_compression_plugin_need_be_deactivated");
		} else if( self::isPluginActive('filosofo-gzip-compression/filosofo-gzip-compression.php') ) {
			throw new Exception("GZIP Output needs to be deactivated<br>This plugin has aldready Gzip feature", "gzip_output_needs_plugin_need_be_deactivated");
		} else if( self::isPluginActive('head-cleaner/head-cleaner.php') ) {
			throw new Exception("Head Cleaner needs to be deactivated", "head_cleaner_plugin_need_be_deactivated");
		} else if( self::isPluginActive('far-future-expiry-header/far-future-expiration.php') ) {
			throw new Exception("Far Future Expiration Plugin needs to be deactivated", "far_future_expiration_plugin_need_be_deactivated");
		} else if( is_writable($path . ".htaccess") ) {
			$htaccess = self::insertLBCRule($htaccess);
			$htaccess = self::insertGzipRule($htaccess);
			$htaccess = self::insertRewriteRule($htaccess);
			$htaccess = self::to_move_gtranslate_rules($htaccess);

			file_put_contents($path . ".htaccess", $htaccess);
		}

		return false;
	}
}