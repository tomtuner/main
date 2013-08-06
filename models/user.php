<?php

class InvalidUserException extends Exception {}

class User extends Model {
	public $custom_config = array(
		'database' => 'users',
		'sequence' => 'users.user_id',
	);
	protected $relationships = array(
		'Organization' => array(
			'type' => 'has_many'
		),
	);
	protected $rules = array();
	
	public function __construct($id = 0) {
		global $db;
		
		$uid = $id;
		if (isset($id) && !empty($id)) {
			if (!is_numeric($id)) {
				$id = trim($id);
				$uid = $db->getOne("SELECT id FROM users.users WHERE username = '$id'");
				if (!$uid) {
					// check in RIT
					require_once ('Net/LDAP2.php');
					
					$ldap = Net_LDAP2::connect (array (
						'dn' => 'uid=cclwww,ou=People,dc=rit,dc=edu',
						'host' => 'ldap.rit.edu',
						'password' => 'zJ8x2e9XPtp8t978rk3b9KJ6b',
						'base' => 'ou=People,dc=rit,dc=edu',
						'starttls' => TRUE,
					));
					
					$attributes = array ('uid','mail','givenName','telephoneNumber','sn');
					$search = $ldap->search(null,"(&(uid={$id}))",array('attributes'=>$attributes));
					$entries = $search->entries();
					
					// user exists in RIT
					// add to users
					if (!empty($entries)) {
						$ritUser = $entries[0]->getValues();
						$user = new User();
						$user->setLastName($ritUser["sn"]);
						$user->setUsername($ritUser["uid"]);
						$user->setFirstName($ritUser["givenName"]);
						if (isset($ritUser["telephoneNumber"])) {
							$user->setPhone($ritUser["telephoneNumber"]);
						}
						if (isset($ritUser["mail"])) {
							$user->setEmail($ritUser["mail"]);
						}
						
						try {
							$user->save();
						} catch (Exception $e) {
							echo "<br>{$e->getMessage()}";
						}
						
						$uid = $user->getId();
					} else {
						//return null;
						throw new InvalidUserException();
					}
				}
			}
		}	
		
		parent::__construct($uid);
	}
	
	public function __call($name, $value) {
		$res = parent::__call($name,$value);
		$catches = array("firstname", "lastname");
		foreach ($catches as $catch) {
			if (stristr($name,$catch)) {
				if (empty($res)) {
					return "<em>mistmatch</em>";
				}
			}
		}
		
		return $res;
	}
	
	// If no email in the DB,
	// will return dce@rit.edu
	public function getEmail() {
		$email = parent::__call("getEmail",null);
		if (empty($email)) {
			$username = $this->getUsername();
			if (!empty($username)) {
				$email = $username."@rit.edu";
			}
		}
		
		return $email;
	}

	public function getFullName() {
		return $this->getFirstName()." ".$this->getLastName();
	}
	
	public function getProperName() {
		return $this->getLastName().", ".$this->getFirstName();
	}
	
	/**
	* Get a user based on first name and last name
	*/
	public static function factory($first,$last) {
		global $db;
		$query = "SELECT id FROM users.users WHERE first_name LIKE '$first' AND last_name LIKE '$last'";
		$id = $db->getOne($query);
		if (PEAR::isError($id)) {
			die($id->getMessage());
		}
		
		return new User($id);
	}
}

?>
