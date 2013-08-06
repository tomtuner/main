<?php

class GetconnectedController extends Controller {
	
	// for right now, we will hard code the user as CCLWWW, which is the
	// president of TestFraternity
	protected $organization = null;
	protected $selectedOrg;
	
	public function __before() {
		global $db;
	
			
		if (!isset($_POST['su_orgid']) && !isset($_SESSION["su_orgid"])) {
			$orgids = implode(",",$this->account->getOrganizationIds());
			
			// we now have a list of organizations this individual belongs to
			// now we need to assert that they are the president.
			
			//$query = "select org_id from organizations.members where user_id = ".$this->account->getId()." AND org_id IN($orgids) AND position_id = 2";
			$query = "select org_id from getconnected.users where user_id = ".$this->account->getId()." AND org_id IN($orgids) AND position_id = 1";
			$org_id = $db->getOne($query);
		
			if (empty($org_id)) {
				// lets also check if they are listed as a president in orginizations.members
				$query = "select org_id from organizations.members where user_id = ".$this->account->getId()." AND org_id IN($orgids) AND position_id = 2";;
				$org_id = $db->getOne($query);
			
				if (!empty($org_id)) {
					// they still have a presidential record in organizations.members
					// we need to move this over to our local users database
					$user = new User($this->account->getId());
					$user->setOrgId($org_id);
					$user->setPosition(1);
					$user->save();
				}
			}
			
			if (PEAR::isError($org_id)) {
						    $error = "You are not the president of a Greek organization.  If this is in error please contact the <a href=\"mailto:cclwww@rit.edu\">Center for Campus Life</a>.";
							$this->organization = new Organization();
							require("views/getconnected/error.html");
							die();
						}
						 
			$this->organization = new Organization($org_id);
		
			

			// check if they have any organizations they are president of,
			// or if the organization they are attempting to edit is not
			// properly associated with them
			if ($this->organization == null || empty($org_id)) {
					$error = "You are not the president of a Greek organization.  If this is in error please contact the <a href=\"mailto:cclwww@rit.edu\">Center for Campus Life</a>.";
					require("views/getconnected/error.html");
					die();
			}
		} // end normal user
		else {
			// super user
			if ($this->superUser) {
				$suOrg = 0;
				if (isset($_POST["su_orgid"])) {
					$suOrg = $_POST["su_orgid"];
					$_SESSION["su_orgid"] = $_POST["su_orgid"];
					//$this->setViewingOrginzation($_POST["su_orgid"]);
				} 

				// make the specified org_id the current org_id
				$this->organization = new Organization($_SESSION["su_orgid"]);
				
				
				
			} else {
				$error = "You are not the president of a Greek organization.  If this is in error please contact the <a href=\"mailto:cclwww@rit.edu\">Center for Campus Life</a>.";
				require("views/getconnected/error.html");
				die();
			}
		} // end super user / normal user logic
		
		if ($this->superUser) {
			echo "<div style=\"text-align: center; background-color: #c0ab80; border: 1px solid blue; width: 80%; margin-left:auto; margin-right:auto\">You have been granted super user privileges.";
			echo "<form method=\"post\">Select an organization 
			<select name=\"su_orgid\" onChange=\"this.form.submit()\">";
			$query = "select id, name from organizations.organizations where category_id = 3 order by name";
			$orgs = $db->getAll($query);
			//print_r($orgs);
			$name;
			$id;
			foreach($orgs as $org)
			{
				$name = $org['name'];
				$id = $org['id'];
				echo "<option value =\"$id\">$name</option>";
  
			}

			echo "</select></form";
			echo "</div>";
		}
		
	}
	
	
	
	
	
	public function roster() {
		global $db;
		$page_title = array('RIT - getConnected/roster');
		
		// get the roster for the organization
		$sql = "SELECT * FROM organizations.members AS m
		LEFT JOIN users.users AS u ON m.user_id = u.id
		WHERE m.org_id='{$this->organization->getId()}' and position_id!=0 order by last_name";
	
		$res = $db->query($sql);
	
		if(PEAR::isError($res)) 
			{
				die($res->getMessage());
			}
			
			$roster = array();
			while($row = $res->fetchRow()) {
				$roster[] = new User($row["user_id"],$org_id = $this->organization->getId());
			}
		
		
		require('views/getconnected/roster/roster.html');
	}
	
	
	
	
	protected function userTest($user_id) {
		global $db;
		$isMember = false;
		if (!empty($user_id)) {
			$query = "select count(*) from organizations.members where org_id = {$this->organization->getId()} AND user_id = $user_id";
			$isMember = $db->getOne($query);
		}
		if ($this->superUser) {
			$isMember = true;
		}
		
		return $isMember;
	}
	
	
	
}

?>
