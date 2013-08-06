<?php

class ApiController extends Controller {
	public function __construct() {
	}

	public function checkConfirmationCode() {
		global $db;

		$count = $db->getOne("SELECT COUNT(*) FROM events WHERE confirmation_code = ". $db->quoteSmart($_GET["confirmation_code"]));
		if($count) {
			echo "true";
		} else {
			echo "false";
		}
	}
	public function apiMessage(){
		if(isset($_REQUEST['1'])&& isset($_REQUEST['2'])&&isset($_REQUEST['3'])&& isset($_REQUEST['4'])){
			$to      = $_REQUEST['1'];
			$subject = $_REQUEST['2'];
		$message = $_REQUEST['3'];
		$from = $_REQUEST['4'];
		$headers = "From: $from \r\n";
		mail($to, $subject, $message, $headers);
		}

	}
}
