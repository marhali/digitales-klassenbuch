<?php
    /**
     * ##################################################################
     * #           L E H R E R - Ü B E R S I C H T S S E I T E          #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 26.01.2019                         #
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
    $title = 'Digitales Klassenbuch | Lehrerübersicht';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Lehrer > Übersicht';

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

    // Link, um einen neuen Lehrer hinzufügen zu können.
    echo('<div><h1><a href="./teacher/">Neuer Lehrer</a></h1></div>');

    /**
     * Zeige alle Lehrer an.
     */

    echo('<h3>Übersicht der Schüler:</h3>');
    
    // Hole alle Lehrer aus der Datenbank.
    $sql = 'SELECT l.Id, Kuerzel, Vorname, Nachname FROM Lehrer l 
        JOIN Nutzer n ON (l.Id = n.Id);';
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
        $db_kuerzel;
        $db_forename;
        $db_surname;

        $stmt->bind_result($db_id, $db_kuerzel, $db_forename, $db_surname);

        while($stmt->fetch()) {
            echo('<div><h3><a href="/school/teachers/teacher/?id=' . $db_id . '">' . 
                $db_kuerzel . ' - ' . $db_forename . ' - ' . $db_surname . '</a></h3></div>');
        }
    }

    echo('</div>');

    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>