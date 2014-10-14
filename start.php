<?php

/**
 * Admin tool for bulk changing entity subtypes
 *
 * @package    Elgg
 * @subpackage elgg_subtype_change
 */
elgg_register_event_handler('init', 'system', 'subtype_change_tool_init');

/**
 * Initialize the plugin
 * @return void
 */
function subtype_change_tool_init() {

	elgg_register_library('db_explorer', dirname(__FILE__) . '/lib/db_explorer.php');

	// Register actions
	$actions_path = dirname(__FILE__) . "/actions/admin/";
	elgg_register_action('admin/change_subtype', $actions_path . 'change_subtype.php', 'admin');

	if (elgg_is_admin_logged_in()) {

		// Add an admin menu item
		elgg_register_menu_item('page', array(
			'name' => 'change_subtype',
			'href' => 'admin/developers/change_subtype',
			'text' => elgg_echo('admin:developers:change_subtype'),
			'context' => 'admin',
			'section' => 'develop'
		));
	}

	elgg_register_plugin_hook_handler('unit_test', 'system', 'subtype_change_tool_test');
}

/**
 * Add test location
 *
 * @param string $hook   'unit_test'
 * @param string $type   'system'
 * @param array  $return array of test locations
 * @param string $params additional params
 * @return array
 */
function subtype_change_tool_test($hook, $type, $return, $params) {

	$return[] = __DIR__ . '/tests/SubtypeChangeToolObjectTest.php';
	$return[] = __DIR__ . '/tests/SubtypeChangeToolGroupTest.php';
	$return[] = __DIR__ . '/tests/SubtypeChangeToolUserTest.php';
	return $return;
}
