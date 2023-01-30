<?php

class WCL_WidgetCache {

	public static function action()
	{
		add_filter('widget_display_callback', array("WCL_WidgetCache", "create_cache"), 10, 3);
	}

	public static function add_filter_admin()
	{
		add_filter('widget_update_callback', array("WCL_WidgetCache", "widget_update"), 5, 3);
		add_action('in_widget_form', array("WCL_WidgetCache", 'in_widget_form'), 5, 3);
	}

	public static function in_widget_form($widget, $return, $instance)
	{
		$wclearfynot = isset($instance['wclearfynot']) ? $instance['wclearfynot'] : '';

		?>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id('wclearfynot'); ?>" name="<?php echo $widget->get_field_name('wclearfynot'); ?>" <?php checked(true, $wclearfynot); ?> />
			<label for="<?php echo $widget->get_field_id('wclearfynot'); ?>">
				<?php _e('Don\'t cache this widget'); ?>
			</label>
		</p>
		<?php
	}

	public static function widget_update($instance, $new_instance)
	{
		WCL_Cache_Helpers::rm_folder_recursively(WCL_Cache_Helpers::getWpContentDir("/cache/wclearfy-widget-cache/"));

		if( isset($new_instance['wclearfynot']) ) {
			$instance['wclearfynot'] = 1;
		} else {
			if( isset($instance['wclearfynot']) ) {
				unset($instance['wclearfynot']);
			}
		}

		return $instance;
	}

	public static function create_cache($instance, $widget, $args)
	{
		if( !isset($args["widget_id"]) || !$args["widget_id"] ) {
			return $instance;
		}

		if( $instance === false ) {
			return $instance;
		}

		// to return instance if not to cache widget
		if( isset($instance["wclearfynot"]) ) {
			return $instance;
		}

		// to exclude WooCommerce Product Categories automatically if show_children_only has been set
		if( isset($instance["show_children_only"]) ) {
			return $instance;
		}

		// to exclude fixed widget Q2W3 Fixed Widget
		if( isset($instance["q2w3_fixed_widget"]) ) {
			return $instance;
		}

		// to exclude Ninja Forms
		if( preg_match("/^ninja_forms_widget/i", $args["widget_id"]) ) {
			return $instance;
		}

		// to exclude WPML Multilingual Language Switcher
		if( preg_match("/^icl_lang_sel_widget/i", $args["widget_id"]) ) {
			return $instance;
		}

		// to exclude Yuzo Related Posts
		if( preg_match("/^yuzo_widget/i", $args["widget_id"]) ) {
			return $instance;
		}

		// to exclude Amazon Affiliate for WordPress
		if( preg_match("/^aawp_widget_/i", $args["widget_id"]) ) {
			return $instance;
		}

		// Flagman theme
		if( preg_match("/^ct_slider_widget_/i", $args["widget_id"]) ) {
			return $instance;
		}

		// to exclude woocommerce product filter
		if( preg_match("/^woof_widget/i", $args["widget_id"]) ) {
			return $instance;
		}

		// to exclude woocommerce product filter
		if( preg_match("/^woocommerce_price_filter/i", $args["widget_id"]) ) {
			return $instance;
		}

		// to exclude Bridge Woocommerce Dropdown Cart
		if( preg_match("/^woocommerce-dropdown-cart/i", $args["widget_id"]) ) {
			return $instance;
		}

		// to exclude woocommerce product filter
		// https://mihajlovicnenad.com/product-filter/
		if( preg_match("/^prdctfltr/i", $args["widget_id"]) ) {
			return $instance;
		}

		$create_cache = false;
		$path = WCL_Cache_Helpers::getWpContentDir("/cache/wclearfy-widget-cache/" . $args["widget_id"] . ".html");

		//to get cache
		if( file_exists($path) ) {
			if( $data = @file_get_contents($path) ) {
				echo $data;

				return false;
			}
		}

		//to get the content of Widget
		ob_start();
		$widget->widget($args, $instance);
		$cached_widget = ob_get_clean();

		//to create cache
		if( $cached_widget ) {
			if( !is_dir(WCL_Cache_Helpers::getWpContentDir("/cache/wclearfy-widget-cache")) ) {
				if( @mkdir(WCL_Cache_Helpers::getWpContentDir("/cache/wclearfy-widget-cache"), 0755, true) ) {
					$create_cache = true;
				}
			} else {
				$create_cache = true;
			}

			//to exclude the widgets which contains nonce value
			//<input type="hidden" id="poll_1_nonce" name="wp-polls-nonce" value="fdd28cece7" />
			if( preg_match("/<input[^\>]+hidden[^\>]+nonce[^\>]+>/", $cached_widget) || preg_match("/<input[^\>]+nonce[^\>]+hidden[^\>]+>/", $cached_widget) ) {
				$create_cache = false;
			}

			if( $create_cache ) {
				@file_put_contents($path, $cached_widget);
			}
		}

		echo $cached_widget;

		return false;
	}
}

?>