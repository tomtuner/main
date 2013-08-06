<?php

abstract class Controller {
	public static $errors;
	protected $account;

	public function __construct() {
		
		// for right now, we will hard code the user as CCLWWW, which is the
		// president of TestFraternity
		$this->account = new User("cclwww");
	
	}
	
	public function __before() {
		// we only want the appropriate navigation section visible
		// this depends on the controller.
		//$this->SPECIAL = "<style type=\"text/css\">#".CONTROLLER." { display: block !important;}</style>";
		$this->SPECIAL = "<script type=\"text/javascript\">showMenu('".CONTROLLER."')</script>";
	}
	
	public function __call($function, $args) { 
     $args = implode(', ', $args);
 
     // is there content for this page?
     $page = Page::getList("controller='".CONTROLLER."' and action='".$function."'");
    
     if (sizeof($page) > 0) {
     		// should we be displaying the view
			
	    	if ($page[0]->getUseview()) {
	    		require('views/'.CONTROLLER.'/'.$function.'.html');
	    	} else {
	     		// or the database content?
	     		
	     		$page_title = array($page[0]->getTitle());  

	     		require("views/smart/render.html");
	     	}
    } else {
    	require("views/notfound.html");
    }
     
     
  }

	public function pageTitle($page_title) {
		if (empty($page_title)){
		return "&raquo;";
		}
		return implode(' &raquo; ', $page_title);
	}

	protected function message($message, $title = '') {
		$page_title = (empty($title)) ? array('Message') : array($title);
		include('views/message.html');
		exit();
	}

	protected function deny() {
		$this->message('You do not have permission to access this page.');
	}

	protected function redirect($url) {
		$url = (substr($url, 0, 4) == 'http') ? $url : dirname($_SERVER['SCRIPT_NAME']) ."/$url";
		header("Location: $url");
		echo $url;
		exit();
	}

	protected function nl2p($text, $class = '') {
		$class_attribute = ($class) ? " class=\"$class\"" : '';
		return "<p$class_attribute>". preg_replace("/(<br \/>\s*){2,}/", '</p><p>', nl2br($text)) .'</p>';
	}

	protected function formatCost($cost) {
		return '$'. number_format($cost, 2, '.', ',');
	}

	protected function formatPhone($phone) {
		return (!empty($phone)) ? substr($phone, 0, 3) .'-'. substr($phone, 3, 3) .'-'. substr($phone, 6, 4) : '';
	}

	public function reviewEditLink($action, $controller = '') {
		if(!$controller) {
			$controller = CONTROLLER;
		}

		return(ACTION == 'review') ? ' (<a href="'. $controller .'/'. $action .'/'. $this->event->getId() .'?review">Edit</a>)' : '';
	}

	protected function reviewRedirect() {
		$controller = ($this->event instanceof OnCampusEvent) ? 'on_campus_event' : 'off_campus_event';
		if(preg_match("/\?review$/", $_SERVER['HTTP_REFERER'])) {
			$this->redirect($controller .'/review/'. $this->event->getId());
		}
	}
	
	public function render() {
		echo 'views/'.CONTROLLER.'/'.ACTION.'.html';
		require('header.html');
		require('views/'.CONTROLLER.'/'.ACTION.'.html');
		require('footer.html');
	}
}

?>
