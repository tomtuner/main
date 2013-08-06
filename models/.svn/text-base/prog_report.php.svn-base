<?php

class ProgReport extends Model {
	public $custom_config = array(
		'table' => 'progreports',
		'database' => 'getconnected',
		'sort' => 'id',
	);

	
	
	public static function getList($conditions = '') {
		return parent::getList(__CLASS__, $conditions);
	}
	
	/*
	* getNumEventsSponsor( $id )
	* This function returns the number of events an organization ($id) has sponsored in the DB
	*/
	public function getNumEventsSponsor($id)
	{
		$sum = 0;
		$events = ProgReport::getList("org_id = $id");
		
		foreach( $events as $event)
		{
			if($event->getSpon() == 1)
			{
				$sum++;
			}
		}
		
		return $sum;
		
	}
	
	/*
	* getNumEventsAttend( $id )
	* This function returns the number of events an organization ($id) has attended in the DB
	*/
	public function getNumEventsAttend($id)
	{
		$sum = 0;
		$events = ProgReport::getList("org_id = $id");
		
		foreach( $events as $event)
		{
			if($event->getSpon() == 0)
			{
				$sum++;
			}
		}
		
		return $sum;
		
	}
	
	
}


?>
