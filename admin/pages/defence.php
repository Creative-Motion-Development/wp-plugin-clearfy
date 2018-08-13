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
		
		public $available_for_multisite = true;
		
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

			//todo: убрать этот хук в hide my wp
			//$options = apply_filters('wbcr_clr_defence_form_base_options', $options);

			$form_options = array();
			
			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);
			
			return apply_filters('wbcr_clr_defence_form_options', $form_options, $this);
		}
	}
