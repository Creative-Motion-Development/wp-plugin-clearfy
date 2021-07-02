<?php

/**
 * The page Settings.
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCL_ImageOptimizationPage extends WCL_Page {

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 *
	 * @since 1.0.0
	 * @see   FactoryPages000_AdminPage
	 *
	 * @var string
	 */
	public $id = "clearfy_rio";

	/**
	 * @var string
	 */
	public $page_parent_page = 'performance';

	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type = 'page';

	/**
	 * Позиция закладки в меню плагина.
	 * 0 - в самом конце, 100 - в самом начале
	 *
	 * @var int
	 */
	public $page_menu_position = 30;

	/**
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-images-alt2';

	/**
	 * @var bool
	 */
	public $available_for_multisite = true;

	/**
	 * @param WCL_Plugin $plugin
	 */
	public function __construct( WCL_Plugin $plugin ) {
		$this->menu_title                  = __( 'Image optimization', 'clearfy' );
		$this->page_menu_short_description = __( 'Compress bulk of images', 'clearfy' );

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}

	/**
	 * Содержание страницы
	 */
	public function showPageContent() {
		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
		$install_button = $this->plugin->get_install_component_button( 'wordpress', 'robin-image-optimizer/robin-image-optimizer.php' );
		$install_button->add_class( 'wbcr-factory-purchase-premium' );
		?>
        <script>
			jQuery(document).ready(function($) {
				$.wfactory_000.hooks.add('core/components/updated', function(button, component_name) {
					if( component_name.plugin_action === 'install' ) {
						button.removeClass('wbcr-factory-purchase-premium');
						button.addClass('wbcr-factory-activate-premium');
					}

					if( component_name.plugin_action === 'activate' ) {
						button.remove();
						window.location.href = '<?= $this->getBaseUrl( 'rio_general' ); ?>';
					}
				});
			});
        </script>
        <div class="wbcr-factory-templates-000-multisite-suggetion">
            <div class="wbcr-factory-inner-contanier">
                <h3><?php _e( 'Install Robin Image Optimizer component', 'clearfy' ) ?></h3>
                <p><?php _e( 'To start optimizing images, you need to install the additional component  Robin image optimizer!', 'clearfy' ) ?></p>
                <p><?php _e( 'Installing the component will not take you long, just click the install button, then	activate.', 'clearfy' ) ?></p>
                <p style="margin-top:20px">
					<?php $install_button->render_link(); ?>
                </p>
            </div>
        </div>
		<?php
	}
}
