<?php

class Organization extends Model {
	public $custom_config = array(
		'database' => 'organizations',
		'sort' => 'name',
	);
	protected $rules = array(
		'category_id' => array(
			'type' => 'int',
			'error' => 'Invalid category id.',
		),
		'name' => array(
			'type' => 'string',
			'error' => 'Invalid name.',
		),
	);
	protected $relationships = array(
		'OrganizationCategory' => array(
			'type' => 'belongs_to',
			'self_key' => 'category_id',
		),
	);

	private $advisors;
	private $advisor_ids;

	public function getCategory() {
		return $this->getOrganizationCategory();
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

	public function getAdvisorIds() {
		if(!isset($this->advisor_ids)) {
			global $db;
			$this->advisor_ids = array();
			if($this->getId()) {
				$this->advisor_ids = $db->getCol("SELECT user_id
					FROM organizations.advisors
					WHERE org_id = ". $this->getId());
			}
		}

		return $this->advisor_ids;
	}

	public function getCertified() {
		global $db;

		$id_list = $db->getCol("SELECT DISTINCT m.user_id
			FROM organizations.members AS m
				LEFT JOIN certifications AS c ON m.user_id = c.user_id
				LEFT JOIN users.users AS u ON m.user_id = u.id
			WHERE m.org_id = ". $this->getId() ."
				AND m.position_id != 0
				AND c.user_id IS NOT NULL
			ORDER BY u.last_name, u.first_name");
		$list = array();
		foreach($id_list as $id) {
			$list[] = new User($id);
		}

		return $list;
	}

	public function hasHolds() {
		global $db;

		$results = $db->getCol( "SELECT hold_id FROM newclubs.holds WHERE org_id = " . $this->getId() );
		if ( count( $results ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	public static function getList($conditions = '') {
		return parent::getList(__CLASS__, $conditions);
	}
}

?>
