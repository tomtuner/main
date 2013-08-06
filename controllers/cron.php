#!/sys_tools/usr/bin/php
<?php
	$filename = time().".sql";
	$filepath="/home/cclweb/docs/default/main/DatabaseBackups/";
	exec("/sys_tools/usr/bin/mysqldump dev_ccl pages pages_seq -u dev_ccl_password",$backup);

	
	
	end($backup); //set the key for backup to the last line of backup because it is a timestamp by defination of a mysql dump
	$backup[key($backup)]=""; //destroy it!

	
	
	$directoryHandle=opendir($filepath);//Get the newest backup file
	$newestFile=$newestTime=0;
	while (false !== ($name = readdir($directoryHandle))){ 
		$curTime = filemtime($filepath.$name);
		if($curTime> $newestTime){
			$newestTime = $curTime;
			$newestFile = $name;
		}
	}
	closedir($directoryHandle);
	
	$NewestBackup = file($filepath.$newestFile);
	
	$NewestBackup = implode($NewestBackup, "");
	
	
	
	
	
	if ($NewestBackup == implode($backup, "\n")){ //We dont need to save a backup if nothing has changed since the last backup
		error_log("Informational".__FILE__.__LINE__." Data Not Changed, Backup not stored");
	} else{
		#Error Checking Code
		if( ! file_put_contents($filepath.$filename, implode($backup,"\n"))){
			error_log("Error".__FILE__.__LINE__." Backup failed to store properly");
		}
		else if (filesize($filepath.$filename) < filesize($filepath.$newestFile)-(5*1024)){//if more than 5kb is deleted, alert the admin
			error_log("Error".__FILE__.__LINE__." Backup smaller than expected, please verify manually");
		}else {
			error_log("Informational".__FILE__.__LINE__." Data Changed, Backup stored properly");
		}
	}
	?>
