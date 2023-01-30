<?php
/**
 * The page Settings.
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCL_CacheProNginxRulesPage extends WBCR\Factory_Templates_000\Pages\PageBase {

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 *
	 * @see   FactoryPages000_AdminPage
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $id = "clearfy_cache_nginx_rules";

	/**
	 * @var string
	 */
	public $page_parent_page = "none";

	/**
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-clock';

	/**
	 * @var bool
	 */
	//public $internal = false;
	
	/**
	 * @var string
	 */
	public $type = 'page';

	public $available_for_multisite = true;

	/**
	 * Show on the page a search form for search options of plugin?
	 *
	 * @since  2.2.0 - Added
	 * @var bool - true show, false hide
	 */
	public $show_search_options_form = false;

	/**
	 * @param WCL_Plugin $plugin
	 */
	public function __construct(WCL_Plugin $plugin)
	{
		$this->menu_title = __('Nginx Rules', 'clearfy');

		parent::__construct($plugin);

		$this->plugin = $plugin;
	}

	public function getPageTitle()
	{
		return __('Nginx Rules', 'clearfy');
	}

	public function showPageContent()
	{
		?>
		<div class="form-group">
			<h2><?php
				_e('Nginx Configuration Rules', 'clearfy') ?></h2>
			<p>
				<?php
				_e('It enables NGINX to directly serve previously cached files without calling WordPress or any PHP. It also adds headers to cached CSS, JS, and images via browser cache.', 'clearfy') ?>
			</p>
			<p>
				<?php
				_e('Unfortunately, our plugin does not have the ability to automatically configure the nginx server. You have to copy the rules below and paste into your server config file.', 'clearfy') ?>
			</p>
			<p>
				<?php
				_e('Copy and paste the following code snippet in the server { } block of “default” file (or whatever the file is being used) located in /etc/nginx/sites-enabled/. Make sure to remove the existing location / { } block before using the one in the following code snippet.', 'clearfy') ?>
			</p>
			<div class="control-group">
				<textarea row="50" cols="100" style="height: 500px;">
					<?php
					echo $this->generate_base_nginx_rules();
					echo $this->generate_browser_cache_rules();
					echo $this->generate_gzip_rules();
					?>
				</textarea>
			</div>
		</div>
		<?php
	}

	protected function generate_base_nginx_rules()
	{
		$rules = "
#####
#CLEARFY CACHE RULES
#
set \$clearfy_bypass 1;				# Should NGINX bypass WordPress and call cache file directly ?
set \$condition '';

# Do not bypass if it's a POST request
if (\$request_method = POST) {
	set \$clearfy_bypass 0;
	set \$clearfy_reason \"POST request\";
	set \$condition \"null\";
}

# Do not bypass if arguments are found (e.g. ?page=2)
if (\$is_args) {
	set \$clearfy_bypass 0;
	set \$clerfy_reason \"Arguments found\";
	set \$condition \"null\";
}

# Do not bypass if the site is in maintenance mode
if (-f \"\$document_root/.maintenance\") {
	set \$clearfy_bypass 0;
	set \$clearfy_reason \"Maintenance mode\";
	set \$condition \"null\";
}

# Do not bypass if one of those cookie if found
# wordpress_logged_in_[hash] : When a user is logged in, this cookie is created (we'd rather let WP-Rocket handle that)
# wp-postpass_[hash] : When a protected post requires a password, this cookie is created.
if (\$http_cookie ~* \"(wordpress_logged_in_|wp\-postpass_|woocommerce_items_in_cart|woocommerce_cart_hash|wptouch_switch_toogle|comment_author_|comment_author_email_)\") {
	set \$clearfy_bypass 0;
	set \$clearfy_reason \"Cookie\";
	set \$condition \"null\";
}

set \$fullurl '/wp-content/cache/all\${condition}';
#CACHE ENDING


location / {
	set \$serve_url \$fullurl\${uri}index.html;
	add_header X-Clearfy-Cache-Location \$serve_URL;
	try_files \$serve_url \$uri \$uri/ /index.php\$is_args\$args;
}

# Debug header (when file is not cached)
add_header X-Clearfy-Nginx-Serving-Static \$clearfy_is_bypassed;
add_header X-Clearfy-Nginx-Reason \$clearfy_reason;
add_header X-Clearfy-Nginx-File \$clearfy_file;
";

		return $rules;
	}

	private function generate_gzip_rules()
	{
		$rules = '
#####
# BROWSER CSS CACHE
#
location ~ /wp-content/cache/all/.*html$ {
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_buffers 16 8k;
    gzip_http_version 1.1;
    gzip_types image/svg+xml text/plain text/html text/xml text/css text/javascript application/xml application/xhtml+xml application/rss+xml application/javascript application/x-javascript application/x-font-ttf application/vnd.ms-fontobject font/opentype font/ttf font/eot font/otf;
}
';

		return $rules;
	}

	private function generate_browser_cache_rules()
	{

		$rules = '
#####
# BROWSER CSS CACHE
#
location ~* \.css$ {
	etag on;
	gzip_vary on;
	expires 30d;
}';

		$rules .= '
####
# BROWSER JS CACHE
#
location ~* \.js$ {
	etag on;
	gzip_vary on;
	expires 30d;	
}';

		$rules .= '
####
# BROWSER MEDIA CACHE
#
location ~* \.(ico|gif|jpe?g|png|svg|eot|otf|woff|woff2|ttf|ogg)$ {
	etag on;
	expires 30d;
}';

		return $rules;
	}
}