<?php

class OnCampusEvent extends Event {
	protected $rules = array(
		'event_id' => array(
			'type' => 'int',
			'error' => 'Invalid event id.',
		),
		'estimated_attendance' => array(
			'type' => 'int',
			'error' => 'You must enter an estimated attendance.',
		),
		'event_type_id' => array(
			'type' => 'int',
			'error' => 'You must select what type of event this is.',
		),
		'promotion_location_id' => array(
			'type' => 'int',
			'error' => 'You must select where this event will be promoted.',
		),
		'other_promotion_method' => array(
			'type' => 'int',
		),
		'events_calendar' => array(
			'type' => 'bool',
		),
		'events_calendar_complete' => array(
			'type' => 'bool',
		),
		'alcohol' => array(
			'type' => 'bool',
		),
		'interpreter' => array(
			'type' => 'bool',
		),
		'num_people_actual' => array(
			'type' => 'int',
		),
		'level' => array(
			'type' => 'int',
			'error' => 'Invalid level.',
		),
	);
	protected $relationships = array(
		'Event' => 'belongs_to',
		'Date' => 'has_many',
		'EventType' => 'has_one',
		'PromotionLocation' => 'has_one',
		'PromotionMethod' => 'has_and_belongs_to_many',
		'Admission' => 'has_one',
		'CampusSafety' => 'has_one',
		'Etc' => 'has_one',
		'FoodService' => 'has_one',
		'FacilitiesManagement' => 'has_one',
		'RitCatering' => 'has_one',
		'TechCrew' => 'has_one',
		'OnCampusFinancial' => 'has_one',
	);

	protected $diff;

	public function __construct($id = 0) {
		parent::__construct($id);
		unset($this->data['facilities_management_id']);
		unset($this->data['campus_safety_id']);
		unset($this->data['tech_crew_id']);
		unset($this->data['etc_id']);
		unset($this->data['rit_catering_id']);
		unset($this->data['food_service_id']);

		if($this->isEventsCalendar()) {
			$this->diff = $this->generateDiff();
		}
	}

	protected function generateDiff() {
		$dates = array();
		foreach($this->getDates() as $date) {
			$dates[] = $date->generateDiff();
		}

		$dates = implode(", ", $dates);

		return <<<END
{$this->getName()}
{$this->getDescription()}
{$dates}
END;
	}

	public function getRooms() {
		if(!isset($this->rooms)) {
			$this->rooms = array();
			foreach($this->getRoomIds() as $id) {
				$this->rooms[] = new Room($id);
			}
		}

		return $this->rooms;
	}

	public function getRoomIds() {
		if(!isset($this->room_ids)) {
			$this->room_ids = array();
			foreach($this->getDates() as $date) {
				$this->room_ids = array_merge($this->room_ids, $date->getRoomIds());
			}
		}

		return array_unique($this->room_ids);
	}

	public function getBuildingIds() {
		if(!isset($this->building_ids)) {
			$this->building_ids = array();
			foreach($this->getRooms() as $room) {
				$this->building_ids[] = $room->getBuildingId();
			}
		}

		return $this->building_ids;
	}

	public function getCost() {
		return $this->getFacilitiesManagement()->getTotalCost() + $this->getTechCrew()->getCost() + $this->getEtc()->getCost();
	}

	public function isFinancial() {
		return $this->isOnCampusFinancial();
	}

	public function getFinancial() {
		return $this->getOnCampusFinancial();
	}

	public function isRisk() {
		if(is_null($this->getEvent()->getRisk())) {
			$i = 0;
			if($this->getEstimatedAttendance() >= 200) {
				$i++;
			}

			if($this->getAdmission()->getRestrictionId() == AdmissionRestriction::RESTRICTION_NONE) {
				$i++;
			}

			// Outdoors
			if(in_array(12, $this->getBuildingIds())) {
				$i++;
			}

			if($this->isAlcohol()) {
				$i++;
			}

			if($i >= 2) {
				return true;
			} else {
				return false;
			}
		} else {
			return $this->getEvent()->isRisk();
		}
	}

	public function getStartTime() {
		$date_id = array_slice($this->getDateIds(), 0, 1);
		if($date_id) {
			$date = new Date($date_id[0]);
		} else {
			$date = new Date();
		}
		return $date->getEventStartTime();
	}

	public function getEndTime() {
		$date_id = array_slice($this->getDateIds(), -1, 1);
		$date = new Date($date_id[0]);
		return $date->getEventEndTime();
	}

	public function autoNotificationGroups() {
		parent::autoNotificationGroups();

		// Apartment Area Requests
		if(array_intersect(array(35, 72, 76, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 134, 135, 136, 137, 138, 139), $this->getBuildingIds())) {
			$this->addNotificationGroup(2);
		}

		// Athletic Fields
		if(array_intersect(array(42, 43, 44, 45, 46, 52, 81, 122), $this->getRoomIds())) {
			$this->addNotificationGroup(4);
		}

		// Public Safety
		if($this->isCampusSafety() || $this->getAdmission()->isDoorCollection()) {
			$this->addNotificationGroup(6);
			$this->addNotificationGroup(32);
		}

		// Public Safety Health & Safety/Permits/Occupancy
		if($this->getCampusSafety()->isTownPermit() || $this->getEstimatedAttendance() >= 1000 || in_array(12, $this->getBuildingIds())) {
			$this->addNotificationGroup(7);
		}

		// Public Safety Transportation/Parking
		if(in_array(151, $this->getBuildingIds())) {
			$this->addNotificationGroup(8);
		}

		// Catering
		if($this->isRitCatering()) {
			$this->addNotificationGroup(9);
		}

		// CIMS
		if(array_intersect(array(67), $this->getBuildingIds())) {
			$this->addNotificationGroup(62);
		}

		// Clark Gym/Aux Gym/Breezeway
		if(array_intersect(array(17, 62, 83), $this->getRoomIds())) {
			$this->addNotificationGroup(10);
		}

		// Crossroads
		if(in_array(12, $this->getRoomIds())) {
			$this->addNotificationGroup(12);
		}

		// Curtains

		// Dining Commons
		if(in_array(14, $this->getRoomIds())) {
			$this->addNotificationGroup(14);
		}

		// ETC
		if($this->isEtc() || array_intersect(array(22, 25, 26, 27, 109), $this->getRoomIds())) {
			$this->addNotificationGroup(16);
		}

		// EVR On Campus Standard
		$this->addNotificationGroup(17);

		// Facilities Management
		if($this->isFacilitiesManagement()) {
			$this->addNotificationGroup(19);
		}

		// Field House
		if(in_array(38, $this->getBuildingIds())) {
			$this->addNotificationGroup(22);
		}

		// Field House Pool
		if(array_intersect(array(20, 107, 108), $this->getRoomIds())) {
			$this->addNotificationGroup(23);
		}

		// Financial Transactions
		if($this->getOrganization()->getCategoryId() == 1 && ($this->isTechCrew() || $this->isEtc() || $this->isFacilitiesManagement())) {
			$this->addNotificationGroup(52);
		}


		// FMS SAU, Athletic, and Eastman Custodial and Setup Requests
		if(array_intersect(array(8, 11, 16, 19), $this->getBuildingIds())) {
			$this->addNotificationGroup(21);
		}

		// Gracies
		if(in_array(114, $this->getRoomIds())) {
			$this->addNotificationGroup(24);
		}

		// Greek Circle Events
		if(array_intersect(array(134, 135, 136, 137, 138, 139), $this->getBuildingIds()) || in_array(19, $this->getRoomIds())) {
			$this->addNotificationGroup(3);
		}

		// Greek Lawn
		if(in_array(122, $this->getRoomIds())) {
			$this->addNotificationGroup(65);
		}

		// Greek List
		if($this->getOrganization()->getCategoryId() == 3) {
			$this->addNotificationGroup(25);
		}

		// Interpreting Recidence Life/RHA
		if($this->isInterpreter() && in_array($this->getOrganizationId(), array(338, 170))) {
			$this->addNotificationGroup(27);
		}

		// Interpreting Student Activities
		if($this->isInterpreter() && !in_array($this->getOrganizationId(), array(338, 170))) {
			$this->addNotificationGroup(28);
		}

		// LBJ Building (Lyndon Baines Johnson)
		if(in_array(59, $this->getBuildingIds())) {
			$this->addNotificationGroup(63);
		}

		// Nathanial Rochester Hall/Sol Heumann/Gibson

		// Off Campus Caterer
		if($this->getFoodService()->getCatererId()) {
			$this->addNotificationGroup(50);
		}

		// Outdoor Academic
		if(array_intersect(array(48, 49, 51, 67), $this->getRoomIds())) {
			$this->addNotificationGroup(31);
		}

		// Residence Hall Conference Rooms
		if(array_intersect(array(79, 102, 103), $this->getRoomIds())) {
			$this->addNotificationGroup(35);
		}

		// Residence Hall: Baker, Colby, Gleason, 90 Area & Sundial
		if(array_intersect(array(41, 42, 43, 44, 45, 46, 47, 48, 49, ), $this->getBuildingIds()) || array_intersect(array(47, 110), $this->getRoomIds())) {
			$this->addNotificationGroup(55);
		}

		// Residence Hall: Ellingson/Peterson/Bell Area
		if(array_intersect(array(55, 56, 57), $this->getBuildingIds()) || in_array(84, $this->getRoomIds())) {
			$this->addNotificationGroup(18);
		}

		// Residence Hall: NRH/SFG Area
		if(array_intersect(array(111, 112), $this->getRoomIds())) {
			$this->addNotificationGroup(56);
		}

		// Residence Halls
		if(in_array(53, $this->getRoomIds())) {
			$this->addNotificationGroup(34);
		}

		// Risk Management
		if($this->isRisk()) {
			$this->addNotificationGroup(36);
		}

		// RITA
		if($this->getEstimatedAttendance() >= 600) {
			$this->addNotificationGroup(32);
		}

		// Ritter Ice Arena
		if(in_array(24, $this->getRoomIds())) {
			$this->addNotificationGroup(37);
		}

		// Ritz SportsZone
		if(in_array(9, $this->getRoomIds())) {
			$this->addNotificationGroup(33);
		}

		// SAU Cafeteria
		if(in_array(3, $this->getRoomIds())) {
			$this->addNotificationGroup(38);
		}

		// Schmitt Interfaith Center
		if(array_intersect(array(16), $this->getRoomIds())) {
			$this->addNotificationGroup(1);
		}

		// Student Government
		if($this->getOrganizationId() == 245) {
			$this->addNotificationGroup(40);
		}

		// Student Government Finance
		if($this->isFinancial()) {
			$this->addNotificationGroup(51);
		}

		// Student Life Center
		if(array_intersect(array(9, 37), $this->getBuildingIds())) {
			$this->addNotificationGroup(41);
		}

		// Tech Crew
		if($this->isTechCrew() || in_array(8, $this->getRoomIds())) {
			$this->addNotificationGroup(42);
		}

		// Xerox Auditorium
		if(in_array(23, $this->getRoomIds())) {
			$this->addNotificationGroup(44);
		}
	}

	public function getEmailSubject() {
		$dates = $this->getDates();
		$subject = date('m.d.y', $dates[0]->getEventStartTime()) .' '. $this->getName() .' ('. $this->getOrganization()->getName() .')';
		if($this->isPreApprovalInProgress()) {
			$subject .= " (Pre-Approval)";
		}
		$subject .= " (EVR)";
		return $subject;
	}

	public function delete() {
		global $db;
		parent::delete();

		if($this->getId()) {
			$result = $db->query("DELETE FROM dates WHERE on_campus_event_id = {$this->getId()}");
			$result = $db->query("DELETE FROM on_campus_event_promotion_location_link WHERE on_campus_event_id = {$this->getId()}");
			$result = $db->query("DELETE FROM on_campus_event_promotion_method_link WHERE on_campus_event_id = {$this->getId()}");
			$result = $db->query("DELETE FROM on_campus_events WHERE id = {$this->getId()}");
		}
	}

	public function duplicate() {
		$dates = $this->getDates();
		$admission = $this->getAdmission();
		$campus_safety = $this->getCampusSafety();
		$etc = $this->getEtc();
		$facilities_management = $this->getFacilitiesManagement();
		$rit_catering = $this->getRitCatering();
		$food_service = $this->getFoodService();
		$tech_crew = $this->getTechCrew();
		$financial = $this->getFinancial();

		parent::duplicate();

		foreach($dates as $date) {
			$date->setId(null);
			$date->setOnCampusEventId($this->getId());
			$date->save();
		}

		if($admission->getId()) {
			$admission->setId(null);
			$admission->setOnCampusEventId($this->getId());
			$admission->save();
		}

		if($campus_safety->getId()) {
			$campus_safety->setId(null);
			$campus_safety->setOnCampusEventId($this->getId());
			$campus_safety->save();
		}

		if($etc->getId()) {
			$etc->setId(null);
			$etc->setOnCampusEventId($this->getId());
			$etc->save();
		}

		if($facilities_management->getId()) {
			$facilities_management->setId(null);
			$facilities_management->setOnCampusEventId($this->getId());
			$facilities_management->save();
		}

		if($rit_catering->getId()) {
			$rit_catering->setId(null);
			$rit_catering->setOnCampusEventId($this->getId());
			$rit_catering->save();
		}

		if($food_service->getId()) {
			$food_service->setId(null);
			$food_service->setOnCampusEventId($this->getId());
			$food_service->save();
		}

		if($tech_crew->getId()) {
			$tech_crew->setId(null);
			$tech_crew->setOnCampusEventId($this->getId());
			$tech_crew->save();
		}

		if($financial->getId()) {
			$financial->setId(null);
			$financial->setOnCampusEventId($this->getId());
			$financial->save();
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

	protected function getSoapParams() {
		$params = parent::getSoapParams();
		$params["category_id"] = $this->getEventTypeId();
		$params["price"] = array(
			"student" => $this->getAdmission()->getStudentCost(),
			"staff" => $this->getAdmission()->getStaffCost(),
			"public" => $this->getAdmission()->getPublicCost(),
		);

		foreach($this->getDates() as $date) {
			$day = array(
				"doors_open_at" => null,
				"start_at" => date(DATE_W3C, $date->getEventStartTime()),
				"end_at" => date(DATE_W3C, $date->getEventEndTime()),
			);

			// Push the rooms onto this day.
			foreach($date->getRooms() as $room) {
				$day["locations"][] = array(
					"room_id" => $room->getId(),
					"other" => null,
				);
			}

			// If another location is specified, handle that.
			if($date->isOtherLocation()) {
				$day["locations"][] = array(
					"room_id" => null,
					"other" => $date->getOtherLocation(),
				);
			}

			$params["days"][] = $day;
		}

		return $params;
	}
}

?>
