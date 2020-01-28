<?php
    /**
     * ##################################################################
     * #      S T U N D E N P L A N - Ü B E R S I C H T S S E I T E     #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 27.01.2019                         #
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
    $title = 'Digitales Klassenbuch | Stundenpläne';
    $styles = array();
    $scripts = array();
    $section = 'Stundenpläne > Übersicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Setze temporären Stundenplan zurück.
    clearTmpData();

    echo('<div class="content">');

    // Statusinformation wurde gesetzt.
    if(isset($_SESSION['process_status'])) {
        echo('<p>' . $_SESSION['process_status'] . '</p>');
        unset($_SESSION['process_status']);
    }

    // Link, um einen neuen Stundenplan hinzufügen zu können.
    echo('<div><h1><a href="./timetable/">Neuer Stundenplan</a></h1></div>');

    /**
     * Zeige alle Stundenpläne an
     */

    echo('<h3>Übersicht der Stundenpläne:</h3>');

    // Hole alle Stundenpläne aus der Datenbank.
    $sql = 'SHOW TABLES LIKE "Stundenplan_%"';
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

        $timetable;
        $stmt->bind_result($timetable);

        while($stmt->fetch()) {
            $id = substr($timetable, 12);
            echo('<div><h3><a href="/timetables/timetable/?id=' . $id . '">' . 
                $id . '</a></h3></div>');
        }
    }

    echo('</div>');

    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');

    /**
     * Löscht alle temporären Sitzungsvariablen
     */
    function clearTmpData() {
        unset($_SESSION['tmp_timetable']);
        unset($_SESSION['tmp_timetable_id']);
        unset($_SESSION['tmp_timetable_column']);
        unset($_SESSION['tmp_timetable_row']);
    }
?>