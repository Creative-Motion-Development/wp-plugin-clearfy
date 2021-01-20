<?php
/**
 * Базовый класс для реализации совместимости со сторонними плагинами
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 12.01.2021, CreativeMotion
 * @version 1.0
 */

namespace Clearfy\ThirdParty;

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

abstract class Base {

	protected $map_options = [];
	protected $defualt_disabled_options = [];

	private $_disabled_clearfy_options = [];

	/**
	 * Получить значение опции конфликтующего плагина
	 * @param string $option_name Имя опции
	 * @return mixed
	 */
	public abstract function get_3rd_plugin_option($option_name);

	public function is_clearfy_option_mapped($option_name)
	{
		return isset($this->map_options[$option_name]);
	}

	public function is_3rd_plugin_option_mapped($option_name)
	{
		return in_array($option_name, $this->map_options);
	}

	public function is_enabled_mapped_option($option_name)
	{
		if( $this->is_clearfy_option_mapped($option_name) ) {
			return $this->is_enabled_3rd_plugin_option($this->map_options[$option_name]);
		}

		return in_array($option_name, (array)$this->defualt_disabled_options);
	}

	public function is_enabled_clearfy_option($option_name)
	{
		return \WCL_Plugin::app()->getPopulateOption($option_name);
	}

	/**
	 * Включена ли опция конфликтующего плагина?
	 * @param string $option_name Имя опции
	 * @return bool - true, если включена, false если нет
	 */
	public function is_enabled_3rd_plugin_option($option_name)
	{
		$option_value = $this->get_3rd_plugin_option($option_name);

		return !is_null($option_value) && $option_value;
	}

	/**
	 * Деактивирует опции в клеарфи, соответствующие карте опций
	 *
	 * Если к примеру в wp rocket включена опции минификации скриптов, то соотвественно
	 * в Clearfy мы деактивируем опцию минификации скриптов.
	 */
	public function disable_clearfy_options()
	{
		if( !empty($this->map_options) ) {
			foreach((array)$this->map_options as $clearfy_option_name => $thirdparty_plugin_option_name) {
				if( $this->is_enabled_clearfy_option($clearfy_option_name) && $this->is_enabled_3rd_plugin_option($thirdparty_plugin_option_name) ) {
					$this->disable_clearfy_option($clearfy_option_name);
				}
			}
		}
		if( !empty($this->defualt_disabled_options) ) {
			foreach((array)$this->defualt_disabled_options as $option_name) {
				if( $this->is_enabled_clearfy_option($clearfy_option_name) ) {
					$this->disable_clearfy_option($option_name);
				}
			}
		}

		$this->save_options();
	}

	private function disable_clearfy_option($option_name)
	{
		$this->_disabled_clearfy_options[] = $option_name;
	}


	private function save_options()
	{
		foreach($this->_disabled_clearfy_options as $option_name) {
			\WCL_Plugin::app()->updatePopulateOption($option_name, 0);
		}
	}
}