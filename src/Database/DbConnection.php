<?php
namespace TodoApp\Database; // Namespace für die Klasse, wichtig für den Autoloader

use PDO; // Importiere die globale PDO-Klasse in diesen Namespace
use PDOException; // Importiere PDOException

class DbConnection{ // Datenbankverbindungsklasse
    // Diese Klasse stellt eine Verbindung zur Datenbank her und ermöglicht den Zugriff auf die Datenbank.
    private $conn = null; // PDO-Objekt für die Verbindung
    private String $servername = DB_HOST;
    private String $username = DB_USER;
    private String $password = DB_PASS;
    private String $dbname = DB_NAME;
    private String $dbcharset = DB_CHARSET; // Optional, aber empfohlen


    public function __construct(){

        // Verhindert mehrfache Verbindungsversuche, falls die Klasse öfter instanziiert wird (Singleton wäre eine Alternative)
        if($this->conn === null){
            try{
                $dsn = "mysql:host={$this->servername};dbname={$this->dbname};charset={$this->dbcharset}";
                $this->conn = new PDO($dsn, $this->username, $this->password); // Erstelle eine neue PDO-Verbindung


                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Sehr nützlich!

            }catch(PDOException $e){
                // Fehlerbehandlung, wenn die Verbindung fehlschlägt
                write_error("Verbindungsfehler: " . $e->getMessage() . __FILE__); // Protokolliere den Fehler
            }
        }
    }

    public function getConnect(): ?PDO{
        return $this->conn; // Gibt die Verbindung zurück
    }

    public function __destruct(){
        $this->conn = null; // Setze die Verbindung auf null, um sie zu schließen
    }
}
?>