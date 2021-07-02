<?php

namespace WBCR\Clearfy\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Default extends \WBCR\Factory_Templates_000\Pages\Step_Custom {

	protected $id = 'step0';
	protected $next_id = 'step1';

	public function get_title()
	{
		return __("Welcome", 'clearfy');
	}

	public function html()
	{
		?>
		<div class="w-factory-templates-000-setup__inner-wrap">
			<div class="w-factory-templates-000-setup-step__new_onboarding-wrapper">
				<p class="w-factory-templates-000-setup-step__new_onboarding-welcome">Welcome to</p>
				<h1 class="w-factory-templates-000-logo">
					<img src="<?php echo WCL_PLUGIN_URL ?>/admin/assets/img/clearfylogo-768x300.png" alt="Clearfy">
				</h1>
				<p><?php _e('Optimize your site even faster using the setup wizard!', 'clearfy') ?></p>
			</div>

		</div>
		<?php $this->render_button(true, false, __('Yes, I want to try the wizard'), 'center'); ?>
		<?php
	}
}