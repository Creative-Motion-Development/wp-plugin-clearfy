<?php
/**
 * Подключение файлов из категории 3rd-party
 *
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 20.01.2021, CreativeMotion
 * @version 1.0
 */

add_filter('wbcr_factory_000_form_items', function ($forms_groups, $name) {

	require_once(WCL_PLUGIN_DIR . '/includes/classes/3rd-party/class-base.php');
	require_once(WCL_PLUGIN_DIR . '/includes/classes/3rd-party/plugins/class-wp-rocket.php');
	require_once(WCL_PLUGIN_DIR . '/includes/classes/3rd-party/class-form-entity.php');

	$form_entities = \Clearfy\ThirdParty\Form_Entity::recursive_search_controls($forms_groups);

	if( !empty($form_entities) ) {
		foreach($form_entities as $control) {
			$wp_rocket_no_conflict = new \Clearfy\ThirdParty\Wp_Rocket();

			if( $wp_rocket_no_conflict->is_enabled_mapped_option($control->get_name()) ) {
				$control->make_control_disabled();
				$control->modify_control_hint(__('WARNING! You cannot enable this option as you are using a similar option in the wp rocket plugin. Using the same functionality in both plugins can lead to conflicts. To enable this option, you must disable a similar option in the wp rocket plugin.', 'clearfy'));
			}
		}
	}

	return $forms_groups;
}, 10, 2);
