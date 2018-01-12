<?php

	/**
	 * This file is the add-ons page.
	 *
	 * @author Alex Kovalev <alex@byonepress.com>
	 * @copyright (c) 2017, OnePress Ltd
	 *
	 * @since 1.0.0
	 */
	class WbcrClr_ComponentsPage extends FactoryPages000_ImpressiveThemplate {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "components";

		public $internal = false;

		public $page_menu_dashicon = 'dashicons-admin-plugins';

		public $type = 'page';

		public function __construct(Factory000_Plugin $plugin)
		{
			$this->menuTitle = __('Components', 'clearfy');

			add_filter('factory_impressive_page_bottom_sidebar_widgets', function ($page_id, $widgets) {
				//if( $page_id == "components" ) {
				//unset($widgets['donate_widget']);
				//}

				$widgets['rating_widget'] = $this->getRatingWidget('http://yandex.ru');

				return $widgets;
			}, 10, 2);

			parent::__construct($plugin);
		}

		/**
		 * Requests assets (js and css) for the page.
		 *
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function assets($scripts, $styles)
		{
			parent::assets($scripts, $styles);

			$this->styles->add(WBCR_CLR_PLUGIN_URL . '/admin/assets/css/components.css');
		}

		public function showPageContent()
		{
			$components = new FactoryAddons000();
			$response = $components->getPluginAddons('sociallocker');
			?>
			<div class="wbcr-clearfy-components">
				<?php foreach($response as $addon): ?>
					<div class="plugin-card plugin-card-akismet">
						<div class="plugin-card-top">
							<div class="name column-name">
								<h3>
									<a href="<?= $addon['url'] ?>" class="thickbox open-plugin-details-modal">
										<?= $addon['title'] ?>
										<img src="https://ps.w.org/akismet/assets/icon-256x256.png?rev=969272" class="plugin-icon" alt="">
									</a>
								</h3>
							</div>
							<div class="desc column-description">
								<p><?= $components->excerpt($addon['description'], 16); ?></p>
							</div>
						</div>
						<div class="plugin-card-bottom">
							<div class="vers column-rating">
								<div class="star-rating">
									<span class="screen-reader-text">5.0 rating based on 796 ratings</span>

									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
								</div>
								<span class="num-ratings" aria-hidden="true">(796)</span>
							</div>
							<!--<div class="column-updated">
								<strong>Last Updated:</strong> 2 weeks ago
							</div>
							<div class="column-downloaded">
								1+ Million Active Installs
							</div>
							<div class="column-compatibility">
								<span class="compatibility-compatible"><strong>Compatible</strong> with your version of WordPress</span>
							</div>-->
							<a class="install-now button" data-slug="akismet" href="http://testwp.dev/wp-admin/update.php?action=install-plugin&amp;plugin=akismet&amp;_wpnonce=64668827e5" aria-label="Install Akismet 4.0 now" data-name="Akismet 4.0">Install
								Now</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php
		}
	}

	FactoryPages000::register($wbcr_clearfy_plugin, 'WbcrClr_ComponentsPage');
