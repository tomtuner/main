<?php

class CrestController extends GetconnectedController {
	public function index() {
		global $db;
		$page_title = array('RIT - getConnected');
		
				

		require('views/crest/index.html');
	}

	public function process() {
		if (isset($_FILES["c_crest"])) {
			// Upload Crest
			$crest = $_FILES['c_crest'];

			$ftype = $crest['type'];

			if(strlen($ftype) > 0) {
				if( ($ftype == "image/jpeg") || ($ftype == "image/pjpeg") ) {
					if(!move_uploaded_file($crest['tmp_name'],"/usr/local/www/data-dist/greek/images/crests/" .$this->organization->getId(). "crest.JPG")){
						$error = "There was a problem uploading this file. Please contact the <a href=\"mailto:cclwww@rit.edu\">system administrator</a> if this error is recurring. <a href=\"$_SERVER[HTTP_REFERER]\">Return</a>";
						require('views/error.html');
					} else {
						$this->redirect("crest/");
					}
				} else {
					$error = "Only JPG images can be uploaded for crests.";
					require('views/error.html');
				}
			}
		} // endif
	}
}

?>
