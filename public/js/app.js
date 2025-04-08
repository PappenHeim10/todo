// (Angepasst, um den AJAX-Endpunkt korrekt anzusprechen)


document.addEventListener('DOMContentLoaded', function() {
    const tasklistDiv = document.querySelector('.taskliste'); // Effizienter: Event Delegation

    if (tasklistDiv) {
        tasklistDiv.addEventListener('click', function(event) {
            // Prüfen, ob der Klick auf einem Delete-Button war
            if (event.target.classList.contains('delete')) {
                const button = event.target;
                const taskDiv = button.closest('.task');
                const taskId = button.dataset.id;

                if (taskDiv && taskId) {
                    if (confirm(`Möchtest du Task ${taskId} wirklich löschen?`)) {
                        
                        // Pfad zum AJAX Handler - relativ zum public Verzeichnis
                        // In der Realität wäre das vielleicht /api/delete_task.php, 
                        // wenn der Webserver Anfragen an /api/* an eine PHP-Datei weiterleitet.
                        // Für Einfachheit hier direkter Pfad (ACHTUNG: SRC sollte nicht direkt zugänglich sein!)
                        // BESSER: Routing in index.php, das /api/delete?id=X an delete_task.php weiterleitet.
                        // Hier vereinfacht für das Beispiel:
                        const deleteUrl = '../src/delete_task.php?id=' + encodeURIComponent(taskId); 
                                            // ^^^ ACHTUNG: Funktioniert nur, wenn der Server direkten Zugriff auf src/ erlaubt, 
                                            // was aus Sicherheitsgründen vermieden werden sollte! Siehe Routing unten.

                        fetch(deleteUrl, { // Fetch ist moderner als XMLHttpRequest
                            method: 'DELETE' // oder GET, je nachdem was delete_task.php erwartet
                        })
                        .then(response => {
                            if (!response.ok) {
                                // Fehler vom Server (z.B. 404 Not Found, 500 Server Error)
                                // Versuche, die Fehlermeldung vom Server zu lesen (falls vorhanden)
                                return response.json().catch(() => { // Fehler beim Parsen des JSON ignorieren
                                     throw new Error(`Serverfehler: ${response.status} ${response.statusText}`);
                                });
                            }
                            return response.json(); // Wandle die JSON-Antwort um
                        })
                        .then(data => {
                            if (data.success) {
                                taskDiv.remove(); // Visuell entfernen
                                console.log(`Task ${taskId} erfolgreich gelöscht.`);
                                // Optional: Erfolgsmeldung
                            } else {
                                // Zeige Fehler vom Server an
                                console.error(`Fehler beim Löschen von Task ${taskId}:`, data.message);
                                alert(`Fehler: ${data.message || 'Unbekannter Serverfehler'}`);
                            }
                        })
                        .catch(error => {
                            // Netzwerkfehler oder Fehler beim Verarbeiten der Antwort
                            console.error('Fehler bei der Löschanfrage:', error);
                            alert(`Ein Fehler ist aufgetreten: ${error.message}`);
                        });
                    }
                }
            }
        });
    }
});

