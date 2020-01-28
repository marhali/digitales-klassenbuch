<?php
    /**
     * ##################################################################
     * #   F E H L Z E I T T Y P E N   - Ü B E R S I C H T S S E I T E  #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 29.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Binde globale Umgebungsvariablen ein.
    include_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/globals.php');

    // Lade Sicherheitsskript und lasse nur eingeloggte Benutzer auf diese Webseite zu.
    include_once(INC_ROOT . 'site-security.php');

    // !!! Geschützter Bereich !!!

    /*
     * Dynamische Variablen der Seite festlegen.
     */
    $title = 'Digitales Klassenbuch | Fehlzeit-Typen';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Fehlzeit-Typen > Übersicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    echo('<div class="content">');

    // Statusinformation wurde gesetzt.
    if(isset($_SESSION['process_status'])) {
        echo('<p>' . $_SESSION['process_status'] . '</p>');
        unset($_SESSION['process_status']);
    }

    // Link, um einen neuen Fehlzeit-Typen hinzufügen zu können.
    echo('<div><h1><a href="./absence-type/">Neuer Fehlzeit-Typ</a></h1></div>');

    /**
     * Zeige alle Fehlzeit-Typen an.
     */

    echo('<h3>Übersicht der Fehlzeit-Typen:</h3>');

    // Hole alle Typen aus der Datenbank.
    $sql = 'SELECT Id, Bezeichnung FROM Fehlzeit_Typ;';
    $stmt = $mysqli->prepare($sql);

    // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
    if($stmt === FALSE) {
        echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
        echo('<a href="/overview/">Zurück zur Globalen-Übersichtsseite</a>');
        exit();

    } else {

        // Query ausführen.
        $stmt->execute();
        $stmt->store_result();

        $db_id;
        $db_description;

        $stmt->bind_result($db_id, $db_description);

        while($stmt->fetch()) {
            echo('<div><h3><a href="/management/absence-types/absence-type/?id=' . $db_id . '">' . 
                $db_description . '</a></h3></div>');
        }
    }

    echo('</div>');

    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>