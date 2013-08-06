<?php

class ReportController extends GetconnectedController {
	public function index() {
		global $db;
		$page_title = array('RIT - getConnected');
		
		// if we are still here, then $organizations is an array of orgs
		// that this user is the president of.
		$organization = $this->organization;
		require('views/reports/reportIndex.html');
	}
	
	public function community()
	{
		global $db;
		$page_title = array('RIT - getConnected');
		
		
		// if we are still here, then $organizations is an array of orgs
		// that this user is the president of.
		$organization = $this->organization;
		$orgIdNum = $organization->getId();
		$numReports = CommReport::getNumEvents($organization->getId());
		$numHours = CommReport::getTotalHours($organization->getId());
		$commEvents = CommReport::getList();
		$last3 = "";
		
		//LIMIT 3 in sql no work, so we just take top 3 and add to new array
		if(sizeof($commEvents)>=3)
		{
		$myCommEvents[]=$commEvents[sizeof($commEvents)-1];
		$myCommEvents[]=$commEvents[sizeof($commEvents)-2];
		$myCommEvents[]=$commEvents[sizeof($commEvents)-3];
		}
		else
		{
			$myCommEvents = $commEvents;
		}
		
		foreach ($myCommEvents as $commEvent)
		{
			//filter for events not in this organization
			if($commEvent->getOrgId()==$orgIdNum)
			{
				$last3.=
				"<tr>
					<td>".$commEvent->getName()."</td>
					<td>".$commEvent->getAttend()."</td>
					<td>".$commEvent->getHours()."</td>
					<td>".substr($commEvent->getDate(),0,10)."</td>
				</tr>";
			}
		}
		require('views/reports/community.html');
	}
	
	public function addCommReport()
	{
		global $db;
		$page_title = array('RIT - getConnected');
		
		$commReport = new CommReport();
		$commReport->setName($_POST['name']);
		$commReport->setDescr($_POST['desc']);
		$commReport->setAttend($_POST['attended']);
		$commReport->setHours($_POST['hours']*$_POST['attended']);
		$commReport->setOrgId($this->organization->getId());
		$commReport->setDate($_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']);
		$commReport->save();
		
		//go back to start page
		$this->redirect("report/community");
	}
	
	public function philanthropy()
	{
		global $db;
		$page_title = array('RIT - getConnected');
		
		// if we are still here, then $organizations is an array of orgs
		// that this user is the president of.
		$organization = $this->organization;
		$numReports = PhilReport::getNumEvents($organization->getId());
		$numDollars = PhilReport::getTotalDollars($organization->getId());
		$orgIdNum = $organization->getId();
		$philEvents = PhilReport::getList();
		$last3 = "";
		
		//LIMIT 3 in sql no work, so we just take top 3 and add to new array
		if(sizeof($philEvents)>=3)
		{
			$myPhilEvents[]=$philEvents[sizeof($philEvents)-1];
			$myPhilEvents[]=$philEvents[sizeof($philEvents)-2];
			$myPhilEvents[]=$philEvents[sizeof($philEvents)-3];
		}
		else
		{
			$myPhilEvents = $philEvents;
		}
		
		foreach ($myPhilEvents as $philEvent)
		{
			//filter for events not in this organization
			if($philEvent->getOrgId()==$orgIdNum)
			{
				$last3.=
				"<tr>
					<td>".$philEvent->getName()."</td>
					<td>".$philEvent->getAttend()."</td>
					<td>".$philEvent->getDollars()."</td>
					<td>".substr($philEvent->getDate(),0,10)."</td>
				</tr>";
			}
		}
		require('views/reports/philanthropy.html');
	}
	
	public function addPhilReport()
	{
		global $db;
		$page_title = array('RIT - getConnected');
		
		$philReport = new PhilReport();
		$philReport->setName($_POST['name']);
		$philReport->setDescr($_POST['desc']);
		$philReport->setAttend($_POST['attended']);
		$philReport->setDollars($_POST['dollars']);
		$philReport->setOrgId($this->organization->getId());
		$philReport->setDate($_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']);
		$philReport->save();
		
		//go back to start page
		$this->redirect("report/philanthropy");
	}
	
	public function programming()
	{
		global $db;
		$page_title = array('RIT - getConnected');
		
		// if we are still here, then $organizations is an array of orgs
		// that this user is the president of.
		$organization = $this->organization;
		$numSpon = ProgReport::getNumEventsSponsor($organization->getId());
		$numAtt = ProgReport::getNumEventsAttend($organization->getId());
		$orgIdNum = $organization->getId();
		$progEvents = ProgReport::getList();
		$last3 = "";
		$sponsor;
		
		//LIMIT 3 in sql no work, so we just take top 3 and add to new array
		if(sizeof($progEvents)>=3)
		{
			$myProgEvents[]=$progEvents[sizeof($progEvents)-1];
			$myProgEvents[]=$progEvents[sizeof($progEvents)-2];
			$myProgEvents[]=$progEvents[sizeof($progEvents)-3];
		}
		else
		{
			$myProgEvents = $progEvents;
		}
		
		foreach ($myProgEvents as $progEvent)
		{
			//filter for events not in this organization
			if($progEvent->getOrgId()==$orgIdNum)
			{
				if($progEvent->getSpon() == 1)
				{
					$sponsor = "Sponsored";
				}
				else
				{
					$sponsor = "Attended";
				}
				$last3.=
				"<tr>
					<td>".$progEvent->getName()."</td>
					<td>".$progEvent->getAttend()."</td>
					<td>".$progEvent->getType()."</td>
					<td>".$sponsor."</td>
					<td>".substr($progEvent->getDate(),0,10)."</td>
				</tr>";
			}
		}
		require('views/reports/programming.html');
	}
	
	public function addProgReport()
	{
		global $db;
		$page_title = array('RIT - getConnected');
		
		$progReport = new ProgReport();
		$progReport->setName($_POST['name']);
		$progReport->setDescr($_POST['desc']);
		$progReport->setAttend($_POST['attended']);
		$progReport->setType($_POST['type']);
		$progReport->setSpon($_POST['sponsor']);
		$progReport->setOrgId($this->organization->getId());
		$progReport->setDate($_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']);
		$progReport->save();
		
		//go back to start page
		$this->redirect("report/programming");
	}
	
	public function quarterly()
	{
		global $db;
		$page_title = array('RIT - getConnected');
		
		// if we are still here, then $organizations is an array of orgs
		// that this user is the president of.
		$organization = $this->organization;
		require('views/reports/quarterly.html');
	}

	public function createQuarterReport()
	{
		$organization = $this->organization;
		$orgIdNum = $organization->getId();
		$fromDate = $_POST['fyear']."-".$_POST['fmonth']."-".$_POST['fday']." 00:00:00";
		
		$fromDatePretty = $_POST['fyear']." - ".$_POST['fmonth']." - ".$_POST['fday'];
	
		$toDate = $_POST['tyear']."-".$_POST['tmonth']."-".$_POST['tday']." 00:00:00";
		
		$toDatePretty = $_POST['tyear']." - ".$_POST['tmonth']." - ".$_POST['tday'];
		/*generate community report */
		$communityReport = 
		"<table class=\"list\">
		<tr>
			<th colspan=\"4\">Community Reports</th>
		</tr>
		<tr>
			<th>Name of Event</th>
			<th># Members Attended</th>
			<th>Hours of Service</th>
			<th>Date of Event (yyyy/mm/dd)</th>
		</tr>";

		$commEvents = CommReport::getList("date >= '$fromDate' AND date <= '$toDate'");
		
		foreach ($commEvents as $commEvent)
		{
			//filter for events not in this organization
			if($commEvent->getOrgId()==$orgIdNum)
			{
				$communityReport.=
				"<tr>
					<td>".$commEvent->getName()."</td>
					<td>".$commEvent->getAttend()."</td>
					<td>".$commEvent->getHours()."</td>
					<td>".substr($commEvent->getDate(),0,10)."</td>
				</tr>";
			}
		}
		
		$communityReport.=
		"</table>
		<br>
		<br>";




		/*generate philanthropy report */
		$philanthropyReport = 
		"<table class=\"list\">
		<tr>
			<th colspan=\"4\">Philanthropy Reports</th>
		</tr>
		<tr>
			<th>Name of Event</th>
			<th># Members Attended</th>
			<th>Total Funds Donated</th>
			<th>Date of Event (yyyy/mm/dd)</th>
		</tr>";
		
		$philEvents = PhilReport::getList("date >= '$fromDate' AND date <= '$toDate'");
		
		foreach ($philEvents as $philEvent)
		{
			if($philEvent->getOrgId()==$orgIdNum)
			{
				$philanthropyReport.=
				"<tr>
					<td>".$philEvent->getName()."</td>
					<td>".$philEvent->getAttend()."</td>
					<td>".$philEvent->getDollars()."</td>
					<td>".substr($philEvent->getDate(),0,10)."</td>
				</tr>";
			}
		}
		
		$philanthropyReport.=
		"</table>
		<br>
		<br>";
		
		/*generate programming report */
		$programmingReport = 
		"<table class=\"list\">
		<tr>
			<th colspan=\"5\">Programming Reports</th>
		</tr>
		<tr>
			<th>Name of Event</th>
			<th>Attendance</th>
			<th>Type of Program</th>
			<th>Sponsor</th>
			<th>Date of Event (yyyy/mm/dd)</th>
		</tr>";
		
		$progEvents = ProgReport::getList("date >= '$fromDate' AND date <= '$toDate'");
		
		foreach ($progEvents as $progEvent)
		{
			if($progEvent->getSpon() == 1)
			{
				$sponsor = "Sponsored";
			}
			else
			{
				$sponsor = "Attended";
			}
			
			if($progEvent->getOrgId()==$orgIdNum)
			{
				$programmingReport.=
				"<tr>
					<td>".$progEvent->getName()."</td>
					<td>".$progEvent->getAttend()."</td>
					<td>".$progEvent->getType()."</td>
					<td>".$sponsor."</td>
					<td>".substr($progEvent->getDate(),0,10)."</td>
				</tr>";
			}
		}
		
		$programmingReport.=
		"</table>
		<br>
		<br>";
		
		//go back to start page
		require('views/reports/quarterlyReport.html');
	}
	
}

?>
