<?php
    /**
     * ##################################################################
     * #           R Ä U M E   - Ü B E R S I C H T S S E I T E          #
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
    $title = 'Digitales Klassenbuch | Raumübersicht';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Räume > Übersicht';

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

    // Link, um ein neuen Raum hinzufügen zu können.
    echo('<div><h1><a href="./room/">Neuer Raum</a></h1></div>');

    /**
     * Zeige alle Fächer an.
     */

    echo('<h3>Übersicht der Räume:</h3>');

    // Hole alle Räume aus der Datenbank.
    $sql = 'SELECT Id, Gebaeude, Etage, Raum FROM Raum;';
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
        $db_building;
        $db_level;
        $db_room;

        $stmt->bind_result($db_id, $db_building, $db_level, $db_room);

        while($stmt->fetch()) {
            echo('<div><h3><a href="/management/rooms/room/?id=' . $db_id . '">' . 
                $db_building . ' - ' . $db_level . ' - ' . $db_room . '</a></h3></div>');
        }
    }

    echo('</div>');

    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>