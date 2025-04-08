<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="papirus.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <title>Todo</title>
</head>

<?php
require_once 'functions.php'; // Funktionen werden hereingestellt



// Datenbankverbindung wird hereingestellt
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todo_list";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    hinweis_log("Connected successfully");

}catch(PDOException $e) {
    write_error("Connection failed: " . $e->getMessage());
}
// Datenbankverbindung wurde hergestellt

// Überprüfen, ob das Formular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob die Aufgabe leer ist
    if (empty($_POST['task'])) {
        write_error("Bitte geben Sie eine Aufgabe ein.");
    } else {
        // Aufgabe in die Datenbank einfügen
        try{
            $sql = "INSERT INTO tasks (task) VALUES (:task)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':task', $_POST['task']);
            $stmt->execute();
            hinweis_log("Aufgabe erfolgreich hinzugefügt.");
        }catch(PDOException $e){
            write_error("Fehler beim Hinzufügen der Aufgabe: " . $e->getMessage());
        }

    }
}
// Hier ist die überprüfung vorebeit
?>


<!-- Hier werden die Aufgaben ausgegeben -->
<div class="taskliste">
    <?php
    $results = getTasks($conn);
    if(isset($results) && count($results) > 0) {
        foreach ($results as $result){
            echo "<div class='task'>";
            echo "<p>" . htmlspecialchars($result['task']) . "</p>";
            echo "<button class='delete' data-id='" . $result['id'] . "'>Delete</button>";
            echo "</div>";
        }
    }
    ?>
</div>
<!-- Hier ist das ende der Aufaben liste -->

<!-- Hier ist das Eingabefeld -->
<form action="" method="POST" id="addTaskForm"> 
    <input type="text" name="task" id="task" placeholder="Add a new task...">
    <button id="addTask" type="submit">Add Task</button>
</form>
<!-- Eingabefeld und Submit Button -->




<footer>
    &copy; 2023 Todo App. All rights reserved.
    <br>
</footer>
</body>
<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Warte, bis das gesamte HTML geladen ist, bevor du versuchst, Elemente zu finden.

        // Finde alle Buttons mit der Klasse 'delete' INNERHALB von Elementen mit der Klasse 'task'.
        // Diese Selektion ist etwas spezifischer und robuster.


        const list = document.querySelectorAll('.taskliste');
        const listItem = document.querySelectorAll('.task');
        const deleteButtons = document.querySelectorAll('.task .delete');

        // Gehe durch jeden gefundenen Lösch-Button.
        deleteButtons.forEach(button => {
            // Füge einen Klick-Event-Listener zu jedem Button hinzu.
            button.addEventListener('click', function(event) {
 
                // --- Schritt 1: Finde das übergeordnete 'div.task' ---
                // Die Methode closest() geht vom aktuellen Element (dem Button)
                // im DOM-Baum nach oben und gibt das erste übergeordnete Element zurück,
                // das dem angegebenen CSS-Selektor entspricht (hier '.task').
                const taskDiv = this.closest('.task');


                // --- Schritt 2: Hole die ID für den Server-Aufruf ---
                // Lies den Wert des 'data-id'-Attributs aus dem geklickten Button.
                const taskId = this.dataset.id; // oder this.getAttribute('data-id');

                // --- Schritt 3: Entferne das 'div.task' aus dem DOM ---
                if (taskDiv) {
                    // Überprüft sicherheitshalber, ob taskDiv gefunden wurde.
                    var xmlhttp = new XMLHttpRequest();

                    // Funktion definieren, die auf die Antwort wartet
                    xmlhttp.onreadystatechange = function() { 
                        if (this.readyState == 4 && this.status == 200) { // Status 4 bedeutet Anfrage abgeschlossen, Status 200 bedeutet "OK"

                            try {
                                const response = JSON.parse(this.responseText);
                                if (response.success) {
                                    // Nur wenn Server bestätigt, das Element visuell entfernen
                                    taskDiv.remove(); 
                                    console.log(`Task ${taskId} erfolgreich auf Server gelöscht und visuell entfernt.`);
                                    // Optional: Erfolgsmeldung anzeigen
                                    // alert('Task erfolgreich gelöscht!');

                                } else {
                                    // Zeige Fehler vom Server an
                                    console.error(`Serverfehler beim Löschen von Task ${taskId}:`, response.message);
                                    alert(`Fehler vom Server: ${response.message || 'Unbekannter Fehler'}`);
                                }
                            } catch (e) {
                                // Fehler beim Parsen der JSON-Antwort
                                console.error(`Ungültige Antwort vom Server für Task ${taskId}:`, this.responseText);
                                alert('Ungültige Antwort vom Server erhalten.');
                            }

                        } else if (this.readyState == 4) {
                            // Fehler bei der Anfrage (nicht Status 200)
                            console.error(`Fehler bei der Anfrage zum Löschen von Task ${taskId}. Status: ${this.status}`);
                            alert(`Fehler bei der Serveranfrage: ${this.status}`);
                        }
                    };

                    xmlhttp.open("GET", "deleteTask.php?id=" + encodeURIComponent(taskId), true); // true für asynchron

                    // SENDE die Anfrage
                    xmlhttp.send();

                } else {
                    // Sollte eigentlich nicht passieren, wenn die HTML-Struktur korrekt ist.
                    console.error("Konnte das übergeordnete '.task'-Div nicht finden.");
                }
            });
        });

    }); 
</script>
</html>