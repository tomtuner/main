<?php
include 'sololib.php';
$link=openDB();

mysql_select_db("organizations",$link);

$q = makeSafe(strtolower($_GET["q"]));
$limit=makeSafe($_GET["limit"]);
if (!$q) return;
if (strlen($q)<1) return;

$query="SELECT DISTINCT `name` FROM `organizations` WHERE `name` LIKE '%$q%' OR `acronym` LIKE '$q%';"; #match either the name or the acrynom

//if ($limit){
//	$limit = $limit + 5;
//    $query=$query." LIMIT $limit";
//	}

$result=smysql_query($query);
$num=mysql_numrows($result);

while ($num>$i){
	$row=mysql_fetch_assoc($result);
	echo $row['name'];
	echo "\n";
	$i++;
}

/*
This is the page that is queried by the art request form  to generate the list of organizations


*/
?>