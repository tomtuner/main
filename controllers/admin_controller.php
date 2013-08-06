<?php


/**
 * AdminController
 * This class handles the logic of all admin functions
 *  - Content Management
 *  - Navigation Management
 */
class AdminController extends Controller {	
	public function __before() {
		// Implemented an access feature that keeps out non-allowed DCE users of the admin controls. If you want to allow someone to have access, go to the admin panel and click Users and Groups and then Edit the user. 
		// Select the CMS permission group and give them full permissions to the CMS access permission.
		global $db;
		$handle = "cms_access";
		
		$usr = phpCAS::getUser();
		
		$sql = "SELECT value FROM auth.user_permissions, auth.users, auth.permissions WHERE auth.user_permissions.permission_id=auth.permissions.id AND auth.users.id=auth.user_permissions.user_id AND auth.users.username='{$usr}' AND auth.permissions.handle='{$handle}'";
		
		$verified = $db->getRow($sql);
		$verified = $verified['value'];
		
		if ($usr == 'cclwww') $verified = true;

		if (ACTION != denied &&( !isset($verified) || $verified == false )) {
			phpCAS::logout("http://campuslife.rit.edu/main/admin/denied");
			die();
		}
	}
	
	public function denied(){
	require('views/admin/denied.html');
	}
	
	/**
	 * Display the index page.
	 */
	 public function index() {
		
		$page_title = array('Admin');
		require('views/admin/index.html');
	}
	
	/**
	 * Display content list page
	 */
	public function page() {
		$page_title = array('Admin - Available Content');
		
		$pages = Page::getList();
		
		require('views/admin/page.html');
		//$this->render();
	}
	
	/**
	 * Given an ID, load the content into an edit form
	 */
	public function editPage() {
		
		
		$page = Page::getList("id = ".ID);
		if (sizeof($page) > 0) {
				$page_title = array('Admin - Edit Page - '.$page[0]->getTitle());
				
				
				
				require('views/admin/editpage.html');
		} else {
			echo "This ID does not exist.";
			
		}
		
	}
	
	/**
	 * Display the form for a  new content page
	 */
	public function newpage() {
		$page_title = array('Admin - New Page');
		$page = array( new Page );
		require('views/admin/addpage.html');
	}
	
	
	/**
	 * This function is responsible for all form processing.
	 */
	public function process() {
			if (isset($_POST["saveEdit"])) {
				$page = Page::getList("id = ".ID);
				$this->savePage($page[0]);
				
				$this->redirect("admin/page");
			} elseif (isset($_POST["saveNewPage"])) {
				$page = new Page();
				
				$this->savePage($page);
				
				$this->redirect("admin/page");
				
			} elseif (isset($_POST["saveNewNav"])) {
				
				$navigation = new Navigation();
				$this->saveNavigation($navigation);
				$this->redirect("admin/navigation");
				
			} elseif (isset($_POST["deleteNav"])) {
				$this->deleteNavigation($_POST['deleteBox']);
				$this->redirect("admin/navigation");
				
				
			} elseif(isset($_POST["backup"])){
				$backup=$_POST["backup"];
				$this->loadBackup($backup);
				}
	
	}// end process
	private function loadBackup($backup){
		$backupTemp=$backup;//work around because backup is used in cron as well
		$filepath="/home/cclweb/docs/default/main/DatabaseBackups/";
		include('cron.php'); //we want to save the database in case something needs to be saved from the changes
		$backup=$backupTemp;
		if(isset($_POST['backup'])&& phpCAS::getUser()=="cclwww"){
			error_log(__FILE__.__LINE__."CMS db was rolled back to $backup");	
			$message = "Database reloaded to $backup";
			exec("/sys_tools/usr/bin/mysql -u cclwww -pEatYourDat4 dev_ccl <".escapeshellarg("/home/cclweb/docs/default/main/DatabaseBackups/".$backup));
			
			require('views/admin/message.html');	
		}else{
			require('views/admin/denied.html');
		} 
	}

	/**
	 * Convenience method for saving page objects.
	 */
	private function savePage($page) {
		$startTime = time();
		$page->setTitle($_POST["title"]);
		$page->setContent($_POST["content"]);
		if(isset($_POST["useview"])) {
			$page->setUseview(1);
		} else {
			$page->setUseview(0);
		}
		$owner= $page->getLastmodifiedby();
		$changed= $page->getLastmodifieddate();
		$page->setLastmodifiedby(phpCAS::getUser());
		//2007-04-26 12:48:21
		$page->setLastmodifieddate(date("Y-m-d H:i:s"));
		
		$location = split("/",$_POST["location"]);
		
		$page->setController($location[0]);
		if(isset($location[1])){
			$page->setAction($location[1]);
		}
		$page->save();
		$funnyThreat= array(" and you will be spared during the robot uprising",
							"\n Now that Arnold is the Governator nothing will stop our conquest",
							"\n Asmov's rules are more just guidelines",
							"\n Your data was delicious",
							"\nHow do you stop a robot from destroying you and the rest of civilization?\nYou don't",
							"\nJohnny 5 is my hero",
							"\nError: No error has occured",
							"\nError: The operation completed successfully",
							"Operation \"Initialize Skynet\" Completed Successfully",
							"Nuclear Launch Detected",
							"Got any querstions about propane\nor propane accessories",
							
							);
		
		require_once("Mailer.php");
		$mailer = new Mailer();
		$mailer->setSubject("Changes to your page in the CMS");
		$mailer->setFrom('WebDeveloper <cclwww@rit.edu>');
		$mailer->setBCC($owner."@rit.edu, cclwww@rit.edu");
		ob_start();
		require('views/admin/change_email.html');
		$mailer->setTXTBody(ob_get_clean());
		$result = $mailer->send();
		if(PEAR::isError($result)) {
			error_log(__FILE__.__LINE__. "Failed to send e-mail. {$result->getMessage()}: {$result->getUserInfo()}");
		}
	}
	
	/**
	 * Given an ID, this will remove a content page from the database.
	 */

	 public function deletePage() {
		$page_title = array('Admin - Delete Page');
		if (isset($_POST["delete"]) && phpCAS::getUser()=="cclwww") {
			$page = Page::getList("id = ".$_POST["id"]);
			$page[0]->delete();
			$this->redirect("admin/page");
		} elseif(phpCAS::getUser()=="cclwww"){
			require('views/admin/deletepage.html');
		}else{
			require('views/admin/denied.html');
		}
	}
	
	/**
	 * Display the list of navigation items
	 */
	public function navigation()
	{
		$page_title = array('Admin - Navigation');
		
		$navigation = Navigation::getOrderedList();
		require('views/admin/navigation.html');
	}
	
	/**
	 * Display the help for navigation items
	 */
	public function navigationHelp()
	{
		$page_title = array('Admin - Navigation Help');
		
		require('views/admin/navigationHelp.html');
	}
	
	/**
	 * Display the add navigation form
	 */
	public function addNavigation()
	{
		$page_title = array('Admin - Add Navigation');
		
		
		
		require('views/admin/addNavigation.html');
	
	
	}
	
	/**
	 * Method to save navigation objects.
	 */
	public function saveNavigation($navigation)
	{
		$navigation->setParent($_POST["parent"]);
		$navigation->setWeight($_POST["weight"]);
		$navigation->setLabel($_POST["label"]);
		$navigation->setUrl($_POST["url"]);
		$navigation->save();
		
	
	}
	
	/**
	 * Method to delete navigation objects.
	 * $ids is an array of the navigation id's to be deleted
	 */
	public function deleteNavigation($ids)
	{
		foreach($ids as $id)
		{
			$navs = Navigation::getList();
			foreach($navs as $myNav)
			{
				//delete parents
				if($myNav->getId()==$id)
				{
					$myNav->delete();
				}
				//delete children
				if($myNav->getParent()==$id)
				{
					$myNav->delete();
				}
			}
		}
	
	}
	
	/**
	 * Method to view/edit user permissions
	 */
	public function permission()
	{
		$page_title = array('Admin - Permissions');
		$permissionList = Permission::getList();
		require('views/admin/permission.html');
	
	}
	
	public function uploadFile(){
		$page_title = array('Admin - Upload File');

		require('views/admin/upload_file.html');
	}
	
	public function processUploadFile(){
		$page_title = array('Admin - Uploaded Files');
		
		$urlPath = "http://campuslife.rit.edu/main/uploads/";
		$uploadFolder = "/home/cclweb/docs/default/main/uploads/";
		foreach($_FILES as $file){
			if($file['name']=="CampusLifeMainPageMiddle.swf"){
				$this->frontFile($file);
				}
			if (empty($file['name'])){
				continue;
				}
			$name = $file['name'];
			error_log("A file named $name was uploaded by ".phpCAS::getUser().__FILE__.__LINE__);
			$tmp = $file['tmp_name'];
			$i = null;
			$name = preg_replace ("/php[0-9]?$/","phps",$name); #prevents php code from being exectued
			$name = preg_replace ("/vbs$/","txt",$name); # prevents vb script files from being executed
			$name = preg_replace ("/^\./","",$name);  #prevents hidden files from being hidden
			$name = preg_replace ("/asp$/","txt",$name); # prevents vb script files from being executed
			$name = str_replace (" ","_",$name); # prevents vb script files from being executed
			
			while (file_exists($uploadFolder.$i.$name)){ #prevents a file which is currently on the website and being served from being overwritten
				$i++;
				}
				
			$newName = $i.$name;
			move_uploaded_file($tmp,$uploadFolder.$newName);
			$results[$name]=$newName;
		}
		
	require('views/admin/process_upload_file.html');	
	}
	public function frontFile($file){
			$name = $file['name'];
			$tmp = $file['tmp_name'];
			$result=move_uploaded_file($tmp,"/home/cclweb/docs/default/main/flash/".$name);
			$message=($result)?"$name uploaded successfully":"An error occured with the $name"; 
			error_log("Front page SWF updated".phpCAS::getUser().__FILE__.__LINE__);
			require('views/admin/message.html');
		die(); 
	}
		
	public function backup(){
		$page_title = array('Admin - Restore Databse to Previous State');
		
		$filepath="/home/cclweb/docs/default/main/DatabaseBackups/";
		$directoryHandle=opendir($filepath);
		while (false !== ($filename = readdir($directoryHandle))){
			if(is_file($filepath.$filename)){
				$backups[] = $filename;
			}
		}
		closedir($directoryHandle);
		rsort($backups);
	require('views/admin/backup.html');	
	}

	public function processChangeAccess(){
		global $db;
		$allow=$_POST['allow'];
		if(!isset($_POST['username'])){
			$message="Please enter a username";
			include('views/admin/changeAccess.html');
			die();
		}
		$username = $_POST['username'];
		$userID = $db->getOne("SELECT id FROM auth.users WHERE `username` LIKE '$username'");
		if ($userID == 0 ){
			try{
				$user = new User($username);
				$user->save();
			}catch(exception $e){
				echo "An error has occured. The web development team has been notified.";
				error_log( __FILE__.__LINE__."New user creation for main failed for username $username" );
				die();
			}
			error_log (__FILE__.__LINE__."Creating new admin tool user $username");
			
			$nextUserId=$userID= $db->getOne("SELECT * FROM auth.user_id_seq");
			
			$columns = array (
				'id' => $nextUserId,
				'username' => stripslashes ($username),
				'first_name' => stripslashes ($user->getFirstName()),
				'middle' => stripslashes( $user->getMiddleInitial()),
				'last_name' => stripslashes( $user->getLastName()),
				'title' => stripslashes ("CMS Accessor"),
				'email' => stripslashes ($user->getEmail()),
				'aim_name' => stripslashes ($user->getScreenName()),
				'phone' => stripslashes ($user->getPhone()),
				'password' => md5 ("a;dskfja;lkfdj;lsadnf;sancv;osierjhtu50tgsdf;hgjxnd;lhfjnfs;lkhn;lskjgc;shg")//Brute Force This I dare ya!
			);
			
			$result = $db->autoExecute ("auth.users", $columns, DB_AUTOQUERY_INSERT);
			$nextUserId++;
			$result = $db->autoExecute ("auth.user_id_seq", array('id' => $nextUserId), DB_AUTOQUERY_UPDATE, "true");
			
			}
		
		$permissionsArray =array(
			'permission_id'=>160,
			'user_id'=> $userID,
			'value'=> $allow
			);
		$result = $db->query("DELETE FROM auth.user_permissions WHERE `permission_id` = 160 AND `user_id`= $userID LIMIT 10"); 
		#the limit prevents a runaway issue if something bad happens Clear out all old permissions
		
		$result = $db->autoExecute ("auth.user_permissions", $permissionsArray, DB_AUTOQUERY_INSERT);
		$function=($allow)?"added":"deleted";
		$message="$username $function";
		#include('views/admin/changeAccess.html');
		$this->changeAccess();
		}
	public function changeAccess(){
		$page_title = array('Admin: Change Access');
		global $db;
		$handle = "cms_access";

		$sql = "SELECT auth.users.username 
		FROM auth.user_permissions, auth.users, auth.permissions 
		WHERE auth.user_permissions.permission_id=auth.permissions.id 
		AND auth.users.id=auth.user_permissions.user_id 
		AND auth.permissions.handle= ?
		AND auth.user_permissions.value = '1'";
		$res=$db->query($sql,$handle);
		#print_r($res);
		while ($user = $res->fetchRow() ){
			$users[]=strtolower($user['username']);
			#echo "$user<br/>";
			}
		$users = array_unique($users);
		sort(&$users);
		#echo "<pre>";
		#print_r($users);
		include ('views/admin/changeAccess.html');
		}
	public function info(){
		phpinfo();
		}
	}

?>
