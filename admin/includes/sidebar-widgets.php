<?php
/**
 * Sidebar widgets
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 01.12.2018, Webcraftic
 * @version 1.0
 */

/**
 * Return premium widget markup
 * @return string
 */
function wbcr_clearfy_get_sidebar_premium_widget() {
	
	$buy_premium_url = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl( WCL_Plugin::app()->getPluginName(), 'pricing', 'license_page' );
	$upgrade_price   = WbcrFactoryClearfy000_Helpers::getClearfyBusinessPrice();
	
	ob_start();
	?>
    <div id="wbcr-clr-go-to-premium-widget" class="wbcr-factory-sidebar-widget">
        <p>
            <strong><?php _e( 'Activation Clearfy Business', 'clearfy' ); ?></strong>
        </p>
        <div class="wbcr-clr-go-to-premium-widget-body">
            <p><?php _e( '<b>Clearfy Business</b> is a paid package of components for the popular free WordPress plugin named Clearfy. You get access to all paid components at one price.', 'clearfy' ) ?></p>
            <p><?php _e( 'Paid license guarantees that you can download and update existing and future paid components of the plugin.', 'clearfy' ) ?></p>
            <a href="<?= $buy_premium_url ?>" class="wbcr-clr-purchase-premium" target="_blank" rel="noopener">
                        <span class="btn btn-gold btn-inner-wrap">
                        <i class="fa fa-star"></i> <?php printf( __( 'Upgrade to Clearfy Business for $%s', 'clearfy' ), $upgrade_price ) ?>
	                        <i class="fa fa-star"></i>
                        </span>
            </a>
        </div>
    </div>
	<?php
	
	$output = ob_get_contents();
	
	ob_end_clean();
	
	return $output;
}

/**
 * Return support widget markup
 * @return string
 */
function wbcr_clearfy_get_sidebar_support_widget() {
	
	$output = '';
	
	$page_premium_support_url         = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl( WCL_Plugin::app()->getPluginName(), 'premium-support', 'support_widget' );
	$page_other_questions_support_url = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl( WCL_Plugin::app()->getPluginName(), 'other-questions-support', 'support_widget' );
	$page_docs_url                    = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl( WCL_Plugin::app()->getPluginName(), 'docs', 'support_widget' );
	$page_hot_support_url             = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl( WCL_Plugin::app()->getPluginName(), 'hot-support', 'support_widget' );
	ob_start();
	?>
    <div id="wbcr-clr-support-widget" class="wbcr-factory-sidebar-widget">
        <p><strong><?php _e( 'Having Issues?', 'clearfy' ); ?></strong></p>
        <div class="wbcr-clr-support-widget-body">
            <p>
				<?php _e( 'We provide free support for this plugin. If you are pushed with a problem, just create a new ticket. We will definitely help you!', 'clearfy' ); ?>
            </p>
            <ul>
                <li><span class="dashicons dashicons-sos"></span>
                    <a href="http://forum.webcraftic.com" target="_blank" rel="noopener"><?php _e( 'Free support forum', 'clearfy' ); ?></a>
                </li>
                <li><span class="dashicons dashicons-star-filled"></span>
                    <a href="<?= $page_premium_support_url ?>" target="_blank" rel="noopener"><?php _e( 'Premium support', 'clearfy' ); ?></a>
                </li>
                <li><span class="dashicons dashicons-editor-help"></span>
                    <a href="<?= $page_other_questions_support_url ?>" target="_blank" rel="noopener"><?php _e( 'Other questions', 'clearfy' ); ?></a>
                </li>
                <li><span class="dashicons dashicons-book"></span>
                    <a href="<?= $page_docs_url ?>" target="_blank"><?php _e( 'Documentation', 'clearfy' ); ?></a>
                </li>
                <li style="margin-top: 15px;background: #fff4f1;padding: 10px;color: #a58074;">
                    <span class="dashicons dashicons-warning"></span>
                    <a href="<?= $page_hot_support_url ?>" target="_blank" rel="noopener"><?php _e( 'Hot support', 'clearfy' ); ?></a>
                    -
					<?php _e( 'Any user can contact us. You can use it only if you find a php error in the plugin, get a white screen, or want to report a vulnerability.', 'clearfy' ); ?>
                </li>
            </ul>
        </div>
    </div>
	<?php
	
	$output = ob_get_contents();
	
	ob_end_clean();
	
	return $output;
}
