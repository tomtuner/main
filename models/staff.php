<?php

class Staff extends Model
{
	public $custom_config = array
	(
		'database' => 'ccl',
		'table' => 'directory',
		'sort' => 'last_name',
	);
	
	protected $rules = array(
		'id' => array(
			'type' => 'int',
			'error' => 'Invalid category id.',
		),
	);
	
	
	public static function getList($conditions = '') 
	{
		return parent::getList(__CLASS__, $conditions);
	}
}

?>
