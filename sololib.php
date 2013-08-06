<?php

#This is solos php function library, It is used by OrgList.php, which is in turn used by the art request form.
#function makeSafe($input){
#	$input =htmlspecialchars($input);
#	return $input;
#	}
	
function openDB(){
	$link= mysql_connect('localhost:3360','cclwww','EatYourDat4');
	if (!$link) {
		die('Not connected : ' . mysql_error());
	}
	return $link;
}

function smysql_query($input){
	return mysql_query($input);
	}

?>
