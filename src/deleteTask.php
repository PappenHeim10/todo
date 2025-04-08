<?php
// Definiere den Root-Pfad der Anwendung, falls noch nicht geschehen


if (!defined('APP_ROOT')) {
     define('APP_ROOT', dirname(__DIR__)); // Annahme: delete_task.php ist direkt in src/
}
header('Content-Type: application/json');

// Lade Konfiguration und Funktionen (falls nicht schon global verfügbar)
require_once APP_ROOT . '/config/config.php'; 
require_once APP_ROOT . '/src/functions.php'; 

// Stelle DB-Verbindung her
try {
    $conn = connect_db();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Datenbankverbindungsfehler.']);
    exit;
}

// --- ID holen und validieren ---
// Erwarte DELETE Methode (oder GET, wenn du GET im JS verwendest)
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'GET') { // GET hinzugefügt, passend zum JS-Beispiel oben
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt.']);
    exit;
}

$taskId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

if ($taskId === false || $taskId === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige ID.']);
    exit;
}

// --- Löschen ---
try {
    $sql = "DELETE FROM tasks WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
    $success = $stmt->execute();

    if ($success && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } elseif ($success && $stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Task nicht gefunden.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Löschen fehlgeschlagen.']);
    }
} catch (PDOException $e) {
    write_error("Fehler beim Löschen (ID: $taskId): " . $e->getMessage()); // Serverseitiges Logging
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler beim Löschen.']);
}

$conn = null;
?>