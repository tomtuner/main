<?php

class Room extends Model {
	public $custom_config = array(
		'database' => 'rit',
		'table' => 'rit_room',
		'primary_key' => 'room_id',
		'sort' => 'to_string',
	);
	protected $rules = array(
		'building_id' => array(
			'type' => 'int',
			'error' => 'Invalid building id.',
		),
		'room_name' => array(
			'type' => 'string',
			'error' => 'Invalid name.',
		),
		'etc' => array(
			'type' => 'bool',
		),
	);
	protected $relationships = array(
		'Building' => array(
			'type' => 'belongs_to',
			'key' => 'building_id',
		),
	);

	public function __toString() {
		return $this->getBuilding()->getName() .': '. $this->getName();
	}

	public function getName() {
		return ($this->data['room_name']) ? $this->data['room_name'] : $this->data['room_number'];
	}

	public function getDiagram() {
		return false;
	}

	public static function getList() {
		return parent::getList(__CLASS__);
	}
}

?>
