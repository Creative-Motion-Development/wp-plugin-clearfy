<?php

namespace WBCR\Clearfy\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Google_Page_Speed_After extends \WBCR\Factory_Templates_000\Pages\Step_Custom {

	protected $prev_id = 'step5';
	protected $id = 'step6';
	protected $next_id = 'step7';

	public function get_title()
	{
		return "Site test #2";
	}

	public function html()
	{
		$site_url = get_home_url();
		?>
		<script>
			jQuery(document).ready(function($) {
				wclearfy_fetch_google_pagespeed_audit("<?php echo wp_create_nonce('fetch_google_page_speed_audit') ?>", true);
			});
		</script>
		<div class="w-factory-templates-000-setup__inner-wrap">
			<h3>Google Page Speed</h3>
			<p style="text-align: left;">
				We analyzed your site on the Google Page Speed service. You can see the test results below. Our plugin
				is to
				improve the score of your site on Google Page Speed. Memorize the results to make a comparison after
				optimization by the Clearfy plugin.
			</p>
			<div class="wclearfy-gogle-page-speed-audit__errors">Memorize the results to make a comparison after
				optimization by the Clearfy plugin.
			</div>
			<div class="wclearfy-gogle-page-speed-audit__preloader"></div>
			<div class="wclearfy-gogle-page-speed-audit" style="display: none;">
				<div class="wclearfy-score">
					<!-- Desktop -->
					<div class="wclearfy-desktop-score">
						<h3><?php _e('Desktop score', 'clearfy'); ?></h3>
						<div class="wclearfy-desktop-score__circle-wrap">
							<div id="wclearfy-desktop-score__circle" class="wclearfy-score-circle"></div>
						</div>
					</div>

					<!-- Mobile -->
					<div class="wclearfy-mobile-score">
						<h3><?php _e('Mobile score', 'clearfy'); ?></h3>
						<div class="wclearfy-mobile-score__circle-wrap">
							<div id="wclearfy-mobile-score__circle" class="wclearfy-score-circle"></div>
						</div>
					</div>
				</div>


				<!-- Statistics -->
				<div class="wclearfy-statistic">
					<div class="wclearfy-statistic__line">
						<span><?php _e('First Contentful Paint', 'clearfy'); ?></span>
						<div class="wclearfy-statistic__results">
							<span id="wclearfy-statistic__desktop-first-contentful-paint">??&nbsp;s</span>&nbsp;/&nbsp;<span id="wclearfy-statistic__mobile-first-contentful-paint">??&nbsp;s</span>
						</div>
					</div>
					<div class="wclearfy-statistic__line">
						<span><?php _e('Speed Index', 'clearfy'); ?></span>
						<div class="wclearfy-statistic__results">
							<span id="wclearfy-statistic__desktop-speed-index">??&nbsp;s</span>&nbsp;/&nbsp;<span id="wclearfy-statistic__mobile-speed-index">??&nbsp;s</span>
						</div>
					</div>
					<div class="wclearfy-statistic__line">
						<span><?php _e('Time to Interactive', 'clearfy'); ?></span>
						<div class="wclearfy-statistic__results">
							<span id="wclearfy-statistic__desktop-interactive">??&nbsp;s</span>&nbsp;/&nbsp;<span id="wclearfy-statistic__mobile-interactive">??&nbsp;s</span>
						</div>
					</div>

					<?php
					$site_url = get_home_url();
					$google_page_speed_call = "https://developers.google.com/speed/pagespeed/insights/?url=" . $site_url;
					?>

					<div style="margin-top: 5px;font-size:12px;">
						<a href="<?php echo $google_page_speed_call; ?>" target="_blank" style="outline: 0;text-decoration: none;"><?php _e('View complete results', 'clearfy'); ?></a> <?php _e('on Google PageSpeed Insights.', 'clearfy'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php $this->render_button(); ?>
		<?php
	}
}