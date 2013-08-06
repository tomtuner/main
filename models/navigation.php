<?php

class Navigation extends Model {
	public $custom_config = array(
		'database' => 'dev_ccl',
		'sort' => 'parent, weight',
	);
	protected $rules = array();
	
	public static function getList($conditions = '') {
		return parent::getList(__CLASS__, $conditions);
	}
	
	/*
	* getParentName()
	* instead of an id number (like what's in the database) you get the acutal label of the parent of a given nav item
	* if the nav item is a tthe top level, returns "None"
	*/
	public function getParentName()
	{
		$return;
		$whosYourDaddy = $this->getParent();
		if ($whosYourDaddy != 0) 
		{
			$temp = new Navigation($whosYourDaddy);
			$return = $temp->getLabel();
		}
		else
		{
			$return = "None";
		}
		
		return $return;
	}
	/*
	* getParentUrl()
	* simple getter for parentUrl
	*/
	public function getParentUrl()
	{
		$return;
		$whosYourDaddy = $this->getParent();
		if ($whosYourDaddy != 0) 
		{
			$temp = new Navigation($whosYourDaddy);
			$return = $temp->getUrl();
		}
		else
		{
			$return = $this->getUrl();
		}
		
		return $return;
	}
	
	/*
	* getFullUrl()
	* This method returns the full url of either a child or parent
	* parent's full url = parentUrl
	* child's full url = parentUrl/childUrl
	*/
	public function getFullUrl()
	{
		$return = "";
		$whosYourDaddy = $this->getParent();
		if ($whosYourDaddy != 0) 
		{
			$temp = new Navigation($whosYourDaddy);
			$return .= $temp->getUrl().'/';
			$return .= $this->getUrl();
		}
		else
		{
			$return = $this->getUrl();
		}
		
		return $return;
	}
	
	/*
	* getOrderedList()
	* This method returns all the navigations in the order in which they will be seen in the menu
	* that is Parent->Child with weights for each
	*/
	public function getOrderedList()
	{
		$navigations = Navigation::getList();
		$orderedNavigations;
		
		foreach($navigations as $myNav)
		{
			//find parents
			if($myNav->getParent() == 0)
			{
				//after parent found add to orderedNavigations
				$orderedNavigations[] = $myNav;
				$currParent = $myNav->getId();
				
				//find the kids!
				foreach($navigations as $myKid)
				{
					if($myKid->getParent() == $currParent)
					{
						$orderedNavigations[] = $myKid;
					}
				}
			}
		}
			
		return $orderedNavigations;
	}
	
}


?>
