<?php

namespace WBCR\Clearfy\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Optimize_Images extends \WBCR\FactoryClearfy000\Pages\Step_Custom {

	protected $prev_id = 'step4';
	protected $id = 'step5';
	protected $next_id = 'step6';

	public function __construct(\WBCR\FactoryClearfy000\Pages\Setup $page)
	{
		parent::__construct($page);
	}


	public function get_title()
	{
		return "Optimize images";
	}

	/**
	 * {@inheritdoc}
	 */
	public function assets($scripts, $styles)
	{
		parent::assets($scripts, $styles);

		$styles->add(WCL_PLUGIN_URL . '/admin/assets/css/setup/step-optimize-images.css');

		if( defined('WRIO_PLUGIN_ACTIVE') ) {
			$scripts->add(WRIO_PLUGIN_URL . '/admin/assets/js/Chart.min.js', ['jquery'], 'wrio-charts');
			$scripts->add(WRIO_PLUGIN_URL . '/admin/assets/js/statistic.js', ['jquery', 'wrio-charts']);
		}
	}

	public function html()
	{
		if( !defined('WRIO_PLUGIN_ACTIVE') ) {
			$this->alternate_html();

			return;
		}

		$is_premium = wrio_is_license_activate();
		$statistics = \WRIO_Image_Statistic::get_instance();

		$data = [
			'is_premium' => $is_premium,
			'scope' => 'media-library',
			'stats' => $statistics->get()
		];
		?>

		<div class="w-factory-clearfy-000-setup__inner-wrap">
			<h3><?php _e("Optimize images", "clearfy") ?></h3>
			<p style="text-align: left;">
				<?php _e("We found unoptimized images in your media library. You can run background image optimization with the Robin image optimizer component of the Clearfy plugin. Background optimization is a long process, your images will be optimized after some time, so you won't be able to immediately see the improvement in Google Page Speed, but when the images are fully optimized you will see a score increase in Google Page Speed.", "clearfy"); ?>
			</p>
			<div class="wio-columns wio-page-statistic">
				<div>
					<div class="wio-chart-container wio-overview-chart-container">
						<canvas id="wio-main-chart" width="180" height="180"
						        data-unoptimized="<?php echo esc_attr($data['stats']['unoptimized']); ?>"
						        data-optimized="<?php echo esc_attr($data['stats']['optimized']); ?>"
						        data-errors="<?php echo esc_attr($data['stats']['error']); ?>"
						        style="display: block;">
						</canvas>
						<div id="wio-overview-chart-percent"
						     class="wio-chart-percent"><?php echo esc_attr($data['stats']['optimized_percent']); ?>
							<span>%</span>
						</div>
						<p class="wio-global-optim-phrase wio-clear">
							<?php _e('You optimized', 'robin-image-optimizer'); ?>
							<span class="wio-total-percent">
                        <?php echo esc_attr($data['stats']['optimized_percent']); ?>%
                    </span>
							<?php _e("of your website's images", 'robin-image-optimizer'); ?>
						</p>
					</div>
					<div style="margin-left:200px;">
						<div id="wio-overview-chart-legend">
							<ul class="wio-doughnut-legend">
								<li>
									<span style="background-color:#d6d6d6"></span>
									<?php _e('Unoptimized', 'robin-image-optimizer'); ?>-
									<span class="wio-num" id="wio-unoptimized-num">
                                <?php echo esc_attr($data['stats']['unoptimized']); ?>
                            </span>
								</li>
								<li>
									<span style="background-color:#8bc34a"></span>
									<?php _e('Optimized', 'robin-image-optimizer'); ?>-
									<span class="wio-num" id="wio-optimized-num">
                                 <?php echo esc_attr($data['stats']['optimized']); ?>
                            </span>
								</li>
								<li>
									<span style="background-color:#f1b1b6"></span>
									<?php _e('Error', 'robin-image-optimizer'); ?>-
									<span class="wio-num" id="wio-error-num">
                                 <?php echo esc_attr($data['stats']['error']); ?>
                            </span>
								</li>
							</ul>
						</div>
						<h3 class="screen-reader-text"><?php _e('Statistics', 'robin-image-optimizer'); ?></h3>
						<div class="wio-bars" style="width: 90%">
							<p><?php _e('Original size', 'robin-image-optimizer'); ?></p>
							<div class="wio-bar-negative base-transparent wio-right-outside-number">
								<div id="wio-original-bar" class="wio-progress" style="width: 100%">
                             <span class="wio-barnb" id="wio-original-size">
                                 <?php echo esc_attr(wrio_convert_bytes($data['stats']['original_size'])); ?>
                             </span>
								</div>
							</div>
							<p><?php _e('Optimized size', 'robin-image-optimizer'); ?></p>
							<div class="wio-bar-primary base-transparent wio-right-outside-number">
								<div id="wio-optimized-bar" class="wio-progress"
								     style="width: <?php echo ($data['stats']['percent_line']) ? esc_attr($data['stats']['percent_line']) : 100; ?>%">
                        <span class="wio-barnb" id="wio-optimized-size">
                            <?php echo esc_attr(wrio_convert_bytes($data['stats']['optimized_size'])); ?>
                        </span>
								</div>
							</div>
						</div>
						<div class="wio-number-you-optimized">
							<p>
                    <span id="wio-total-optimized-attachments-pct" class="wio-number">
                        <?php echo esc_attr($data['stats']['save_size_percent']); ?>%
                    </span>
								<span class="wio-text">
						<?php _e("that's the size you saved <br>by using Image Optimizer", 'robin-image-optimizer'); ?>
					</span>
							</p>
						</div>
					</div>
				</div>
			</div>

		</div>
		<?php $this->render_button(true, true, __('Shedule optimization and Continue', 'clearfy')); ?>
		<?php
	}

	protected function continue_step($skip = false)
	{
		if( defined('WRIO_PLUGIN_ACTIVE') ) {
			\WRIO_Plugin::app()->updatePopulateOption('cron_running', 'media-library');
			\WRIO_Cron::start();
		}
		parent::continue_step($skip);
	}


	private function alternate_html()
	{
		$install_robin_plugin_btn = \WCL_Plugin::app()->getInstallComponentsButton('wordpress', 'robin-image-optimizer/robin-image-optimizer.php');
		$install_robin_plugin_btn->addClass('wclearfy-setup__install-component-button');

		?>
		<div class="w-factory-clearfy-000-setup__inner-wrap">
			<h3><?php _e("Optimize images", "clearfy") ?></h3>
			<p style="text-align: left;">
				<?php _e("Robin Image optimizer plugin isn't installed or activated, you need to install or activate it to optimize your images.", "clearfy"); ?>
			</p>

			<p style="text-align: center"><?php $install_robin_plugin_btn->renderButton(); ?></p>
		</div>
		<?php $this->render_button(false, true); ?>
		<?php
	}
}