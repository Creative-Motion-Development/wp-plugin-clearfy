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

	class WCL_PerformancePage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "performance";

		/**
		 * @var string
		 */
		public $page_menu_dashicon = 'dashicons-performance';

		/**
		 * @var int
		 */
		public $page_menu_position = 20;

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Performance', 'clearfy');

			parent::__construct($plugin);

			$this->plugin = $plugin;

			add_action('wbcr_factory_000_imppage_flush_cache', array($this, 'afterSave'), 10);
		}

		public function afterSave($plugin_name)
		{
			if( $plugin_name == WCL_Plugin::app()->getPluginName() ) {
				if( $this->getOption('disable_gravatars') ) {
					update_option('show_avatars', false);
				} else {
					update_option('show_avatars', true);
				}
			}
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
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Очистить код от лишних скриптов</strong>.', 'clearfy') . '<p>' . __('Этот набор настроек поможет вам убрать лишние ссылки и код из секции head, а так же уменьшить вес страниц вашего сайта.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_feed',
				'title' => __('Disable RSS feeds', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('By default, WordPress generates all types of different RSS feeds for your site. While RSS feeds can be useful if you are running a blog, businesses might not always utilize these. Not every site out there has a blog.', 'clearfy') . '<br><b>Clearfy: </b>' . sprintf(__('Removes a link to the RSS-feed from the %s section, closes and puts the redirect from all RSS-feeds.', 'clearfy'), '&lt;head&gt;'),
				'default' => false,
				'eventsOn' => array(
					'show' => '.factory-control-disabled_feed_behaviour'
				),
				'eventsOff' => array(
					'hide' => '.factory-control-disabled_feed_behaviour'
				)
			);

			$options[] = array(
				'type' => 'dropdown',
				'way' => 'buttons',
				'name' => 'disabled_feed_behaviour',
				'data' => array(
					array('redirect_301', __('Redirect 301', 'clearfy')),
					array('redirect_404', __('Page 404', 'clearfy')),
				),
				'title' => __('Redirect feed requests', 'clearfy'),
				'hint' => __('Перенаправлять все запросы на страницу 404 или на главную страницу через 301 редирект.', 'clearfy'),
				//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => 'redirect_301'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_json_rest_api',
				'title' => __('Remove REST API Links', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'red'),
				'hint' => __('The WordPress REST API provides API endpoints for WordPress data types that allow developers to interact with sites remotely by sending and receiving JSON (JavaScript Object Notation) objects. However, a lot of sites don’t use this, and therefore in most cases, it is just unnecessary code.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes REST API link tag from the front end and the REST API header link from page requests.', 'clearfy'),
				'default' => false,
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_emoji',
				'title' => __('Disable Emojis', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('Emojis are fun and all, but if you are aren’t using them they actually load a JavaScript file (wp-emoji-release.min.js) on every page of your website. For a lot of businesses, this is not needed and simply adds load time to your site. So we recommend disabling this.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes WordPress Emojis JavaScript file (wp-emoji-release.min.js).', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_jquery_migrate',
				'title' => __('Remove jQuery Migrate', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'red'),
				'hint' => __('They started adding jQuery migrate in WordPress 3.6. Most up-to-date frontend code and plugins don’t require jquery-migrate.min.js. In most cases, this simply adds unnecessary load to your site. You can see this running if you launch Chrome Devtools console.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes jQuery Migrate JavaScript file (jquery-migrate.min.js).', 'clearfy') . '<br>--<br><span class="hint-warnign-color">' . __('Warning! If there is a broke on your site, disable this option!', 'clearfy') . '</span>',
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_embeds',
				'title' => __('Disable Embeds', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Embeds were released with WordPress 4.4. This is basically the magic that auto converts your YouTube videos, Tweets, and URLs into pretty previews while you are editing. However, this actually loads a JavaScript file (wp-embed.min.js) on every page of your website. If you don’t care about the auto converting preview (which we don’t), you can disable this across your site.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes WordPress Embed JavaScript file (wp-embed.min.js)', 'clearfy'),
				'default' => false
			);

			/*$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_dns_prefetch',
				'title' => __('Remove dns-prefetch', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => sprintf(__('Since version 4.6.1 in WordPress there are new links in the section %s this type of: ', 'clearfy'), 'head') . ' <code>link rel="dns-prefetch" href="//s.w.org"</code><br><br><b>Clearfy: </b>' . sprintf(__('Removes dns-prefetch links from the %s section', 'clearfy'), 'head'),
				'default' => false
			);*/
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_rsd_link',
				'title' => __('Remove RSD Link', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('The above link is used by blog clients. If you edit your site from your browser then you don’t need this. It is also used by some 3rd party applications that utilize XML-RPC requests. In most cases, this is just unnecessary code.', 'clearfy') . '<br><code>link rel="EditURI" type="application/rsd+xml" title="RSD"</code><br><br><b>Clearfy: </b>' . __('Remove RSD (Real Simple Discovery) link tag.', 'clearfy'),
				'default' => false
			);
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_xfn_link',
				'title' => __('Removing XFN (XHTML Friends Network) Profile Link', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Атрибут profile указывает адрес профиля метаданных. Обычно браузер распознаёт значение этого атрибута и выполняет некоторые соглашения, связанные с указанным профилем. Загрузки самого документа по указанному адресу в реальности не происходит, более того, его может вообще не быть.
В частности, profile используется для микроформата XFN (XHTML Friends Network) — это способ представления отношений между людьми с помощью ссылок и атрибутов rel с разными значениями. WordPress также активно применяет profile в своих шаблонах.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Remove link tag', 'clearfy') . '<br><code>link rel="profile" href="http://gmpg.org/xfn/11"</code>',
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_wlw_link',
				'title' => __('Remove wlwmanifest Link', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => '<code>link rel="wlwmanifest" type="application/wlwmanifest+xml"</code><br>' . __('This link is actually used by Windows Live Writer. If you don’t know use Windows Live Writer, which we are guessing you don’t, this is just unnecessary code.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Remove wlwmanifest (Windows Live Writer) link tag.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_shortlink_link',
				'title' => __('Remove Shortlink', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => sprintf(__('By default, the following tag shows up in every WordPress install. %s This is used for a shortlink to your pages and posts. However, if you are already using pretty permalinks, such as domain.com/post, then there is no reason to keep this, it is just unnecessary code.', 'clearfy'), '<br><code>link rel="shortlink" href="https://domain.com?p=712"</code><br>') . '<br><br><b>Clearfy: </b>' . __('Remove Shortlink link tag.', 'clearfy'),
				'default' => false
			);
			
			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_adjacent_posts_link',
				'title' => __('Remove links to previous, next post', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('If you use Wordpress as a CMS, then you can delete these links, they can only come in handy for a blog.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Remove the previous and next post links within the wp_head of your wordpress theme.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_recent_comments_style',
				'title' => __('Remove .recentcomments styles', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('WP by default for the widget "recent comments" prescribes in the code styles that are almost impossible to change, because to them apply! important.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes .recentcomments styles from head section.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'lazy_load_font_awesome',
				'title' => __('Font Awesome asynchronous', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('По умолчанию Wordpress загружает Font Awesome иконки синхронно, то есть ваша страница не будет полностью загружена, пока не будут загружены Font Awesome иконки. Такой алгоритм работы замедляет загрузку вашей страницы и создает ошибки в google page speed. Используя эту опцию, ваши Font Awesome иконки будут загружаться после полной загрузки вашей страницы, но есть и минусы этой функции, вы будете визуально видеть подмену иконок по умолчанию, на ваш подгружаемый шрифт.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_dashicons',
				'title' => __('Disable Dashicons', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Dashicons is the official icon font of the WordPress admin as of 3.8. Some of you have requested that we add a feature to remove Dashicons. Some themes and developers utilize this (dashicons.min.css) on the front-end of their sites.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_gravatars',
				'title' => __('Disable gravatars', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('This function that prevents the user’s gravatar being automatically obtained from gravatar.com based on their registered email. This would be useful for sites where users require an extra layer of privacy, or if you just want to prevent potentially silly or embarrasing avatar accidents.
			If you’re using Identicons or any other generated default avatar, the user should keep a consistent avatar unless they change their registered email.
			', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'html_minify',
				'title' => __('HTML minify', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Reduces the weight of the page by removing line breaks, tabs, spaces, etc.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Minify pages.', 'clearfy'),
				'default' => false
			);

			$form_options = array();

			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_code_clean_form_options', $form_options, $this);
		}
	}