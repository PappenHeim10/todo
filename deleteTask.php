<?php
// Wichtig: Setze den Content-Type auf JSON für die Antwort
header('Content-Type: application/json');

// --- Datenbankverbindung herstellen ---
// Du kannst deine functions.php includieren, wenn dort die Verbindung aufgebaut wird,
// oder die Verbindungsdaten hier erneut angeben. 
// Stellen wir sicher, dass $conn verfügbar ist.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todo_list";
$conn = null; // Initialisieren

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Sende JSON-Fehler zurück
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Datenbankverbindungsfehler.']);
    // Wenn du deine Log-Funktionen hier verfügbar hast:
    // include_once 'functions.php'; 
    // write_error("Connection failed: " . $e->getMessage()); // Logge den Fehler serverseitig
    exit; // Wichtig: Skript beenden
}

// --- ID aus der Anfrage holen und validieren ---
// Wir erwarten die ID als GET-Parameter (?id=...)
if (!isset($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Keine ID übergeben.']);
    exit;
}

// Validiere die ID (z.B. sicherstellen, dass es eine positive Ganzzahl ist)
$taskId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

if ($taskId === false || $taskId === null) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Ungültige ID übergeben.']);
    exit;
}

// --- Löschvorgang durchführen ---
try {
    $sql = "DELETE FROM tasks WHERE id = :id"; // Sicherstellen, dass Tabellen- und Spaltenname korrekt sind!
    $stmt = $conn->prepare($sql);

    // Binde den validierten Integer-Wert
    $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);

    $success = $stmt->execute();

    // Überprüfe, ob die Löschung erfolgreich war und ob eine Zeile betroffen war
    if ($success && $stmt->rowCount() > 0) {
        // Erfolg!
        echo json_encode(['success' => true, 'message' => 'Task erfolgreich gelöscht.']);
    } elseif ($success && $stmt->rowCount() === 0) {
         // ID nicht gefunden (technisch kein Fehler, aber keine Löschung)
         http_response_code(404); // Not Found
         echo json_encode(['success' => false, 'message' => 'Task mit dieser ID nicht gefunden.']);
    } else {
         // Execute() gab false zurück oder rowCount war negativ (unwahrscheinlich mit PDO)
         http_response_code(500); // Internal Server Error
         echo json_encode(['success' => false, 'message' => 'Fehler beim Ausführen des Löschbefehls.']);
    }

} catch (PDOException $e) {
    // Fehler bei der Datenbankoperation
    http_response_code(500); // Internal Server Error
    // Logge den Fehler serverseitig für Debugging!
    // include_once 'functions.php'; // Falls noch nicht geschehen
    // write_error("Fehler beim Löschen der Aufgabe (ID: $taskId): " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler beim Löschen.']);
}

// Verbindung schließen (optional bei PDO am Skriptende)
$conn = null;

?>