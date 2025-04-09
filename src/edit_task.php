<?php

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__)); // Annahme: deleteTask.php ist in src/
}


// Lade Konfigurationen und Autoloader (oder benötigte Klassen/Funktionen)
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/vendor/autoload.php'; // ODER dein custom autoload.php
require_once APP_ROOT . '/src/functions.php'; // Für Logging

// *** NEU: JSON Header setzen - GANZ AM ANFANG! ***
header('Content-Type: application/json');


try {
    // Verwende deine DbConnection Klasse
    $db = new TodoApp\Database\DbConnection(); // Stelle sicher, dass der Namespace stimmt
    $conn = $db->getConnect(); // Hole die PDO-Verbindung

    // Prüfen, ob die Verbindung erfolgreich war
    if ($conn === null) {
        // Sende einen generischen Fehler, wenn die Verbindung fehlschlägt
        write_error("Datenbankverbindung konnte nicht hergestellt werden.". __FILE__);
    }
} catch (\PDOException | \RuntimeException $e) {
    // Fehler beim Verbindungsaufbau
     write_error("deleteTask DB Connect Fehler: " . $e->getMessage() . __FILE__);

    http_response_code(500); // Internal Server Error
    // Sende JSON-Fehler und beende Skript
    echo json_encode(['success' => false, 'message' => 'Interner Serverfehler bei Datenbankverbindung.']);

    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'EDIT' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt.']);

    write_error("Methode nicht erlaubt: " . $_SERVER['REQUEST_METHOD']);

    exit;
}

$taskId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
$taskdescription = filter_input(INPUT_GET, 'description', FILTER_SANITIZE_STRING);
$taskdescription = trim($taskdescription); // Leerzeichen entfernen


if ($taskId === false || $taskId === null) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Ungültige ID.']);
    write_error("Ungültige ID: " . $taskId . __FILE__);
    exit; // Wichtig: Skript hier beenden!
}


?>