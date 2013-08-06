<?php

class Detailsclubs extends Model
{
	public $custom_config = array
	(
		'database' => 'newclubs',
		'table' => 'club_profile',
	);
	
	public static function getList($conditions = '')
	{
		return parent::getList(__CLASS__, $conditions);
	}
}

?>
