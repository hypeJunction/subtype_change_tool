<?php

$type = get_input('entity_type');
$source_subtype = get_input('source_subtype');
$target_subtype = get_input('target_subtype');

try {
	$tool = new SubtypeChangeTool($type, $source_subtype, $target_subtype);
	$tool->runScripts();

	if (get_input('remove_source_subtype')) {
		remove_subtype($type, $source_subtype);
	}
} catch (Exception $ex) {
	register_error($ex->getMessage());
	forward(REFERER);
}

system_message(elgg_echo('change_subtype:success'));
forward(REFERER);
