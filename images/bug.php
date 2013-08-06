<?php

#this is the bug portion of the bot tracking system. It returns the headers for an image, reads the image and writes to the database

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

header('Content-Type: image/png');
readfile('bug.png');
/**
$mysql=mysql_connect("127.0.0.1","comp","vmce,FPuUEWSDYa4");
mysql_select_db('computer');
$query="DELETE FROM 

$result= mysql_query($query);
**/
?>


