document.addEventListener('DOMContentLoaded', function() {

    const tasklistDiv = document.querySelector('.taskliste');


    // --- NEU: Definiere die Basis-URL für den Update-Endpunkt ---
    // Diese Variable sollte in deinem HTML/PHTML mit PHP definiert werden,
    // ähnlich wie wir es für DELETE_TASK_URL_BASE besprochen haben.
    // Beispiel: const UPDATE_TASK_URL_BASE = '/index.php?action=update_task';
    // Stelle sicher, dass diese Variable global verfügbar ist, bevor dieses Skript läuft!
    if (typeof UPDATE_TASK_URL_BASE === 'undefined') {
         console.error("FEHLER: Globale Variable UPDATE_TASK_URL_BASE nicht definiert!");
         // Optional: Funktion hier stoppen oder Fallback-URL verwenden (nicht empfohlen)
    }
     // --- Ende neue URL Definition ---


    if (tasklistDiv) {
        tasklistDiv.addEventListener('click', function(event) {

            // --- Handler für DELETE Button ---
            if (event.target.classList.contains('delete')) {
                const button = event.target;
                const taskDiv = button.closest('.task');
                const taskId = button.dataset.id;

                if (taskDiv && taskId) {
                    // Verwende eine korrekt definierte URL (z.B. DELETE_TASK_URL_BASE)
                     if (typeof DELETE_TASK_URL_BASE === 'undefined') {
                         console.error("FEHLER: Globale Variable DELETE_TASK_URL_BASE nicht definiert!");
                         alert("Konfigurationsfehler beim Löschen.");
                         return;
                     }
                    const deleteUrl = DELETE_TASK_URL_BASE + encodeURIComponent(taskId); // Annahme: DELETE_TASK_URL_BASE endet mit ?id= oder /

                    if (confirm(`Möchtest du Task ${taskId} wirklich löschen?`)) {
                        fetch(deleteUrl, {
                            method: 'DELETE' // Oder GET, je nach Backend
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().catch(() => {
                                    throw new Error(`Serverfehler: ${response.status} ${response.statusText}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                taskDiv.remove();
                                console.log(`Task ${taskId} erfolgreich gelöscht.`);
                            } else {
                                console.error(`Fehler beim Löschen von Task ${taskId}:`, data.message);
                                alert(`Fehler: ${data.message || 'Unbekannter Serverfehler'}`);
                            }
                        })
                        .catch(error => {
                            console.error('Fehler bei der Löschanfrage:', error);
                            alert(`Ein Fehler ist aufgetreten: ${error.message}`);
                        });
                    }
                }
            }

            // --- Handler für EDIT/ABBRECHEN Button ---
            else if (event.target.classList.contains('edit')) {
                const button = event.target;
                const taskDiv = button.closest('.task');
                const taskId = button.dataset.id;

                const taskParagraph = taskDiv.querySelector('p');
                const editInput = taskDiv.querySelector('.edit-input');
                const saveButton = taskDiv.querySelector('.save');
                const deleteButton = taskDiv.querySelector('.delete');

                if (!taskParagraph || !editInput || !saveButton || !deleteButton) {
                    console.error("Fehler: Nicht alle benötigten Elemente (p, .edit-input, .save, .delete) im Task-Div gefunden für Task ID:", taskId);
                    alert("Ein Fehler ist beim Umschalten des Bearbeitungsmodus aufgetreten.");
                    return;
                }

                const isInEditMode = taskDiv.classList.toggle('editmode');

                if (isInEditMode) {
                    const currentText = taskParagraph.textContent;
                    editInput.value = currentText;
                    editInput.focus();
                    editInput.setSelectionRange(currentText.length, currentText.length);
                    button.textContent = 'Abbrechen'; // Ändere Button-Text
                } else {
                    button.textContent = 'Edit'; // Ändere Button-Text zurück
                    // Optional: Input-Wert zurücksetzen, falls Änderungen verworfen werden sollen
                    // editInput.value = taskParagraph.textContent;
                }
            }

            // --- NEU: Handler für SAVE Button ---
            else if (event.target.classList.contains('save')) {
                const button = event.target; // Der Save-Button
                const taskDiv = button.closest('.task');
                const taskId = button.dataset.id;

                // Finde die zugehörigen Elemente
                const editInput = taskDiv.querySelector('.edit-input');
                const taskParagraph = taskDiv.querySelector('p');
                const editButton = taskDiv.querySelector('.edit'); // Um den Text zurückzusetzen

                if (!editInput || !taskParagraph || !editButton || !taskId) {
                    console.error("Fehler: Nicht alle Elemente (edit-input, p, edit-button) oder Task-ID gefunden für Speichern.");
                    alert("Fehler beim Speichern der Aufgabe.");
                    return;
                }

                const newText = editInput.value.trim(); // Hole neuen Text und entferne Leerzeichen

                // Einfache Validierung: Leerer Task nicht erlaubt
                if (newText === "") {
                    alert("Die Aufgabe darf nicht leer sein.");
                    editInput.focus(); // Fokus zurück aufs Input
                    return;
                }

                // Prüfe, ob die Basis-URL definiert ist
                 if (typeof UPDATE_TASK_URL_BASE === 'undefined') {
                     console.error("FEHLER: Globale Variable UPDATE_TASK_URL_BASE nicht definiert!");
                     alert("Konfigurationsfehler beim Speichern.");
                     return;
                 }
                // Erstelle die vollständige URL - Annahme: URL erwartet ID als Query-Parameter
                 // Passe dies an, falls deine API die ID im Pfad oder Body erwartet
                const updateUrl = UPDATE_TASK_URL_BASE + (UPDATE_TASK_URL_BASE.includes('?') ? '&' : '?') + 'id=' + encodeURIComponent(taskId);


                // Sende die Daten an den Server
                fetch(updateUrl, {
                    method: 'PATCH', // Oder PUT, oder POST - muss zum Backend passen!
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        // Sende nur die Daten, die geändert werden sollen
                        task: newText
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        // Versuche, Fehlermeldung vom Server zu bekommen
                        return response.json().catch(() => {
                             // Falls Server kein JSON sendet (z.B. bei 500 Error ohne JSON Body)
                            throw new Error(`Serverfehler: ${response.status} ${response.statusText}`);
                        });
                    }
                    return response.json(); // Server sollte die aktualisierten Daten oder Erfolgsstatus senden
                })
                .then(data => {
                    if (data.success) {
                        // --- Update erfolgreich ---
                        console.log(`Task ${taskId} erfolgreich aktualisiert.`);

                        // 1. Aktualisiere den Text im Paragraphen
                        //    Optional: Verwende den vom Server zurückgegebenen Text, falls vorhanden
                        taskParagraph.textContent = data.updatedTask ? data.updatedTask.task : newText;

                        // 2. Verlasse den Edit-Modus
                        taskDiv.classList.remove('editmode');

                        // 3. Setze den Edit-Button-Text zurück
                        editButton.textContent = 'Edit';

                    } else {
                        // --- Update fehlgeschlagen (vom Server gemeldet) ---
                        console.error(`Fehler beim Aktualisieren von Task ${taskId}:`, data.message);
                        alert(`Fehler beim Speichern: ${data.message || 'Unbekannter Serverfehler'}`);
                    }
                })
                .catch(error => {
                    // --- Netzwerkfehler oder Fehler beim Verarbeiten der Antwort ---
                    console.error('Fehler bei der Speicheranfrage:', error);
                    alert(`Ein Fehler ist aufgetreten: ${error.message}`);
                });
            }
            // --- Ende des SAVE-Handlers ---

        }); // Ende des Haupt-Event-Listeners für tasklistDiv
    } // Ende if (tasklistDiv)
}); // Ende DOMContentLoaded