# Einfache Todo-App

Eine simple Webanwendung zur Verwaltung von Aufgaben, erstellt mit PHP, AJAX und MySQL.

## Features

* Aufgaben anzeigen
* Aufgaben hinzufügen
* Aufgaben löschen (mit AJAX/Fetch)

## Technologie-Stack

* PHP 8.4
* MySQL / MariaDB
* PDO für Datenbankzugriff
* HTML5
* CSS3
* Vanilla JavaScript (Fetch API)

## Voraussetzungen

* PHP >= 8.0
* Webserver (Apache, Nginx oder der eingebaute PHP-Server)
* MySQL oder MariaDB Datenbankserver
* Composer (optional, falls Abhängigkeiten hinzugefügt werden)

## Installation & Setup

1.  **Repository klonen:**
    ```bash
    git clone <url-zum-repository>
    cd <projekt-ordner>
    ```
2.  **Datenbank erstellen:**
    * Erstelle eine Datenbank (z.B. `todo_list`).
    * Importiere die Tabellenstruktur aus der Datei `data/sqp_createdb001.sql`:
      ```bash
      mysql -u DEIN_DB_USER -p DEINE_DATENBANK < data/sqp_createdb001.sql
      ```
      *(Ersetze `DEIN_DB_USER` und `DEINE_DATENBANK`)*
3.  **Konfiguration anpassen:**
    * Kopiere `config/config.php.example` (falls vorhanden) nach `config/config.php`.
    * Öffne `config/config.php` und trage deine Datenbank-Zugangsdaten ein (Host, Benutzer, Passwort, Datenbankname).
    ```php
    <?php
    // Konfiguration für die Datenbankverbindung
    define('DB_HOST', "localhost");     // Dein Datenbank-Host
    define('DB_USER', "root");          // Dein Datenbank-Benutzer
    define('DB_PASS', "");              // Dein Datenbank-Passwort
    define('DB_NAME', "todo_list");     // Dein Datenbank-Name
    define('DB_PORT', 3306);            // Dein Datenbank-Port (Standardmäßig 3306)
    define('DB_CHARSET', "utf8mb4");    // Dein Datenbank-Zeichensatz (Standardmäßig "utf8mb4")
    ?>
    ```
4.  **(Optional) Composer:** Wenn du Composer verwendest (z.B. für den Autoloader):
    ```bash
    composer install
    ```
5.  **Webserver konfigurieren:**
    * Stelle sicher, dass der Webserver auf das `public/`-Verzeichnis als Document Root zeigt.
    * Alternativ: Starte den eingebauten PHP-Server aus dem `public/`-Verzeichnis:
      ```bash
      cd public
      php -S localhost:8000
      ```

## Verwendung

Öffne deinen Browser und navigiere zu der Adresse, die dein Webserver bereitstellt (z.B. `http://localhost:8000` oder deine konfigurierte lokale Domain).

* Gib eine Aufgabe in das Textfeld ein und klicke auf "Add Task".
* Klicke auf den "Delete"-Button neben einer Aufgabe, um sie zu löschen.

## Lizenz

---

© 2025 Cohen Dos Santos Imperial. All rights reserved.