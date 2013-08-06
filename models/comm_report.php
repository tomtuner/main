<?php

class CommReport extends Model {
	public $custom_config = array(
		'table' => 'commreports',
		'database' => 'getconnected',
		'sort' => 'id',
	);

	
	
	public static function getList($conditions = '') {
		return parent::getList(__CLASS__, $conditions);
	}
	
	/*
	* getNumEvents( $id )
	* This function returns the number of events an organization ($id) has in the DB
	*/
	public function getNumEvents($id)
	{
		$events = CommReport::getList("org_id = $id");
		return count($events);
		
	}
	
	/*
	* getTotalHours( $id )
	* This function returns the total number of hours an organization ($id) has in the DB
	*/
	public function getTotalHours($id)
	{
		$sum = 0;
		$events = CommReport::getList("org_id = $id");
		foreach( $events as $event)
		{
			$sum+=$event->getHours();
		}
		return $sum;
	}
}


?>
