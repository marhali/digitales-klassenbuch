<?php
    /**
     * ##################################################################
     * #          S C H Ü L E R - Ü B E R S I C H T S S E I T E         #
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
    $title = 'Digitales Klassenbuch | Schülerübersicht';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Schüler > Übersicht';

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
        echo('<h1>' . $_SESSION['process_status'] . '</h1>');
        unset($_SESSION['process_status']);
    }

    // Link, um einen neuen Schüler hinzufügen zu können.
    echo('<div><h1><a href="./student/">Neuer Schüler</a></h1></div>');

    /**
     * Zeige alle Schüler an.
     */

    echo('<h3>Übersicht der Schüler:</h3>');

    // Hole alle Schüler aus der Datenbank.
    $sql = 'SELECT s.Id, k.Kuerzel, n.Vorname, n.Nachname FROM Schueler s 
        LEFT JOIN Klasse k ON (s.Klasse_Id = k.Id) JOIN Nutzer n ON (s.Id = n.Id);';
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
        $db_forename;
        $db_surname;

        $stmt->bind_result($db_id, $db_klasse, $db_forename, $db_surname);

        while($stmt->fetch()) {
            echo('<div><h3><a href="/school/students/student/?id=' . $db_id . '">' . 
                $db_klasse . ' - ' . $db_forename . ' - ' . $db_surname . '</a></h3></div>');
        }
    }

    echo('</div>');

    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>