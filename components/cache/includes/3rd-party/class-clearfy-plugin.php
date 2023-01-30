<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Clearfy cache
 *
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @copyright (c) 2018 Webraftic Ltd
 * @version       1.0
 */
class WCACHE_Plugin {

	/**
	 * @see self::app()
	 * @var WCL_Plugin
	 */
	private static $app;

	/**
	 * Конструктор
	 *
	 * Применяет конструктор родительского класса и записывает экземпляр текущего класса в свойство $app.
	 * Подробнее о свойстве $app см. self::app()
	 *
	 * @param string $plugin_path
	 * @param array  $data
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		if( !class_exists('WCL_Plugin') ) {
			throw new Exception('Plugin Clearfy is not installed!');
		}

		self::$app = WCL_Plugin::app();

		$this->global_scripts();

		if( is_admin() ) {
			$this->admin_scripts();
		}
	}

	/**
	 * Статический метод для быстрого доступа к интерфейсу плагина.
	 *
	 * Позволяет разработчику глобально получить доступ к экземпляру класса плагина в любом месте
	 * плагина, но при этом разработчик не может вносить изменения в основной класс плагина.
	 *
	 * Используется для получения настроек плагина, информации о плагине, для доступа к вспомогательным
	 * классам.
	 *
	 * @return WCL_Plugin
	 */
	public static function app()
	{
		return self::$app;
	}

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.0
	 */
	protected function init_activation()
	{
		include_once(WCACHE_PLUGIN_DIR . '/admin/activation.php');
		self::app()->registerActivation('WCACHE_Activation');
	}

	/**
	 * @throws \Exception
	 * @since  1.0.0
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	private function register_pages()
	{
		self::app()->registerPage('WCACHE_CachePage', WCACHE_PLUGIN_DIR . '/admin/pages/class-pages-performance-cache.php');
		self::app()->registerPage('WCL_CacheProNginxRulesPage', WCACHE_PLUGIN_DIR . '/admin/pages/class-pages-nginx-rules.php');
	}

	/**
	 * @throws \Exception
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	private function admin_scripts()
	{
		require(WCACHE_PLUGIN_DIR . '/admin/boot.php');

		$this->init_activation();
		$this->register_pages();
	}

	private function global_scripts()
	{
		require_once WCACHE_PLUGIN_DIR . '/includes/helpers.php';
		require_once WCACHE_PLUGIN_DIR . '/includes/cache.php';

		if( self::app()->getPopulateOption('enable_cache') ) {
			if( self::app()->getPopulateOption('widget_cache') ) {
				require_once WCACHE_PLUGIN_DIR . "/includes/widget-cache.php";

				WCL_WidgetCache::action();
			}
		}

		if( self::app()->getPopulateOption('cache_mobile_theme') ) {
			require_once WCACHE_PLUGIN_DIR . '/includes/mobile-cache.php';
		}

		add_filter('wbcr/clearfy/adminbar_menu_items', function ($menu_items) {
			$menu_items['clearfy-clear-all-cache'] = [
				'id' => 'clearfy-clear-all-cache',
				'title' => '<span class="dashicons dashicons-update"></span> ' . __('Clear all cache', 'clearfy'),
				'href' => esc_url(add_query_arg('wclearfy_cache_delete', '1'))
			];

			return $menu_items;
		});
	}
}