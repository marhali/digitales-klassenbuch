<?php
    /**
     * ##################################################################
     * #       P R O T O K O L L   - Ü B E R S I C H T S S E I T E      #
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
    $title = 'Digitales Klassenbuch | Protokoll';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Protokoll';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    $sql = 'SELECT Zeitstempel, Benutzername, Info FROM Protokoll p 
        JOIN Nutzer n ON (p.Nutzer_Id = n.Id)';
    $stmt = $mysqli->prepare($sql);

    // SQL-Syntax konnte nicht erstellt werden.
    if($stmt === FALSE) {
        echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
        echo('<a href="/overview/">Zurück zur Globalen-Übersichtsseite</a>');
        exit();
    }

    // In diesem Array werden alle Protokollinformationen gespeichert.
    $log = array();

    // Query ausführen.
    $stmt->execute();
    $stmt->store_result();

    $timestamp;
    $username;
    $info;

    $stmt->bind_result($timestamp, $username, $info);

    while($stmt->fetch()) {
        $log[] = array('Zeitstempel' => $timestamp, 
            'Benutzername' => $username, 'Info' => $info);
    }
?>

    <table>
        <tr>
            <th colspan="3">System Protokoll</th>
        </tr>
        <tr>
            <th>Zeitstempel</th>
            <th>Nutzer</th>
            <th>Info</th>
        </tr>
        <?php
            foreach($log as $row) {
                echo('
                <tr>
                    <td>' . $row['Zeitstempel'] . '</td>
                    <td>' . $row['Benutzername'] . '</td>
                    <td>' . $row['Info'] . '</td>
                </tr>
                ');
            }
        ?>
    </table>

<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>