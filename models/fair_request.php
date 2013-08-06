<?php
class FairRequest extends Model {
	public $custom_config = array(
		'database' => 'newclubs',
		'table' => 'fair_requests',
	);
	
	public static function getList($conditions = '')  {
		return parent::getList(__CLASS__, $conditions);
	}
}
?>