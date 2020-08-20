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
			<p style="text-align: left;">
				<?php _e("However, you can still improve your site's Google Page Speed score by simply purchasing the Pro version of our plugin.", "clearfy") ?>
				<br><br>
				<a href="<?php echo esc_url($pricing_page_url); ?>" class="wclearfy-setup__install-component-button" target="_blank"><?php _e('Go Pro', 'clearfy') ?></a>
			</p>
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