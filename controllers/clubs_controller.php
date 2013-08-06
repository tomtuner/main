<?php

class ClubsController extends Controller
{
	public function find()
	{
		$page_title = array('Find a Club');
		
		require('views/clubs/find.html');
	}
	
	public function all()
	{
		$page_title = array('Find a Club | All Clubs List');
		
		global $db;
		// NOTE: WE NO LONGER SHOW MSO'S OR FRATERNITIES, EVEN IF THEY HAVE PROFILES (this is handled in the view)
		// A better fix would be to have Connections Admin not create a profile for these organizations in the first place
		$sql = "SELECT organizations.organizations.name, organizations.organizations.id FROM organizations.organizations INNER JOIN newclubs.club_profile ON organizations.organizations.id=newclubs.club_profile.org_id AND newclubs.club_profile.inactive=0 AND newclubs.club_profile.show_web=1 ORDER BY organizations.organizations.name";
		$result = $db->query($sql);
	
		//For AJAX search page
		if( isset($_GET['mode']) )
		{
			require('views/clubs/search.html');
		}
		//All club list  page
		else
		{
			require('views/clubs/all.html');
		}
	}
	
	public function category()
	{
	
	if (! isset($_REQUEST['name']) || !isset($_REQUEST['id'])){
		header("Location: http://campuslife.rit.edu/main/clubs/find");
		die();
	}	

		$join_title = "Find a Club | " . $_GET['name'];
		$page_title = array( $join_title );
		
		global $db;
		$sql = "SELECT organizations.organizations.name, organizations.organizations.id FROM organizations.organizations INNER JOIN newclubs.club_profile ON organizations.organizations.id = newclubs.club_profile.org_id AND newclubs.club_profile.category_id=" . $_GET['id'] . " AND newclubs.club_profile.inactive=0 AND club_profile.show_web=1 ORDER BY organizations.organizations.name";
		$result = $db->query($sql);
			
		require('views/clubs/all.html');
	}
	
	public function fairRequest() {
		$page_title = array('Club Fair Registration');
		$t = time();
		//if ($this->account->getUsername() == "cclwww")
		//	require("views/clubs/fairclosed.html");
		/// TODO comment back in when needed!
		//if (date("W") > 17 && $t < mktime(17, 0, 0, 4, 14, date("Y")))
		if ($t > mktime(0, 0, 0, 5, 7, date("Y")) && $t < mktime(0, 0, 0, 8, 28, date("Y")))
			require("views/clubs/fair.html");
		else
			require("views/clubs/fairclosed.html");
	}
	
	public function submitFairRequest() {
	
		if (isset($_POST["submitted"])) {
			foreach ($_POST as $key => $value) {
				$$key = addslashes($value);
			}
			
			//Error checking
			if (empty($organization_name)) $_POST["errors"][] = "An organization name is required!";
			if (empty($responsible_rep)) $_POST["errors"][] = "A responsible rep is required!";
			if (empty($email)) {
				$_POST["errors"][] = "An email is required!";
			} else {
				if (!preg_match("/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) {
					$_POST["errors"][] = "You entered an invalid email!";
				}
			}
			
			if (empty($phone)) {
				$_POST["errors"][] = "A phone number is required!";
			} else {
				if (!preg_match("/([0-9]{3})-([0-9]{3})-([0-9]{3})/", $phone)) {
					$_POST["errors"][] = "You entered an invalid phone number!";
				}
			}
			
			if (!isset($_POST["errors"])) {
			//	$request = new FairRequest();
			//	$request->setOrganizationName($organization_name);
			//	$request->setResponsibleRep($responsible_rep);
			//	$request->setEmail($email);
			//	$request->setPhone($phone);
			//	$request->setPowerSource($power_source);
			//	$request->setOtherRequests($other_requests);
			//	$request->setDate(date("Y-m-d",time()));
				//$request->save();				
					
				//$need_power = ($power_source) ? "Yes" : "No";
				
				$need_power = ($power_source) ? "1" : "0";
				$need_interp = ($interpreting_services) ? "1": "0";
				
				global	$db;
				$sql="insert into newclubs.fair_requests values('','".$organization_name."','".$responsible_rep."','".$email."','".$phone."','".$need_power."','".$need_interp."','".$other_requests."','".date("Y-m-d",time())."')";
				$db->query($sql);
				
				require_once("Mailer.php");
				$mailer = new Mailer();
				$mailer->setSubject("Club Fair Confirmation For " . $organization_name);
				$mailer->setFrom("Clubs Administration Coordinator Sarah Griffith <sbgccl@rit.edu>");
				$mailer->setBCC($email . ",sbgccl@rit.edu,cclwww@rit.edu");
				ob_start();
				require('views/clubs/fairEmail.txt');
				$mailer->setTXTBody(ob_get_clean());
				
				ob_start();
				require('views/clubs/fairEmail.html');
				$mailer->setHTMLBody(ob_get_clean());
				$result = $mailer->send();
				
				if(PEAR::isError($result)) {
					error_log("Failed to send club fair confirmation email to " . $email);
				}
				
				$this->redirect("clubs/fairRequestSuccess");
				//$this->fairRequestSuccess();
			} else {
				$this->fairRequest();
			}
		}
	}
	
	public function sendFairConfirmations() {
		if (isset($_POST["emailRequestSubmitted"])) {
			$link = mysql_connect("127.0.0.1", "cclwww", "EatYourDat4");
			if (!$link) {
				die("Database Connection Failure " . mysql_error());
			}
			$selectedDB = mysql_select_db("newclubs", $link);
			if (!$selectedDB) {
				die("Database Selection Failure " . mysql_error());
			}
			$year = date("Y");
			$date= "WHERE date > 01-01-$year";
			echo "<h3>Club Fair Registerations submitted since 01-01-$year</h3>";
			$sql = "SELECT * FROM fair_requests $date";
			$result = mysql_query($sql);
			if (!$result) {
				die("Mass Club Fair Confirmation Email Query Failed!\n" . mysql_error() . $sql);
			}
			
			//$organization_name, $phone, $email, $responsible_rep, $need_power, $other_requests;
			require_once("Mailer.php");
			echo "Sending confirmation emails to the following addresses: " . "<br />";
			while($row = mysql_fetch_assoc($result)) {
				// first set the variables
				$organization_name = $row["organization_name"];
				$phone = $row["phone"];
				$email = $row["email"];
				$responsible_rep = $row["responsible_rep"];
				$need_power = $row["power_source"];
				$need_interp = $row["interp_services"];
				$other_requests = $row["other_requests"];
				
			    echo "Address: " . $row['email'] . "<br />";
				
				// create and send email
				$mailer = new Mailer();
				$mailer->setSubject("Club Fair Confirmation For " . $organization_name);
				$mailer->setFrom('Clubs Administration Coordinator Sarah Griffith <sbgccl@rit.edu>');
				$mailer->setBCC($email . ",jlansing3@gmail.com,sbgccl@rit.edu,cclwww@rit.edu");
				ob_start();
				require('views/clubs/fairEmail.txt');
				$mailer->setTXTBody(ob_get_clean());
				
				ob_start();
				require('views/clubs/fairEmail.html');
				$mailer->setHTMLBody(ob_get_clean());
				$mailerResult = $mailer->send();
				if(PEAR::isError($mailerResult)) {
					error_log("Failed to send club fair confirmation email to " . $email);
				}
			}
			if (!mysql_free_result($result)) {
				error_log("Problem freeing result of club fair confirmations query!");
			}
		}
	}
	
	public function fairRequestSuccess() {
		$page_title = array('Club Fair Registration Success!');
		require("views/clubs/fairRequestSuccess.html");
		
	}
	
	public function fairAdmin() {
		ob_start();
		
		include_once('CAS/CAS.php');
		phpCAS::client(CAS_VERSION_2_0, 'webapps.rit.edu', 443, '/cas');
		phpCAS::forceAuthentication();
		
		$us = phpCAS::getUser();
		echo $us;	
		$authorized= array("cclwww","bxbccl","rjgccl", "sbgccl", "clubs", "clubsa");
		
		if (!in_array($us,$authorized)){
			error_log(__FILE__.__LINE__." ".$us." was denied access to the club fair admin panel ");			
			$this->redirect("clubs/fairAdminDeny"); 
			die();
		}
		
		$mysql=mysql_connect("127.0.0.1","cclwww","EatYourDat4");
		
		mysql_select_db('newclubs');
		$year = date("Y");
		$date= "WHERE date > 01-01-$year";
		$query="select * from fair_requests $date";
		echo "<form id=\"sendEmailsForm\" action=\"/main/clubs/sendFairConfirmations\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"emailRequestSubmitted\" value=\"Send Confirmation Emails\"/>";
		echo "</form>";
		echo "<h3>Club Fair Registerations submitted since 01-01-$year</h3>";
		$result= mysql_query($query);
		$this->mysqlToTable($result);
		ob_end_flush();
	}
	#in views/navigation.html, the link to clubsfair is shown durring certian weeks per year, most of which occur durring the summer. Because the link is not available from approx september 7 to may 1 no reports should be generated before 01-01 for the year in question. Automated by Solo	
	
	// todo: FINISH ME!
	public function fairAdminExcel() {
		ob_start();
		
		include_once('CAS/CAS.php');
		phpCAS::client(CAS_VERSION_2_0, 'webapps.rit.edu', 443, '/cas');
		phpCAS::forceAuthentication();
		
		$us = phpCAS::getUser();
		echo $us;	
		$authorized= array("cclwww","bxbccl","rjgccl", "sbgccl", "clubs", "clubsa");
		
		if (!in_array($us,$authorized)){
			error_log(__FILE__.__LINE__." ".$us." was denied access to the club fair admin panel ");			
			$this->redirect("clubs/fairAdminDeny"); 
			die();
		}
		
		$mysql=mysql_connect("127.0.0.1","cclwww","EatYourDat4");
		
		mysql_select_db('newclubs');
		$year = date("Y");
		$date= "WHERE date > 01-01-$year";
		$query="select * from fair_requests $date";
		echo "<form id=\"sendEmailsForm\" action=\"/main/clubs/sendFairConfirmations\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"emailRequestSubmitted\" value=\"Send Confirmation Emails\"/>";
		echo "</form>";
		echo "<h3>Club Fair Registerations submitted since 01-01-$year</h3>";
		$result= mysql_query($query);
		$this->mysqlToTable($result);
		ob_end_flush();
		
		$filename = "clubcontactlist.xls";
		header("Content-Type: application/msexcel");
		header('Content-Disposition: attachment; filename="'.$filename.'"');
	
		require("views/clubs/fairAdminExcel.html");
	}	
	
	public function advisor() {
		$page_title = array('Club Advisor Form');
		require("views/clubs/advisor.html");
		
	}
	
	public function submitAdvisorForm() {
		if (isset($_POST["submitted"])) {
			foreach ($_POST as $key => $value) {
				$$key = addslashes($value);
			}
			$linesum=$line1+$line2+$line3+$line4+$line5+$line6+$line7+$line8;
			//Error checking
			if (empty($advisorName)) $_POST["errors"][] = "Your name is required!";
			if (empty($advisorDept)) $_POST["errors"][] = "Your department name is required!";
			if (empty($advisorTitle)) $_POST["errors"][] = "Your title is required!";
			if (!isset($staff)) $_POST["errors"][] = "You must select staff or faculty!";
			if (empty($eSig)) $_POST["errors"][] = "You must enter your name in the box before clicking submit!";
			if ($linesum < 8) $_POST["errors"][] = "You failed to check ".(8-$linesum)." checkboxes!";
			if (empty($advisorEmail)) {
				$_POST["errors"][] = "An email is required!";
			} else {
				if (!preg_match("/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $advisorEmail)) {
					$_POST["errors"][] = "You entered an invalid email!";
				}
			}
			
			if (empty($advisorPhone)) {
				$_POST["errors"][] = "A phone number is required!";
			} else {
				if (!preg_match("/([0-9]{3})-([0-9]{3})-([0-9]{3})/", $advisorPhone)) {
					$_POST["errors"][] = "You entered an invalid phone number!";
				}
			}
			
			if (!isset($_POST["errors"])) {
			
				//function AdvisorDocGen($AName="Leroy Jenkins",$ADept="Technomancy Department",$ATitle="Arch Technomancer",$APhone="123-456-7890",$AEmail="abc1234@rit.edu",$AStaff=0)
				$this->AdvisorDocGen($advisorName,$advisorDept,$advisorTitle,$advisorPhone,$advisorEmail,$staff,$eSig);
				
				require_once("Mailer.php");
				$mailer = new Mailer();
				$mailer->setSubject("Club Advisor Form Submission From " . $advisorName);
				$mailer->setFrom("Clubs Administration Coordinator Sarah Griffith <sbgccl@rit.edu>");
				$mailer->setTo("");
				$mailer->setCC("");
				$mailer->setBCC($advisorEmail . ",sbgccl@rit.edu,cclwww@rit.edu");
				
				ob_start();
				
				require('views/clubs/advisorEmailTxt.php');
				$mailer->setTXTBody(ob_get_clean());
				
				ob_start();
				require('views/clubs/advisorEmailHtml.php');
				$mailer->setHTMLBody(ob_get_clean());
				$result = $mailer->send();
				
				if(PEAR::isError($result)) {
					error_log("Failed to send club fair confirmation email to " . $advisorEmail);
				}
				
				$this->redirect("clubs/advisorFormSuccess");
				//$this->fairRequestSuccess();
			} else {
				$this->advisor();
			}
		}
	}
	
	public function advisorFormSuccess() {
		$page_title = array('Club Advisor Form Submission Success!');
		require("views/clubs/advisorFormSuccess.html");
		
	}
	
	public function details()
	{	
		if (!isset($_GET['clubId']) || !isset($_GET['clubName'])){ //send the person back to find instead of throwing an error. Silly Robots!
			header('Location: http://campuslife.rit.edu/main/clubs/find');
			die();
		}
			
		$page_title = array('Find a Club');
		$join_details = "org_id=" . $_GET['clubId'];
		$foo = DetailsClubs::getList( $join_details );
		
		require('views/clubs/details.html');
	}
	
	protected function mysqlToTable($result){
		if (mysql_num_rows($result) == 0) {
			echo "No rows found, nothing to print";
		}
		else{
			$row = mysql_fetch_assoc($result);
			echo '<table><tr>';
			foreach($row as $key => $value){
				echo "<th>$key</th>";
				}
			echo '</tr>';
			do{
				echo "<tr>";
				foreach($row as $key => $value){
					echo "<td>$value</td>";
				}
				echo '</tr>';
			}while ($row = mysql_fetch_assoc($result));
			echo "</table>";
		}
	}

	
	#public function resources()
	# This has moved into the CMS
	#{
	#	$page_title = array('Resources');
	#	
	#	require('views/clubs/resources.html');
	#}
	
	public function AdvisorDocGen($AName="Leroy Jenkins",$ADept="Technomancy Department",$ATitle="Arch Technomancer",$APhone="123-456-7890",$AEmail="abc1234@rit.edu",$AStaff=0,$eSig="Invalid"){
		ini_set('display_errors','On');
		error_reporting(E_ALL);
		$fp=fopen("/home/cclweb/docs/default/main/AdvisorDocs/".$AName.".doc",'w+');
		$str = "<P ALIGN=CENTER><B>In order to be considered for official recognition, every student club must have a full-time faculty or staff member as an advisor.&nbsp; Hourly, part-time individuals may act as assistant advisors but the main advisor needs to be full-time and is the one ultimately responsible for all club purposes.</B></P>";
		$strbr ="<BR><BR>";
		$strlis ="</LI><LI>";
		$str .="Advisor's Name: ".$AName."<BR>";
		$str .="Department: ".$ADept."&nbsp;&nbsp; Title: ".$ATitle."<BR>";
		$str .="Phone Number: ".$APhone."&nbsp;&nbsp; Email: ".$AEmail."<BR>";
		if($AStaff){
			$str .="___ Staff &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <U>&nbsp;X&nbsp;</U> Faculty <BR>";
		} else {
			$str .="<U>&nbsp;X&nbsp;</U> Staff &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___ Faculty <BR>";
		}
		$str .= $strbr;
		$str .="As an advisor for the club, I agree to assume counseling and informational roles in relation to the club by (but not limited to):<BR>";
		$str .="<UL><LI>";
		$str .="As the advisor, I will help students understand and follow campus policies and procedures appropriately.&nbsp; I will ensure that all student leaders, co-advisors, and myself read and understand the RIT Clubs Handbook of Policies and Procedures. (For access to the Clubs Handbook of Policies and Procedures, please visit clubs.rit.edu)";
		$str .=$strlis;
		$str .="As the advisor, I will assist the club in identifying yearly goals, completing necessary paperwork, and aid in the clarification of officer and member responsibilities within the club.&nbsp; In addition, I will serve as a source of information, guidance, and leadership, while understanding that the club is <B>student-directed: I am an advocate, not the director.</B>";
		$str .=$strlis;
		$str .="As the advisor, I will ensure the club designates club members that will attent mandatory club meetings and requirements, and completes requirements/paperwork on time to the Club Center.";
		$str .=$strlis;
		$str .="As the advisor, I will oversee the financial affairs of the club.&nbsp; I will review the Expense Approval Forms (EAF) and other financial material in a timely manner and will ensure that the club e-board members maintain accurate financial records that are consistent with the Club Center Finance Department.&nbsp; I will also ensure that all club funds are properly collected, donated, fundraised, and deposited and done so in accordance with RIT policies.&nbsp; All club dues and proceedings MUST be deposited through the club budget account in the Club Center.";
		$str .=$strlis;
		$str .="As the advisor, I agree to attend scheduled meetings and events to the best of my ability.&nbsp; In addition to attending, I will stay informed about events the club is planning and ensure proper steps are taken to protect the safety and welfare of the club members participating.";
		$str .=$strlis;
		$str .="As the advisor, I will ensure that I or a co-advisor will be in attendance when possible with events that require travel and any level of risk.&nbsp; I will also ensure that Risk Management, the Club Center staff and other parties are notified and that waivers/additional travel forms have been completed before leaving if necessary.";
		$str .=$strlis;
		$str .="As the advisor, I will actively participate in the planning of all on-campus and off-campus events when possible.&nbsp; I will make time available for club officers and members to discuss club matters.&nbsp; I will also serve as a resource to aid in resolving issues confronting the club and its members.";
		$str .=$strlis;
		$str .="As the advisor, I will be an advocate for club programs, purpose, and goals.";
		$str .="</LI></UL>";
		$str .="<P>Thank you for considering serving as an advisor to a campus club.&nbsp; Please contact the RIT Clubs Administrator, Sarah Griffith (sbgccl@rit.edu), with any questions or concerns.&nbsp; For additional information regarding club policies and procedures please refer to our online handbook and documents at clubs.rit.edu</P>";
		$str .="<P>I further understand that I must notify the Clubs Administrator if, for any reason, I am unable to continue with my responsibilities so that a suitable replacement may be found.</P>";
		$str .=$strbr;
		$str .="Signature <U>&nbsp;&nbsp;&nbsp;".$eSig."&nbsp;&nbsp;&nbsp;</U> &nbsp;&nbsp;&nbsp; Date <U>&nbsp;&nbsp;".date("M j, Y")."&nbsp;&nbsp;</U>";
		
		fwrite($fp, $str);
		fclose($fp);
	}
}

?>
