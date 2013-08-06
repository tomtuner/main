<?php

class Position extends Model {
	public $custom_config = array(
		'database' => 'getconnected',
		'sort' => 'id',
	);

	
	
	public static function getList($conditions = '') {
		return parent::getList(__CLASS__, $conditions);
	}
	
}


?>
