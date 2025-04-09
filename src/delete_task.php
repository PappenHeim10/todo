<?php

// Definiere den Root-Pfad der Anwendung, falls noch nicht geschehen
// Wichtig, wenn dieses Skript direkt aufgerufen wird
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__)); // Annahme: deleteTask.php ist in src/
}

// Lade Konfigurationen und Autoloader (oder benötigte Klassen/Funktionen)
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/vendor/autoload.php'; // ODER dein custom autoload.php
require_once APP_ROOT . '/src/functions.php'; // Für Logging

// *** NEU: JSON Header setzen - GANZ AM ANFANG! ***
header('Content-Type: application/json');

// *** NEU: Datenbankverbindung herstellen ***
try {
    // Verwende deine DbConnection Klasse
    $db = new TodoApp\Database\DbConnection(); // Stelle sicher, dass der Namespace stimmt
    $conn = $db->getConnect(); // Hole die PDO-Verbindung

    // Prüfen, ob die Verbindung erfolgreich war
    if ($conn === null) {
        // Sende einen generischen Fehler, wenn die Verbindung fehlschlägt
        throw new \RuntimeException("Datenbankverbindung konnte nicht hergestellt werden.");
    }
    // Optional: Log nach erfolgreicher Verbindung
    // hinweis_log("deleteTask: Datenbankverbindung erfolgreich.");

} catch (\PDOException | \RuntimeException $e) {
    // Fehler beim Verbindungsaufbau
    if (function_exists('write_error')) {
         write_error("deleteTask DB Connect Fehler: " . $e->getMessage());
    } else {
         error_log("deleteTask DB Connect Fehler: " . $e->getMessage()); // Fallback
    }
    http_response_code(500); // Internal Server Error
    // Sende JSON-Fehler und beende Skript
    echo json_encode(['success' => false, 'message' => 'Interner Serverfehler bei Datenbankverbindung.']);
    exit; // Wichtig: Skript hier beenden!
}

// --- ID holen und validieren ---
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt.']);
    // Optional: Log...
    if (function_exists('write_error')) write_error("Methode nicht erlaubt: " . $_SERVER['REQUEST_METHOD']);
    exit; // Wichtig: Skript hier beenden!
}

$taskId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

if ($taskId === false || $taskId === null) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Ungültige ID.']);
    exit; // Wichtig: Skript hier beenden!
}

// --- Löschen ---
try {
    $sql = "DELETE FROM tasks WHERE id = :id";
    // *** JETZT ist $conn definiert! ***
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
    $success = $stmt->execute();

    if ($success && $stmt->rowCount() > 0) {
        // Status Code 200 ist Standard, muss nicht gesetzt werden
        echo json_encode(['success' => true]);
    } elseif ($success && $stmt->rowCount() === 0) {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Task nicht gefunden.']);
    } else {
        // execute() gab false zurück
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Löschen fehlgeschlagen.']);
    }
} catch (\PDOException $e) { // Fange PDOException spezifisch hier
    if (function_exists('write_error')) {
         write_error("Fehler beim Löschen (ID: $taskId): " . $e->getMessage());
    } else {
        error_log("Fehler beim Löschen (ID: $taskId): " . $e->getMessage());
    }
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler beim Löschen.']);
} finally {
    // Optional: Verbindung hier explizit schließen (oft nicht nötig)
    // $conn = null;
    // $db = null; // Löst __destruct aus, wenn vorhanden
}

exit; // Sicherstellen, dass das Skript hier endet und nichts mehr ausgibt

?>