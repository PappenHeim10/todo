<?php

// Definiere den Root-Pfad der Anwendung
define('APP_ROOT', dirname(__DIR__));

// Der Autoloader ist wichtig, um Klassen automatisch zu laden
require_once APP_ROOT . '/vendor/autoload.php';

// Lade wichtige Konfigurationen (enthält z.B. DB-Zugangsdaten als Konstanten oder Variablen)
require_once APP_ROOT . '/config/config.php'; 

// Lade globale Funktionen (wie dein getTasks, write_error etc.)
require_once APP_ROOT . '/src/functions.php'; 


// Hier wird die Datenbankverbindung erstellt ANFANG
try {
    $db = new TodoApp\Database\DbConnection; // HIer wird die Datenbankverbindung erstellt
    $conn = $db->getConnect(); // Verbindung geholt
    $taskController = new TodoApp\TaskController($conn); // Instanz der TaskController-Klasse erstellen
    if ($conn === null) {
        write_error("Verbindungsfehler: Verbindung ist null." . __FILE__); // Protokolliere den Fehler
    }
} catch(PDOException $e) {
    write_error("Verbindungsfehler: " . $e->getMessage() . __FILE__); // Protokolliere den Fehler
}
// Hier wird die Datenbanverbindung erstellt ENDE



// Die Logik für das Hinzufügen einer Aufgabe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_task') {
     if (!empty($_POST['task'])) {
        $taskController->addTask($_POST['task']);
     } else {
        write_error("Bitte Aufgabe eingeben."); // Oder besser: Fehlermeldung für den Nutzer vorbereiten
     }
     // Leite um oder lade die Seite neu, um Doppel-Posts zu verhindern (Post/Redirect/Get Pattern)
     header("Location: index.php"); 
     exit;
}


// Hole die Tasks für die Anzeige
$tasks = $taskController->getTasks(); // Hier wird die Methode getTasks() aufgerufen, um alle Aufgaben zu holen


// Lade das HTML-Template und übergib die Tasks
// Hier beginnt die Trennung von Logik und Präsentation
include APP_ROOT . '/templates/tasks_page.phtml'; 

?>