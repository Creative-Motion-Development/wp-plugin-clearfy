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

	public $page_parent_page = "defence";

	/**
	 * @var string
	 */
	//public $page_parent_page = 'defence';

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
	//public $page_menu_position = 20;

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
		$this->menu_title = __('Malware Scanner, Firewall', 'clearfy');
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
		$install_button = $this->plugin->get_install_component_button('creativemotion', 'anti-spam/anti-spam.php');
		$install_button->add_class('wbcr-factory-purchase-premium');
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
						window.location.href = '<?= admin_url('admin.php?page=dashboard-titan_security'); ?>';
					}
				});
			});
		</script>
		<div class="wbcr-factory-templates-000-multisite-suggetion">
			<div class="wbcr-factory-inner-contanier">
				<h3>
					<a href="https://wordpress.org/plugins/anti-spam" target="_blank"><?php _e('Install Firewall and Malware scanner (Titan sucurity) component', 'clearfy') ?></a>
				</h3>

				<p><?php _e('Titan includes anti-spam, firewall, malware scanner, site accessibility checking, security and threats audits for WordPress websites. Our security functions provide Titan with the latest firewall rules, malware signatures, and database of malicious IP addresses – all you need to ensure the security of your website.', 'clearfy') ?></p>
				<p>
					<?php _e('Titan is a comprehensive WordPress security solution, completed by a set of additional features as add-ons, which was placed into a simple and intuitive interface.', 'clearfy') ?>
					<a href="https://wordpress.org/plugins/anti-spam" target="_blank"><?php _e('Read more', 'clearfy'); ?></a>
				</p>

				<p style="color:#ff4d00"><?php _e('Installing the component will not take you long, just click the install button, then	activate.', 'clearfy') ?></p>
				<p style="margin-top:20px">
					<?php $install_button->render_link(); ?>
				</p>
			</div>
		</div>
		<?php
	}
}
