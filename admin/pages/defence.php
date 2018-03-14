<?php
	
	/**
	 * The page Settings.
	 *
	 * @since 1.0.0
	 */
	
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	class WCL_DefencePage extends WCL_Page {
		
		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "defence";
		
		public $page_menu_dashicon = 'dashicons-shield-alt';
		
		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Defence', 'clearfy');
			
			parent::__construct($plugin);

			add_filter('wp_unique_post_slug', array($this, 'loginPathNoConflict'), 10, 4);

			$this->plugin = $plugin;
		}
		
		/**
		 * Уведомления о несовместимости плагинов
		 */
		public function warningNotice()
		{
			if( is_plugin_active('hide-my-wp/index.php') ) {
				$this->setWpLoginConflictNotite('Hide My WP');
			}
			if( is_plugin_active('clearfy/hide-my-wp.php') ) {
				$this->setWpLoginFunctionContainNotite('Hide My WP');
			}
			if( is_plugin_active('rename-wp-login/rename-wp-login.php') ) {
				$this->setWpLoginConflictNotite('Rename wp-login.php');
			}
			if( is_plugin_active('wps-hide-login/wps-hide-login.php') ) {
				$this->setWpLoginConflictNotite('WPS Hide Login');
			}
			if( is_plugin_active('wp-cerber/wp-cerber.php') ) {
				$this->setWpLoginFunctionContainNotite('WP Cerber Security & Antispam');
			}
			if( is_plugin_active('all-in-one-wp-security-and-firewall/wp-security.php') ) {
				$this->setWpLoginFunctionContainNotite('All In One WP Security');
			}
			if( is_plugin_active('wp-hide-security-enhancer/wp-hide.php') ) {
				$this->setWpLoginFunctionContainNotite('WP Hide & Security Enhancer');
			}
		}

		/**
		 * Показывает уведомление о полной несовместимости плагинов
		 * @param string $plugin_name
		 */
		public function setWpLoginConflictNotite($plugin_name)
		{
			$this->printWarningNotice(sprintf(__("We found that you are use the (%s) plugin to change wp-login.php page address. Please delete it, because Clearfy already contains these functions and you do not need to use two plugins. If you do not want to remove (%s) plugin for some reason, please do not use wp-login.php page address change feature in the Clearfy plugin, to avoid conflicts.", 'clearfy'), $plugin_name, $plugin_name));
		}

		/**
		 * Показывает частичную несовместимость с плагином
		 * @param string $plugin_name
		 */
		public function setWpLoginFunctionContainNotite($plugin_name)
		{
			$this->printWarningNotice(sprintf(__("We found that you are use the (%s) plugin. Please do not use its wp-login.php page address change and the same feature in the Clearfy plugin, to avoid conflicts.", 'clearfy'), $plugin_name, $plugin_name));
		}

		/**
		 * Предотвращаем попытки убить доступ к админ панели
		 */
		protected function afterFormSave()
		{
			$login_path = $this->plugin->getOption('login_path');
			$valid_path = !is_numeric($login_path) && preg_match('/^[0-9A-z_-]{3,}$/', $login_path);

			if( !empty($login_path) ) {
				if( !$valid_path ) {
					$this->deleteLoginNewPath();

					$this->redirectToAction('index', array('wbcr_clr_login_path_incorrect' => 1));
				}

				$args = array(
					'name' => $login_path,
					'post_type' => array('page', 'post', 'attachment'),
					'numberposts' => 1
				);

				$posts = get_posts($args);

				if( !empty($posts) ) {
					$this->deleteLoginNewPath();

					$this->redirectToAction('index', array('wbcr_clr_login_path_exists' => 1));
				}

				$old_login_path = $this->plugin->getOption('old_login_path');

				if( !$old_login_path || $login_path != $old_login_path ) {

					$recovery_code = md5(rand(1, 9999) . microtime());

					$body = __("Hi,\nThis is %s plugin. Here is your new WordPress login address:\nURL: %s", 'clearfy') . PHP_EOL . PHP_EOL;
					$body .= __("IMPORTANT! Be sure that you wrote down the new login page address", 'clearfy') . '!' . PHP_EOL . PHP_EOL;
					$body .= __("If unable to access the login/admin section anymore, use the Recovery Link which reset links to default: \n%s", 'clearfy') . PHP_EOL . PHP_EOL;
					$body .= __("Best Regards,\n%s", 'clearfy') . PHP_EOL;

					$new_url = site_url('wp-login.php');

					$body = sprintf($body, WCL_Plugin::app()
							->getPluginTitle(), $new_url, $this->getRecoveryUrl($recovery_code), WCL_Plugin::app()
							->getPluginTitle()) . PHP_EOL;

					$subject = sprintf(__('[%s] Your New WP Login!', 'clearfy'), WCL_Plugin::app()->getPluginTitle());

					wp_mail(get_option('admin_email'), $subject, $body);

					$this->plugin->updateOption('old_login_path', $login_path);
					$this->plugin->updateOption('login_recovery_code', $recovery_code);
				}
			}
		}

		public function deleteLoginNewPath()
		{
			$this->plugin->deleteOption('login_path');
			$this->plugin->deleteOption('hide_login_path');
		}

		public function loginPathNoConflict($slug, $post_ID, $post_status, $post_type)
		{
			if( in_array($post_type, array('post', 'page', 'attachment')) ) {
				$login_path = $this->plugin->getOption('login_path');

				if( !empty($login_path) ) {
					if( $slug == trim($login_path) ) {
						$slug = $slug . rand(10, 99);
					}
				}
			}

			return $slug;
		}

		/**
		 * We register notifications for some actions
		 *
		 * @see libs\factory\pages\themplates\FactoryPages000_ImpressiveThemplate
		 * @param $notices
		 * @param Wbcr_Factory000_Plugin $plugin
		 * @return array
		 */
		public function getActionNotices($notices)
		{
			$notices[] = array(
				'conditions' => array(
					'wbcr_clr_login_path_incorrect' => 1,
				),
				'type' => 'danger',
				'message' => __('You entered an incorrect part of the path to your login page. The path to the login page can not consist only of digits, at least 3 characters, you must use only the characters [0-9A-z_-]!', 'clearfy')
			);
			$notices[] = array(
				'conditions' => array(
					'wbcr_clr_login_path_exists' => 1,
				),
				'type' => 'danger',
				'message' => __('The entered login page name is already used for one of your pages. Try to choose a different login page name!', 'clearfy')
			);

			return $notices;
		}

		/**
		 * Permalinks options.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getOptions()
		{
			$options = array();
			
			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Base settings</strong>.', 'clearfy') . '<p>' . __('Basic recommended security settings.', 'clearfy') . '</p></div>'
			);
			
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'protect_author_get',
				'title' => __('Hide author login', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('An attacker can find out the author\'s login, using a similar request to get your site. mysite.com/?author=1', 'clearfy') . '<br><b>Clearfy: </b>' . __('Sets the redirect to exclude the possibility of obtaining a login.', 'clearfy'),
				'default' => false
			);
			
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'change_login_errors',
				'title' => __('Hide errors when logging into the site', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('WP by default shows whether you entered a wrong login or incorrect password, which allows attackers to understand if there is a certain user on the site, and then start searching through the passwords.', 'clearfy') . '<br><b>Clearfy: </b>' . __('Changes in the text of the error so that attackers could not find the login.', 'clearfy'),
				'default' => false
			);
			
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_x_pingback',
				'title' => __('Disable XML-RPC', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('A pingback is basically an automated comment that gets created when another blog links to you. A self-pingback is created when you link to an article within your own blog. Pingbacks are essentially nothing more than spam and simply waste resources.', 'clearfy') . '<br><b>Clearfy: </b>' . __('Removes the server responses a reference to the xmlrpc file.', 'clearfy'),
				'default' => false
			);
			
			$options = apply_filters('wbcr_clr_defence_form_base_options', $options);
			
			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Protect your admin login</strong>.', 'clearfy') . '<p>' . __('Dozens of bots attack your login page at /wp-login.php and /wp-admin/daily. Bruteforce and want to access your admin panel. Even if you\'re sure that you have created a complex and reliable password, this does not guarantee security and does not relieve your login page load. The easiest way is to protect the login page by simply changing its address to your own and unique.', 'clearfy') . '</p></div>'
			);
			
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'hide_wp_admin',
				'title' => __('Hide wp-admin', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __("Hides the /wp-admin directory for unauthorized users. If the option is disabled, when you request the page /wp-admin you will be redirected to the login page, even if you changed its address. Therefore, for protection purposes enable this option.", 'clearfy')
			);
			
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'hide_login_path',
				'title' => __('Hide Login Page', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'red'),
				'hint' => __("Hides the wp-login.php and wp-signup.php pages.", 'clearfy') . '<br>--<br><span class="hint-warnign-color">' . __("Use this option carefully! If you forget the new login page address, you can not get into the admin panel.", 'clearfy') . '</span>'
			);

			$recovery_url = $this->getRecoveryUrl();
			$recovery_url = !empty($recovery_url)
				? '<br><br>' . sprintf(__("If unable to access the login/admin section anymore, use the Recovery Link which reset links to default: \n%s", 'clearfy'), $recovery_url)
				: '';
			$new_login_url = $this->getNewLoginUrl();

			$options[] = array(
				'type' => 'textbox',
				'name' => 'login_path',
				'placeholder' => 'secure/auth.php',
				'title' => __('New login page', 'clearfy'),
				'hint' => __('Set a new login page name without slash. Example: mysecretlogin', 'clearfy') . '<br><span style="color:red">' . __("IMPORTANT! Be sure that you wrote down the new login page address", 'clearfy') . '</span>: <b><a href="' . $new_login_url . '" target="_blank">' . $new_login_url . '</a></b>' . $recovery_url,
				//'units' => '<i class="fa fa-unlock" title="' . __('This option will protect your blog against unauthorized access.', 'clearfy') . '"></i>',
				//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'red')
			);
			
			//$options = apply_filters('wbcr_clr_defence_form_login_options', $options);
			
			$form_options = array();
			
			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);
			
			return apply_filters('wbcr_clr_defence_form_options', $form_options, $this);
		}

		/**
		 * @return string
		 */
		public function getNewLoginUrl()
		{
			$login_path = WCL_Plugin::app()->getOption('login_path');

			if( empty($login_path) ) {
				return home_url('/') . 'wp-login.php';
			}

			if( WCL_Helper::isPermalink() ) {
				return WbcrFactoryClearfy000_Helpers::userTrailingslashit(home_url('/') . $login_path);
			} else {
				return home_url('/') . '?' . $login_path;
			}
		}

		/**
		 * @param null $recovery_code
		 * @return string|void
		 */
		public function getRecoveryUrl($recovery_code = null)
		{
			$recovery_code = empty($recovery_code)
				? $this->getOption('login_recovery_code')
				: $recovery_code;

			if( empty($recovery_code) ) {
				return '';
			}

			return home_url('/?wbcr_clearfy_login_recovery=' . $recovery_code);
		}
	}
