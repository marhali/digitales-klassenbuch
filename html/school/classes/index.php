<?php
    /**
     * ##################################################################
     * #         K L A S S E N - Ü B E R S I C H T S S E I T E          #
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
    $title = 'Digitales Klassenbuch | Klassenübersicht';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Klassen > Übersicht';

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

    // Link, um eine neue Klasse hinzufügen zu können.
    echo('<div><h1><a href="./class/">Neue Klasse</a></h1></div>');

    /**
     * Zeige alle Klassen an.
     */

    echo('<h3>Übersicht der Klassen:</h3>');

    // Hole alle Klassen aus der Datenbank.
    $sql = 'SELECT k.Id, k.Kuerzel, l.Kuerzel FROM Klasse k 
        JOIN Lehrer l ON (k.Lehrer_Id = l.Id);';
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
        $db_klasse;
        $db_lehrer;

        $stmt->bind_result($db_id, $db_klasse, $db_lehrer);

        while($stmt->fetch()) {
            echo('<div><h3><a href="/school/classes/class/?id=' . $db_id . '">' . 
                $db_klasse . ' - ' . $db_lehrer . '</a></h3></div>');
        }
    }

    echo('</div>');

    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>