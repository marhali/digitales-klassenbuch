<?php
    /**
     * ##################################################################
     * #       K L A S S E N B U C H  - E I N Z E L A N S I C H T       #
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
    $title = 'Digitales Klassenbuch | Einträge';
    $styles = array();
    $scripts = array();
    $section = 'Übersicht > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Hole die übergebene Id.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_STRING);

    // Keine Id übergeben.
    if($input_id === null) {
        echo('<p>Es wurde keine Klasse angegeben!</p>');
        echo('<a href="/overview/">Zurück zur Übersichtsseite</a>');
        exit();
    }

    // Id aufbereiten.
    $input_id = mysqli_real_escape_string($mysqli, $input_id);

    $sql = 'SELECT Datum, Stunde, Fach_Id_Soll, Fach_Id_Ist, Lehrer_Id, 
        Thema, Hausaufgabe, Best_Lehrer_Id FROM Klassenbuch_' . $input_id . '_Tag';
    $stmt = $mysqli->prepare($sql);

    // SQL-Syntax-Fehler
    if($stmt === FALSE) {
        echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
        echo('<a href="/overview/">Zurück zur Übersichtsseite</a>');
        exit();
    }

    $stmt->execute();
    $stmt->store_result();

    // Speichere alle Datensätze im Array ab.
    $entries = array();

    $date;
    $hour;
    $subject_should;
    $subject_is;
    $teacher;
    $topic;
    $homework;
    $signature;

    $stmt->bind_result($date, $hour, $subject_should, $subject_is, $teacher, 
        $topic, $homework, $signature);

    while($stmt->fetch()) {
        $entries[] = array(
            'Datum' => $date,
            'Stunde' => $hour,
            'Fach_Id_Soll' => $subject_should,
            'Fach_Id_Ist' => $subject_is,
            'Lehrer_Id' => $teacher,
            'Thema' => $topic,
            'Hausaufgabe' => $homework,
            'Best_Lehrer_Id' => $signature
        );
    }
?>

    <table>
        <tr>
            <th>Datum</th>
            <th>Stunde</th>
            <th>Fach Soll</th>
            <th>Fach Ist</th>
            <th>Lehrer</th>
            <th>Thema</th>
            <th>Hausaufgabe</th>
            <th>Unterschrift</th>
        </tr>
        <?php
            // Binde Hilfsfunktionen ein.
            include_once(INC_ROOT . 'db-helper.php');

            // Iteriere durch alle Datensätze
            foreach($entries as $row) {

                $subject_should;
                foreach(get_subjects($mysqli) as $data) {
                    foreach($data as $id => $shortname) {
                        if($id == $row['Fach_Id_Soll']) {
                            $subject_should = $shortname;
                        }
                    }
                }

                $subject_is;
                foreach(get_subjects($mysqli) as $data) {
                    foreach($data as $id => $shortname) {
                        if($id == $row['Fach_Id_Ist']) {
                            $subject_is = $shortname;
                        }
                    }
                }

                $teacher;
                foreach(get_teacher($mysqli) as $data) {
                    foreach($data as $id => $value) {
                        if($id == $row['Lehrer_Id']) {
                            $teacher = $value['Kuerzel'];
                        }
                    }
                }

                $signature;
                foreach(get_teacher($mysqli) as $data) {
                    foreach($data as $id => $value) {
                        if($id == $row['Best_Lehrer_Id']) {
                            $signature = $value['Kuerzel'];
                        }
                    }
                }

                echo('
                <tr>
                    <td>' . $row['Datum'] . '</td>
                    <td>' . $row['Stunde'] . '</td>
                    <td>' . $subject_should . '</td>
                    <td>' . $subject_is . '</td>
                    <td>' . $teacher . '</td>
                    <td>' . $row['Thema'] . '</td>
                    <td>' . $row['Hausaufgabe'] . '</td>
                    <td>' . $signature . '</td>
                </tr>
                ');
            }
        ?>

    </table>
    <form>
    <button formaction="/overview/" formnovalidate>Zurück zur Übersichtsseite</button>
    </form>

<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>