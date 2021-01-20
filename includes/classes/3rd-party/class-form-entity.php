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

class Form_Entity {

	private $control = [];

	public function __construct(array &$control)
	{
		$this->control = &$control;
	}


	/**
	 * Extracted options from a nested array
	 * @param array $options
	 * @return array
	 * @since 2.2.0
	 */
	public static function recursive_search_controls(&$options)
	{
		$extracted_options = [];

		foreach($options as &$option) {
			if( isset($option['items']) ) {
				$extracted_options = array_merge($extracted_options, static::recursive_search_controls($option['items']));
			} else {
				if( static::is_control($option) ) {
					$extracted_options[] = new static($option);
				}
			}
		}

		return $extracted_options;
	}

	public function get_name()
	{
		if( !isset($this->control['name']) ) {
			return null;
		}

		return $this->control['name'];
	}

	protected static function is_control(array $item)
	{
		return isset($item['type']) && isset(\Wbcr_FactoryForms000_Manager::$registered_controls[$item['type']]);
	}

	/**
	 * Returns true if a given item is an control holder item.
	 *
	 * @param mixed[] $item
	 * @return bool
	 * @since 1.0.0
	 */
	protected static function is_control_holder(array $item)
	{
		return isset($item['type']) && isset(\Wbcr_FactoryForms000_Manager::$registered_holders[$item['type']]);
	}

	/**
	 * Returns true if a given item is html markup.
	 *
	 * @param mixed[] $item
	 * @return bool
	 * @since 1.0.0
	 */
	protected static function is_custom_element(array $item)
	{
		return isset($item['type']) && isset(\Wbcr_FactoryForms000_Manager::$registered_custom_elements[$item['type']]);
	}

	public function make_control_disabled()
	{
		$this->control['cssClass'] = ['factory-checkbox-disabled disabled'];
	}

	public function modify_control_hint($message)
	{
		unset($this->control['layout']['hint-type']);
		unset($this->control['layout']['hint-icon-color']);

		$this->control['layout']['column-left'] = '4';
		$this->control['layout']['column-right'] = '8';

		$old_hint = !empty($this->control['hint']) ? $this->control['hint'] . "<br><br>" : "";
		$this->control['hint'] = $old_hint . '<span style="display:block; font-size:12px; color:#d48a6f;">' . $message . '</span>';
	}
}