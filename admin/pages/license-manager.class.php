<?php #comp-page build: free, premium, trial-7
/**
 * The file contains a page for showing the OnePress licensing manager.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2013, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * Licensing Manager Page
 * 
 * License page is a place where a user can check updated and manage the license.
 * 
 * @since 1.0.0
 */
class OnpLicensing000_LicenseManagerPage extends FactoryPages000_AdminPage  {
    
    public $id = 'license-manager';
    public $purchasePrice = '$';
    
    /**
     * If true, offers to activate a trial version.
     * 
     * @since 3.1.1
     * @var boolean
     */
    public $trial = true;
    
    /**
     * If true, shows hits regarding where the user can get a key linked with CodeCanyon.
     * 
     * @since 3.1.1
     * @var boolean 
     */
    public $codecanyon = true;
    
    /**
     * If true, shows the FAQ section.
     * 
     * @since 3.1.1
     * @var boolean 
     */
    public $faq = true;
    
    /**
     * If true, then tells how to activate a 'premium version',
     * otherwise tells how to activate 'plugin'.
     * @since 3.1.1
     * @var boolean
     */
    public $premium = true;
    
    public function __construct( $plugin) { 
        parent::__construct($plugin);

		if( !current_user_can('administrator') ) {
			$this->capabilitiy = 'manage_opanda_licensing';
		}

        if ( !$this->menuTitle ) $this->menuTitle = __('License Manager', 'onp_licensing_000');
        
        // turns off the license manager if we use the embedded license key
        if ( $plugin->license && $plugin->license->isEmbedded() ) {
            $this->hidden = true;
        }
        
        $this->site = site_url();
        $this->domain = parse_url( $this->site, PHP_URL_HOST );
        
        $nounce = wp_create_nonce('hidelm');
        $optionName = 'onp_lm_' . $this->plugin->pluginName . '_' . $nounce;
        $optionValue = get_option( $optionName );
        
        if ( !empty( $optionValue ) ) {
            $this->hidden = true;
        }
    }
    
    /**
     * [MAGIC] Magic method that configures assets for a page.
     */
    public function assets($scripts, $styles) {
        
        $this->styles->request( array( 
            'bootstrap.core'
            ), 'bootstrap' ); 
        
        $this->styles->add(ONP_LICENSING_000_URL . '/assets/css/license-manager.css');
        $this->scripts->add(ONP_LICENSING_000_URL . '/assets/js/license-manager.js');   
    }

    // ------------------------------------------------------------------
    // Page Actions
    // ------------------------------------------------------------------

    
    /**
     * Shows current license type.
     */
    public function indexAction() {

        $licenseManager = $license = $this->plugin->license;
        $updatesManager = $this->plugin->updates;
        
        $licenseKey = isset( $_POST['licensekey'] ) ? htmlspecialchars ( trim( $_POST['licensekey'] ) ) : null;
        
        $scope = isset( $_GET['scope'] ) ? $_GET['scope'] : null;  
        $error = $response = null;
        
        // processing a form submission for activation a key

        if ( isset( $_POST['licensekey'] ) ) {
            $scope = 'submit-key';
            $licenseKey = htmlspecialchars( trim( $_POST['licensekey'] ));
            
            if ( empty( $licenseKey ) ) {
                $error = new WP_Error('FORM:KeyEmpty', __('Please enter your license key to continue.', 'onp_licensing_000'));
            } else {
                $response = $licenseManager->activateKey( $licenseKey );
                if ( is_wp_error( $response ) ) $error = $response;
                else $licenseKey = null;   
            }

        // displaying results of other actions (deletion of a key, checking updates and so on) 
            
        } else {
            $code = isset( $_GET['code'] ) ? $_GET['code'] : null;
            $message = isset( $_GET['message'] ) ? $_GET['message'] : null; 
            if ( $code && $message ) $error = new WP_Error($code, urldecode ( base64_decode($message) ) );
        }
        
        // preparing some data to display

        $licenseData = $licenseManager->data;
        $remained = round( ( $licenseData['Expired'] - time() ) / (60 * 60 * 24), 2 );
        $isInfinity = empty( $licenseData['Expired']);
        
        // preparing an error data to dispaly
        
        if ( $error ) {
            $parts = explode( ':', $error->get_error_code() );
            $errorSource = $parts[0];
        }
        
        // creating a customer account if it's required

        if ( $response && !$error && ( isset( $licenseManager->data['KeyBound'] ) && !$licenseManager->data['KeyBound'] ) ) {
            $this->redirectToAction('createAccount', array('onp_ref' => 'key-activation'));
        }
        
        if ( $response && !$error && ( isset( $licenseManager->data['KeyBound'] ) && $licenseManager->data['KeyBound'] ) ) {
            $this->redirectToAction('finish');
        }
        
        ?>

        <div class="factory-bootstrap-000 factory-fontawesome-000 onp-page-wrap <?php echo $licenseData['Category'] ?>-license-manager-content" id="license-manager">
            
            <a id="onp-hide-license-manager" href="<?php $this->actionUrl('hideLM') ?>">
                <i class="fa fa-eye-slash"></i><?php _e('Hide License Manager', 'onp_licensing_000') ?>
            </a>

            <?php if ( $error ) { ?>
                <?php $this->showError($error, $scope) ?>
            <?php } else { ?>
                <div class="license-message <?php echo $licenseData['Category'] ?>-license-message">
                    
                    <?php if ($scope == 'delete-key') { ?>
                    <div class="alert alert-normal alert-warning-icon">
                        <strong><?php _e('The key has been deleted successfully.', 'onp_licensing_000') ?></strong>
                        <p><?php _e('Please check the <a href="plugins.php">Plugins</a> page and update the plugin to complete deletion if it\'s needed.', 'onp_licensing_000') ?></p>    
                    </div>
                    <?php } ?>     
                    
                    <?php if ($scope == 'reset-license') { ?>
                    <div class="alert alert-success">
                        <?php _e('Your license details have been reset successfully.', 'onp_licensing_000') ?>
                    </div>
                    <?php } ?>                      

                    <?php if ($scope == 'check-updates') { ?>
                    <div class="alert alert-normal">
                        <strong><?php _e('The updates have been checked successfully.', 'onp_licensing_000') ?></strong>
                        <p>
                        <?php if ( $updatesManager->isActualVersion() ) { ?>
                            <?php _e('You use the actual version of the plugin.', 'onp_licensing_000') ?>
                        <?php } else { ?>
                            <?php if ( $updatesManager->needChangeAssembly() ) { ?>
                            <?php printf( 
                                    __('You need to upgrade to the %1$s version. <a href="plugins.php">Click here</a> to get the update.', 'onp_licensing_000'), $this->plugin->license->build  ) ?>
                            <?php } else { ?>
                            <?php printf( 
                                    __('The %1$s version is available to download. <a href="plugins.php">Click here</a> to get the update.', 'onp_licensing_000'),
                                    $updatesManager->lastCheck['Build'] . '-' . $updatesManager->lastCheck['Version'] ) ?>
                            <?php } ?>
                        <?php } ?>
                        </p>
                    </div>
                    <?php } ?>  
                </div>
            <?php } ?>
            
            <div class="onp-container">
                <div class="license-details">
                    <?php if ( onp_build('free') ) { ?>
                        <?php if ( $licenseManager->hasUpgrade() ) { ?>
                        <a href="<?php onp_licensing_000_purchase_url( $this->plugin ) ?>" id="purchase-premium">
                            <span class="btn btn-gold btn-inner-wrap">
                            <?php if ( !empty( $this->purchasePrice ) ) { ?>
                            <i class="fa fa-star"></i> <?php printf( __('Upgrade to Premium for %1$s', 'onp_licensing_000'), $this->purchasePrice ) ?> <i class="fa fa-star"></i></i>
                            <?php } else { ?>
                            <i class="fa fa-star"></i> <?php _e('Upgrade to Premium', 'onp_licensing_000') ?> <i class="fa fa-star"></i></i>
                            <?php } ?>
                            </span>
                        </a>
                        <?php } ?>
                    <?php } else { ?> 
                        <?php if ( !onp_license('paid') ) { ?>
                        <a href="<?php onp_licensing_000_purchase_url( $this->plugin ) ?>" id="purchase-premium">
                            <span class="btn btn-gold btn-inner-wrap">
                            <?php if ( !empty( $this->purchasePrice ) ) { ?>
                            <i class="fa fa-star"></i> <?php printf( __('Get Key For %1$s Only', 'onp_licensing_000'), $this->purchasePrice ) ?> <i class="fa fa-star"></i></i>
                            <?php } else { ?>
                            <i class="fa fa-star"></i> <?php _e('Get Premium Key', 'onp_licensing_000') ?> <i class="fa fa-star"></i></i>
                            <?php } ?>
                            </span>
                        </a> 
                        <?php } ?> 
                    <?php } ?>   
                    <?php if ( empty( $this->plugin->pluginTitle ) ) { ?>
                    <p><?php _e('Your current license:', 'onp_licensing_000') ?></p>
                    <?php } else { ?>
                    <p><?php printf( __('Your current license for %1$s:', 'onp_licensing_000'), $this->plugin->pluginTitle ) ?></p>   
                    <?php } ?>
                    <div class="license-details-block <?php echo $licenseData['Category'] ?>-details-block">
                        
                        <?php if ( $licenseManager->hasKey() ) { ?>
                        <a href="<?php $this->actionUrl('deleteKey') ?>" class="btn btn-default btn-small license-delete-button"><i class="icon-remove-sign"></i> <?php _e('Delete Key', 'onp_licensing_000') ?></a>
                        <?php } ?>
                        
                        <h3><?php echo $licenseData['Title'] ?></h3>
                        <?php if ( $licenseManager->hasKey() ) { ?>
                        <div class="licanse-key-identity"><?php echo $licenseData['Key'] ?></div>
                        <?php } ?>
                        
                        <div class="licanse-key-description">
                        <?php if ( $licenseManager->data['Build'] !== 'free' ) { ?>
                            <?php if ( $licenseManager->data['Category'] == 'free' ) { ?>
                                <?php if ( $this->codecanyon ) { ?>
                                <p>
                                    <?php _e('Please, activate the plugin to unlock its features. Enter the key you received with the plugin into the form below. Don\'t know where the key is? <a href="#" id="open-faq">Click here</a>.', 'onp_licensing_000') ?>
                                </p>
                                <?php } else { ?>
                                <p>
                                    <?php _e('Please, activate the plugin to unlock its features. Enter the key you received with the plugin into the form below.', 'onp_licensing_000') ?>
                                </p>
                                <?php } ?>
                                <?php if ( $this->trial ) { ?>
                                <p class="activate-trial-hint">
                                    <?php printf( __('Also you can activate a <a href="%1$s">trial version</a> for 7 days to test the plugin on this site.', 'onp_licensing_000'), $this->getActionUrl('activateTrial') ) ?>
                                </p>
                                <?php } ?>
                            <?php } else { ?>
                                <?php echo $licenseData['Description'] ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ( $licenseManager->data['Category'] == 'free' ) { ?>
                                <p><?php _e('Public License is a GPLv2 compatible license allowing you to change and use this version of the plugin for free. Please keep in mind this license covers only free edition of the plugin. Premium versions are distributed with other type of a license.', 'onp_licensing_000') ?>
                                </p>
                                <?php if ( $this->trial ) { ?>
                                <p class="activate-trial-hint">
                                    <?php printf( __('You can <a href="%1$s">activate</a> a premium version for a trial period (7 days).', 'onp_licensing_000'), $this->getActionUrl('activateTrial') ) ?>
                                    <?php printf( __('Or click <a target="_blank" href="%1$s">here</a> to learn more about the premium version.', 'onp_licensing_000'), onp_licensing_000_get_purchase_url( $this->plugin ) ) ?>
                                </p>
                                <?php } ?>
                            <?php } else { ?>
                                <?php echo $licenseData['Description'] ?>
                            <?php } ?>
                        <?php } ?>
                        </div>
                        
                        <table class="license-params" colspacing="0" colpadding="0">
                            <tr>
                                <td class="license-param license-param-domain">
                                    <span class="license-value"><?php echo $this->domain ?></span>
                                    <span class="license-value-name"><?php _e('domain', 'onp_licensing_000') ?></span>
                                </td>   
                                <td class="license-param license-param-version">
                                    <span class="license-value"><?php echo $this->plugin->version ?> <small><?php _e('version', 'onp_licensing_000') ?></small></span>
                                    <?php if ( $updatesManager->isVersionChecked() ) { ?>
                                        <?php if ( $updatesManager->isActualVersion() ) { ?>
                                            <span class="license-value-name"><?php _e('up-to-date', 'onp_licensing_000') ?></span>
                                        <?php } else { ?>
                                            <?php if ( $updatesManager->needChangeAssembly() ) { ?>
                                            <span class="license-value-name">
                                                <a href="plugins.php" class="link-to-upgrade">
                                                <?php printf( __('upgrade to %s', 'onp_licensing_000'), $this->plugin->license->build ) ?>
                                                </a>
                                            </span>    
                                            <?php } else { ?>
                                            <span class="license-value-name">
                                                <a href="plugins.php" class="link-to-upgrade">
                                                <?php echo $updatesManager->lastCheck['Build'] ?>-<?php echo $updatesManager->lastCheck['Version'] ?> <?php _e('available', 'onp_licensing_000') ?>
                                                </a>
                                            </span>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } else { ?>
                                    <span class="license-value-name"><span><?php _e('up-to-date', 'onp_licensing_000') ?></span></span>
                                    <?php } ?>
                                </td>  
                                <td class="license-param license-param-days">
                                    <span class="license-value"><?php echo $licenseManager->data['Build'] ?></span>                                   
                                    <span class="license-value-name"><?php _e('assembly', 'onp_licensing_000') ?></span>
                                </td>
                                <td class="license-param license-param-days">
                                    <?php if ( $licenseManager->isExpired() ) {?>
                                        <span class="license-value"><?php _e('EXPIRED!', 'onp_licensing_000') ?></span>
                                        <span class="license-value-name"><?php _e('please update the key', 'onp_licensing_000') ?></span>
                                    <?php } else { ?>  
                                        <span class="license-value">                               
                                            <?php if ( $isInfinity ) {?><?php _e('infinity', 'onp_licensing_000') ?><?php } else { ?>
                                                <?php echo $remained; ?><small> <?php _e('day(s)', 'onp_licensing_000') ?></small>
                                            <?php } ?>
                                        </span>
                                        <span class="license-value-name"><?php _e('remained', 'onp_licensing_000') ?></span>
                                    <?php } ?>    
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="license-input">
                    <form action="<?php $this->actionUrl("index") ?>" method="post">
                        
                        <?php if ( $this->premium ) { ?>
                            <p><?php _e('Have a key to activate the premium version? Paste it here:', 'onp_licensing_000') ?><p>
                        <?php } else { ?>
                            <p><?php _e('Have a key to activate the plugin? Paste it here:', 'onp_licensing_000') ?><p>
                        <?php } ?>
                            
                        <a href="#" class="btn btn-default" id="license-submit">
                            <?php _e('Submit Key', 'onp_licensing_000') ?>
                        </a>  
                        <div class="license-key-wrap">
                            <input type="text" id="license-key" name="licensekey" value="<?php echo $licenseKey ?>" class="form-control" />
                        </div>
                            
                        <?php if ( $this->premium ) { ?>
                            <p style="margin-top: 10px;">
                                <?php printf( __('<a href="%1$s">Lean more</a> about the premium version and get the license key to activate it now!', 'onp_licensing_000'), onp_licensing_000_get_purchase_url( $this->plugin ) ) ?>
                            </p>
                        <?php } else { ?>
                            <p style="margin-top: 10px;">
                                <?php printf( __('<a href="%1$s">Lean more</a> about this plugin and get the license key to activate it now!', 'onp_licensing_000'), onp_licensing_000_get_purchase_url( $this->plugin ) ) ?>
                            </p>
                        <?php } ?>
                    </form>
                </div>
            </div>
            <div id="plugin-update-block">
                <?php if ( $updatesManager->needCheckUpdates() ) { ?>
                    <?php if ( !$updatesManager->isVersionChecked() ) { ?>
                        <?php if ( isset( $updatesManager->lastCheck['Checked'] ) ) { ?>
                            <?php printf( __('The upadtes were checked at <strong>%1$s</strong>.', 'onp_licensing_000'), date( 'g:i a, j M y', $updatesManager->lastCheck['Checked'] ) ) ?>
                        <?php } else { ?>
                            <?php _e('The updates were checked <strong>never</strong>.', 'onp_licensing_000') ?>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ( $updatesManager->isActualVersion() ) { ?>
                            <?php printf( __('The updates were checked at <strong>%1$s</strong>, you use the up-to-date version. ', 'onp_licensing_000'), date( 'g:i a, j M y', $updatesManager->lastCheck['Checked'] ) ) ?>
                        <?php } else { ?>
                            <?php printf( __('The updates were checked at <strong>%1$s</strong>, <strong>%2$s</strong>. ', 'onp_licensing_000'), date( 'g:i a, j M y', $updatesManager->lastCheck['Checked'] ), $updatesManager->lastCheck['Version'] ) ?>
                        <?php } ?>
                    <?php } ?>
                    <?php printf( __('Click <a href="%1$s">here</a> to check updates manually.', 'onp_licensing_000'), $this->getActionUrl('checkUpdates') ) ?>
                <?php } ?>
                <span class="gray-link">[ <a href="<?php echo $this->getActionUrl('internalKeys') ?>"><?php _e('internal keys', 'onp_licensing_000') ?></a> ]</span>       
            </div>
            
            <?php if ( $this->faq ) { ?>
            
            <div id="faq-block">
                <ul>
                    <li>
                        <a class="faq-header" id="how-to-find-the-key">
                            <?php _e('I purchased the plugin on CodeCanyon, but I cannot find the license key. Where is it?', 'onp_licensing_000') ?>
                        </a>
                        <div>
                            <p>
                                <?php _e('If you purchased the plugin on CodeCanyon, visit your Downloads section and click Licence Certificate.', 'onp_licensing_000') ?>
                                <?php _e('Find Item Purchase Code in the text document and paste it into the form above.', 'onp_licensing_000') ?>
                            </p>
                            <p style="text-align: center;">
                                <img src="<?php echo ONP_LICENSING_000_URL . '/assets/img/how-to-find-key.png' ?>" />
                            </p>
                        </div>
                    </li>
                    <li>
                        <a class="faq-header">
                            <?php _e('The plugin I purchased comes with the bonus. Where I can find a key to activate the bonus?', 'onp_licensing_000') ?>
                        </a>
                        <div>
                            <p>
                                <?php _e('You can activate the bonus plugin via the key you got for the principal plugin.', 'onp_licensing_000') ?>
                            </p>
                        </div>
                    </li>
                    <li>
                        <a class="faq-header">
                            <?php _e('Is it possible to get the premium version without the License Manager?', 'onp_licensing_000') ?>
                        </a>
                        <div>
                            <p>
                                <?php _e('Yes, it\'s possible if you want to distribute the plugin as a part of your product. Please contact us to discuss details: support@onepress-media.com', 'onp_licensing_000') ?>
                            </p>
                        </div>
                    </li>
                    <li>
                        <a class="faq-header">
                            <?php _e('I see the message "... Please install another plugin assembly (premium) ...". How to fix?', 'onp_licensing_000') ?>
                        </a>
                        <div>
                            <p>
                                <?php printf( __('If you upgraded the plugin from the free version up to the premium version, it\'s okay, just visit the page <a href="%s"><strong>Plugins</strong></a> and download the update.', 'onp_licensing_000'), admin_url('plugins.php') ) ?>
                                <?php printf( __('If you have been using the premium version and now would like to use the free version but get this message, <a href="%s"><strong>click here</strong></a> to reset your license details.', 'onp_licensing_000'), $this->getActionUrl('resetLicense') ) ?>
                            </p>
                        </div>
                    </li>                    
                </ul>
            </div>
            
            <?php } ?>

        </div>
        <?php
    }

    /**
     * Show one of the result messages.
     * 
     * @since 3.1.0
     */
    public function finishAction() {
        $ref = isset( $_REQUEST['onp_ref'] ) ? $_REQUEST['onp_ref'] : null;
        
        $updatesManager = $this->plugin->updates;
        $licenseManager = $license = $this->plugin->license;
                
        $email = isset( $_GET['email'] ) ? $_REQUEST['email'] : null;
                
        $urlToRedirect = apply_filters('onp_license_manager_success_redirect_' . $this->plugin->pluginName, $this->getActionUrl('index') );
        $btnText = apply_filters('onp_license_manager_success_button_' . $this->plugin->pluginName, __('Okay, I got it', 'onp_licensing_000') );
        
        ?>
        <div class="factory-bootstrap-000 factory-fontawesome-000 onp-page-wrap onp-single-block" id="finish">

            <div class='onp-header'>  
                <?php if ( $ref == 'trial' || $ref == 'manual-trial-activation' ) { ?>
                    <h4><?php _e('Congratulations! you\'ve successfully activated a trial version', 'onp_licensing_000') ?></h4>
                    <p>
                        <?php printf( __('Thank you for using %s. The plugin is ready.', 'onp_licensing_000'), $this->plugin->options['title'] ) ?>
                    </p>
                <?php } elseif ( $ref == 'binding' ) { ?>    
                    <h4><?php _e('Good job!', 'onp_licensing_000') ?></h4>
                    <p><?php _e('The plugin is ready to use.', 'onp_licensing_000') ?></p>
                <?php } else { ?>
                    <h4><?php _e('Congratulations! you\'ve successfully activated your key', 'onp_licensing_000') ?></h4>
                    <?php if ( $ref !== 'manual-key-activation' ) { ?>
                        <p><?php _e('You can transfer your key to another site at any time via your customer account.', 'onp_licensing_000') ?></p>
                    <?php } ?>
                <?php } ?>
            </div>
            
            <div class="onp-container">
                <?php if ( $ref == 'trial' || $ref == 'manual-trial-activation' ) { ?>
                    <p>
                        <?php _e('Your trial key will expire in ', 'onp_licensing_000') ?>
                        <?php
                            $licenseData = $licenseManager->data;
                            $remained = round( ( $licenseData['Expired'] - time() ) / (60 * 60 * 24), 2 );
                        ?>
                        <?php echo $remained; ?> <?php _e('day(s).', 'onp_licensing_000') ?>
                    </p>
                <?php } elseif ( $ref == 'binding' ) { ?>  
                    <p><?php printf( __('The key has been bound to your account (<strong>%s</strong>).', 'onp_licensing_000'), $email ) ?></p>
                    <p>
                        <?php _e('You can log in to your account at any time to manage your key by using the login details sent you via email earlier:', 'onp_licensing_000') ?>
                        <a href="<?php echo $this->plugin->options['account'] ?>" target="_blank"><?php echo $this->plugin->options['account'] ?></a>
                    </p>
                <?php } else { ?>
                    <p><?php _e('Thank you for activating your license key.', 'onp_licensing_000') ?></p>
                <?php } ?>
                <?php if ( $updatesManager->needChangeAssembly() ) { ?>
                    <p style="background-color: #fafafa; padding: 10px;">
                        <?php _e('Please move to the Plugins page and download the update to complete activation.', 'onp_licensing_000') ?>
                    </p>
                    <div class='onp-actions'>
                        <a href="plugins.php" class="btn btn-lg btn-primary btn-large"><?php _e('Open the Plugins page', 'onp_licensing_000') ?> <i class="fa fa-cloud-download"></i></a>
                    </div>
                <?php } else { ?>   
                    <p><i><?php _e('Please feel free to contact us if you need any help.', 'onp_licensing_000') ?></i></p>
                    <div class='onp-actions'>
                        <a href="<?php echo $urlToRedirect ?>" class="btn btn-lg btn-primary btn-large"><?php echo $btnText ?></a>
                    </div>
                <?php } ?>
            </div>
            
        </div>
        <?php  
    }
    
    // ------------------------------------------------------------------
    // Creating a customer account and binding a license key
    // ------------------------------------------------------------------
    
    /**
     * Creates a customer account.
     * 
     * @since 3.1.0
     */
    public function createAccountAction() {
        $licenseManager = $this->plugin->license;
        
        $currentUser = wp_get_current_user();
        $email = isset( $_REQUEST['email'] ) ? $_REQUEST['email'] : $currentUser->user_email;
        
        $key = $this->plugin->license->key;
        $pluginName = $this->plugin->pluginTitle;
        
        // if true, the congratulation text will be shown
        $ref = isset( $_REQUEST['onp_ref'] ) ? $_REQUEST['onp_ref'] : null;
        
        // if not empty, the error message will be shown
        $error = false;
        
        if ( isset( $_POST['submit'] ) ) {
            $congrats = false;
            $subscribe = isset( $_REQUEST['subscribe'] ) ? true: false;

            if ( empty($email) ) {
                $error = new WP_Error('FORM:EmailEmpty', __('Please enter your email adress to create a customer account.', 'onp_licensing_000'));
            } else {
                $result = $licenseManager->createAccount( $email, $subscribe );    
                if ( is_wp_error( $result ) ) $error = $result;
            }

            // everything is ok, account created
            if ( !$error ) {
                $this->redirectToAction('accountCreated', array( 
                    'email' => rawurlencode( $email ),
                    'cancelCode' => isset( $result['CancelCode'] ) ? $result['CancelCode'] : null,
                    'confirmationId' => isset( $result['ConfirmationId'] ) ? $result['ConfirmationId'] : null          
                ));
            }
   
            if ( $error ) {

                $code = $error->get_error_code();

                // the account for this email is already crated, are you sure to link the key to this account?
                if ( $code === 'API:KeyBinding.CustomerExiststsAreYouSureToBindKey' ) {
                    $this->redirectToAction('accountAlreadyCreated', array('email' => rawurlencode( $email ))); 
                }
                
                // the key already is linked to this email
                if ( $code === 'API:KeyBinding.KeyAlreadyBoundToThisEmail' ) {
                    $this->redirectToAction('finish', array('email' => rawurlencode( $email ), 'onp_ref' => 'binding')); 
                }
            }
        }
 
        ?>
        <form method="POST">
        <div class="factory-bootstrap-000 factory-fontawesome-000 onp-page-wrap onp-single-block" id="create-account">
            
            <?php if ( $ref == 'key-activation' ) { ?>
            <div class='onp-header'>  
                <h4><?php _e('Congratulations! you\'ve successfully activated your key', 'onp_licensing_000') ?></h4>
                <p><?php _e('You can start using the plugin right now, but we recommend you to make one more step ...', 'onp_licensing_000' ) ?></p>
            </div>
            <?php } else { ?>
            <div class='onp-header'>  
                <h4><?php _e('Your license key is not bound to your email address!', 'onp_licensing_000') ?></h4>
            </div>
            <?php } ?>   
            
            <?php $this->showError( $error ); ?>
            
            <?php if ( $ref == 'cancel-account' ) { ?>
            <div class="license-message">
              <div class="alert alert-success">
                  <h4 class="alert-heading"><?php _e('The incorrect account has been canceled successfully', 'onp_licensing_000') ?></h4>
                  <p><?php _e('Please be careful when typing your email address.', 'onp_licensing_000') ?></p>
              </div>    
            </div>
            <?php } ?>

            <div class="onp-container">

                <div class="onp-container-header">
                    <h4><?php _e('It\'s time to protect your license key', 'onp_licensing_000' ) ?></h4>
                    <div class="onp-key-info">
                        <i class="fa fa-key"></i> <?php echo $key ?> (<i><?php echo $pluginName ?></i>)
                    </div>
                </div>

                <p><?php _e('Enter your email address to bind the license key. We will create a customer account for you by using your email address. After that, you will be able to:', 'onp_licensing_000' ) ?></p>
                <ul class='onp-benefits'>
                    <li><?php _e('View where your key is used.', 'onp_licensing_000' ) ?></li>
                    <li><?php _e('Remove the key for sites where it\'s activated.', 'onp_licensing_000' ) ?></li> 
                    <li><?php _e('Transfer the key to another sites.', 'onp_licensing_000' ) ?></li>                     
                </ul>
                
                <div class='onp-form'>
                      <input type='text' class="form-control" name='email' id='email' value='<?php echo $email ?>' placeholder="your email address" />
                      <div class="checkbox">
                          <label>
                              <input type="checkbox" name='subscribe' id='subscribe' checked='checked' value="1" /> 
                              <?php echo sprintf( __('I want to get news regarding the <strong>%s</strong> plugin and exclusive offers from OnePress (a few emails a month).', 'onp_licensing_000'), $this->plugin->pluginTitle ) ?>
                          </label>
                      </div>
                  </div>
                
                <div class='onp-actions'>
                    <input type="submit" class='btn btn-lg btn-large btn-primary' name="submit" value='<?php _e('Protect & Create Account', 'onp_licensing_000' ) ?>' />
                    <?php _e('(The login details will be sent via email)', 'onp_licensing_000' ) ?>
                </div>
            </div>
        </div>
        </form>
        <?php
    }
    
    /**
     * Account exists, are you sure to link the key to this account?
     * @since 3.1.0
     */
    public function accountAlreadyCreatedAction() {
        $email = $_GET['email'];
        $licenseManager = $this->plugin->license;
        $updatesManager = $this->plugin->updates;
                
        // if not empty, the error message will be shown
        $error = false;
        
        if ( isset( $_POST['submit'] ) ) {
            
            if ( empty($email) ) {
                $error = new WP_Error('FORM:EmailEmpty', __('Please enter your email adress.', 'onp_licensing_000'));
            } else {
                $result = $licenseManager->bindKey( $email );    
                if ( is_wp_error( $result ) ) $error = $result;
            }
            
            if ( !$error ) {
                $this->redirectToAction('finish', array('email' => rawurlencode( $email ), 'onp_ref' => 'binding'));
            }
            
            if ( $error ) {
                $code = $error->get_error_code();

                // the key already is linked to this email
                if ( $code === 'API:KeyBinding.KeyAlreadyBoundToThisEmail' ) {
                    $this->redirectToAction('finish', array('email' => rawurlencode( $email ), 'onp_ref' => 'binding')); 
                }
            }
        }
        
        ?>
        <form method="POST">
        <div class="factory-bootstrap-000 factory-fontawesome-000 onp-page-wrap onp-single-block" id="account-already-created">
            
            <div class='onp-header'>
                <h4><?php _e('A customer with the specified email already registered. Are you sure to bind your key to this email?', 'onp_licensing_000') ?></h4>
            </div>
            
            <?php $this->showError( $error ); ?>
    
            <div class="onp-container">
                <p><?php _e( 'On all probability, you created a customer account in past to bind another key. If  it\'s your email, no problem, just click the confirmation button below.', 'onp_licensing_000') ?><p>
                <p><?php _e( 'If it\'s not email (you entered  your email incorrectly), return back.', 'onp_licensing_000') ?>
                <p><?php _e('Email:', 'onp_licensing_000') ?> <strong style='font-size: 16px;'><?php echo $email ?></strong>
                <p><?php printf( __('You can try to log in via this email <a href="%s" target="_blank">here</a>.', 'onp_licensing_000'), $this->plugin->options['account'] ) ?></p>
                <div class='onp-actions'>
                    <input type="submit" class='btn btn-lg btn-large btn-primary' name="submit" value='<?php _e('Yes, this is my email', 'onp_licensing_000') ?>' />
                    <a href='<?php $this->actionUrl('createAccount', array('email' => $email )) ?>' class='btn btn-lg btn-large btn-default'><?php _e('No, return back', 'onp_licensing_000') ?></a>
                </div>
            </div>
            
        </div>
        </form>
        <?php
    }
    
    /**
     * Shows the success message about created account.
     */
    public function accountCreatedAction() {
        $email = $_GET['email'];
        $cancelCode = isset( $_GET['cancelCode'] ) ? $_GET['cancelCode'] : null;
        $confirmationId = isset( $_GET['confirmationId'] ) ? $_GET['confirmationId'] : null;
        
        $error = false;
        $code = isset( $_GET['code'] ) ? $_GET['code'] : null;
        $message = isset( $_GET['message'] ) ? $_GET['message'] : null; 
        if ( $code && $message ) $error = new WP_Error($code, base64_decode($message));
        
        ?>
        <div class="factory-bootstrap-000 factory-fontawesome-000 onp-page-wrap onp-single-block" id="account-created">

            <div class='onp-header'>
                <h4><?php _e('Your customer account has been successfully created!', 'onp_licensing_000') ?></h4>
                <p><?php _e('We have sent the login details and the confirmation link to your email address.', 'onp_licensing_000' ) ?></p>
            </div>
            
            <?php $this->showError( $error ); ?>
            
            <div class="onp-container">

                <p>
                    <?php _e('Please perform these simple final steps:', 'onp_licensing_000') ?>
                </p>
                
                <div class="onp-steps">
                    <p class="onp-step">
                        <strong class="onp-num">1</strong>
                        <span class="onp-desc"><?php printf( __('Visit your email box <strong>%s</strong>.', 'onp_licensing_000'), $email ) ?></span>
                    </p>
                    <p class="onp-step">
                        <strong class="onp-num">2</strong>
                        <span class="onp-desc"><?php printf( __('Confirm your email address by clicking the link inside the email we sent.', 'onp_licensing_000'), $email ) ?></span>
                    </p>
                </div>
                
                 <div class='onp-actions'>
                     <a href="<?php $this->actionUrl('finish', array('onp_ref' => 'binding', 'email' => rawurlencode( $email ))) ?>" class='btn btn-lg btn-large btn-primary'><?php _e('Okay, done', 'onp_licensing_000') ?></a>
                    <a class="onp-cancel" href="<?php $this->actionUrl('cancelAccount', array('email' => rawurlencode( $email ), 'cancelCode' => $cancelCode, 'confirmationId' => $confirmationId) ) ?>">
                        <i class="fa fa-trash-o"></i> <?php _e("I've mistaken. It's not my email address.", 'onp_licensing_000') ?>
                    </a>
                </div>
            </div>
            
        </div>
        <?php
    }
    
    /**
     * Cancels the recently created account.
     */
    public function cancelAccountAction() {
        $email = $_GET['email'];
        $cancelCode = isset( $_GET['cancelCode'] ) ? $_GET['cancelCode'] : null;
        $confirmationId = isset( $_GET['confirmationId'] ) ? $_GET['confirmationId'] : null;
        
        $licenseManager = $this->plugin->license;
                
        $result = $licenseManager->cancelAccount( $cancelCode, $confirmationId );    
        if ( is_wp_error( $result ) ) $error = $result;

        if ( !$error ) {
            $this->redirectToAction('createAccount', array('onp_ref' => 'cancel-account'));
        }

        // retrun back if it's not possible to cancel the account
        if ( $error ) {
            $code = $error->get_error_code();
            $this->redirectToAction('accountCreated', array(
                'code' => $code,     
                'message' => base64_encode( $error->get_error_message( $code ) ), 
                'email' => rawurlencode( $email ), 
                'cancelCode' => $cancelCode, 
                'confirmationId' => $confirmationId
            ));
        }
    }
    
    /**
     * Show error from got from the License Server.
     * 
     * @since 1.0.5
     * @param WP_Error $error An error to show.
     */
    private function showError( $error, $action = null ) {
        if ( empty( $error ) ) return;
        
        $parts = explode( ':', $error->get_error_code() );
        $errorSource = $parts[0];
        $errorCode = $parts[1];
                
        ?>
            <div class="license-message">
                <?php if ( $errorSource == 'API' ) { ?>
                <div class="alert alert-danger">
                    <h4 class="alert-heading"><?php _e('The request has been rejected by the Licensing Server', 'onp_licensing_000') ?></h4>
                    <p><?php echo $error->get_error_message() ?></p>
                </div>    
                <?php } elseif ( $errorSource == 'HTTP' ) { ?>
                <div class="alert alert-danger">
                    <h4 class="alert-heading"><?php _e('Unable to connect to the Licensing Server', 'onp_licensing_000') ?></h4>
                    <p><?php echo $error->get_error_message() ?></p>
                    <p>
                    <?php if ($action == 'submit-key' ) {      
                          printf( 
                              __('Please <a href="%s">click here</a> for trying to activate your key manualy.', 'onp_licensing_000'),
                              $this->getActionUrl('activateKeyManualy', array('key' => $_POST['licensekey'] ))
                          );
                      } elseif ($action == 'trial') {
                          printf( 
                              __('Please <a href="%s">click here</a> for trying to activate your trial manualy.', 'onp_licensing_000'),
                              $this->getActionUrl('activateTrialManualy') 
                          );
                      } elseif ($action == 'delete-key') {
                          printf( 
                              __('Please <a href="%s">click here</a> for trying to delete key manualy.', 'onp_licensing_000'),
                              $this->getActionUrl('deleteKeyManualy')
                          );
                      } else {
                          ?>
                        <i><?php _e('Please contact OnePress support if you need to perform this action.', 'onp_licensing_000') ?></i> 
                          <?php
                      } ?>
                      </p>
                </div>
                <?php } else { ?>
                <div class="alert alert-danger">
                    <h4 class="alert-heading"><?php _e('Unable to apply the specified key', 'onp_licensing_000') ?></h4>
                    <p><?php echo $error->get_error_message() ?></p>
                </div>  
                <?php } ?>
            </div>
        <?php
    }
    
    // ------------------------------------------------------------------
    // Hiding/Showing License Manager
    // ------------------------------------------------------------------
    
    public function hideLMAction() {
        $nounce = wp_create_nonce('hidelm');
        $optionName = 'onp_lm_' . $this->plugin->pluginName . '_' . $nounce;
        
        if ( isset( $_GET['onp_nounce'] ) && $_GET['onp_nounce'] == $nounce ) {
            update_option($optionName, $nounce);
            wp_redirect(admin_url('edit.php?post_type=opanda-item') );
            exit;
        }
        
        ?>
        <div class="wrap factory-fontawesome-000">
            <h2><?php _e('Hiding License Manager', 'onp_licensing_000') ?></h2>
            
            <div class="factory-bootstrap-000" >
                <div class="margin-top: 0px; margin-bottom: 30px;">
                    <p style="margin: 0px;"><?php _e('<strong>Warning!</strong> The License Manager will get inaccessible when you click the button "Hide License Manager".', 'onp_licensing_000') ?></p>
                    <p style="margin: 10px 0 0 0;"><?php printf( __('To restore access to the License Manager, you will need to clear the option <strong>%s</strong> on the page <strong>options.php</strong>:', 'onp_licensing_000'), $optionName ) ?></p
                    <p style="margin: 0px;">
                        <a href="<?php echo admin_url( "options.php" ) ?>"><?php echo admin_url( "options.php" ) ?></a>
                    </p>
                </div>
                <div style="margin-top: 20px;">
                    <a href="<?php $this->actionUrl("hideLM", array('onp_nounce' => $nounce)) ?>" class="btn btn-danger"><?php _e('Hide License Manager', 'onp_licensing_000') ?></a>
                    <a href="<?php $this->actionUrl("index") ?>" class="btn btn-default" style="margin-left: 5px;"><?php _e('Return Back', 'onp_licensing_000') ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    // ------------------------------------------------------------------
    // The rest actions
    // ------------------------------------------------------------------
    
    /**
     * Resets the current license details.
     */
    public function resetLicenseAction() {
        
        $licenseManager = $this->plugin->license;
        $licenseManager->resetLicense(true);
        
        $this->redirectToAction('index', array('scope' => 'reset-license'));
    }
    
    /**
     * Deletes a current key.
     */
    public function deleteKeyAction() {
        
        $licenseManager = $license = $this->plugin->license;
        if ( $licenseManager->hasKey()) {
            $error = $licenseManager->deleteKey();
            
            if (is_wp_error($error)) {
                $this->redirectToAction('index', array(
                    'scope' => 'delete-key', 
                    'code' => $error->get_error_code(), 
                    'message' => base64_encode( $error->get_error_message())));
            }
            $this->redirectToAction('index', array('scope' => 'delete-key'));
        }
        
        $this->indexAction(); 
    }   
    
    /**
     * Trys to activate the trial license and then redirect to the index page.
     */
    public function activateTrialAction() {
        $licenseManager = $this->plugin->license;
        $response = $licenseManager->activateTrial();   
        
        if (is_wp_error($response)) {
            $this->redirectToAction('index', array(
                'scope' => 'trial', 
                'code' => $response->get_error_code(), 
                'message' => urlencode( base64_encode( $response->get_error_message()))) );
        }
        
        $this->redirectToAction('finish', array('onp_ref' => 'trial'));
    }
    
    /**
     * Allows to activate a key manualy.
     */
    public function activateKeyManualyAction() {
        $licenseManager = $this->plugin->license;

        if ( isset( $_POST['response'] ) ) {
            $error = $licenseManager->activateKeyManualy( $_POST['response'] );
            if (is_wp_error($error)) {
                $this->redirectToAction('index', array(
                    'scope' => 'submit-key', 
                    'code' => $error->get_error_code(), 
                    'message' => urlencode( base64_encode( $error->get_error_message()))) );
            }
            $this->redirectToAction('finish', array('onp_ref' => 'manual-key-activation'));
        } else {
            $this->requestUrl = $licenseManager->getLinkToActivateKey( $_GET['key'] );  
        }
        
        ?>
        <div class="factory-bootstrap-000 onp-page-wrap" id="activate-key-manual">
            <form action="<?php $this->actionUrl('activateKeyManualy') ?>" method="post">
            <div class="onp-container">
                <h2 style="margin-bottom: 10px;"><?php _e('Key Activation', 'onp_licensing_000') ?></h2>
                <p style="margin-top: 0px;"><?php _e('Please perform the following steps to activate the plugin manually.', 'onp_licensing_000') ?></p>
                <ul>
                    <li>
                        1. <?php printf( __('<a href="%s" target="_blank">Click here</a> to get an activation code.', 'onp_licensing_000'), $this->requestUrl ) ?>
                    </li>
                    <li>
                        2. <?php _e('Copy the code and paste it below, then submit the form:', 'onp_licensing_000') ?>
                        <textarea name="response" class="license-reponse-code" placeholder="<?php _e('Activation Code', 'onp_licensing_000') ?>"></textarea>
                    </li>
                </ul>
                <a href="#" class="btn btn-primary" id="manual-trial-submit">
                    <i class="icon-ok-sign icon-white"></i> <?php _e('Verify Code', 'onp_licensing_000') ?>
                </a>  
            </div>
            </form>
        </div>
        <?php
    }   
    
    /**
     * Deletes a current key.
     */
    public function deleteKeyManualyAction() {
        $licenseManager = $this->plugin->license;
        $this->requestUrl = $licenseManager->getLinkToDeleteKey();

        if ( isset( $_POST['response'] ) ) {
            $error = $licenseManager->deleteKeyManualy( $_POST['response'] );
            
            if (is_wp_error($error)) {
                $this->redirectToAction('index', array(
                    'scope' => 'delete-key', 
                    'code' => $error->get_error_code(), 
                    'message' => urlencode( base64_encode( $error->get_error_message()))) );
            }
            $this->redirectToAction('index', array('scope' => 'delete-key', 'code' => 'ok'));
        }
        
        ?>
        <div class="factory-bootstrap-000 onp-page-wrap" id="activate-key-manual">
            <form action="<?php $this->actionUrl('deleteKeyManualy') ?>" method="post">
            <div class="onp-container">
                <h2 style="margin-bottom: 10px;"><?php _e('Key Deactivation', 'onp_licensing_000') ?></h2>
                <p style="margin-top: 0px;"><?php _e('Please perfome the following steps to activate the plugin manualy.', 'onp_licensing_000') ?></p>
                <ul>
                    <li>
                        1. <?php printf( __('<a href="%s" target="_blank">Click here</a> to send the deactivation request.', 'onp_licensing_000'), $this->requestUrl ) ?>
                    </li>
                    <li>
                        2. <?php _e('Copy the code from the field on the site and paste it below, then submit the form:', 'onp_licensing_000') ?>
                        <textarea name="response" class="license-reponse-code" placeholder="<?php _e('Response code from the Licensing Server', 'onp_licensing_000') ?>"></textarea>
                    </li>
                </ul>
                <a href="#" class="btn btn-primary" id="manual-trial-submit">
	                <?php _e('<i class="icon-ok-sign icon-white"></i> verify code', 'onp_licensing_000'); ?>
                </a>  
            </div>
            </form>
        </div>
        <?php
    }   
    
    public function activateTrialManualyAction() {
        $licenseManager = $this->plugin->license;
        
        $this->requestUrl = $licenseManager->getLinkToActivateTrial();

        if ( isset( $_POST['response'] ) ) {
            $error = $licenseManager->activateKeyManualy( $_POST['response'] );
            
            if (is_wp_error($error)) {
                $this->redirectToAction('index', array(
                    'scope' => 'trial', 
                    'code' => $error->get_error_code(), 
                    'message' => urlencode( base64_encode( $error->get_error_message()))) );
            }
            $this->redirectToAction('finish', array('onp_ref' => 'manual-trial-activation'));
        }
        
        ?>
        <div class="factory-bootstrap-000 onp-page-wrap" id="activate-key-manual">
            <form action="<?php $this->actionUrl('activateTrialManualy') ?>" method="post">
            <div class="onp-container">
                <h2 style="margin-bottom: 10px;"><?php _e('Trial Activation', 'onp_licensing_000'); ?></h2>
                <p style="margin-top: 0px;"><?php _e('Please perfome the following steps to activate the plugin manualy.', 'onp_licensing_000'); ?></p>
                <ul>
                    <li>
                        1. <?php printf( __('<a href="%s" target="_blank">Click here</a> to send the activation request.', 'onp_licensing_000'), $this->requestUrl ) ?>
                    </li>
                    <li>
                        2. <?php _e('Copy the code from the field on the site and paste it below, then submit the form:', 'onp_licensing_000') ?>
                        <textarea name="response" class="license-reponse-code" placeholder="<?php _e('Response code from the Licensing Server', 'onp_licensing_000') ?>"></textarea>
                    </li>
                </ul>
                <a href="#" class="btn btn-primary" id="manual-trial-submit">
                    <?php _e('<i class="icon-ok-sign icon-white"></i> verify code', 'onp_licensing_000'); ?>
                </a>  
            </div>
            </form>
        </div>
        <?php
    }
    
    public function checkUpdatesAction() {
        $error = $this->plugin->updates->checkUpdates();
        
        if (is_wp_error($error)) {
            $this->redirectToAction('index', array(
                'scope' => 'check-updates', 
                'code' => $error->get_error_code(), 
                'message' => urlencode( base64_encode( $error->get_error_message()))) );
        }
        $this->redirectToAction('index', array('scope' => 'check-updates'));
    }
    
    public function internalKeysAction( $sender = 'index' ) {
        $licenseManager = $this->plugin->license;
        
        $saved = false;
        
        if ( isset( $_POST['site_secret'] ) ) {
            update_option('onp_site_secret', trim( $_POST['site_secret'] ));
            $saved = true;
        }
        
        $siteSecret = get_option('onp_site_secret', null);
        $keySecret = ( !empty($licenseManager->data ) && isset($licenseManager->data['KeySecret']) ) 
                ? $licenseManager->data['KeySecret'] 
                : '';
        
        ?>
        <div class="wrap ">
            <h2><?php _e('License Manager Internal Keys', 'onp_licensing_000'); ?></h2>
            <p style="margin-top: 0px; margin-bottom: 30px;"><?php _e('You actually don\'t need to change something here. Please change the values below only if OnePress supports ask you to do it.', 'onp_licensing_000'); ?></p>
            
            <div class="factory-bootstrap-000" style="max-width: 800px;">
                <form method="post" class="form-horizontal" action="<?php echo $this->actionUrl('internalKeys') ?>">

                <div>
                
                    <?php if ( $saved ) { ?>
                    <div class="alert alert-success" style="margin-bottom: 25px;">
                        <?php _e('The changes has been saved successfully!', 'onp_licensing_000'); ?>
                    </div>
                    <?php } ?>
                    
                    <?php if ( $sender == 'reset' ) { ?>
                    <div class="alert alert-success" style="margin-bottom: 25px;">
                        <?php _e('The license has been reset successfully!', 'onp_licensing_000'); ?>
                    </div>
                    <?php } ?>                    
                    
                    <div class="form-group control-group">
                        <label class="control-label col-sm-2" for="site_secret"><?php _e('Site Secret', 'onp_licensing_000'); ?></label>
                        <div class="controls col-sm-10">
                            <input type="text" name="site_secret" id="site_secret" value="<?php echo $siteSecret ?>" class="form-control" />
                        </div>
                    </div> 
                    
                    <div class="form-group control-group">
                        <label class="control-label col-sm-2" for="key_secret"><?php _e('Key Secret', 'onp_licensing_000'); ?></label>
                        <div class="controls col-sm-10">
                            <input type="text" name="key_secret" id="key_secret" value="<?php echo $keySecret ?>" class="form-control" />
                        </div>
                    </div>           
               
                    <div class="form-group form-actions">
                      <div class="col-sm-offset-2 col-sm-10">
                        <input name="save-action" class="btn btn-primary" type="submit" value="<?php _e('Save changes', 'onp_licensing_000'); ?>"/>
                      </div>
                    </div>

                    <div style="clear: both;"></div>
                </div>


            </form>
            </div>
        </div>    
        <?php
    }
}