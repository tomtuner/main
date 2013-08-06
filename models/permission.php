<?php

class Permission extends Model 
{
	public $custom_config = array
	(
		'database' => 'dev_ccl',
		'sort' => 'id',
	);

	
	public static function getList($conditions = '') 
	{
		return parent::getList(__CLASS__, $conditions);
	}
	
	


?>
