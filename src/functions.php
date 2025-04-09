<?php


function write_error($message):void {
	$logFile = APP_ROOT . '/error.log';// Protokolldatei im übergeordneten Verzeichnis
	$timestamp = date('d-m-Y H:i:s');
	$logMessage = "[$timestamp] $message\n";
	
	file_put_contents($logFile, $logMessage, FILE_APPEND);
}


function hinweis_log($message):void{
	$hinweise = APP_ROOT . '/hinweise.log';
	$timestamp = date('d-m-Y H:i:s');
	$logMessage = "[$timestamp] $message\n";

	file_put_contents($hinweise, $logMessage, FILE_APPEND | LOCK_EX);
}



?>