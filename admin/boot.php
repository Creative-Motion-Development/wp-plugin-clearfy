<?php
	/**
	 * Admin boot
	 * @author Webcraftic <alex.kovalevv@gmail.com>
	 * @copyright Webcraftic 25.05.2017
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * We assets scripts in the admin panel on each page.
	 * @param $hook
	 */
	function wbcr_clearfy_enqueue_global_scripts($hook)
	{
		wp_enqueue_style('wbcr-clearfy-install-addons', WCL_PLUGIN_URL . '/admin/assets/css/install-addons.css', array(), WCL_Plugin::app()
			->getPluginVersion());
		wp_enqueue_script('wbcr-clearfy-install-addons', WCL_PLUGIN_URL . '/admin/assets/js/install-addons.js', array('jquery'), WCL_Plugin::app()
			->getPluginVersion());
	}

	add_action('admin_enqueue_scripts', 'wbcr_clearfy_enqueue_global_scripts');

	/**
	 * Ошибки совместимости с похожими плагинами
	 */
	function wbcr_clearfy_admin_notices($notices)
	{

		if( is_plugin_active('wp-disable/wpperformance.php') ) {
			$default_notice = WCL_Plugin::app()
					->getPluginTitle() . ': ' . __('We found that you have the plugin %s installed. The functions of this plugin already exist in %s. Please deactivate plugin %s to avoid conflicts between plugins functions.', 'clearfy');
			$default_notice .= ' ' . __('If you do not want to deactivate the plugin %s for some reason, we strongly recommend do not use the same plugins functions at the same time!', 'clearfy');

			$notices[] = array(
				'id' => 'clearfy_plugin_conflicts_notice',
				'type' => 'warning',
				'dismissible' => true,
				'dismiss_expires' => 0,
				'text' => '<p>' . sprintf($default_notice, 'WP Disable', WCL_Plugin::app()
						->getPluginTitle(), 'WP Disable', 'WP Disable') . '</p>'
			);
		}

		$new_external_componetns = array(
			array(
				'name' => 'cyr3lat',
				'base_path' => 'cyr3lat/cyr-to-lat.php',
				'type' => 'wordpress',
				'title' => __('Robin image optimizer – saves your money on image optimization!', 'clearfy'),
				'description' => '<br><span><b>'.__('Our new component!', 'clearfy').'</b> '.__('We’ve created a 100% free solution for image optimization, which is as good as the paid products. The plugin optimizes your images automatically, reducing their weight with no quality loss. More details in here:', 'clearfy') . ' <a href="#">fsdfsdf</a></span><br>'
			),
			array(
				'name' => 'hide_login_page',
				'base_path' => 'hide-login-page/hide-login-page.php',
				'type' => 'wordpress',
				'title' => __('Hide login page (Reloaded) – hides your login page!', 'clearfy'),
				'description' => '<br><span> <b style="color:red;">'.__('Attention! If you’ve ever used features associated with hiding login page, then, please, re-activate this component.', 'clearfy').'</b><br> '.__('This simple module changes the login page URL to a custom link quickly and safely. The plugin requires installation.', 'clearfy').'</span><br>'
			),
			array(
				'name' => 'webcraftic-hide-my-wp',
				'type' => 'freemius',
				'title' => __('Hide my wp (Premium) – hides your WordPress from hackers and bots!', 'clearfy'),
				'description' => '<br><span><b>'.__('Our new component! ', 'clearfy').'</b>'.__('This premium component helps in hiding your WordPress from hackers and bots. Basically, it disables identification of your CMS by changing directories and files names, removing meta data and replacing HTML content which can provide all information about the platform you use.
Most websites can be hacked easily, as hackers and bots know all security flaws in plugins, themes and the WordPress core. You can secure the website from the attack by hiding the information the hackers will need.
', 'clearfy').'</span><br>'
			),
			array(
				'name' => 'minify_and_combine',
				'type' => 'internal',
				'title' => __('Minify and Combine (JS, CSS) – optimizes your scripts and styles!', 'clearfy'),
				'description' => '<br><span><b>'.__('Our new component! ', 'clearfy').'</b> '.__('This component combines all your scripts and styles in one file, compresses & caches it. ', 'clearfy').'
</span><br>'
			),
			array(
				'name' => 'html_minify',
				'type' => 'internal',
				'title' => __('Html minify (Reloaded) – reduces the amount of code on your pages!', 'clearfy'),
				'description' => '<br><span><b>'.__('Our new component! ', 'clearfy').'</b> '.__('We’ve completely redesigned HTML compression of the pages and added these features to another component. It’s more stable and reliable solution for HTML code optimization of your pages.', 'clearfy').'</span><br>'
			),
		);

		$need_show_new_components_notice = false;

		$new_component_notice_text = '<div>';
		$new_component_notice_text .= '<h3>' . __('Welcome to Clearfy!', 'clearfy') . '</h3>';
		$new_component_notice_text .= '<p>' . __('We apologize for the delay in updates!', 'clearfy') . ' ';
		$new_component_notice_text .= __('Our team has spent a lot of time designing new, useful, and the most important – free! – features of the Clearfy plugin! ', 'clearfy') . ' ';
		$new_component_notice_text .= __('Now it is time to try it.', 'clearfy').'</p>';

		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';

		foreach($new_external_componetns as $new_component) {
			$slug = $new_component['name'];

			if( $new_component['type'] == 'wordpress' ) {
				$slug = $new_component['base_path'];
			}

			$install_button = new WCL_InstallPluginsButton($new_component['type'], $slug);

			if( $install_button->isPluginActivate() ) {
				continue;
			}
			$new_component_notice_text .= '<div class="wbcr-clr-new-component">';
			$new_component_notice_text .= '<h4>' . $new_component['title'] . '</h4>';
			$new_component_notice_text .= $new_component['description'];
			$new_component_notice_text .= $install_button->render(false);
			$new_component_notice_text .= '</div>';

			$need_show_new_components_notice = true;
		}

		$new_component_notice_text .= '</div>';

		if( $need_show_new_components_notice ) {
			$notices[] = array(
				'id' => 'clearfy_plugin_install_new_components_notice',
				'type' => 'warning',
				'dismissible' => true,
				'dismiss_expires' => 0,
				'text' => $new_component_notice_text
			);
		}

		return apply_filters('wbcr_clearfy_admin_notices', $notices);
	}

	add_filter('wbcr_factory_admin_notices', 'wbcr_clearfy_admin_notices', 10, 2);

	/**
	 * Fake stubs for the Clearfy plugin board
	 */
	function wbcr_clearfy_fake_boards()
	{
		if( !defined('WIO_PLUGIN_ACTIVE') ) {
			require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
			$install_button = new WCL_InstallPluginsButton('wordpress', 'cyr3lat/cyr-to-lat.php');

			//$install_button->removeClass('button');
			//$install_button->removeClass('button-default');
			//$install_button->removeClass('button-primary');
			?>
			<div class="col-sm-12">
				<div class="wbcr-clearfy-fake-image-optimizer-board wbcr-clearfy-board">
					<h4 class="wio-text-left"><?php _e('Images optimization', 'image-optimizer'); ?></h4>

					<div class="wbcr-clearfy-fake-widget">
						<div class="wbcr-clearfy-widget-overlay">
							<img src="<?= WCL_PLUGIN_URL ?>/admin/assets/img/robin-image-optimizer-fake-board.png" alt=""/>
						</div>
						<?php $install_button->render(); ?>
					</div>
				</div>
			</div>
		<?php
		}
	}

	add_action('wbcr_clearfy_quick_boards', 'wbcr_clearfy_fake_boards');
