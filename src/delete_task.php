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
        write_error("Datenbankverbindung konnte nicht hergestellt werden.");
    }
} catch (\PDOException | \RuntimeException $e) {
    // Fehler beim Verbindungsaufbau
     write_error("deleteTask DB Connect Fehler: " . $e->getMessage());

    http_response_code(500); // Internal Server Error
    // Sende JSON-Fehler und beende Skript
    echo json_encode(['success' => false, 'message' => 'Interner Serverfehler bei Datenbankverbindung.']);

    exit;
}

// --- ID holen und validieren ---
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt.']);

    write_error("Methode nicht erlaubt: " . $_SERVER['REQUEST_METHOD']);

    exit;
}


$taskId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);


if ($taskId === false || $taskId === null) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Ungültige ID.']);
    exit; // Wichtig: Skript hier beenden!
}



exit; // Sicherstellen, dass das Skript hier endet und nichts mehr ausgibt

?>