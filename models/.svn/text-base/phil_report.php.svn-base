<?php

class PhilReport extends Model {
	public $custom_config = array(
		'table' => 'philreports',
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
		$events = PhilReport::getList("org_id = $id");
		return count($events);
		
	}
	
	/*
	* getTotalDollars( $id )
	* This function returns the total number of dollars(money) an organization ($id) has in the DB
	*/
	public function getTotalDollars($id)
	{
		$sum = 0;
		$events = PhilReport::getList("org_id = $id");
		foreach( $events as $event)
		{
			$sum+=$event->getDollars();
		}
		return $sum;
	}
}


?>
