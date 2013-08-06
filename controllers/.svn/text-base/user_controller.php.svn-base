<?php

class UserController extends Controller {
	public function __construct() {
		parent::__construct();

		// We'll allow anyone to edit their profile (which also means access to 
		// the ajax function for adding organizations).
		if($this->account->getLevel() < User::LEVEL_INTAKE && !in_array(ACTION, array("profile", "process", "add_organization"))) {
			$this->deny();
		}
	}

	public function profile() {
		if(!isset($this->user)) {
			$this->user = $this->account;
		}

		$page_title = array('Users', $this->user->getFullName());
		require('views/user/record.html');
	}

	public function index() {
		global $db;
		require_once('Pager/Pager.php');

		if(!isset($_GET['letter'])) {
			$_GET['letter'] = 'A';
		}

		$user_ids = $db->getCol("SELECT id
			FROM users.users
			WHERE last_name LIKE ". $db->quoteSmart($_GET['letter'] .'%') ."
			ORDER BY last_name, first_name");
		$users = array();
		foreach($user_ids as $user_id) {
			$users[] = new User($user_id);
		}

		$page_title = array('Users');
		require('views/user/index.html');
	}

	public function results() {
		global $db;

		$id_list = $db->getCol("SELECT id
			FROM users.users
			WHERE MATCH (username, first_name, last_name, email) AGAINST (". $db->quoteSmart($_GET['search']) .")
			ORDER BY last_name, first_name");

		// If we only have one result, just go ahead and redirect to that
		// user's edit page rather than showing a results list.
		if(count($id_list) == 1) {
			$this->redirect('user/edit/'. $id_list[0]);
		}

		foreach($id_list as $id) {
			$users[] = new User($id);
		}

		$page_title = array('Users', 'Search Results');
		require('views/user/results.html');
	}

	public function add() {
		global $db;

		if(!isset($this->user)) {
			$this->user = new User();
		}

		$page_title = array('Users', 'Add');
		require('views/user/record.html');
	}

	public function edit() {
		if(!isset($this->user)) {
			$this->user = new User(ID);
		}

		$page_title = array('Users', $this->user->getFullName());
		require('views/user/record.html');
	}

	public function addOrganization() {
		$this->user = new User(ID);
		$this->user->appendOrganization($_GET["organization_id"]);
		$this->user->save();
	}

	public function process() {
		// Normal users should only be able to update their own account.
		if($this->account->getLevel() < User::LEVEL_INTAKE) {
			$this->user = $this->account;
		} else {
			if(defined('ID')) {
				$this->user = new User(ID);
			} else {
				$this->user = new User();
			}
		}

		global $db;
		$existing_id = $db->getOne("SELECT id
			FROM users.users
			WHERE username = ". $db->quoteSmart($_POST['username']));
		if($existing_id && $existing_id != $this->user->getId()) {
			self::$errors[] = 'The username you selected already exists within our database. You may <a href="user/edit/'. $existing_id .'">edit</a> that user.';
		}

		if($this->account->getLevel() >= User::LEVEL_INTAKE) {
			try {
				$this->user->setUsername($_POST['username']);
			} catch(InputException $e) {
				self::$errors[] = $e->getMessage();
			}

			try {
				$this->user->setFirstName($_POST['first_name']);
			} catch(InputException $e) {
				self::$errors[] = $e->getMessage();
			}

			try {
				$this->user->setLastName($_POST['last_name']);
			} catch(InputException $e) {
				self::$errors[] = $e->getMessage();
			}

			try {
				$this->user->setPhone($_POST['phone']);
			} catch(InputException $e) {
				self::$errors[] = $e->getMessage();
			}

			if($this->account->getLevel() >= User::LEVEL_ADMIN) {
				try {
					$this->user->setLevel($_POST['level']);
				} catch(InputException $e) {
					self::$errors[] = $e->getMessage();
				}

				if($_POST['level'] >= User::LEVEL_CAB) {
					try {
						$this->user->setSupervisorId($_POST['supervisor_id']);
					} catch(InputException $e) {
						self::$errors[] = $e->getMessage();
					}
				} else {
					$this->user->setSupervisorId(null);
				}
			}

			try {
				$this->user->save();
			} catch(Exception $e) {
				self::$errors[] = $e->getMessage();
			}

			if($this->account->getLevel() >= User::LEVEL_ADMIN) {
				if(isset($_POST['certified'])) {
					try {
						$this->user->getCertification()->set(array(
							'user_id' => $this->user->getId(),
							'certification_time' => $_POST['certification_time'],
							'expiration_time' => $_POST['expiration_time'],
						));
						$this->user->getCertification()->save();
					} catch(Exception $e) {
					}
				} else {
					$this->user->getCertification()->delete();
				}

				foreach($_POST['certification_score'] as $id => $data) {
					if($data['preliminary'] === '' && $data['final'] === '') {
						continue;
					}

					try {
						$score = ($id > 0) ? new CertificationScore($id) : new CertificationScore();
						$data['user_id'] = $this->user->getId();
						$score->set($data);
						$score->save();
					} catch(Exception $e) {
					}
				}
			}
		}

		try {
			$this->user->setOrganizationIds(explode(',', $_POST['organization_id_list']));
			// If the user has forgotten to click add, handle that.
			if(isset($_POST["organization"]) && $_POST["organization"] > 0) {
				$this->user->appendOrganization($_POST["organization"]);
			}
		} catch(InputException $e) {
		}

		if(!isset(self::$errors)) {
			try {
				$this->user->save();

				if($this->account->getLevel() < User::LEVEL_INTAKE) {
					$this->redirect('user/profile');
				} else {
					$this->redirect('user/');
				}
			} catch(Exception $e) {
				self::$errors[] = $e->getMessage();
			}
		}

		if(isset(self::$errors)) {
			$this->edit();
		}
	}

	public function delete() {
		global $db;

		$this->user = new User(ID);

		$replacement_ids = $db->getCol("SELECT id
			FROM users.users
			WHERE (MATCH (username, first_name, last_name, email) AGAINST (". $db->quoteSmart($this->user->getFullName()) .")
				OR MATCH (username, first_name, last_name, email) AGAINST (". $db->quoteSmart($this->user->getUsername()) ."))
				AND id != ". $this->user->getId());
		$replacements = array();
		foreach($replacement_ids as $replacement_id) {
			$replacements[] = new User($replacement_id);
		}

		$page_title = array('Users', $this->user->getFullName(), 'Delete');
		require('views/user/delete.html');
	}

	public function processDelete() {
		global $db;

		try {
			if($_POST['replacement_id']) {
				$id = intval($_POST['replacement_id']);
				$result = $db->query("UPDATE certifications SET user_id = $id WHERE user_id = ". ID);
				$result = $db->query("UPDATE event_advisor_link SET user_id = $id WHERE user_id = ". ID);
				$result = $db->query("UPDATE events SET responsible_rep_id = $id WHERE responsible_rep_id = ". ID);
				$result = $db->query("UPDATE events SET advisor_id = $id WHERE advisor_id = ". ID);
				$result = $db->query("UPDATE events SET intake_user_id = $id WHERE intake_user_id = ". ID);
				$result = $db->query("UPDATE events SET reviewer_id = $id WHERE advisor_id = ". ID);
				$result = $db->query("UPDATE events SET pre_approval_initiator_id = $id WHERE pre_approval_initiator_id = ". ID);
				$result = $db->query("UPDATE events SET pre_approver_id = $id WHERE pre_approver_id = ". ID);
				$result = $db->query("UPDATE exceptions SET user_id = $id WHERE user_id = ". ID);
				$result = $db->query("UPDATE messages SET user_id = $id WHERE user_id = ". ID);
				$result = $db->query("UPDATE notification_group_user_link SET user_id = $id WHERE user_id = ". ID);

				$result = $db->query("UPDATE organizations.members SET user_id = $id WHERE user_id = ". ID);
				$result = $db->query("UPDATE organizations.advisors SET user_id = $id WHERE user_id = ". ID);

				$result = $db->query("UPDATE newclubs.archive SET user_id = $id WHERE user_id = ". ID);
				$result = $db->query("UPDATE newclubs.new_financecerts SET user_id = $id WHERE user_id = ". ID);
				$result = $db->query("UPDATE newclubs.user_profile SET user_id = $id WHERE user_id = ". ID);

				$result = $db->query("UPDATE newgreeks.staff SET userid = $id WHERE userid = ". ID);
			}

			$result = $db->query("DELETE FROM notification_group_user_link WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM exceptions WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM event_advisor_link WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM certifications WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM organizations.members WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM organizations.advisors WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM newclubs.user_profile WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM newclubs.new_financecerts WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM users WHERE user_id = ". ID);
			$result = $db->query("DELETE FROM users.users WHERE id = ". ID);

			exit();
			$this->redirect('user/');
		} catch(SqlException $e) {
			echo $e->getMessage();
			exit();
		}
	}

	public function isPhone() {
		$this->user = new User(ID);
		echo ($this->user->getPhone()) ? "true" : "false";
	}
}

?>
