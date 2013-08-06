<?php

class Event extends Model {
	protected $rules = array(
		'organization_id' => array(
			'type' => 'int',
			'error' => 'You must select an organization.',
		),
		'name' => array(
			'type' => 'string',
			'error' => 'This event needs a name.',
		),
		'description' => array(
			'type' => 'string',
			'error' => 'You must enter a description of this event.',
		),
		'num_people' => array(
			'type' => 'int',
			'error' => "We need to know how many people you're expecting.",
		),
		'additional_info' => array(
			'type' => 'string',
		),
		'reviewer_id' => array(
			'type' => 'int',
		),
		'intake_time' => array(
			'type' => 'int',
		),
		'sent_time' => array(
			'type' => 'int',
		),
		'confirm_time' => array(
			'type' => 'int',
		),
		'cancel_time' => array(
			'type' => 'int',
		),
		'agreement' => array(
			'type' => 'bool',
		),
		'waiver' => array(
			'type' => 'bool',
		),
		'waiver_description' => array(
			'type' => 'string',
		),
		'risk' => array(
			'type' => 'bool',
		),
	);
	protected $relationships = array(
		'Organization' => 'has_one',
		'Notification' => 'has_many',
		'Message' => 'has_many',
		'File' => 'has_and_belongs_to_many',
	);

	protected $responsible_rep;
	protected $advisors;
	protected $advisor_ids;
	protected $intake_user;
	protected $agreement;
	protected $notification_group_ids;
	protected $notification_user_ids;
	protected $messages;

	public function save() {
		try {
			global $db;

			// If this is an OnCampusEvent or OffCampusEvent, first save the base
			// Event object.
			if(get_class($this) != 'Event') {
				$this->getEvent()->save();
			}

			if(get_class($this) == 'Event') {
				if(!$this->getConfirmationCode()) {
					// Generate a unique confirmation string.
					do {
						$confirmation_code = substr(md5("MmHmm! " . microtime() . " Salty."), 0, 16);
					} while($db->getOne("SELECT confirmation_code FROM events WHERE confirmation_code = '$confirmation_code'"));

					$this->setConfirmationCode($confirmation_code);
				}
			}

			parent::save();

			$advisor_ids = $this->getAdvisorIds();
			if($this->getEventId()) {
				$result = $db->query("DELETE FROM event_advisor_link WHERE event_id = {$this->getEventId()}");
				foreach($advisor_ids as $advisor_id) {
					$columns = array(
						'event_id' => $this->getEventId(),
						'user_id' => $advisor_id,
					);

					$result = $db->autoExecute('event_advisor_link', $columns, DB_AUTOQUERY_INSERT);
				}
			}

			// Insert or update the promo database.
			if(get_class($this) == "OnCampusEvent") {
				if($this->isEventsCalendar() && $this->isEventsCalendarComplete()) {
					if(isset($this->diff) && $this->diff != $this->generateDiff()) {
						$db->query("UPDATE on_campus_events SET events_calendar_update = 1 WHERE id = {$this->getId()}");
					}
				}

				if($this->isEventsCalendar() && $this->isSent()) {
					// Quiet any warnings that might pop-up, since we'll handle 
					// them through exceptions.
					$client = @new SoapClient("http://campuslife.rit.edu/api/promo/events/wsdl");

					// If we've previously sent this event to the promo database, 
					// use that instance.
					if($this->getPromoEventId()) {
						$result = $client->update($this->getSoapParams());
					} else {
						$result = $client->new($this->getSoapParams());
						if($result->id) {
							$db->query("UPDATE events SET promo_event_id = {$result->id} WHERE id = {$this->getEventId()}");
						}
					}
				}
			}
		} catch(SoapFault $e) {
			$mailer = new Mailer();
			$mailer->setSubject("SOAP Errors");
			$mailer->setFrom("cclwww@rit.edu");
			$mailer->setTo("cclwww@rit.edu");

			ob_start();
			var_dump($e);
			$mailer->setTXTBody(ob_get_clean());

			$mailer->send();
		} catch(Exception $e) {
			throw $e;
		}
	}

	public static function factory($event_id, $type = '') {
		global $db;

		if($type) {
			$table_name = substr(preg_replace("/([A-Z])/e", "'_'. strtolower('\\1')", $type) .'s', 1);
			$id = $db->getOne("SELECT id FROM $table_name WHERE event_id = $event_id");
		} else {
			$id = $db->getOne("SELECT id FROM on_campus_events WHERE event_id = $event_id");
			if($id) {
				$type = 'OnCampusEvent';
			} else {
				$id = $db->getOne("SELECT id FROM off_campus_events WHERE event_id = $event_id");
				$type = 'OffCampusEvent';
			}
		}

		$object = new $type($id);
		return new $type($id);
	}

	public function getEventId() {
		if($this instanceof OnCampusEvent || $this instanceof OffCampusEvent) {
			return $this->getColumn('event_id');
		} else {
			return $this->getId();
		}
	}

	public function getResponsibleRep() {
		if(!isset($this->responsible_rep)) {
			$this->responsible_rep = new User($this->getResponsibleRepId());
		}

		return $this->responsible_rep;
	}

	public function getAdvisorIds() {
		if(!isset($this->advisor_ids)) {
			global $db;

			$this->advisor_ids = $db->getCol("SELECT user_id
				FROM event_advisor_link
				WHERE event_id = {$this->getEventId()}");
		}

		return $this->advisor_ids;
	}

	public function getAdvisors() {
		if(!isset($this->advisors)) {
			$this->advisors = array();
			foreach($this->getAdvisorIds() as $advisor_id) {
				$this->advisors[] = new User($advisor_id);
			}
		}

		return $this->advisors;
	}

	public function setAdvisorIds($advisor_ids) {
		try {
			$this->advisor_ids = $this->parseInt($advisor_ids);
		} catch(IntZeroException $e) {
		}
	}

	public function autoSetAdvisors() {
		global $db;

		$this->setAdvisorIds($db->getCol("SELECT user_id
			FROM organizations.advisors
			WHERE org_id = {$this->getOrganizationId()}"));
	}

	public function getIntakeUser() {
		if(!isset($this->intake_user)) {
			$this->intake_user = new User($this->getIntakeUserId());
		}

		return $this->intake_user;
	}

	public function autoSetReviewer() {
		if(!$this->getColumn("reviewer_id")) {
			// Matt: Black Awareness Coordinating Committee, Global Union, 
			// OCASA, Residence Halls Association, Physician Assistant Student 
			// Association, OASIS, Outing Club, Model Railroad Club, 
			// InterVarsity Christian Fellowship, Brothers and Sisters in 
			// Christ
			if(in_array($this->getOrganizationId(), array(26, 83, 144, 170, 159, 147, 195, 194, 107, 32))) {
				$reviewer_id = 6851;
			// Jessica: Greek Organizations, Greek Councils, Greek Council
			} else if(in_array($this->getOrganization()->getCategoryId(), array(3, 9)) || $this->getOrganizationId() == 444) {
				$reviewer_id = 165;
			// Ryan: Honors Fraternities, CAB
			} else if($this->getOrganization()->getCategoryId() == 6 || $this->getOrganizationId() == 46) {
				$reviewer_id = 33;
			// Tara: Everything else
			} else {
				$reviewer_id = 204;
			}

			$this->setReviewerId($reviewer_id);
		}
	}

	public function getReviewerId() {
		if(!isset($this->reviewer_id)) {
			$this->reviewer_id = ($this->getColumn('reviewer_id')) ? $this->getColumn('reviewer_id') : $this->getIntakeUser()->getSupervisorId();
		}

		return $this->reviewer_id;
	}

	public function getReviewer() {
		if(!isset($this->reviewer)) {
			$this->reviewer = new User($this->getReviewerId());
		}

		return $this->reviewer;
	}

	public function getPreApprovalInitiator() {
		if(!isset($this->pre_approval_initiator)) {
			if($this->getPreApprovalInitiatorId()) {
				$this->pre_approval_initiator = new User($this->getPreApprovalInitiatorId());
			}
		}

		return $this->pre_approval_initiator;
	}

	public function getPreApprover() {
		if(!isset($this->pre_approver)) {
			if($this->getPreApproverId()) {
				$this->pre_approver = new User($this->getPreApproverId());
			} else {
				$this->pre_approver = new User();
			}
		}

		return $this->pre_approver;
	}

	public function isIntakeComplete() {
		return $this->getEvent()->isIntakeTime();
	}

	public function isPreApprovalInitiated() {
		return $this->isPreApprovalInitiateTime();
	}

	public function isPreApproved() {
		return $this->isPreApprovalTime();
	}

	public function isPreApprovalInProgress() {
		return (($this->isPreApprovalInitiated() || $this->isPreApproved()) && !$this->isSent());
	}

	public function isSent() {
		return $this->getEvent()->isSentTime();
	}

	public function isConfirmed() {
		return $this->getEvent()->isConfirmTime();
	}

	public function isCanceled() {
		return $this->getEvent()->isCancelTime();
	}

	public function unconfirm() {
		$this->getEvent()->setConfirmTime(null);
	}

	public function uncancel() {
		$this->getEvent()->setCancelTime(null);
	}

	//public abstract function getStartTime();

	//public abstract function getEndTime();

	public function getPreApprovalNotifications() {
		if(!isset($this->pre_approval_notifications)) {
			$this->pre_approval_notifications = array();

			global $db;
			$ids = $db->getCol("SELECT id FROM notifications WHERE event_id = ". $this->getEventId() ." AND pre_approval = 1");
			foreach($ids as $id) {
				$this->pre_approval_notifications[] = new Notification($id);
			}
		}

		return $this->pre_approval_notifications;
	}

	public function getNotificationGroupIds() {
		if(!isset($this->notification_group_ids)) {
			$this->notification_group_ids = array();
			foreach($this->getEvent()->getNotifications() as $notification) {
				if($notification->getNotificationGroupId()) {
					$this->notification_group_ids[] = $notification->getNotificationGroupId();
				}
			}
		}

		return $this->notification_group_ids;
	}

	public function getNotificationUserIds() {
		if(!isset($this->notification_user_ids)) {
			$this->notification_user_ids = array();
			foreach($this->getEvent()->getNotifications() as $notification) {
				if($notification->getUserId()) {
					$this->notification_user_ids[] = $notification->getUserId();
				}
			}
		}

		return $this->notification_user_ids;
	}

	public function autoNotificationGroups() {
		global $db;

		$this->addNotificationUser($this->getResponsibleRep()->getId());

		foreach($this->getAdvisors() as $advisor) {
			$this->addNotificationUser($advisor->getId());
		}

		// SG-Recognized Clubs
		if($this->getOrganization()->getCategoryId() == 1) {
			// Clubs
			$this->addNotificationGroup(11);

			$sub_category_id = $db->getOne("SELECT category_id
				FROM newclubs.club_profile
				WHERE id = ". $this->getOrganizationId());

			// Religious Clubs
			if($sub_category_id == 6) {
				$this->addNotificationGroup(53);
			}

			// Sports Clubs
			if($sub_category_id == 4) {
				$this->addNotificationGroup(54);
			}
		}

		// CAB List
		if($this->getOrganizationId() == 46) {
			$this->addNotificationGroup(5);
		}

		// OCASA
		if($this->getOrganizationId() == 144) {
			$this->addNotificationGroup(30);
		}

		// NTID List
		if(in_array($this->getOrganizationId(), array(141, 11, 225, 59, 62, 21, 95, 36, 247, 93, 57, 61, 239, 139, 140, 322))) {
			$this->addNotificationGroup(66);
		}
	}

	public function addNotificationGroup($notification_group_id) {
		try {
			$notification = new Notification();
			$notification->setEventId($this->getEventId());
			$notification->setNotificationGroupId($notification_group_id);
			$notification->save();
		} catch(Exception $e) {
			Controller::$errors[] = $e;
		}
	}

	public function removeNotificationGroup($notification_group_id) {
		global $db;

		$result = $db->query("DELETE FROM notifications
			WHERE event_id = ". $this->getEventId() ." AND notification_group_id = ". intval($notification_group_id));
	}

	public function addNotificationUser($user_id) {
		try {
			$notification = new Notification();
			$notification->setEventId($this->getEventId());
			$notification->setUserId($user_id);
			$notification->save();
		} catch(Exception $e) {
			Controller::$errors[] = $e;
		}
	}

	public function addNotificationOther($email, $name, $pre_approval = false) {
		try {
			$notification = new Notification();
			$notification->setEventId($this->getEventId());
			$notification->setOther($email);
			$notification->setOtherName($name);
			$notification->setPreApproval($pre_approval);
			$notification->save();
		} catch(Exception $e) {
			Controller::$errors[] = $e;
		}
	}

	//public abstract function getEmailSubject();

	public function getEmailRecipients() {
		$addresses = array();

		if($this->isPreApprovalInProgress()) {
			$addresses[] = $this->getPreApprovalInitiator()->getEmail();
			$addresses[] = "evrccl@rit.edu";

			$notifications = $this->getPreApprovalNotifications();
		} else {
			$notifications = $this->getNotifications();
		}

		foreach($notifications as $notification) {
			if($notification->getNotificationGroupId()) {
				foreach($notification->getNotificationGroup()->getUsers() as $user) {
					$addresses[] = $user->getEmail();
				}
			} else if($notification->getUserId()) {
				$addresses[] = $notification->getUser()->getEmail();
			} else if($notification->getOther()) {
				$addresses[] = $notification->getOther();
			}
		}

		$addresses[] = $this->getResponsibleRep()->getEmail();

		// Skip naughty blanks.
		$to = array();
		foreach($addresses as $address) {
			if($address) {
				$to[] = $address;
			}
		}

		return implode(', ', array_unique($to));
	}

	public function getPostEventSurvey() {
		if(!isset($this->post_event_survey)) {
			$this->post_event_survey = new PostEventSurvey($this->getPostEventSurveyId());
		}

		return $this->post_event_survey;
	}

	public function getPostEventSurveyId() {
		if(!isset($this->post_event_survey_id)) {
			global $db;
			$this->post_event_survey_id = $db->getOne("SELECT id FROM post_event_surveys WHERE event_id = {$this->getEventId()}");
		}

		return $this->post_event_survey_id;
	}

	public function delete() {
		if($this->getEventId()) {
			global $db;
			$result = $db->query("DELETE FROM event_advisor_link WHERE event_id = {$this->getEventId()}");
			$result = $db->query("DELETE FROM messages WHERE event_id = {$this->getEventId()}");
			$result = $db->query("DELETE FROM notifications WHERE event_id = {$this->getEventId()}");
			$result = $db->query("DELETE FROM post_event_surveys WHERE event_id = {$this->getEventId()}");
			$result = $db->query("DELETE FROM events WHERE id = {$this->getEventId()}");
		}
	}

	public function duplicate() {
		$notifications = $this->getNotifications();

		$this->setId(null);
		$this->getEvent()->setId(null);
		$this->getEvent()->setName($this->getEvent()->getName() .' (Duplicate)');
		$this->getEvent()->setReviewerId(null);
		$this->getEvent()->setPreApprovalInitiatorId(null);
		$this->getEvent()->setPreApproverId(null);
		$this->getEvent()->setIntakeTime(time());
		$this->getEvent()->setPreApprovalInitiateTime(null);
		$this->getEvent()->setPreApprovalTime(null);
		$this->getEvent()->setSentTime(null);
		$this->getEvent()->setConfirmTime(null);
		$this->getEvent()->setCancelTime(null);
		$this->getEvent()->setConfirmationCode(null);
		$this->getEvent()->setPromoEventId(null);
		$this->getEvent()->save();
		$this->setEventId($this->getEvent()->getId());

		$this->getEvent()->autoSetReviewer();
		$this->save();

		foreach($notifications as $notification) {
			$new_notification = new Notification();
			$new_notification->setEventId($this->getEventId());

			if($notification->getNotificationGroupId()) {
				$new_notification->setNotificationGroupId($notification->getNotificationGroupId());
			}

			if($notification->getUserId()) {
				$new_notification->setUserId($notification->getUserId());
			}

			if($notification->getOther()) {
				$new_notification->setOther($notification->getOther());
			}

			if($notification->getOtherName()) {
				$new_notification->setOtherName($notification->getOtherName());
			}

			$new_notification->save();
		}
	}

	protected function comparable() {
		return $this->getStartTime();
	}

	public static function getList($conditions = '', $start_time = 0, $end_time = null) {
		global $db;

		$on_conditions = $off_conditions = $on_time_conditions = array();

		// If the conditions are actually an array full of valid event ids,
		// then we'll make sure we limit our query to those events (useful if
		// we just want to limit the events based on time to a specific group
		// of events). Otherwise, we'll just treat the conditions variable as
		// an SQL WHERE clause.
		if(is_array($conditions)) {
			// If we're using an array, and that array is empty, we actually
			// want to disallow every event.
			$on_conditions[] = $off_conditions[] = (!empty($conditions)) ? 'e.id IN('. implode(', ', $conditions) .')' : 'e.id != e.id';
		} else if($conditions) {
			$on_conditions[] = $off_conditions[] = $conditions;
		}

		// The times work differently for on and on campus events, so handle
		// the time restrictions for each event separately.
		if($start_time) {
			$on_time_conditions[] = "MAX(d.event_start_time) > $start_time";
			$off_conditions[] = "return_end_time > $start_time";
		}

		if($end_time) {
			$on_time_conditions[] = "MIN(d.event_start_time) < $end_time";
			$off_conditions[] = "departure_start_time < $end_time";
		}

		// Now that we have all these conditions, put them into a eaiser to
		// handle strings. The one caveat here is that the time restrictions
		// for on campus events have to be handled in a HAVING clause (due to
		// joins and whatnot).
		$on_where_sql = ($on_conditions) ? "WHERE " . implode(' AND ', $on_conditions) : '';
		$on_having_sql = ($on_time_conditions) ? "HAVING " . implode(' AND ', $on_time_conditions) : '';
		$off_where_sql = ($off_conditions) ? "WHERE " . implode(' AND ', $off_conditions) : '';

		// Perform the mondo-union query. This grabs all the events matching
		// the specified conditions within the given time range. It then merges
		// the on and off campus results and orders everything by the start
		// time.
		$ids = $db->getCol("(SELECT o.event_id, MIN(d.event_start_time) AS start_time
				FROM on_campus_events AS o
					LEFT JOIN events AS e ON o.event_id = e.id
					LEFT JOIN dates AS d ON o.id = d.on_campus_event_id
				$on_where_sql
				GROUP BY o.id
				$on_having_sql)
			UNION (SELECT o.event_id, o.departure_start_time AS start_time
				FROM off_campus_events AS o
					LEFT JOIN events AS e ON o.event_id = e.id
				$off_where_sql)
			ORDER BY start_time");

		$list = array();
		foreach ($ids as $id) {
			$list[] = Event::factory($id);
		}

		return $list;
	}

	protected function getSoapParams() {
		$params = array(
			"id" => null,
			"category_id" => 0,
			"organization_id" => $this->getOrganizationId(),
			"name" => $this->getName(),
			"description" => $this->getDescription(),
			"contact" => array(
				"name" => $this->getResponsibleRep()->getFullName(),
				"email" => $this->getResponsibleRep()->getEmail(),
				"phone" => $this->getResponsibleRep()->getPhone(),
			),
			"price" => null,
			"days" => array(),
		);

		if($this->getPromoEventId()) {
			$params["id"] = $this->getPromoEventId();
		}

		return $params;
	}
}

?>
