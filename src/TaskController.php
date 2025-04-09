<?php

namespace TodoApp; // Namespace für die Klasse, wichtig für den Autoloader


class TaskController{

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

    public function deleteTask($taskId){
        try {
            $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->bindParam(':id', $taskId);
            $stmt->execute();
            hinweis_log("Aufgabe erfolgreich gelöscht: " . $taskId);
        } catch (\PDOException $e) {
            write_error("Fehler beim Löschen der Aufgabe: " . $e->getMessage()); // Protokolliere den Fehler
            return false; // Gibt false zurück, wenn ein Fehler auftritt
        }
    }

    public function updateTask($taskId, $newDescription){
        // Hier wird eine Aufgabe aktualisiert
        try {
            $stmt = $this->conn->prepare("UPDATE tasks SET task = :task WHERE id = :id");
            $stmt->bindParam(':task', $newDescription);
            $stmt->bindParam(':id', $taskId);
            $stmt->execute();
            hinweis_log("Aufgabe erfolgreich aktualisiert: " . $taskId);
        } catch (\PDOException $e) {
            write_error("Fehler beim Aktualisieren der Aufgabe: " . $e->getMessage()); // Protokolliere den Fehler
            return false; // Gibt false zurück, wenn ein Fehler auftritt
        }
    }
}

?>