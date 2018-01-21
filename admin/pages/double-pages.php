<?php

	/**
	 * The page Settings.
	 *
	 * @since 1.0.0
	 */
	class WbcrClr_DoublePagesPage extends WbcrClr_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "double_pages";

		public $page_menu_dashicon = 'dashicons-admin-page';

		public $page_menu_position = 16;

		public function __construct(Factory000_Plugin $plugin)
		{
			$this->menuTitle = __('Duplicate pages', 'clearfy');

			parent::__construct($plugin);
		}

		/**
		 * Shows the description above the options.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		/*public function _showHeader()
		{
			?>
			<div class="wbcr-clearfy-header">
				<?php _e('This page contains settings for setting up duplicate pages.', 'clearfy') ?>
			</div>
		<?php
		}*/

		/**
		 * Permalinks options.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getOptions()
		{
			$options = array();

			/*$options[] = array(
				'type' => 'html',
				'html' => array($this, '_showHeader')
			);*/

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'redirect_archives_date',
				'title' => __('Remove archives date', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => sprintf(__('Many duplicates in date archives. Imagine, in addition, that your article will be displayed in the main and in the category, you will still receive at least 3 duplicates: in archives by year, month and date, for example %s.', 'clearfy'), '/2016/2016/02 / /2016/02/15') . '<br><b>Clearfy: </b>' . __('Removes all pages with the date archives and puts a redirect.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'redirect_archives_author',
				'title' => __('Remove author archives ', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => sprintf(__('If the site is only filled by you - a mandatory item. Allows you to get rid of duplicates on user archives, for example %s.', 'clearfy'), '/author/admin/') . '<br><b>Clearfy: </b>' . __('Removes all pages with the author archives and puts a redirect.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'redirect_archives_tag',
				'title' => __('Remove archives tag', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('If you use tags only for the block Similar records, or do not use them at all - it will be more correct to close them to avoid duplicates.', 'clearfy') . '<br><b>Clearfy: </b>' . __('Removes all pages with the tag archives and puts a redirect.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'attachment_pages_redirect',
				'title' => __('Remove attachment pages', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('Every of the pictures has its own page on the site. Such pages are successfully indexed and create duplicates. The site can have thousands of same-type attachment pages.', 'clearfy') . '<br><b>Clearfy: </b>' . __('Removes attachment pages and puts a redirect.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_single_pagination_duplicate',
				'title' => __('Remove post pagination', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => sprintf(__('In WordPress, any post can be divided into parts (pages), each part will have its own address. But this functionality is rarely used, but it can create trouble for you. For example, you can add a number to the address of any entry of your blog, %s - the post itself will open, which will be a duplicate. You can substitute any number.', 'clearfy'), '/privet-mir/1/') . '<br><b>Clearfy: </b>' . sprintf(__('Removes the pagination from the post and puts a redirect. Example: %s', 'clearfy'), '/post-name/number'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_replytocom',
				'title' => __('Remove ?replytocom', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => sprintf(__('WordPress adds %s to the link "Reply" in the comments, if you use hierarchical comments.', 'clearfy'), '?replytocom') . '<br><b>Clearfy: </b>' . __('?relpytocom remove and and puts a redirect.', 'clearfy'),
				'default' => false
			);

			/*$options[] = array(
				'type' => 'separator',
				'cssClass' => 'factory-separator-dashed'
			);

			$options[] = array(
				'type' => 'html',
				'html' => array($this, '_showFormButton')
			);*/

			$form_options = array();

			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_double_form_options', $form_options, $this);
		}
	}

	FactoryPages000::register($wbcr_clearfy_plugin, 'WbcrClr_DoublePagesPage');
