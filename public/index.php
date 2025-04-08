<?php
// Definiere den Root-Pfad der Anwendung
define('APP_ROOT', dirname(__DIR__)); // Zeigt auf /meinprojekt

// Lade wichtige Konfigurationen (enthält z.B. DB-Zugangsdaten als Konstanten oder Variablen)
require_once APP_ROOT . '/config/config.php'; 
// Lade globale Funktionen (wie dein getTasks, write_error etc.)
require_once APP_ROOT . '/src/functions.php'; 
// Stelle die Datenbankverbindung her (nutzt Daten aus config.php)
// Die Verbindungslogik könnte auch in einer Funktion in functions.php sein
// oder in einer separaten Datei wie config/db_connect.php
try {
    $conn = connect_db(); // Annahme: Funktion connect_db() ist in functions.php definiert
    hinweis_log("Connected successfully"); 
} catch(PDOException $e) {
    write_error("Connection failed: " . $e->getMessage());
    // Zeige eine nutzerfreundliche Fehlerseite an und beende das Skript
    include APP_ROOT . '/templates/error_page.phtml'; // Beispiel
    exit; 
}

// --- HIER KOMMT DEINE LOGIK FÜR DIE AKTUELLE SEITE --- 
// Z.B. Hinzufügen von Tasks (wie in deinem Code)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_task') {
     if (!empty($_POST['task'])) {
         addTask($conn, $_POST['task']); // Funktion addTask() in functions.php
     } else {
         write_error("Bitte Aufgabe eingeben."); // Oder besser: Fehlermeldung für den Nutzer vorbereiten
     }
     // Leite um oder lade die Seite neu, um Doppel-Posts zu verhindern (Post/Redirect/Get Pattern)
     header("Location: index.php"); 
     exit;
}

// Hole die Tasks für die Anzeige
$tasks = getTasks($conn); // Funktion in functions.php

// Lade das HTML-Template und übergib die Tasks
// Hier beginnt die Trennung von Logik und Präsentation
include APP_ROOT . '/templates/tasks_page.phtml'; 

?>