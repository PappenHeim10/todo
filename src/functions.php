<?php



function write_error($message):void {
	$logFile = __DIR__ . '\error.log';// Protokolldatei im übergeordneten Verzeichnis
	$timestamp = date('d-m-Y H:i:s');
	$logMessage = "[$timestamp] $message\n";
	
	file_put_contents($logFile, $logMessage, FILE_APPEND);
}


function hinweis_log($message):void{
	$hinweise = __DIR__ . '\hinweis.log';
	$timestamp = date('d-m-Y H:i:s');
	$logMessage = "[$timestamp] $message\n";

	file_put_contents($hinweise, $logMessage, FILE_APPEND | LOCK_EX);
}


function getTasks($conn) {
    try{
        $sql = "SELECT * FROM tasks ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        write_error("Fehler beim Abrufen der Aufgaben: " . $e->getMessage());
        return [];
    }

}

function deleteTask($conn, $taskId) {

}

?>