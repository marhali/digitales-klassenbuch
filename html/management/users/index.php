<?php
    /**
     * ##################################################################
     * #          N U T Z E R - Ü B E R S I C H T S S E I T E           #
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
    $title = 'Digitales Klassenbuch | Nutzerübersicht';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Nutzer > Gesamtübersicht';

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

    // Link, um einen neuen Nutzer hinzufügen zu können.
    echo('<div><h1><a href="./user/">Neuer Nutzer</a></h1></div>');

    /**
     * Zeige alle Nutzer an.
     */

    echo('<h3>Übersicht der Nutzer:</h3>');

    // Hole alle Nutzer aus der Datenbank.
    $sql = 'SELECT Id, Benutzername, Vorname, Nachname FROM Nutzer;';
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
        $db_username;
        $db_forename;
        $db_surname;

        $stmt->bind_result($db_id, $db_username, $db_forename, $db_surname);

        while($stmt->fetch()) {
            echo('<div><h3><a href="/management/users/user/?id=' . $db_id . '">' . 
                $db_username . ' - ' . $db_forename . ' - ' . $db_surname . '</a></h3></div>');
        }
    }

    echo('</div>');

    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>