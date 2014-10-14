<?php

foreach (SubtypeChangeTool::getTypes() as $type) {
	if (count(SubtypeChangeTool::getSubtypes($type))) {
		echo elgg_view_module('main', elgg_echo("change_subtype:$type"), elgg_view_form('admin/change_subtype', array(), array('entity_type' => $type)));
	}
}


