<?php

class AboutController extends Controller 
{	
	public function departments() 
	{
		$page_title = array('Departments');
		
		$foo = Department::getList('view=1');

		require('views/about/departments.html');
	}
	
	public function profstaff()
	{
		$page_title = array('Professional Staff');	
		//Staff details page
		if(isset($_GET['uId']))
		{
			$join_condition = "id=" . $_GET['uId'];
			$foo = Staff::getList( $join_condition );
			
			require('views/about/profstaffBio.html');
		}
		//Staff list page
		else
		{
			$foo = Staff::getList('group_id=2');
			require('views/about/profstaff.html');
		}		
	}
	
	public function studentstaff()
	{
		$page_title = array('Student Staff');
		
		$foo = Staff::getList('group_id=1');
		require('views/about/studstaff.html');
	}
	public function yourip(){
		echo $_SERVER['REMOTE_ADDR'];
		}
}

?>
