<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Transliteration core class
 *
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @copyright (c) 19.02.2018, Webcraftic
 */
class WCACHE_Plugin extends Wbcr_Factory000_Plugin {

	/**
	 * @see self::app()
	 * @var Wbcr_Factory000_Plugin
	 */
	private static $app;

	/**
	 * @since  1.1.0
	 * @var array
	 */
	private $plugin_data;

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
	public function __construct($plugin_path, $data)
	{
		parent::__construct($plugin_path, $data);

		self::$app = $this;
		$this->plugin_data = $data;

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
	 * @return \Wbcr_Factory000_Plugin|\WCTR_Plugin
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
		$this->registerPage('WCACHE_CachePage', WCACHE_PLUGIN_DIR . '/admin/pages/class-pages-performance-cache.php');
	}

	/**
	 * @throws \Exception
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	private function admin_scripts()
	{
		$this->init_activation();
		$this->register_pages();
	}

	private function global_scripts()
	{
		require_once WCACHE_PLUGIN_DIR . '/includes/includes/helpers.php';
		require_once WCACHE_PLUGIN_DIR . '/includes/cache.php';

		if( is_admin() ) {
			require(WCACHE_PLUGIN_DIR . '/admin/boot.php');
			if( class_exists('WCL_CachePage') ) {
				WCL_Plugin::app()->registerPage('WCL_CacheProPage', WCACHE_PLUGIN_DIR . '/admin/pages/class-pages-cache.php');
			}
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

