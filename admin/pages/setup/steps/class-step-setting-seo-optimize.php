<?php

namespace WBCR\Clearfy\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Setting_Seo extends \WBCR\FactoryClearfy000\Pages\Step_Form {

	protected $prev_id = 'step3';
	protected $id = 'step4';
	protected $next_id = 'step5';

	public function get_title()
	{
		return "Optimize SEO";
	}

	public function get_form_description()
	{
		return 'Recommended settings that can complement your SEO plugin.';
	}

	public function get_form_options()
	{
		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'content_image_auto_alt',
			'title' => __('Automatically set the alt attribute', 'clearfy'),
			'layout' => array('hint-type' => 'icon'),
			'hint' => __('The alt attribute is mandatory, so most SEO experts say. If you missed or did not fill it at all, it will be automatically assigned and will be equal to the title of the article.', 'clearfy') . '<br><br><b>Clearfy: </b>' . sprintf(__('Replaces the %s, on attribute with an article name %s', 'clearfy'), '<code>img scr="" alt=""</code>', '<code>img scr="" alt="Hello world"</code>'),
			'default' => true
		);

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'set_last_modified_headers',
			'title' => __('Automatically insert the Last Modified header', 'clearfy'),
			'default' => false,
			'layout' => array('hint-type' => 'icon'),
			'hint' => __('Automatically insert the Last Modified header', 'clearfy') . '<br><b>Clearfy: </b>' . __('Removes attachment pages and puts a redirect.', 'clearfy'),
			'eventsOn' => array(
				'show' => '.factory-control-disable_frontpage_last_modified_headers'
			),
			'eventsOff' => array(
				'hide' => '.factory-control-disable_frontpage_last_modified_headers'
			)
		);

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'if_modified_since_headers',
			'title' => __('Return an If-Modified-Since responce', 'clearfy'),
			'layout' => array('hint-type' => 'icon'),
			'hint' => __('Return an If-Modified-Since responce.', 'clearfy') . '<br><b>Clearfy: </b>' . __('Removes attachment pages and puts a redirect.', 'clearfy'),

			'default' => false
		);

		if( $this->plugin->isActivateComponent('yoast_seo') ) {
			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('For the Yoast SEO plugin', 'clearfy') . '</strong>' . '<p>' . __('These settings will help you eliminate some problems associated with the popular Yoast SEO plugin', 'clearfy') . '</p>' . '</div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_last_item_breadcrumb_yoast',
				'title' => __('Remove duplicate names in breadcrumbs WP SEO by Yoast', 'clearfy'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('The last element in the breadcrumbs in the Yoast SEO plugin duplicates the title of the article. Some SEO-specialists consider this duplication to be superfluous.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes duplication of the name in the breadcrumbs of the WP SEO plugin from Yoast.', 'clearfy'),
				'default' => true
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'yoast_remove_image_from_xml_sitemap',
				'title' => sprintf(__('Remove the tag %s from XML site map', 'clearfy'), 'image:image'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'green'),
				'hint' => __('Yandex.Webmaster swears on a standard XML card from the plugin Yoast, tk. it has a specific tag', 'clearfy') . 'image:image<br><br><b>Clearfy: </b>' . sprintf(__('Remove the tag %s from XML site map of the plugin Yoast SEO.', 'clearfy'), 'image:image') . '<br>--<br><span class="wbcr-factory-light-orange-color">' . __('Attention! After activation, turn off the site map and enable it back to regenerate it.', 'clearfy') . '</span>' . '<br><span class="wbcr-factory-light-orange-color">' . __('In older versions of Yoast SEO may not work - update the plugin Yoast', 'clearfy') . '</span>',
				'default' => true,
				'eventsOn' => array()
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'yoast_remove_json_ld_search',
				'title' => __('Disable JSON-LD sitelinks searchbox', 'clearfy') . '</span>',
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('If you’re not familiar with Search Action it’s the mark-up that helps search engines add a shiny Sitelinks Search Box below your search engine results. For the majority of webmasters the extra search box is an absolutely fantastic feature but for many it’s not required or wanted, especially if a site only has a few pages or if the site uses a customised search platform that only searches blog posts and not pages.', 'clearfy') . ' <br><b>Clearfy: </b>' . __('Disable JSON-LD sitelinks searchbox using WordPress in plugin Yoast SEO.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'yoast_remove_json_ld_output',
				'title' => __('Disable Yoast Structured Data', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text"></span>',
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Prevents output of the script tag of type application/ld+json containing
schema.org data from the popular Yoast SEO and Yoast SEO Premium plugins.
There is currently no UI to do so.', 'clearfy') . ' <br><b>Clearfy: </b>' . __('Disable Structured Data in plugin Yoast SEO.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'yoast_remove_head_comment',
				'title' => sprintf(__('Remove comment from %s section', 'clearfy'), 'head'),
				'layout' => array('hint-type' => 'icon'),
				'hint' => sprintf(__('The Yoast SEO plugin displays a comment of the form %s in %s section', 'clearfy'), '!-- This site is optimized with the Yoast SEO plugin v3.1.1 - https://yoast.com/wordpress/plugins/seo/ --', 'head') . '<br><br><b>Clearfy: </b>' . sprintf(__('Removes the Yoast SEO plugin comment of their section %s', 'clearfy'), 'head'),
				'default' => true
			);
		}

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'redirect_archives_date',
			'title' => __('Remove archives date', 'clearfy'),
			'layout' => array('hint-type' => 'icon'),
			'hint' => sprintf(__('Many duplicates in date archives. Imagine, in addition, that your article will be displayed in the main and in the category, you will still receive at least 3 duplicates: in archives by year, month and date, for example %s.', 'clearfy'), '/2016/2016/02 / /2016/02/15') . '<br><b>Clearfy: </b>' . __('Removes all pages with the date archives and puts a redirect.', 'clearfy'),
			'default' => true
		);

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'redirect_archives_author',
			'title' => __('Remove author archives ', 'clearfy'),
			'layout' => array('hint-type' => 'icon'),
			'hint' => sprintf(__('If the site is only filled by you - a mandatory item. Allows you to get rid of duplicates on user archives, for example %s.', 'clearfy'), '/author/admin/') . '<br><b>Clearfy: </b>' . __('Removes all pages with the author archives and puts a redirect.', 'clearfy'),
			'default' => true
		);

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'attachment_pages_redirect',
			'title' => __('Remove attachment pages', 'clearfy'),
			'layout' => array('hint-type' => 'icon'),
			'hint' => __('Every of the pictures has its own page on the site. Such pages are successfully indexed and create duplicates. The site can have thousands of same-type attachment pages.', 'clearfy') . '<br><b>Clearfy: </b>' . __('Removes attachment pages and puts a redirect.', 'clearfy'),
			'default' => true
		);

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'remove_replytocom',
			'title' => __('Remove ?replytocom', 'clearfy'),
			'layout' => array('hint-type' => 'icon'),
			'hint' => sprintf(__('WordPress adds %s to the link "Reply" in the comments, if you use hierarchical comments.', 'clearfy'), '?replytocom') . '<br><b>Clearfy: </b>' . __('?relpytocom remove and and puts a redirect.', 'clearfy'),
			'default' => true
		);

		return $options;
	}
}