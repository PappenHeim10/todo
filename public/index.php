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


//Bestimmt die geünschte Aktion. Standard ist 'list' (Aufgaben anzeigen)
$action = $_GET['action'] ?? 'list';

// Bestimmt die HTTP-Methode
$method = $_SERVER['REQUEST_METHOD'];


// Die Logik für das Hinzufügen einer Aufgabe
// 1. Aufgabe hinzufügen (POST-Request auf ?action=add_task)
if ($action === 'add_task' && $method === 'POST') {
    if (!empty($_POST['task'])) {
        $taskController->addTask($_POST['task']); // Methode im Controller aufrufen
    } else {
        write_error("Versuch, eine leere Aufgabe hinzuzufügen.");
    }
    // Nach POST immer weiterleiten, um Doppel-Posts zu verhindern (Post/Redirect/Get Pattern)
    header("Location: index.php"); // Leite zur Listenansicht zurück
    exit;
}


// 2. Aufgabe löschen (DELETE oder GET Request auf ?action=delete_task&id=...)
elseif ($action === 'delete_task' && ($method === 'DELETE' || $method === 'GET')) {
    header('Content-Type: application/json'); // API-Antwort ist JSON
    $taskId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($taskId) {
        // Rufe die delete-Methode im Controller auf (diese muss erstellt werden!)
        $success = $taskController->deleteTask($taskId);
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            // Controller sollte bei Fehlern false zurückgeben oder Exception werfen
            // Hier könnte man spezifischere Fehlercodes setzen (404 wenn nicht gefunden, 500 bei DB-Fehler)
            http_response_code(404); // Annahme: Nicht gefunden oder Fehler
            echo json_encode(['success' => false, 'message' => 'Task nicht gefunden oder Löschen fehlgeschlagen.']);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Ungültige oder fehlende Task-ID.']);
    }
    exit; // Wichtig: Skript nach API-Antwort beenden
}


// 3. Aufgabe aktualisieren (PATCH oder PUT/POST Request auf ?action=update_task&id=...)
elseif ($action === 'update_task' && ($method === 'PATCH' || $method === 'PUT' || $method === 'POST')) {
    header('Content-Type: application/json'); // API-Antwort ist JSON
    $taskId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // ID aus der URL holen

    // Hole die Daten aus dem Request Body (JSON)
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true); // true -> assoziatives Array

    // Prüfe, ob ID und Daten gültig sind
    if ($taskId && $data !== null && isset($data['task']) && trim($data['task']) !== '') {
        $newTaskText = trim($data['task']);
        // Rufe die update-Methode im Controller auf (diese muss erstellt werden!)
        $updatedTaskData = $taskController->updateTask($taskId, $newTaskText); // Methode muss implementiert werden

        if ($updatedTaskData) { // Methode könnte bei Erfolg die aktualisierten Daten zurückgeben
            echo json_encode(['success' => true, 'updatedTask' => $updatedTaskData]);
        } else {
            http_response_code(404); // Annahme: Nicht gefunden oder Fehler
            echo json_encode(['success' => false, 'message' => 'Task nicht gefunden oder Update fehlgeschlagen.']);
        }
    } elseif (!$taskId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ungültige oder fehlende Task-ID.']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Fehlende oder ungültige Task-Daten im Request Body.']);
    }
    exit; // Wichtig: Skript nach API-Antwort beenden
}


// 4. Aufgabenliste anzeigen (Standardaktion, wenn action='list' oder keine action angegeben ist)
elseif ($action === 'list' && $method === 'GET') {
    // Hole alle Tasks über den Controller
    $tasks = $taskController->getTasks();
    // Lade das HTML-Template und übergib die Tasks
    include APP_ROOT . '/templates/tasks_page.phtml'; // Pfad zum Template anpassen
    exit;
}


// 5. Unbekannte Aktion oder Methode
else {
    http_response_code(404); // Not Found
    // Zeige eine einfache Fehlerseite oder Nachricht
    include APP_ROOT . '/templates/404_page.phtml'; // Beispiel
    // Oder einfach: echo "Seite nicht gefunden.";
    exit;
}

?>