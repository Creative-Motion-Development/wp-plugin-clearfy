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

class WCL_TitanSecurityPage extends WCL_Page {

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
	public $id = "clearfy_titan";

	/**
	 * @var string
	 */
	public $page_parent_page = 'defence';

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
	public $page_menu_dashicon = 'dashicons-shield-alt';

	/**
	 * @var bool
	 */
	public $available_for_multisite = true;

	/**
	 * @param WCL_Plugin $plugin
	 */
	public function __construct(WCL_Plugin $plugin)
	{
		$this->menu_title = __('Firewall and Malware scanner', 'clearfy');
		$this->page_menu_short_description = __('Firewall and Anti-virus', 'clearfy');

		parent::__construct($plugin);

		$this->plugin = $plugin;
	}

	/**
	 * Содержание страницы
	 */
	public function showPageContent()
	{
		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
		$install_button = new WCL_InstallPluginsButton('wordpress', 'anti-spam/anti-spam.php');
		$install_button->addClass('wbcr-factory-purchase-premium');
		?>
		<script>
			jQuery(document).ready(function($) {
				$.wbcr_factory_clearfy_000.hooks.add('clearfy/components/updated', function(button, component_name) {
					if( component_name.plugin_action == 'install' ) {
						button.removeClass('wbcr-factory-purchase-premium');
						button.addClass('wbcr-factory-activate-premium');
					}

					if( component_name.plugin_action == 'activate' ) {
						button.remove();
						window.location.href = '<?= admin_url('admin.php?page=dashboard-titan_security'); ?>';
					}
				});
			});
		</script>
		<div class="wbcr-factory-clearfy-000-multisite-suggetion">
			<div class="wbcr-factory-inner-contanier">
				<h3><?php _e('Install Firewall and Malware scanner (Titan sucurity) component', 'clearfy') ?></h3>
				<p><?php _e('To start optimizing images, you need to install the additional component  Titan security!', 'clearfy') ?></p>
				<p><?php _e('Installing the component will not take you long, just click the install button, then	activate.', 'clearfy') ?></p>
				<p style="margin-top:20px">
					<?php $install_button->renderLink(); ?>
				</p>
			</div>
		</div>
		<?php
	}
}
