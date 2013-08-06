<?php

class FratsorlifeController extends Controller 
{	
	public function chapters()
	{
		$page_title = array('Greek Chapters');
		
		global $db;
		$result = $db->query ("SELECT * FROM newgreeks.greek_profile WHERE inactive='0' ORDER BY n_name");
		
		require('views/fratsorlife/chapters.html');
	}
	
	public function chapterDetails()
	{
	if (!isset($_GET['id'])){
		header("Location: http://campuslife.rit.edu/main/fratsorlife/index");
		die();
	}
		$join_title = "Greek Chapters | ";
		if(isset($_GET['type'])){
			$join_title = $join_title. $_GET['type'];
		}
		$page_title = array($join_title);
		
		global $db;
		$result = $db->query ("SELECT * FROM newgreeks.greek_profile WHERE org_id='" . $_GET['id'] . "'");
		
		//Getting Local Contact Information
		$seql = "SELECT * FROM users.users INNER JOIN (organizations.members INNER JOIN organizations.organizations ON organizations.members.org_id = organizations.organizations.id) on users.users.id = organizations.members.user_id AND organizations.members.position_id=2 AND organizations.organizations.id = " . $_GET['id'];
		
		$sql = "SELECT * FROM users.users INNER JOIN getconnected.users ON users.users.id = getconnected.users.user_id AND getconnected.users.position_id=1 AND getconnected.users.org_id = " . $_GET['id'];
		
		$presidentInfo = $db->query ( $sql );
		
		require('views/fratsorlife/chapterDetails.html');
	}
	
	public function forms()
	{
		$page_title = array('Resources | Forms');
		
		global $db;
		$result = $db->query ( "SELECT * FROM newgreeks.forms WHERE visible='1' ORDER BY name" );
		
		while( $row = $result->fetchRow() )
		{
			//Changing the extension from .doc to .pdf
			list( $fileName, $extension ) = explode( "[.]", $row['filename'] );
			$fileName = $fileName . ".pdf";
						
			switch( $row['category'] )
			{
				case "form":
				{
					$formName[] = $row['name'];
					$formFile[] = $fileName;
					break;
				}
				case "const":
				{
					$constName[] = $row['name'];
					$constFile[] = $fileName;
					break;
				}
				case "misc":
				{
					$miscName[] = $row['name'];
					$miscFile[] = $fileName;
					break;
				}
				case "policy":
				{
					$policyName[] = $row['name'];
					$policyFile[] = $fileName;
					break;
				}
			}
		}
		$linkResult = $db->query ( "SELECT * FROM newgreeks.links ORDER BY name" );
		
		require('views/fratsorlife/forms.html');
	}
	
	public function minutes()
	{
		$page_title = array('Resources | Minutes');
		
		global $db;
		$result = $db->query ( "SELECT * FROM newgreeks.minutes" );
		
		while( $row = $result->fetchRow() )
		{
			switch( $row['council'] )
			{
				case "88":
				{
					$filename['88'][] = $row['filename'];
					$date['88'][] = date( "m/d/y", $row['date'] );
					break;
				}
				case "403":
				{
					$filename['403'][] = $row['filename'];
					$date['403'][] = date( "m/d/y", $row['date'] );
					break;
				}
				case "102":
				{
					$filename['102'][] = $row['filename'];
					$date['102'][] = date( "m/d/y", $row['date'] );
					break;
				}
				case "49":
				{
					$filename['49'][] = $row['filename'];
					$date['49'][] = date( "m/d/y", $row['date'] );
					break;
				}
				case "401":
				{
					$filename['401'][] = $row['filename'];
					$date['401'][] = date( "m/d/y", $row['date'] );
					break;
				}
			}
		}
		
		require('views/fratsorlife/minutes.html');
	}
	
	public function terms()
	{
		$page_title = array('Resources | Alphabet and Terms');
		
		require('views/fratsorlife/terms.html');
	}
	
	public function between() {
		$page_title = array("Resources | Between The Columns");
		global $db;
		$result = $db->query ( "SELECT * FROM newgreeks.forms WHERE visible='1' AND category='btc' ORDER BY name" );
		
		while( $row = $result->fetchRow() ) {
			$files[] = $row;
		}
		require("views/fratsorlife/between.html");
	}
	
	public function greekcouncil()
	{
		$page_title = array('Governing Councils | Greek Council');
		
		require('views/fratsorlife/greekcouncil.html');
	}
	
	public function cpc()
	{
		$page_title = array('Greek Councils | CPC');
		
		require('views/fratsorlife/cpc.html');
	}

	public function gamma()
	{
		$page_title = array('Greek Councils | GAMMA');
		
		require('views/fratsorlife/gamma.html');
	}
	
	public function ifc()
	{
		$page_title = array('Greek Councils | IFC');
		
		require('views/fratsorlife/ifc.html');
	}
	
	public function nphc()
	{
		$page_title = array('Greek Councils | NPHC');
		
		require('views/fratsorlife/nphc.html');
	}

	public function tips()
	{
		$page_title = array('Resources | Recruitment Tips');
		
		require('views/fratsorlife/tips.html');
	}
	
	public function fratsorInfo()
	{
		$page_title = array('Request more information');
		
		global $db;
		$result = $db->query ("SELECT * FROM newgreeks.greek_profile WHERE inactive='0' ORDER BY n_name");
		
		require('views/fratsorlife/fratsorinfo.html');
	}
	
	public function submitRequest()
	{
		global $db;
		
		$allowed_to_pass = 1;
		
		// Array containing the specific error messages to return to the user
		$errorMessage = array('first_name' => 'First Name',
							'last_name' => 'Last Name',
							'street' => 'Street',
							'city' => 'City',
							'zip' => 'Zip Code',
							'email' => 'E-mail',
							'bad_email' => 'E-mail address is not valid',
							'phone' => 'Phone number',
							'phone_area' => 'Phone Number',
							'bad_phone' => 'Phone number is not valid');
		
		
		
		
		// Get all information from the page
		$personal_info = $_POST['personal_info'];
		
		$permanent_address = $_POST['permanent_address'];
		$current_address = $_POST['current_address'];
		
		$sendInfoTo	= $_POST['sendInfoTo'];
		
		$contact_info = $_POST['contact_info'];
		
		$enrollmentstatus = $_POST['enrollmentstatus'];
		
		$info_on = $_POST['info_on'];
		
		//$specific_request = $_POST['specific_request'];
		
		// ###################################
		// ########### Begin Validation ##############
		// ###################################
		
		// Prepare persistant data in case we have to return an error to the user
		$persistantData = "";
		foreach ($_POST as $name=>$value)
		{
			#echo ("name: " . $name . ", value: " . $value . "<br>");
			if ( is_array($value) )
			{
				foreach ($value as $name2=>$value2)
				{
					if ($name== "permanent_address")
					{
						$prefix = "perm_";
					} elseif ($name== "current_address")
					{
						$prefix = "curr_";
					} else
					{
						$prefix = "";
					}
					
					$persistantData = $persistantData . "&" . $prefix . $name2 . "=" . $value2;
				}
			} 
			else
			{
				$persistantData = $persistantData . "&" . $name . "=" . $value;
			}
		}
		
		// Checks for empty fields (middle initial and im are allowed to be empty)
 		foreach ( $_POST as $names=>$values )
		{
			if ( is_array($values) )
			{
				foreach ($values as $name2=>$value2)
				{
					if ( $value2 == "" && $name2 != 'middle_initial' && $name2 != 'im' && $name2 != '' )
					{
						$allowed_to_pass = 0;
						$errMsg = "Please fill in the " . $errorMessage[$name2] . " field.";
						header ( "Location: /main/fratsorlife/fratsorInfo?e=1&emsg=" . $errMsg . $persistantData );
					}
				}
			} else if ( $values == "")
			{
				$allowed_to_pass = 0;
				$errMsg = "Please fill in the " . $errorMessage[$names] . " field.";
				header ( "Location: /main/fratsorlife/fratsorInfo?e=1&emsg=" . $errMsg . $persistantData );
			}
		}
		
		// Check for valid email
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $contact_info['email'])) {
			$allowed_to_pass = 0;
			header ( "Location: /main/fratsorlife/fratsorInfo?e=1&emsg=" . $errorMessage['bad_email'] . $persistantData );
		}

		// Check for valid phone number
		if( (!ereg("^[0-9]{3,5}$", $contact_info['phone_area'])) || (!ereg("^[0-9-]{0,8}$", $contact_info['phone'])) ){
			$allowed_to_pass = 0;
			header ( "Location: /main/fratsorlife/fratsorInfo?e=1&emsg=" . $errorMessage['bad_phone'] . $persistantData );
		}
		
		// ####################################
		// ############ End Validation ###############
		// ###################################

		// If the information passed validation, insert into the DB and send out a notification email
		if ( $allowed_to_pass == 1 )
		{
			// Start email body text
			$body = "";

			$body = $body . "Request for fraternity and sorority information created by : " . $personal_info['first_name'] . " ";
			$body = $body . $personal_info['middle_initial'] . " " . $personal_info['last_name'] . "\n";
			$body = $body . "E-mail: " . $contact_info["email"] . "\n";
			$body = $body . "Phone #: (" . $contact_info["phone_area"] . ") " . $contact_info["phone"] . "\n";
			$body = $body . "IM: " . $contact_info["im"] . "\n\n";
			
			$body = $body . "Permanent address:\n";
			$body = $body . "   " . $permanent_address['street'] . "\n";
			$body = $body . "   " . $permanent_address['city'] . "\n";
			$body = $body . "   " . $permanent_address['state'] . " " . $permanent_address['zip'] . "\n\n";
			
			$body = $body . "Current address:\n";
			$body = $body . "   " . $current_address['street'] . "\n";
			$body = $body . "   " . $current_address['city'] . "\n";
			$body = $body . "   " . $current_address['state'] . " " . $current_address['zip'] . "\n\n";
			
			$body = $body . "Send info to: " . $sendInfoTo . "\n\n";
			
			$body = $body . "Enrollment status: " . $enrollmentstatus . "\n\n";
			
			// Insert the request into the DB
			$sql = $db->prepare( "INSERT INTO newgreeks.info_requests( first_name, middle_i, last_name, gender, perm_street, perm_city, perm_state, perm_zip, curr_street, curr_city, curr_state, curr_zip, send_to, im, mail, phone, enrollment, info_on ) VALUES ( ?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )" );
			$data = array ( $personal_info['first_name'], $personal_info['middle_initial'], $personal_info['last_name'], $personal_info['gender'],$permanent_address['street'],$permanent_address['city'], $permanent_address['state'], $permanent_address['zip'], $current_address['street'],$current_address['city'], $current_address['state'], $current_address['zip'], $sendInfoTo, $contact_info["im"], $contact_info["email"], "(" . $contact_info["phone_area"] . ") " . $contact_info["phone"], $enrollmentstatus, $info_on);
			$res = $db->execute($sql,$data);
			
			if(PEAR::isError($res))
			{
				die($res->getMessage());
			}
			
			// Get the request ID, this gets the last ID inserted into the database, since the request is made right after the insert command, the risk of concurrent inputs is minimal
			$id_temp = $db->query ("SELECT LAST_INSERT_ID();")->fetchRow();
			$request_id = $id_temp['LAST_INSERT_ID()'];
						
			$body = $body . "Looking for information on: " . $info_on . "\n\n";
			
			// Only append organizations requested to email and add rows to table if specific information is requested
			if ( ($info_on != 'general') && (isset ( $_POST['specific_request'] )) )
			{
							
				$orgs = "";
				$first = true;
				foreach ( $_POST['specific_request'] as $name=>$value )
				{					
					if ($first)
					{
						
						$orgs = $orgs . " ( org_id = " . $name . " )";
						$first = false;
					} 
					else
					{
					
						$orgs = $orgs . " OR ( org_id = " . $name . " )";
					}
					$sql = $db->prepare( "INSERT INTO newgreeks.info_requests_to_profiles( request_id, profile_id) VALUES ( ?, ?)");
					$data = array ( $request_id, $name);
					$res = $db->execute($sql,$data);
			
					if(PEAR::isError($res))
					{
						die($res->getMessage());
					}
				}
							
				$body = $body . "Organizations requested:\n";
				
				// Retrieve organization names from the DB based on org_id to include in the email body
				$sql_text = "SELECT n_name FROM newgreeks.greek_profile WHERE " . $orgs . ";";
				
				$result = $db->query ($sql_text);

				if(PEAR::isError($result))
				{
					die($result->getMessage());
				}
					
				while($row = $result->fetchRow())
				{
					$body = $body . $row['n_name'] . "\n";
				}
				
				$body = $body . ".\n\n";
			}
			
			// Begin to construct the mail message.
			require_once("/var/www/lib/Mailer.php");
			$mailer = new Mailer();
			$mailer->setSubject("Information on Fraternity and Sorority life requested");
			$mailer->setFrom("cclwww@rit.edu");
			$mailer->setBCC("cclwww@rit.edu");
			$mailer->setCC("");
			/* Send to addresses:
				- cclwww@rit.edu
			*/
			#$mailer->setTo("cclwww@rit.edu");
			$mailer->setTo("greek@rit.edu");
			
			
			$mailer->setTXTBody( $body );

			$result = $mailer->send();
			
			if(PEAR::isError($result)) {
				self::$errors[] = "Failed to send e-mail. {$result->getMessage()}: {$result->getUserInfo()}";
			}
			
			// Redirect to the data submitted page.
			header( 'Location: /main/fratsorlife/requestSubmitted' );
			
		} // End if allowed to pass
		
	} // End submitRequest
	
	//Display page after information is submitted to the database, confirms the submission to the database
	public function requestSubmitted()
	{
		$page_title = array('Request Submitted');
		
		require('views/fratsorlife/submit_response.html');
	}
}

