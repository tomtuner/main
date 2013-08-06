<?php

class OrganizationCategory extends Model {
	public $custom_config = array(
		'database' => 'organizations',
		'table' => 'categories',
		'sort' => 'name',
	);
	protected $rules = array(
		'name' => array(
			'type' => 'string',
			'error' => 'Invalid name.',
		),
	);
	protected $relationships = array(
		'Organization' => array(
			'type' => 'has_many',
			'key' => 'category_id',
		),
	);

	public static function getList() {
		return parent::getList(__CLASS__, 'id != 4');
	}
}

?>
