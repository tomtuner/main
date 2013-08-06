<?php

class RosterController extends GetconnectedController {
	public function index() {
		global $db;
		$page_title = array('RIT - getConnected/roster');
		
		// get the roster for the organization
		$sql = "SELECT * FROM organizations.members AS m
		LEFT JOIN users.users AS u ON m.user_id = u.id
		WHERE m.org_id='{$this->organization->getId()}' order by last_name";
	
		$res = $db->query($sql);
	
		if(PEAR::isError($res)) 
			{
				die($res->getMessage());
			}
			
			$roster = array();
			while($row = $res->fetchRow()) {
				$roster[] = new User($row["user_id"],$org_id = $this->organization->getId());
			}
		
		$statusFilter[] = 1;
		$statusFilter[] = 0;
		require('views/roster/roster.html');
	}
	
	public function active()
	{
		global $db;
		$page_title = array('RIT - getConnected/roster');
		
		// get the roster for the organization
		$sql = "SELECT * FROM organizations.members AS m
		LEFT JOIN users.users AS u ON m.user_id = u.id
		WHERE m.org_id='{$this->organization->getId()}' order by last_name";
	
		$res = $db->query($sql);
	
		if(PEAR::isError($res)) 
			{
				die($res->getMessage());
			}
			
			$roster = array();
			while($row = $res->fetchRow()) {
				$roster[] = new User($row["user_id"],$org_id = $this->organization->getId());
			}
		
		$statusFilter[] = 1;
		$statusFilter[] = 0;
		require('views/roster/roster.html');
	
	}
	
	public function other()
	{
		global $db;
		$page_title = array('RIT - getConnected/roster');
		
		// get the roster for the organization
		$sql = "SELECT * FROM organizations.members AS m
		LEFT JOIN users.users AS u ON m.user_id = u.id
		WHERE m.org_id='{$this->organization->getId()}' order by last_name";
	
		$res = $db->query($sql);
	
		if(PEAR::isError($res)) 
			{
				die($res->getMessage());
			}
			
			$roster = array();
			while($row = $res->fetchRow()) {
				$roster[] = new User($row["user_id"],$org_id = $this->organization->getId());
			}
		
		$statusFilter[] = 5;
		$statusFilter[] = 6;
		$statusFilter[] = 7;
		
		require('views/roster/roster.html');
	
	}
	
	public function coop()
	{
		global $db;
		$page_title = array('RIT - getConnected/roster');
		
		// get the roster for the organization
		$sql = "SELECT * FROM organizations.members AS m
		LEFT JOIN users.users AS u ON m.user_id = u.id
		WHERE m.org_id='{$this->organization->getId()}' order by last_name";
	
		$res = $db->query($sql);
	
		if(PEAR::isError($res)) 
			{
				die($res->getMessage());
			}
			
			$roster = array();
			while($row = $res->fetchRow()) {
				$roster[] = new User($row["user_id"],$org_id = $this->organization->getId());
			}
		
		$statusFilter[] = 3;
		require('views/roster/roster.html');
	
	}
	
	public function graduated()
	{
		global $db;
		$page_title = array('RIT - getConnected/roster');
		
		// get the roster for the organization
		$sql = "SELECT * FROM organizations.members AS m
		LEFT JOIN users.users AS u ON m.user_id = u.id
		WHERE m.org_id='{$this->organization->getId()}' order by last_name";
	
		$res = $db->query($sql);
	
		if(PEAR::isError($res)) 
			{
				die($res->getMessage());
			}
			
			$roster = array();
			while($row = $res->fetchRow()) {
				$roster[] = new User($row["user_id"],$org_id = $this->organization->getId());
			}
		
		$statusFilter[] = 4;
		
		require('views/roster/roster.html');
	
	}
	
	public function edit() {
		global $db;
		if ($this->userTest(ID)) {
			$user = new User(ID, $this->organization->getId());
			$positions = Position::getList();
			
			// get all status options
			$query = "select * from getconnected.status";
			$result = $db->query($query);
			$statusList = array();
			while($row = $result->fetchRow()) {
				$statusList[] = $row;
			}
			
			require('views/roster/edit.html');
		}	else {
			// this is not a member of this fraternity
			require('views/permissiondenied.html');
		}
		
	}
	
	public function process() {
		global $db;
		
		if (isset($_POST["saveEdit"])) {
			if ($this->userTest(ID)) {
				$user = new User(ID, $this->organization->getId());
				try {
					// we wont worry about changes to their first and last name
					// if their names ever need to be changed, that is an administrative task
					// and not a task for a faternity leader to have control of.
					$user->setPhone($_POST["phone"]);
					$user->setAddress($_POST["address"]);
					$user->setEmail($_POST["email"]);
					
					
					$user->setPosition($_POST["position"]);
			
				
					$user->setStatus($_POST["status"]);
					
					$user->save();
					
					$this->redirect("roster/");
				}  catch (Exception $e) {
						$error = $e->getMessage();
						require('views/error.html');
				}
				
			} else {
				// this is not a member of this fraternity
				require('views/roster/permissiondenied.html');
			}
		} elseif (isset($_POST["saveUserDCE"])) {
			 // this is a user who needs to be first added to the CCL users.users table
			 // THEN added to this frats roster.
			 
			 $user = new User();
			 try {
				 $user->setOrgId($this->organization->getId());
				 $user->setUsername($_POST["username"]);
				 $user->setFirstname($_POST["first_name"]);
				 $user->setLastname($_POST["last_name"]);
				 $user->setEmail($_POST["email"]);
				 $user->setPhone($_POST["phone"]);
				 $user->setAddress($_POST["address"]);
				 $user->setPosition($_POST["position"]);
				 $user->setStatus($_POST["status"]);
				 
				 $user->save();
			} catch (Exception $e) {
				$error = $e->getMessage();
				require('views/error.html');
			}
		}elseif (isset($_POST["saveUser"])) {
			 // this user is already registered with CCL and just needs to be populated
			 // to getconnected, and organizations.members
			 $user = new User();
			 try {
				 $user->setOrgId($this->organization->getId());
				 $user->setUsername($_POST["username"]);
				 $user->setEmail($_POST["email"]);
				 $user->setPhone($_POST["phone"]);
				 $user->setAddress($_POST["address"]);
				 $user->setPosition($_POST["position"]);
				 $user->setStatus($_POST["status"]);
				 $user->save();
				 
				// echo "status: {$_POST['status']}";
				 
				 $this->redirect("roster/");
			} catch (Exception $e) {
				$error = $e->getMessage();
				require('views/error.html');
			}
			 
			
		}
	}
	
	public function add() {
		global $db;
		// We first have to check if the DCE exists in our global users database
		// THEN we can add the new user to the roster
		
		// If the DCE does NOT exist in the global users database,
		// then we will have to add it
		
		if (isset($_POST["dce"]) && isset($_POST["checkDCE"])) {
			// check dce
			$query = "select * from users.users where username = '".$_POST["dce"]."'";
			$dceUser = $db->getRow($query);
		
			$positions = Position::getList();
			
			// get all status options
			$query = "select * from getconnected.status";
			$result = $db->query($query);
			$statusList = array();
			while($row = $result->fetchRow()) {
				$statusList[] = $row;
			}
			
			// are they already a member of the roster?
			$isMember = $this->userTest($dceUser['id']);
			
			if ($isMember) {
				$error = "This person is already on your roster.";
				require('views/error.html');
			}  else {
					
				
				// user does not exist already in the global database
				if (!$dceUser) {
					$user = new User();
					try {
						$user->setUsername($_POST["dce"]);
						require('views/roster/add_nodce.html');
					} catch (Exception $e) {
						$error = $e->getMessage();
						require('views/error.html');
					}
					
				} else {
					// user does already exist in the global database
					$user = new User($dceUser["id"]);
					
					require('views/roster/add_dce.html');
				}
			} // end member check
		} else {		
			require('views/roster/add.html');
		}
	}

public function remove() {
		global $db;
		
		$user = new User(ID);
		
		// assert that this user belongs to the same organization
		
		if ($this->userTest(ID)) {
		
			if (isset($_POST["removeUser"])) {
				// we have been given a reason for removal
				$request = new RemoveRequest();
				$request->setReason($_POST["reason"]);
				$request->setUserId(ID);
				$request->setOrgId($this->organization->getId());
				// add this request to the appropriate table
				$request->save();
				// and send out the email
				$to = 'cclwww@rit.edu';
				$subject = 'Request For Removal';
				$message = "<p>A request has been made to remove the following member from <b>{$this->organization->getName()}</b>.</p>
				<p>Name: <strong>{$user->getLastname()}, {$user->getFirstname()}</strong></p>
				<p>The reason for removal: ".$request->getReason()."</p>";
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'To: Greek@rit.edu <cclwww@rit.edu>' . "\r\n";
				$headers .= 'From: Greek Admin Tool' . "\r\n";
				mail($to, $subject, $message, $headers);
				
				$this->redirect("roster/");
			} else {		
				require('views/roster/remove.html');
			}
		} else {
			// this is not a member of this fraternity
			require('views/getconnected/permissiondenied.html');
		}
	}
	
}

?>
