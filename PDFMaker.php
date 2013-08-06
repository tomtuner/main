<?php
$id=1337;
$AName = "Leroy Jenkins";
$CName = "Raid Ruiners";
$ADept = "Technomancy Department";
$ATitle = "Arch Technomancer";
$AStaff=0;
$APhone="123-456-7890";
$AEmail="abc1234@rit.edu";
ini_set('display_errors','On');
error_reporting(E_ALL);
$fp=fopen($id.".doc",'w+');
$str = "<P ALIGN=CENTER><B>In order to be considered for official recognition, every student club must have a full-time faculty or staff member as an advisor.&nbsp; Hourly, part-time individuals may act as assistant advisors but the main advisor needs to be full-time and is the one ultimately responsible for all club purposes.</B></P>";
$strbr ="<BR><BR>";
$strlis ="</LI><LI>";
$str .="Advisor's Name: ".$AName."<BR>";
$str .="Club Name: ".$CName."<BR>";
$str .="Department: ".$ADept."&nbsp;&nbsp; Title: ".$ATitle."<BR>";
$str .="Phone Number: ".$APhone."&nbsp;&nbsp; Email: ".$AEmail."<BR>";
if($AStaff){
	$str .="___ Staff &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _<U>X</U>_ Faculty <BR>";
} else {
	$str .="_<U>X</U>_ Staff &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___ Faculty <BR>";
}
$str .= $strbr;
$str .="As an advisor for ".$CName.".&nbsp; I agree to assume counseling and informational roles in relation to the club by (but not limited to):<BR>";
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
$str .="Signature ___<U>".$AName."</U>___ &nbsp;&nbsp;&nbsp; Date __<U>".date("M j, Y")."</U>__";

fwrite($fp, $str);
fclose($fp);
?>