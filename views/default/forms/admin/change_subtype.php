<?php
$type = elgg_extract('entity_type', $vars, 'object');

if (!in_array($type, SubtypeChangeTool::getTypes())) {
	return true;
}

echo elgg_view('input/hidden', array(
	'name' => 'entity_type',
	'value' => $type,
));
?>
<div>
	<label><?php echo elgg_echo('change_subtype:source_subtype') ?>
		<?php
		echo elgg_view('input/dropdown', array(
			'name' => 'source_subtype',
			'options' => SubtypeChangeTool::getSubtypes($type)
		));
		?>
	</label>
</div>

<div>
	<label><?php echo elgg_echo('change_subtype:target_subtype') ?>
		<?php
		echo elgg_view('input/text', array(
			'name' => 'target_subtype',
		));
		?>
	</label>
</div>

<div>
	<label>
		<?php
		echo elgg_view('input/checkbox', array(
			'name' => 'remove_source_subtype',
			'default' => false,
			'value' => 1,
			'checked' => true,
		));
		echo elgg_echo('change_subtype:remove_source_subtype');
		?>
	</label>
</div>

<div class="elgg-foot">
	<?php
	echo elgg_view('input/submit', array(
		'class' => 'elgg-requires-confirmation',
		'rel' => elgg_echo('change_subtype:warning')
	));
	?>
</div>