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
		$install_robin_plugin_btn = \WCL_Plugin::app()->getInstallComponentsButton('wordpress', 'robin-image-optimizer/robin-image-optimizer.php');
		$install_wp_super_cache_btn = \WCL_Plugin::app()->getInstallComponentsButton('wordpress', 'wp-super-cache/wp-cache.php');
		$install_assets_manager_component_btn = \WCL_Plugin::app()->getInstallComponentsButton('internal', 'assets_manager');
		$install_minify_and_combine_component_btn = \WCL_Plugin::app()->getInstallComponentsButton('internal', 'minify_and_combine');
		?>
		<div class="w-factory-clearfy-000-setup__inner-wrap">
			<h3>Installing plugins</h3>
			<p style="text-align: left;">We analyzed your site and decided that in order to get the maximum result in
				optimizing your site, you will need to install additional plugins.</p>
			<table class="form-table">
				<thead>
				<tr>
					<th>Plugin</th>
					<th style="width:50px">Score</th>
					<th style="width:200px">Score with PRO</th>
					<th></th>
				</tr>
				</thead>
				<tr>
					<td>Robin image optimizer</td>
					<td style="color:grey">+10</td>
					<td style="color:green">+15</td>
					<td>
						<?php $install_robin_plugin_btn->renderLink(); ?>
					</td>
				</tr>
				<tr>
					<td>Assets manager component</td>
					<td style="color:grey">+5</td>
					<td style="color:green">+10</td>
					<td><?php $install_assets_manager_component_btn->renderLink(); ?></td>
				</tr>
				<tr>
					<td>WP Super Cache</td>
					<td style="color:grey">+8</td>
					<td></td>
					<td><?php $install_wp_super_cache_btn->renderLink(); ?></td>
				</tr>
				<tr>
					<td>Minify and Combine component</td>
					<td style="color:grey">+10</td>
					<td style="color:green">+15</td>
					<td><?php $install_minify_and_combine_component_btn->renderLink(); ?></td>
				</tr>
			</table>
		</div>
		<?php $this->render_button(); ?>
		<?php
	}
}