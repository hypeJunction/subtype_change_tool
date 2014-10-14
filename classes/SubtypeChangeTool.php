<?php

/**
 * Change entity subtypes
 *
 * @package    Elgg
 * @subpackage elgg_subtype_change
 */
class SubtypeChangeTool {

	protected $type;
	protected $source_subtype;
	protected $target_subtype;
	protected $source_subtype_id;
	protected $target_subtype_id;
	protected $target_class;
	protected $dbprefix;

	/**
	 * Construct a new tool object
	 *
	 * @param string      $type         Entity type
	 * @param string|null $source_subtype Source entity subtype
	 * @param string      $target_subtype   Target entity subtype
	 * @throws Exception
	 * @return SubtypeChangeTool
	 */
	function __construct($type, $source_subtype, $target_subtype) {

		if (!in_array($type, self::getTypes())) {
			throw new Exception(elgg_echo('Subtypes can only be changed for registered entity types'));
		}

		if ($source_subtype && !in_array($source_subtype, self::getSubtypes($type))) {
			throw new Exception(elgg_echo("SubtypeChangeTool expects a valid registered source $type subtype ($source_subtype given)"));
		}

		if (!$target_subtype || !is_string($target_subtype)) {
			throw new Exception(elgg_echo("SubtypeChangeTool expects a valid registered target $type subtype ($source_subtype given)"));
		}

		$this->type = sanitize_string($type);
		$this->source_subtype = sanitize_string($source_subtype);
		$this->target_subtype = sanitize_string($target_subtype);

		$this->source_subtype_id = add_subtype($this->type, $this->source_subtype);
		$this->target_subtype_id = add_subtype($this->type, $this->target_subtype);

		$this->target_class = $this->getTargetClass();

		$this->dbprefix = elgg_get_config('dbprefix');
	}

	/**
	 * Run sql scripts
	 * @return boolean
	 */
	function runScripts() {

		$result = $this->alterEntitiesTable();

		if ($result) {
			$result_river = $this->alterRiverTable();
			$result_log = $this->alterSystemLogTable();
		}

		return ($result_river && $result_log);
	}

	/**
	 * Update subtype references in entities table
	 * @return boolean
	 */
	protected function alterEntitiesTable() {
		$query = "UPDATE {$this->dbprefix}entities SET subtype=$this->target_subtype_id
			WHERE type='$this->type' AND subtype=$this->source_subtype_id";
		return update_data($query);
	}

	/**
	 * Update subtype references in river table
	 * @return boolean
	 */
	protected function alterRiverTable() {
		$query = "UPDATE {$this->dbprefix}river SET subtype='$this->target_subtype'
			WHERE type='$this->type' AND subtype='$this->source_subtype'";
		return update_data($query);
	}

	/**
	 * Update subtype references in system log table
	 * @return boolean
	 */
	protected function alterSystemLogTable() {
		$query = "UPDATE {$this->dbprefix}system_log SET object_subtype='$this->target_subtype', object_class='$this->target_class'
			WHERE object_type='$this->type' AND object_subtype='$this->source_subtype'";
		return update_data($query);
	}

	/**
	 * Get entity class associated with the target type/subtype pair
	 * @return string
	 */
	function getTargetClass() {
		$target_class = get_subtype_class($this->type, $this->target_subtype);
		if (!$target_class) {
			switch ($this->type) {
				default :
				case $this->type :
					return 'ElggObject';
				case 'group' :
					return 'ElggGroup';
				case 'user' :
					return 'ElggUser';
				case 'site' :
					return 'ElggSite';
			}
		}
	}

	/**
	 * Get types from the subtypes table
	 * @return array
	 */
	static function getTypes() {

		$dbprefix = elgg_get_config('dbprefix');

		$types = array();

		$query = "SELECT DISTINCT(type) FROM {$dbprefix}entity_subtypes";
		$data = get_data($query);

		if ($data) {
			foreach ($data as $row) {
				$types[] = $row->type;
			}
		}

		return $types;
	}

	/**
	 * Get registered entity subtypes
	 * 
	 * @param string $type Entity type
	 * @return array
	 */
	static function getSubtypes($type = 'object') {

		$dbprefix = elgg_get_config('dbprefix');

		$subtypes = array();

		$type = sanitize_string($type);
		$query = "SELECT DISTINCT(subtype) FROM {$dbprefix}entity_subtypes WHERE type='$type'";
		$data = get_data($query);

		if ($data) {
			foreach ($data as $row) {
				$subtypes[] = $row->subtype;
			}
		}

		return $subtypes;
	}

}
