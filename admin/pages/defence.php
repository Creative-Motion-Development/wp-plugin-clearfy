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
			
			$this->plugin = $plugin;
		}
		
		/**
		 * Conflict notites
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
		
		public function setWpLoginConflictNotite($plugin_name)
		{
			$this->printWarningNotice(sprintf(__("Мы обнаружили, что вы используете плагин (%s) для изменения адреса страницы wp-login.php, пожалуйста удалите его, так как Clearfy уже содержит эти функции и вам незачем использовать два плагина. Если вы по каким-то причинам не хотите удалять плагин (%s), пожалуйста не используте его функции и функции по измению адреса страницы wp-login.php в плагине Clearfy, чтобы не было конфликтов.", 'clearfy'), $plugin_name, $plugin_name));
		}
		
		public function setWpLoginFunctionContainNotite($plugin_name)
		{
			$this->printWarningNotice(sprintf(__("Мы обнаружили, что вы используете плагин (%s). Пожалуйста не используте его функции по измению адреса страницы wp-login.php и схожие функции в плагине Clearfy, чтобы не было конфликтов.", 'clearfy'), $plugin_name, $plugin_name));
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
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Protect your admin login</strong>.', 'clearfy') . '<p>' . __('Dozens of bots attack your login page at /wp-login.php and / wp-admin / daily. Bruteforce and want to access your admin panel. Even if you\'re sure that you have created a complex and reliable password, this does not guarantee security and does not relieve your login page load. The easiest way is to protect the login page by simply changing its address to your own and unique.', 'clearfy') . '</p></div>'
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
			
			$login_path = WCL_Plugin::app()->getOption('login_path', 'wp-login.php');
			$login_path = $login_path == ''
				? 'wp-login.php'
				: $login_path;
			$login_page_url = home_url() . '/' . $login_path;
			
			$options[] = array(
				'type' => 'textbox',
				'name' => 'login_path',
				'placeholder' => 'secure/auth.php',
				'title' => __('New login page', 'clearfy'),
				'hint' => __('Set a new login page name without slash. Example: mysecretlogin', 'clearfy') . '<br><span style="color:red">' . __("IMPORTANT! Be sure that you wrote down the new login page address", 'clearfy') . '</span>: <b>' . $login_page_url . '</b>',
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
	}
