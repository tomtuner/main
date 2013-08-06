<?php

class Department extends Model 
{
	public $custom_config = array
	(
		'database' => 'dev_ccl',
		'sort' => 'title',
	);
	
	protected $rules = array(
		'id' => array(
			'type' => 'int',
			'error' => 'Invalid category id.',
		),
		'title' => array(
			'type' => 'string',
			'error' => 'Invalid name.',
		),
	);
	
	
	public static function getList($conditions = '') 
	{
		return parent::getList(__CLASS__, $conditions);
	}
	
}


?>
