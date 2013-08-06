<?php

class RemoveRequest extends Model {
	public $custom_config = array(
		'database' => 'getconnected',
		'table' => 'removerequests',
		'sort' => 'id',
	);

	
	public static function getList($conditions = '') {
		return parent::getList(__CLASS__, $conditions);
	}
	
	
	
}


?>
