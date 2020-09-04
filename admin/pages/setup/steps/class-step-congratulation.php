<?php

namespace WBCR\Clearfy\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Congratulation extends \WBCR\FactoryClearfy000\Pages\Step_Custom {

	protected $prev_id = 'step6';
	protected $id = 'step7';

	//protected $next_id = 'step2';

	public function get_title()
	{
		return __("Finish", "clearfy");
	}

	public function html()
	{
		$pricing_page_url = $this->plugin->get_support()->get_pricing_url(true, 'setup_wizard');
		?>
		<div class="w-factory-clearfy-000-setup__inner-wrap">
			<h3><?php echo __("Congratulations, the plugin configuration is complete!", "clearfy"); ?></h3>
			<p style="text-align: left;">
				<?php _e('You have successfully completed the basic plugin setup! You can go to the general plugin settings to enable other options that we did not offer you.', 'clearfy'); ?>
			</p>
			<hr>
			<div>
				<p style="text-align: left;">
					<?php _e("However, you can still improve your site's Google Page Speed score by simply purchasing the Pro version of our plugin.", "clearfy") ?>
				</p>
				<table style="width: 100%">
					<thead>
					<tr>
						<th></th>
						<th>Free</th>
						<th>PRO</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><a href="https://wordpress.org/plugins/cyrlitera/" target="_blank">
								Transliteration of links and file names</a></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Optimize Yoast Seo</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Post tools</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Admin bar managers</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><a href="https://wordpress.org/plugins/disable-admin-notices/" target="_blank">Disable admin
								notices</a></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Disable widgets</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>
							<a href="https://wordpress.org/plugins/comments-plus/" target="_blank">Disable comments</a>
						</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><a href="https://wordpress.org/plugins/gonzales/" target="_blank">Assets Manager</a></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Minify and combine (JS, CSS)</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Html minify</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><a href="https://robinoptimizer.com/" target="_blank">Image optimizer</a></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Hide login page</td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><a href="https://clearfy.pro/hide-my-wp/" target="_blank">Hide My Wp PRO</a></td>
						<td class="wclearfy-setup__color--red"><span class="dashicons dashicons-minus"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><a href="https://clearfy.pro/assets-manager/" target="_blank">Assets Manager PRO</a></td>
						<td class="wclearfy-setup__color--red"><span class="dashicons dashicons-minus"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Multisite control</td>
						<td class="wclearfy-setup__color--red"><span class="dashicons dashicons-minus"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>Update manager PRO</td>
						<td class="wclearfy-setup__color--red"><span class="dashicons dashicons-minus"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td>SEO friendly images PRO</td>
						<td class="wclearfy-setup__color--red"><span class="dashicons dashicons-minus"></span></td>
						<td class="wclearfy-setup__color--green"><span class="dashicons dashicons-yes"></span></td>
					</tr>
					</tbody>
				</table>
				<p>
					<a href="<?php echo esc_url($pricing_page_url); ?>" class="wclearfy-setup__install-component-button" target="_blank"><?php _e('Go Pro', 'clearfy') ?></a>
				</p>
			</div>
		</div>
		<?php $this->render_button();
		?>
		<?php
	}

	protected function continue_step($skip = false)
	{
		$next_id = $this->get_next_id();
		if( !$next_id ) {
			wp_safe_redirect($this->plugin->getPluginPageUrl('quick_start'));
			die();
		}
		wp_safe_redirect($this->page->getActionUrl($next_id));
		die();
	}
}