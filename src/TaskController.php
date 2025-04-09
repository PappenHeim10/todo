<?php

namespace TodoApp; // Namespace für die Klasse, wichtig für den Autoloader


class TaskController{
    // Diese Klasse ist für die Verwaltung der Aufgaben verantwortlich.
    // Sie enthält Methoden zum Hinzufügen, Bearbeiten, Löschen und Abrufen von Aufgaben.

    private $conn; // PDO-Objekt für die Verbindung

    public function __construct($dbConnection){
        $this->conn = $dbConnection; // Setze die Verbindung
    }

    public function addTask($taskDescription){
        // Hier wird eine neue Aufgabe zur Datenbank hinzugefügt
        try {
            $stmt = $this->conn->prepare("INSERT INTO tasks (task) VALUES (:task)");
            $stmt->bindParam(':task', $taskDescription);
            $stmt->execute();
            hinweis_log("Aufgabe erfolgreich hinzugefügt: " . $taskDescription); // Protokolliere die Aktion

        } catch (\PDOException $e) {
            
            write_error("Fehler beim Hinzufügen der Aufgabe: " . $e->getMessage()); // Protokolliere den Fehler
        }
    }

    public function getTasks(){
        // Hier werden alle Aufgaben aus der Datenbank abgerufen
        try {
            $stmt = $this->conn->prepare("SELECT * FROM tasks");
            $stmt->execute();
            return $stmt->fetchAll(); // Gibt alle Aufgaben zurück
        } catch (\PDOException $e) {
            write_error("Fehler beim Abrufen der Aufgaben: " . $e->getMessage()); // Protokolliere den Fehler
            return []; // Gibt ein leeres Array zurück, wenn ein Fehler auftritt
        }
    }
}

?>