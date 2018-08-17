<?php #comp-page builds: premium

	/**
	 * Updates for altering the table used to store statistics data.
	 * Adds new columns and renames existing ones in order to add support for the new social buttons.
	 */
	class WCL_Update010300 extends Wbcr_Factory000_Update {

		public function install()
		{
			// Deactivate components for code minification, if alternative plugins are installed
			// -------------
			$minify_js_plugins = array(
				'autoptimize/autoptimize.php',
				'fast-velocity-minify/fvm.php',
				'js-css-script-optimizer/js-css-script-optimizer.php',
				'merge-minify-refresh/merge-minify-refresh.php',
				'wp-super-minify/wp-super-minify.php'
			);

			$is_activate_minify_js = true;
			foreach($minify_js_plugins as $m_plugin) {
				if( is_plugin_active($m_plugin) ) {
					$is_activate_minify_js = false;
				}
			}

			if( $is_activate_minify_js ) {
				WCL_Plugin::app()->deactivateComponent('minify_and_combine');
				WCL_Plugin::app()->deactivateComponent('html_minify');
			} else {
				/**
				 * Migration for the component html minify
				 */

				if( WCL_Plugin::app()->getOption('html_minify') ) {
					WCL_Plugin::app()->activateComponent('html_minify');
					WCL_Plugin::app()->updateOption('html_optimize', 1);
				} else {
					WCL_Plugin::app()->deactivateComponent('html_minify');
				}
			}

			WCL_Plugin::app()->deleteOption('html_minify');
			WCL_Plugin::app()->deleteOption('minify_javascript');
			WCL_Plugin::app()->deleteOption('minify_html_comments');
			WCL_Plugin::app()->deleteOption('minify_html_xhtml');
			WCL_Plugin::app()->deleteOption('minify_html_relative');
			WCL_Plugin::app()->deleteOption('minify_html_scheme');
			WCL_Plugin::app()->deleteOption('minify_html_utf8');

			/**
			 * Migration for the component Hide login page
			 */
			$hide_wp_admin = WCL_Plugin::app()->getOption('hide_wp_admin');
			$hide_login_path = WCL_Plugin::app()->getOption('hide_login_path');
			$login_path = WCL_Plugin::app()->getOption('login_path');
			$old_login_path = WCL_Plugin::app()->getOption('old_login_path');
			$login_recovery_code = WCL_Plugin::app()->getOption('login_recovery_code');

			if( $hide_wp_admin ) {
				update_option('wbcr_hlp_hide_wp_admin', $hide_wp_admin);
			}
			if( $hide_login_path ) {
				update_option('wbcr_hlp_hide_login_path', $hide_login_path);
			}
			if( $login_path ) {
				update_option('wbcr_hlp_login_path', $login_path);
			}
			if( $old_login_path ) {
				update_option('wbcr_hlp_old_login_path', $old_login_path);
			}
			if( $login_recovery_code ) {
				update_option('wbcr_hlp_login_recovery_code', $login_recovery_code);
			}

			WCL_Plugin::app()->deleteOption('hide_wp_admin');
			WCL_Plugin::app()->deleteOption('login_path');
			WCL_Plugin::app()->deleteOption('hide_login_path');
			WCL_Plugin::app()->deleteOption('old_login_path');
			WCL_Plugin::app()->deleteOption('login_recovery_code');
			
			if( WCL_Plugin::app()->getOption('remove_style_version') ) {
				WCL_Plugin::app()->updateOption('disable_remove_style_version_for_auth_users', 1);
			}
			if( WCL_Plugin::app()->getOption('set_last_modified_headers') ) {
				WCL_Plugin::app()->updateOption('disable_frontpage_last_modified_headers', 1);
			}
		}
	}
