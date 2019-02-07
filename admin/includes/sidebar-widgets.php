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
	
	$free_support_url     = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl( WCL_Plugin::app()->getPluginName(), 'support', 'support_widget' );
	$page_hot_support_url = WbcrFactoryClearfy000_Helpers::getWebcrafticSitePageUrl( WCL_Plugin::app()->getPluginName(), 'hot-support', 'support_widget' );
	
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
                    <a href="<?= $free_support_url ?>" target="_blank" rel="noopener"><?php _e( 'Get starting free support', 'clearfy' ); ?></a>

                </li>
                <li style="margin-top: 15px;background: #fff4f1;padding: 10px;color: #a58074;">
                    <span class="dashicons dashicons-warning"></span>
					<?php printf( __( 'If you find a php error or a vulnerability in plugin, you can <a href="%s" target="_blank" rel="noopener">create ticket</a> in hot support that we responded instantly.', 'clearfy' ), $page_hot_support_url ); ?>
                </li>
            </ul>
        </div>
    </div>
	<?php
	
	$output = ob_get_contents();
	
	ob_end_clean();
	
	return $output;
}

/**
 * Return subscribe form markup
 * @return string
 */
function wbcr_clearfy_get_sidebar_subscribe_widget(){
    $output = '';
    $terms = "https://clearfy.pro/?bizpanda=privacy-policy";

    ob_start();
    ?>
    <div id="wbcr-clr-subscribe-widget" class="wbcr-factory-sidebar-widget wbcr-factory-subscribe-widget">
        <p><strong><?php _e( 'Subscribe', 'clearfy' ); ?></strong></p>
        <div class="wbcr-clr-subscribe-widget-body">
            <p>
				<?php _e( 'Please subscribe to our awesome plugin', 'clearfy' ); ?>
            </p>
            <div id="wbcr-factory-subscribe-widget-msg-ok" class="wbcr-factory-subscribe-widget-msgbox">
                <div class="wbcr-factory-subscribe-widget-msg success"><?=__("Thanks", "clearfy")?></div>
                <?=__("Please confirm your email", "clearfy");?>
            </div>
            <form id="wbcr-factory-subscribe-widget-form" method="post">
                <input class="wbcr-factory-subscribe-widget-field" type="email" name="email" placeholder="You email" required>
                <label class="wbcr-factory-subscribe-widget-checkbox-label">
                    <input class="wbcr-factory-subscribe-widget-checkbox" type="checkbox" name="agree_terms" checked required>
                    <a href="<?= $terms ?>" target="_blank"><?=__("i agree terms & conditions", "clearfy");?></a>
                </label>
                <input type="submit" value="Subscribe" class="btn wbcr-factory-subscribe-widget-btn">
                <?php wp_nonce_field('wbcr-clr-subscribe', '_wpnonce_subscribe');?>
            </form>
        </div>
    </div>

    <?php

	$output = ob_get_contents();

	ob_end_clean();

	return $output;
}