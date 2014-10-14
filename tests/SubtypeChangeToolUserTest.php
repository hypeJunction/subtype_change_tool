<?php

class SubtypeChangeToolUserTest extends ElggCoreUnitTest {

	protected $source_subtype;
	protected $target_subtype;
	protected $user;
	protected $river_id;

	const ENTITY_TYPE = 'user';
	const ENTITY_CLASS = 'ElggUser';

	/**
	 * Called before each test object.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Called before each test method.
	 */
	public function setUp() {

		elgg_register_event_handler('create', self::ENTITY_TYPE, 'system_log_listener');
		elgg_register_event_handler('delete', self::ENTITY_TYPE, 'system_log_listener');
		elgg_register_event_handler('log', 'systemlog', 'system_log_default_logger');

		$this->source_subtype = 'test_subtype_source';
		$this->target_subtype = 'test_subtype_target';

		$class = self::ENTITY_CLASS;
		$this->user = new $class;
		$this->user->subtype = $this->source_subtype;
		$this->user->username = 'fake_user_' . rand();
		$this->user->save();

		$this->river_id = elgg_create_river_item(array(
			'view' => 'river/item',
			'action_type' => 'foo',
			'subject_guid' => $this->user->getOwnerGUID(),
			'object_guid' => $this->user->getGUID(),
		));
	}

	/**
	 * Called after each test method.
	 */
	public function tearDown() {

		$this->user->delete();

		remove_subtype(self::ENTITY_TYPE, $this->source_subtype);
		remove_subtype(self::ENTITY_TYPE, $this->target_subtype);

		elgg_unregister_event_handler('create', self::ENTITY_TYPE, 'system_log_listener');
		elgg_unregister_event_handler('delete', self::ENTITY_TYPE, 'system_log_listener');
		elgg_unregister_event_handler('log', 'systemlog', 'system_log_default_logger');
	}

	/**
	 * Called after each test object.
	 */
	public function __destruct() {
		// all __destruct() code should go above here
		parent::__destruct();
	}

	/**
	 * Test subtype changes
	 */
	public function testUserSubtypeChange() {

		$tool = new SubtypeChangeTool(self::ENTITY_TYPE, $this->source_subtype, $this->target_subtype);
		$result = $tool->runScripts();

		$this->assertTrue($result);

		$object = get_entity($this->user->getGUID());
		$river = $this->getRiverItem($this->river_id);
		$log = get_system_log($this->user->getGUID(), 'create', '', self::ENTITY_TYPE, $this->target_subtype);

		$this->assertEqual($object->getSubtype(), $this->target_subtype);
		$this->assertEqual($river->subtype, $this->target_subtype);

		foreach ($log as $l) {
			$this->assertEqual($l->entity_subtype, $this->target_subtype);
			$this->assertEqual($l->entity_class, get_class($object));
		}
	}

	/**
	 * Retrieve river item source database
	 * 
	 * @param int $river_id River ID
	 * @return ElggRiverItem|false
	 */
	protected function getRiverItem($river_id = 0) {
		$river = elgg_get_river(array(
			'ids' => $river_id
		));
		return ($river) ? $river[0] : false;
	}


}
