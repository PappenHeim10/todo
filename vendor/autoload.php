<?php
/**
 * Einfacher PSR-4 Autoloader
 *
 * Dieser Autoloader lädt Klassen automatisch, basierend auf ihrem Namespace
 * und der Verzeichnisstruktur unterhalb des definierten Basisverzeichnisses.
 */

/**
 * 
 * Registriert die Autoload-Funktion.
 *
 * @param string $className Der vollqualifizierte Name der Klasse (z.B. App\Database\DbConnection).
 */

spl_autoload_register(function ($className) {
    // Definiere den Basis-Namespace deines Projekts.
    // Du kannst dies anpassen, z.B. 'TodoApp' oder was immer du bevorzugst.
    // WICHTIG: Dieser Namespace muss auch in deinen Klassendateien verwendet werden (z.B. namespace App\Database;)
    $baseNamespace = 'TodoApp\\'; // NOTE: Beachte den doppelten Backslash!

    // Definiere das Basisverzeichnis für deine Klassen (relativ zu APP_ROOT).
    // Normalerweise ist das 'src/'.
    $baseDirectory = APP_ROOT . '/src/';


    // Prüfe, ob die Klasse den Basis-Namespace verwendet.
    $namespaceLength = strlen($baseNamespace);
    if (strncmp($baseNamespace, $className, $namespaceLength) !== 0) {
        // Klasse gehört nicht zu diesem Autoloader, ignoriere sie.
        // Andere Autoloader (falls vorhanden) können es versuchen.
        return;
    }

    // Entferne den Basis-Namespace vom Klassennamen.
    // z.B. aus "App\Database\DbConnection" wird "Database\DbConnection"
    $relativeClassName = substr($className, $namespaceLength);

    // Ersetze die Namespace-Trenner (\) durch Verzeichnis-Trenner (/).
    // z.B. aus "Database\DbConnection" wird "Database/DbConnection"
    // Füge die Dateiendung .php hinzu.
    $fileName = str_replace('\\', '/', $relativeClassName) . '.php';

    // Kombiniere das Basisverzeichnis mit dem relativen Dateipfad.
    $filePath = $baseDirectory . $fileName;

    // Prüfe, ob die Datei existiert.
    if (file_exists($filePath)) {
        // Lade die Datei.
        require $filePath;
        // OPTIM: Optional: Protokolliere das Laden (nützlich für Debugging)
        // error_log("Autoloaded: " . $filePath);
    } else {
        //OPTIM: Optional: Protokolliere, wenn eine Datei nicht gefunden wurde
        // error_log("Autoload Error: File not found for class " . $className . " at path " . $filePath);
    }
});


//OPTIM: Optional: Lade Konfigurationsdateien oder globale Funktionen hier,
// wenn sie *immer* benötigt werden und keine Klassen sind.
// require_once APP_ROOT . '/config/config.php';
// require_once APP_ROOT . '/src/functions.php'; // Beachte: Funktionen können NICHT autoloaded werden!

// Optional: Hinweis auf Erfolg
// echo "Autoloader registriert.<br>"; // Nur für Debugging