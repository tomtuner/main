<?php

class Date extends Model {
	public $custom_config = array(
		'sort' => 'event_start_time',
	);
	protected $rules = array(
		'on_campus_event_id' => array(
			'type' => 'int',
			'error' => 'Invalid on campus event id.',
		),
		'reservation_start_time' => array(
			'type' => 'time',
			'error' => 'Invalid reservation start time.',
		),
		'reservation_end_time' => array(
			'type' => 'time',
			'error' => 'Invalid reservation end time.',
		),
		'event_start_time' => array(
			'type' => 'time',
			'error' => 'Invalid event start time.',
		),
		'event_end_time' => array(
			'type' => 'time',
			'error' => 'Invalid event end time.',
		),
		'other_location' => array(
			'type' => 'string',
		),
		'description' => array(
			'type' => 'string',
			'error' => 'A description must be entered.',
		),
	);
	protected $relationships = array(
		'OnCampusEvent' => 'belongs_to',
		'Room' => 'has_and_belongs_to_many',
	);


	public function __construct($id = 0) {
		parent::__construct($id);

		$this->diff = $this->generateDiff();
	}

	public function save() {
		parent::save();

		if($this->diff != $this->generateDiff()) {
			global $db;
			$db->query("UPDATE on_campus_events SET events_calendar_update = 1 WHERE id = {$this->getOnCampusEventId()}");
		}
	}

	public function generateDiff() {
		$rooms = array();
		foreach($this->getRooms() as $room) {
			$rooms[] = "{$room->getName()}: {$room->getBuilding()->getName()}";
		}

		$rooms = implode(", ", $rooms);

		return "{$this->getEventStartTime()} - {$this->getEventEndTime()}: {$rooms}";
	}

	public function getEvent() {
		return $this->getOnCampusEvent();
	}

	public function getOverlappingDates() {
		if(!isset($this->overlapping_dates)) {
			global $db;

			$this->overlapping_dates = array();

			$date_ids = $db->getCol("SELECT id FROM dates
				WHERE id != {$this->getId()}
					AND (reservation_start_time >= {$this->getReservationStartTime()} AND reservation_start_time <= {$this->getReservationEndTime()})
					OR (reservation_end_time >= {$this->getReservationStartTime()} AND reservation_end_time <= {$this->getReservationEndTime()})");
			foreach($date_ids as $id) {
				$date = new Date($id);
				if(array_intersect($this->getRoomIds(), $date->getRoomIds()) && $date->getEvent()->isIntakeComplete() && !$date->getEvent()->isCanceled()) {
					$this->overlapping_dates[] = $date;
				}
			}
		}

		return $this->overlapping_dates;
	}

	public function isRestricted() {
		return (bool) $this->getRestrictionId();
	}

	public function getRestrictionId() {
		if(!isset($this->restriction_id)) {
			global $db;

			$id = $db->getOne("SELECT id
				FROM date_restrictions
				WHERE {$this->getReservationStartTime()} BETWEEN start_time AND end_time
					OR {$this->getReservationEndTime()} BETWEEN start_time AND end_time");
			$restriction = new DateRestriction($id);

			if(!in_array($this->getEvent()->getOrganizationId(), $restriction->getOrganizationIds())) {
				$this->restriction_id = $id;
				$this->restriction = $restriction;
			}
		}

		return $this->restriction_id;
	}

	public function getRestriction() {
		if($this->isRestricted()) {
			if(!isset($this->restriction)) {
				$this->restriction = new DateRestriction($this->getRestrictionId());
			}
		}

		return $this->restriction;
	}
}

?>
