<?php
	/**
	 * This class configures security settings
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_ConfigSecurity extends Wbcr_FactoryClearfy000_Configurate {

		/**
		 * @var bool
		 */
		private $wp_login_php;

		/**
		 * @var bool
		 */
		private $disable_wp_admin;

		/**
		 * @var bool
		 */
		private $disable_wp_login;

		/**
		 * @var string
		 */
		private $login_path;

		/**
		 * @var
		 */
		private $login_recovery;

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		public function registerActionsAndFilters()
		{
			if( !is_admin() ) {
				if( $this->getOption('change_login_errors') ) {
					add_filter('login_errors', array($this, 'changeLoginErrors'));
				}

				if( $this->getOption('protect_author_get') ) {
					add_action('wp', array($this, 'protectAuthorGet'));
				}
			}

			$this->disable_wp_admin = WCL_Plugin::app()->getOption('hide_wp_admin');
			$this->disable_wp_login = WCL_Plugin::app()->getOption('hide_login_path');
			$this->login_path = WCL_Plugin::app()->getOption('login_path');

			add_filter('init', array($this, 'init'));

			if( $this->disable_wp_admin ) {
				add_filter('auth_redirect_scheme', array($this, 'stopRedirect'), 9999);
			}

			if( $this->login_path ) {
				add_action('plugins_loaded', array($this, 'pluginsLoaded'), 9999);
				add_action('wp_loaded', array($this, 'wpLoaded'));
				add_filter('site_url', array($this, 'siteUrl'), 10, 4);
				add_filter('wp_redirect', array($this, 'wpRedirect'), 10, 2);
				add_filter('site_option_welcome_email', array($this, 'welcomeEmail'));
			}
		}

		public function init()
		{
			if( $this->disable_wp_admin ) {
				remove_action('template_redirect', 'wp_redirect_admin_locations', 9999);
			}

			//check for recovery link run
			if( !empty($this->login_path) && isset($_GET['wbcr_clearfy_login_recovery']) ) {
				$user_recovery_code = sanitize_text_field($_GET['wbcr_clearfy_login_recovery']);
				$plugin_recovery_code = $this->getOption('login_recovery_code');

				if( empty($plugin_recovery_code) || empty($user_recovery_code) || $user_recovery_code !== $plugin_recovery_code ) {
					return;
				}

				$this->plugin->deleteOption('hide_wp_admin');
				$this->plugin->deleteOption('login_path');
				$this->plugin->deleteOption('hide_login_path');
				$this->plugin->deleteOption('old_login_path');
				$this->plugin->deleteOption('login_recovery_code');

				$this->login_path = null;
				$this->disable_wp_login = null;
				$this->disable_wp_admin = null;
				$this->wp_login_php = false;

				wp_safe_redirect(admin_url());
				exit;
			}
		}


		function stopRedirect($scheme)
		{
			if( $user_id = wp_validate_auth_cookie('', $scheme) ) {
				return $scheme;
			}

			WbcrFactoryClearfy000_Helpers::setError404();
		}

		public function pluginsLoaded()
		{
			global $pagenow;

			$request = parse_url($_SERVER['REQUEST_URI']);

			$is_login = WbcrFactoryClearfy000_Helpers::strContains(rawurldecode($_SERVER['REQUEST_URI']), 'wp-login.php') || untrailingslashit($request['path']) === site_url('wp-login', 'relative');
			$is_signup = WbcrFactoryClearfy000_Helpers::strContains(rawurldecode($_SERVER['REQUEST_URI']), 'wp-signup');
			$is_activate = WbcrFactoryClearfy000_Helpers::strContains(rawurldecode($_SERVER['REQUEST_URI']), 'wp-activate');

			if( ($is_login || $is_signup || $is_activate) && !is_admin() ) {
				$this->wp_login_php = true;
				$pagenow = 'index.php';
			} elseif( (untrailingslashit($request['path']) === home_url($this->login_path, 'relative')) || (!get_option('permalink_structure') && isset($_GET[$this->login_path]) && empty($_GET[$this->login_path])) ) {
				$pagenow = 'wp-login.php';
			}
		}

		public function wpLoaded()
		{
			global $pagenow;

			if( is_admin() && !is_user_logged_in() && !defined('DOING_AJAX') && $pagenow !== 'admin-post.php' ) {
				$ddisable_wp_admin = WCL_Plugin::app()->getOption('hide_wp_admin');

				if( !$ddisable_wp_admin ) {
					$redirect_uri = untrailingslashit(home_url($this->login_path));

					if( !get_option('permalink_structure') ) {
						$redirect_uri = add_query_arg(array(
							$this->login_path => ''
						), home_url());
					}

					wp_safe_redirect($redirect_uri);
					die();
				}

				return;
			}

			$request = parse_url($_SERVER['REQUEST_URI']);

			if( $pagenow === 'wp-login.php' && $request['path'] !== WbcrFactoryClearfy000_Helpers::userTrailingslashit($request['path']) && get_option('permalink_structure') ) {
				$query_string = !empty($_SERVER['QUERY_STRING'])
					? '?' . $_SERVER['QUERY_STRING']
					: '';

				wp_safe_redirect(WbcrFactoryClearfy000_Helpers::userTrailingslashit($this->login_path) . $query_string);
				die();
			} elseif( $this->wp_login_php ) {
				$new_login_redirect = false;
				$referer = wp_get_referer();
				$parse_referer = parse_url($referer);

				if( $referer && WbcrFactoryClearfy000_Helpers::strContains($referer, 'wp-activate.php') && $parse_referer && !empty($parse_referer['query']) ) {

					parse_str($parse_referer['query'], $parse_referer);

					if( !empty($parse_referer['key']) && ($result = wpmu_activate_signup($parse_referer['key'])) && is_wp_error($result) && ($result->get_error_code() === 'already_active' || $result->get_error_code() === 'blog_taken') ) {
						$new_login_redirect = true;
					}
				}

				if( !$this->disable_wp_login || $new_login_redirect ) {
					$query_string = !empty($_SERVER['QUERY_STRING'])
						? '?' . $_SERVER['QUERY_STRING']
						: '';

					if( WCL_Helper::isPermalink() ) {
						$redirect_uri = $this->login_path . $query_string;
					} else {
						$redirect_uri = home_url() . '/' . add_query_arg(array(
								$this->login_path => ''
							), $query_string);
					}

					if( WbcrFactoryClearfy000_Helpers::strContains($_SERVER['REQUEST_URI'], 'wp-signup') ) {
						$redirect_uri = add_query_arg(array(
							'action' => 'register'
						), $redirect_uri);
					}

					wp_safe_redirect($redirect_uri);
					die();
				}

				WbcrFactoryClearfy000_Helpers::setError404();
			} elseif( $pagenow === 'wp-login.php' ) {
				if( is_user_logged_in() && !isset($_REQUEST['action']) ) {
					wp_safe_redirect(admin_url());
					die();
				}

				if( !defined('DONOTCACHEPAGE') ) {
					define('DONOTCACHEPAGE', true);
				}

				@require_once ABSPATH . 'wp-login.php';

				die();
			}
		}

		public function siteUrl($url, $path, $scheme, $blog_id)
		{
			return $this->filterWpLoginPhp($url, $scheme);
		}

		public function wpRedirect($location, $status)
		{
			return $this->filterWpLoginPhp($location);
		}

		public function filterWpLoginPhp($url, $scheme = null)
		{
			if( strpos($url, 'wp-login.php') !== false ) {
				if( is_ssl() ) {
					$scheme = 'https';
				}

				$args = explode('?', $url);

				if( isset($args[1]) ) {
					parse_str($args[1], $args);
					$url = add_query_arg($args, $this->newLoginUrl($scheme));
				} else {
					$url = $this->newLoginUrl($scheme);
				}
			}

			return $url;
		}

		public function welcomeEmail($value)
		{
			return $value = str_replace('wp-login.php', WbcrFactoryClearfy000_Helpers::userTrailingslashit($this->login_path), $value);
		}

		public function newLoginUrl($scheme = null)
		{
			if( WCL_Helper::isPermalink() ) {
				return WbcrFactoryClearfy000_Helpers::userTrailingslashit(home_url('/', $scheme) . $this->login_path);
			} else {
				return home_url('/', $scheme) . '?' . $this->login_path;
			}
		}

		/**
		 * Change login error message
		 *
		 * @return string
		 */

		public function changeLoginErrors()
		{
			return __('<strong>ERROR</strong>: Wrong login or password', 'clearfy');
		}

		/**
		 * Protect author get
		 */

		public function protectAuthorGet()
		{
			if( isset($_GET['author']) ) {
				wp_redirect(home_url(), 301);

				die();
			}
		}
	}