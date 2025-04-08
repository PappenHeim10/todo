<?php
// Stelle sicher, dass Konfigurationen (falls nötig) geladen sind
// (oft nicht nötig, wenn index.php das schon macht)

// Funktion zur Datenbankverbindung (Beispiel)
function connect_db() {
    // Hole Zugangsdaten (z.B. aus Konstanten, die in config.php definiert wurden)
    $servername = DB_HOST; 
    $username = DB_USER;
    $password = DB_PASS;
    $dbname = DB_NAME;
    $dbcharset = DB_CHARSET; // Optional, aber empfohlen

    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=$dbcharset", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Nützlich!
    return $conn;
}


// Deine anderen Funktionen...
function getTasks(PDO $conn) {
    $
}


function addTask(PDO $conn, string $taskText) {
    try {
        $sql = "INSERT INTO tasks (task) VALUES (:task)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':task', $taskText);
        $stmt->execute();
        hinweis_log("Aufgabe erfolgreich hinzugefügt.");
        return true;
    } catch(PDOException $e) {
        write_error("Fehler beim Hinzufügen der Aufgabe: " . $e->getMessage());
        return false;
    }
}

function hinweis_log(string $message) {
    // Deine Logging-Implementierung
    error_log("HINWEIS: " . $message);
}

function write_error(string $message) {
    // Deine Error-Logging-Implementierung
    error_log("FEHLER: " . $message);
}


?>