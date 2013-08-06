<?php

class SauController extends Controller 
{
	public function reservation()
	{
		$page_title = array("Services | Campus Life Reservation");
			
		require('views/sau/reservation.html');
	}
	
	public function building()
	{
		$page_title = array("Services | Building Services");
		
		global $db;
		if (!isset($_GET['sort'])){
			$_GET['sort']="place";
		}
		$result = $db->query("SELECT * FROM ccl.idaphone WHERE in_sau='1' ORDER BY " . $_GET['sort']);
		
		require('views/sau/building.html');
	}
	
	public function buildingDetails()
	{
		$page_title = array("Services | Building Services | Details");
		
		global $db;
		if(!isset($_GET['id']) || !is_numeric($_GET['id'])){ 
                        header('Location: http://campuslife.rit.edu/main/sau/building?sort=Place');
                        die();
                }	
		if($_GET['id']==342){//Move the salon to global village
			header('Location: http://www.rit.edu/fa/globalvillage/content/shops-and-points-interest');
			die();
		}
		$result = $db->query("SELECT * FROM ccl.idaphone WHERE ID=" . $_GET['id']);
		
		require ("views/sau/buildingDetails.html");
	}
	
	public function mailReservation()
	{
		//No title required. This is a logic page
		require ("views/sau/mailReservation.html");
	}
	
	public function hours()
	{
		$page_title = array("SAU | Hours of Operation");
		
		require("views/sau/hours.html");
	}
	
	public function evr()
	{
		$page_title = array("Services | Event Registration");
		
		require("views/sau/evr.html");
	}
	
	public function roomDetails()
	{
		$page_title = array("Room Descriptions | Details");
		
		global $db;
		if(!isset($_GET['id']) || !is_numeric($_GET['id']) || !$_GET['id']>0  ){
			header('Location: http://campuslife.rit.edu/main/sau/descriptions');
			die();
		}
		$result = $db->query("SELECT * FROM ccl.saurooms WHERE ID=" . $_GET['id']);
		
		require("views/sau/roomDetails.html");
	}
	
	public function descriptions()
	{
		$page_title = array("Room Descriptions");
		
		global $db;
		$allRooms = $db->query("SELECT * FROM ccl.saurooms WHERE displayInDescriptionList=1 ORDER BY Name");
		$roomBuildings = $db->query("SELECT * FROM ccl.saubuildings ORDER BY saubuildings.name");
		
		require("views/sau/descriptions.html");
	}
}

?>
