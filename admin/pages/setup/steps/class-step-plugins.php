<?php

namespace WBCR\Clearfy\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Plugins extends \WBCR\FactoryClearfy000\Pages\Step_Custom {

	protected $prev_id = 'step1';
	protected $id = 'step2';
	protected $next_id = 'step3';

	public function get_title()
	{
		return "Setup Plugins";
	}

	public function html()
	{
		$install_robin_plugin_btn = $this->plugin->get_install_component_button('wordpress', 'robin-image-optimizer/robin-image-optimizer.php');
		$install_wp_super_cache_btn = $this->plugin->get_install_component_button('wordpress', 'wp-super-cache/wp-cache.php');
		$install_assets_manager_component_btn = $this->plugin->get_install_component_button('internal', 'assets_manager');
		$install_minify_and_combine_component_btn = $this->plugin->get_install_component_button('internal', 'minify_and_combine');
		?>
		<div class="w-factory-clearfy-000-setup__inner-wrap">
			<h3><?php _e('Installing plugins', 'clearfy') ?></h3>
			<p style="text-align: left;"><?php _e('We analyzed your site and decided that in order to get the maximum result in
				optimizing your site, you will need to install additional plugins.', 'clearfy') ?></p>
			<table class="form-table">
				<thead>
				<tr>
					<th><?php _e('Plugin', 'clearfy') ?></th>
					<th style="width:50px"><?php _e('Score', 'clearfy') ?></th>
					<th style="width:200px"><?php _e('Score with PRO', 'clearfy') ?></th>
					<th></th>
				</tr>
				</thead>
				<tr>
					<td>Robin image optimizer</td>
					<td style="color:grey">+10</td>
					<td style="color:green">+15</td>
					<td>
						<?php $install_robin_plugin_btn->render_link(); ?>
					</td>
				</tr>
				<tr>
					<td>Assets manager component</td>
					<td style="color:grey">+5</td>
					<td style="color:green">+10</td>
					<td><?php $install_assets_manager_component_btn->render_link(); ?></td>
				</tr>
				<!--<tr>
					<td>WP Super Cache</td>
					<td style="color:grey">+8</td>
					<td></td>
					<td><?php /*$install_wp_super_cache_btn->renderLink(); */ ?></td>
				</tr>-->
				<tr>
					<td>Minify and Combine component</td>
					<td style="color:grey">+10</td>
					<td style="color:green">+15</td>
					<td><?php $install_minify_and_combine_component_btn->render_link(); ?></td>
				</tr>
			</table>
		</div>
		<?php $this->render_button(); ?>
		<?php
	}
}