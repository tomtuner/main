<?php

class Building extends Model {
	public $custom_config = array(
		'database' => 'rit',
		'table' => 'rit_building',
		'primary_key' => 'building_id',
	);
	protected $rules = array(
		'building_name' => array(
			'type' => 'string',
			'error' => 'Invalid name.',
		),
	);
	protected $relationships = array(
		'Room' => 'has_many',
	);

	public function getName() {
		return $this->data['building_name'];
	}
}

?>
