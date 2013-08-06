<?php

class OffCampusEvent extends Event {
	protected $rules = array(
		'event_id' => array(
			'type' => 'int',
			'error' => 'Invalid event id.',
		),
		'departure_start_time' => array(
			'type' => 'time',
			'error' => 'Invalid departure start time.',
		),
		'departure_start_location_id' => array(
			'type' => 'int',
			'error' => 'Invalid departure start location.',
		),
		'departure_end_time' => array(
			'type' => 'time',
			'error' => 'Invalid departure end time.',
		),
		'departure_end_location_id' => array(
			'type' => 'int',
			'error' => 'Invalid departure end location.',
		),
		'return_start_time' => array(
			'type' => 'time',
			'error' => 'Invalid return start time.',
		),
		'return_start_location_id' => array(
			'type' => 'int',
			'error' => 'Invalid return start location.',
		),
		'return_end_time' => array(
			'type' => 'time',
			'error' => 'Invalid return end time.',
		),
		'return_end_location_id' => array(
			'type' => 'int',
			'error' => 'Invalid return end location.',
		),
		'distance' => array(
			'type' => 'int',
			'error' => 'Invalid distance.',
		),
		'overnight' => array(
			'type' => 'bool',
		),
		'num_people' => array(
			'type' => 'int',
			'error' => 'Invalid number of people.',
		),
		'num_cs_vans' => array(
			'type'=> 'int',
			'error' => 'Invalid number of CS vans.',
		),
		'num_cs_van_passengers' => array(
			'type'=> 'int',
			'error' => 'Invalid number of CS van passengers.',
		),
		'num_sg_vans' => array(
			'type'=> 'int',
			'error' => 'Invalid number of SG vans.',
		),
		'num_sg_van_passengers' => array(
			'type'=> 'int',
			'error' => 'Invalid number of SG van passengers.',
		),
	);
	protected $relationships = array(
		'Event' => 'belongs_to',
		'TravelMode' => 'has_and_belongs_to_many',
		'OvernightLocation' => 'has_many',
		'VanDriver' => 'has_many',
		'OffCampusFinancial' => 'has_one',
	);

	private $departure_start_location;
	private $departure_end_location;
	private $return_start_location;
	private $return_end_location;

	public function save() {
		try {
			global $db;
			parent::save();

			$result = $db->query("DELETE FROM tickets
				WHERE off_campus_financial_id = {$this->getOffCampusFinancialId()}
					AND travel_mode_id NOT IN(". implode(',', $this->getTravelModeIds()) .")");
		} catch(Exception $e) {
			throw $e;
		}
	}

	public function getDepartureStartLocation() {
		if(!isset($this->departure_start_location)) {
			$this->departure_start_location = new Location($this->getDepartureStartLocationId());
		}

		return $this->departure_start_location;
	}

	public function getDepartureEndLocation() {
		if(!isset($this->departure_end_location)) {
			$this->departure_end_location = new Location($this->getDepartureEndLocationId());
		}

		return $this->departure_end_location;
	}

	public function getReturnStartLocation() {
		if(!isset($this->return_start_location)) {
			$this->return_start_location = new Location($this->getReturnStartLocationId());
		}

		return $this->return_start_location;
	}

	public function getReturnEndLocation() {
		if(!isset($this->return_end_location)) {
			$this->return_end_location = new Location($this->getReturnEndLocationId());
		}

		return $this->return_end_location;
	}

	public function getOvernightTotal() {
		$total = 0;
		foreach($this->getOvernightLocations() as $location) {
			$total += $location->getTotal();
		}

		return $total;
	}

	public function isFinancial() {
		return $this->isOffCampusFinancial();
	}

	public function getFinancial() {
		return $this->getOffCampusFinancial();
	}

	public function isRisk() {
		if(is_null($this->getEvent()->getRisk())) {
			if($this->isAbroad()) {
				return true;
			}
		} else {
			return $this->getEvent()->isRisk();
		}
	}

	public function isAbroad() {
		if($this->getDepartureStartLocation()->getCountry() == 'usa' && $this->getDepartureEndLocation()->getCountry() == 'usa' && $this->getReturnStartLocation()->getCountry() == 'usa' && $this->getReturnEndLocation()->getCountry() == 'usa') {
			return false;
		} else {
			return true;
		}
	}

	public function getStartTime() {
		return $this->getDepartureStartTime();
	}

	public function getEndTime() {
		return $this->getReturnEndTime();
	}

	public function autoNotificationGroups() {
		parent::autoNotificationGroups();

		// Public Safety
		$this->addNotificationGroup(6);

		// Public Safety Transportation/Parking
		if(array_intersect(array(TravelMode::CS_VAN, TravelMode::SG_VAN), $this->getTravelModeIds())) {
			$this->addNotificationGroup(8);
		}

		// Director of Campus Life
		if($this->isAbroad()) {
			$this->addNotificationGroup(15);
		}

		// EVR Travel Standard
		$this->addNotificationGroup(46);

		// Financial Transactions
		if($this->getOrganization()->getCategoryId() == 1) {
			$this->addNotificationGroup(52);
		}

		// Risk Management
		if($this->isRisk()) {
			$this->addNotificationGroup(36);
		}

		// Student Government Finance
		if($this->isFinancial()) {
			$this->addNotificationGroup(51);
		}

		// Van Requests
		if(array_intersect(array(TravelMode::CS_VAN, TravelMode::SG_VAN), $this->getTravelModeIds())) {
			$this->addNotificationGroup(49);
		}
	}

	public function getEmailSubject() {
		$subject = date('m.d.y', $this->getDepartureStartTime()) .' TRAVEL: '. $this->getName() .' ('. $this->getOrganization()->getName() .')';
		if($this->isPreApprovalInProgress()) {
			$subject .= " (Pre-Approval)";
		}
		$subject .= " (EVR)";
		return $subject;
	}

	public function delete() {
		parent::delete();

		if($this->getId()) {
			global $db;
			$result = $db->query("DELETE FROM off_campus_event_travel_mode_link WHERE off_campus_event_id = {$this->getId()}");
			$result = $db->query("DELETE FROM overnight_locations WHERE off_campus_event_id = {$this->getId()}");
			$result = $db->query("DELETE FROM van_drivers WHERE off_campus_event_id = {$this->getId()}");
			$result = $db->query("DELETE FROM off_campus_events WHERE id = {$this->getId()}");
		}
	}

	public function duplicate() {
		$travel_modes = $this->getTravelModes();
		$overnight_locations = $this->getOvernightLocations();
		$van_drivers = $this->getVanDrivers();
		$financial = $this->getFinancial();
		$tickets = $financial->getTickets();

		parent::duplicate();

		foreach($overnight_locations as $overnight_location) {
			$overnight_location->setId(null);
			$overnight_location->setOffCampusEventId($this->getId());
			$overnight_location->save();
		}

		foreach($van_drivers as $van_driver) {
			$van_driver->setId(null);
			$van_driver->setOffCampusEventId($this->getId());
			$van_driver->save();
		}

		if($financial->getId()) {
			$financial->setId(null);
			$financial->setOffCampusEventId($this->getId());
			$financial->save();
		}

		foreach($tickets as $ticket) {
			$ticket->setId(null);
			$ticket->setOffCampusFinancialId($financial->getId());
			$ticket->save();
		}
	}

	/*
	 * Slightly more accessible accessor/mutator methods for the base event
	 * class. This is borning and tedious, but it's the one tradeoff for all
	 * the fancy auto accessor/mutator methods (I don't think there's any way
	 * in PHP to call the dynamic functions that don't really exist in the
	 * parent class).
	 *
	 * Be sure to keep in sync with the actual Event class.
	 */
	public function getName() {
		return $this->getEvent()->getName();
	}

	public function setName($name) {
		$this->getEvent()->setName($name);
	}

	public function getDescription() {
		return $this->getEvent()->getDescription();
	}

	public function setDescription($name) {
		$this->getEvent()->setDescription($name);
	}

	public function getNumPeople() {
		return $this->getEvent()->getNumPeople();
	}

	public function setNumPeople($name) {
		$this->getEvent()->setNumPeople($name);
	}

	public function getAdditionalInfo() {
		return $this->getEvent()->getAdditionalInfo();
	}

	public function setAdditionalInfo($name) {
		$this->getEvent()->setAdditionalInfo($name);
	}

	public function getResponsibleRepId() {
		return $this->getEvent()->getResponsibleRepId();
	}

	public function setResponsibleRepId($name) {
		$this->getEvent()->setResponsibleRepId($name);
	}

	public function getIntakeUserId() {
		return $this->getEvent()->getIntakeUserId();
	}

	public function setIntakeUserId($name) {
		$this->getEvent()->setIntakeUserId($name);
	}

	public function getReviewerId() {
		return $this->getEvent()->getReviewerId();
	}

	public function setReviewerId($reviewer_id) {
		$this->getEvent()->setReviewerId($reviewer_id);
	}


	public function getIntakeTime() {
		return $this->getEvent()->getIntakeTime();
	}

	public function setIntakeTime($name) {
		$this->getEvent()->setIntakeTime($name);
	}

	public function getSentTime() {
		return $this->getEvent()->getSentTime();
	}

	public function setSentTime($name) {
		$this->getEvent()->setSentTime($name);
	}

	public function getConfirmTime() {
		return $this->getEvent()->getConfirmTime();
	}

	public function setConfirmTime($name) {
		$this->getEvent()->setConfirmTime($name);
	}

	public function getCancelTime() {
		return $this->getEvent()->getCancelTime();
	}

	public function setCancelTime($name) {
		$this->getEvent()->setCancelTime($name);
	}

	public function getOrganizationId() {
		return $this->getEvent()->getOrganizationId();
	}

	public function setOrganizationId($organization_id) {
		$this->getEvent()->setOrganizationId($organization_id);
	}

	public function getOrganization() {
		return $this->getEvent()->getOrganization();
	}

	public function isWaiver() {
		return $this->getEvent()->isWaiver();
	}

	public function setWaiver($waiver) {
		$this->getEvent()->setWaiver($waiver);
	}

	public function getWaiverDescription() {
		return $this->getEvent()->getWaiverDescription();
	}

	public function setWaiverDescription($waiver_description) {
		$this->getEvent()->setWaiverDescription($waiver_description);
	}

	public function setRisk($risk) {
		$this->getEvent()->setRisk($risk);
	}

	public function getNotificationIds() {
		return $this->getEvent()->getNotificationIds();
	}

	public function getNotifications() {
		return $this->getEvent()->getNotifications();
	}

	public function getMessages() {
		return $this->getEvent()->getMessages();
	}
}

?>
