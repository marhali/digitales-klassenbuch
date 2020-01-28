<?php
    /**
     * ##################################################################
     * #        S T U N D E N P L A N - E I N Z E L A N S I C H T       #
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
    $title = 'Digitales Klassenbuch | Stundenplan';
    $styles = array();
    $scripts = array();
    $section = 'Stundenplan > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    // Importiere unsere Hilfsklasse für DB-Funktionen
    include_once(INC_ROOT . 'db-helper.php');

    /**
     * Content
     */

    $found_timetable = FALSE;

    // Feld, welches alle Datensätze eines Stundenplanes beinhaltet.
    $table = array();
    $numColumn = 0;
    $numRow = 0;

    // Wenn Id übergeben, lade Datensätze aus Datenbank
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_STRING);
    if($input_id !== null) {
        $table_name = 'Stundenplan_' . $mysqli->real_escape_string($input_id);

        // Bereite SQL-Query vor.
        $sql = 'SELECT Tag, Stunde, Fach_Id, Lehrer_Id, Raum_Id FROM ' . $table_name . ';';
        $stmt = $mysqli->prepare($sql);

        // Ein Stundenplan der gegebenen Klassen konnte nicht gefunden werden.
        if($stmt === FALSE || $stmt->error) {
            echo('<p>Es wurde kein Stundenplan der Klasse ' . $input_id . ' gefunden.</p>');
            echo('<a href="/timetables/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Query ausführen.
            $stmt->execute();
            $stmt->store_result();

            // Ein Stundenplan der Klasse mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->error) {
                
                echo('<p>Es wurde kein Stundenplan der Klasse ' . $input_id . ' gefunden.</p>');
                echo('<a href="/timetables/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_timetable = TRUE;

                $day;
                $hour;
                $subject_id;
                $teacher_id;
                $room_id;

                // Ergebnis speichern.
                $stmt->bind_result($day, $hour, $subject_id, $teacher_id, $room_id);

                while($stmt->fetch()) { // Alle Zeilen durchgehen.
                    $table[] = array('Tag' => $day, 'Stunde' => $hour, 'Fach_Id' => $subject_id, 
                        'Lehrer_Id' => $teacher_id, 'Raum_Id' => $room_id);
                }
            }
        }
    }

    // Es existiert ein temporärer Stundenplan. Wir überschreiben alle vorherigen Daten.
    if(isset($_SESSION['tmp_timetable'])) {
        $table = $_SESSION['tmp_timetable'];
        $input_id = $_SESSION['tmp_timetable_id'];
        $numRow = $_SESSION['tmp_timetable_row'];
        $numColumn = $_SESSION['tmp_timetable_column'];
    }
?>

    <form action="process_timetable.php" method="POST" name="timetable-form" class="timetable">
        <div><h1>Stundenplan</h1></div>

        <div><label>Klasse:</label></div>
        <div>
            <input type="text" required name="class" placeholder="Klasse" value="<?php echo($input_id); ?>">
        </div>
        
        <table>
            <tr>
                <!-- Kopfzeile - ANFANG -->
                <th>Stunde / Tag</th>

                <?php
                    $days = array();

                    foreach($table as $row) {
                        if(!in_array($row['Tag'], $days)) {
                            $days[] = $row['Tag'];
                        }
                    }

                    // Passe die größe der Tabelle an den vorhanden Datensatz an.
                    if(count($days) > $numColumn) {
                        $numColumn = count($days);
                    }

                    for ($i=0; $i < $numColumn; $i++) {
                        echo('
                        <th colspan="3">
                            <input type="text" name="day_' . $i . '" placeholder="Neuer Tag" 
                                value="' . $days[$i] . '">
                        </th>
                        ');
                    }
                ?>
                <!-- Kopfzeile - ENDE -->
            </tr>

            <tr>
                <!-- Kopfzeile - ANFANG -->
                <th></th>

                <?php
                    $days = array();

                    foreach($table as $row) {
                        if(!in_array($row['Tag'], $days)) {
                            $days[] = $row['Tag'];
                        }
                    }

                    // Passe die größe der Tabelle an den vorhanden Datensatz an.
                    if(count($days) > $numColumn) {
                        $numColumn = count($days);
                    }

                    for ($i=0; $i < $numColumn; $i++) {
                        echo('<th>Fach</th>');
                        echo('<th>Lehrer</th>');
                        echo('<th>Raum</th>');
                    }
                ?>
                <!-- Kopfzeile - ENDE -->
            </tr>

            <!-- Dynamische Auflistung der Stunden -->
            <?php
                $hours = array();

                foreach($table as $row) {
                    if(!in_array($row['Stunde'], $hours)) {
                        $hours[] = $row['Stunde'];
                    }
                }

                // Passe die größe der Tabelle an den vorhanden Datensatz an.
                if(count($hours) > $numRow) {
                    $numRow = count($hours);
                }

                for ($i=0; $i < $numRow; $i++) {
                    echo('<tr>');

                    echo('
                    <td>
                        <input type="text" name="hour_' . $i . '" placeholder="Neue Stunde"
                            value="' . $hours[$i] . '">
                    </td>
                    ');

                    $days = array();

                    foreach($table as $row) {
                        if(!(in_array($row['Tag'], $days))) {
                            $days[] = $row['Tag'];
                        }
                    }

                    $fill = array();

                    for($j=0; $j < $numColumn; $j++) {
                    
                        foreach($table as $data) {
                            // Datensatz gefunden.
                            if($data['Tag'] == $days[$j] && $data['Stunde'] == $hours[$i]) {
                                $fill = $data;
                            }
                        }

                        echo('
                        <td>
                            <select name="subject_' . $j . '_' . $i . '">
                        ');
                        foreach(get_subjects($mysqli) as $row) {
                            foreach($row as $id => $shortname) {
                                echo('<option ' . ($id == $fill['Fach_Id'] ? 'checked':'') . 
                                    ' value="' . $id . '">' . $shortname . '</option>');
                            }
                        }
                        echo('</select></td>');

                        echo('
                        <td>
                            <select name="teacher_' . $j . '_' . $i . '">
                        ');
                        foreach(get_teacher($mysqli) as $row) {
                            foreach($row as $id => $value) {
                                echo('<option ' . ($id == $fill['Lehrer_Id'] ? 'checked':'') . 
                                    ' value="' . $id . '">' . $value['Kuerzel'] . '</option>');
                            }
                        }
                        echo('</select></td>');


                        echo('
                        <td>
                            <select name="room_' . $j . '_' . $i . '">
                        ');
                        foreach(get_rooms($mysqli) as $row) {
                            foreach($row as $id => $value) {
                                echo('<option ' . ($id == $fill['Raum_Id'] ? 'checked':'') . 
                                    ' value="' . $id . '">' . $value . '</option>');
                            }
                        }
                        echo('</select></td>');

                        /*
                        echo('
                        <td>
                            <input type="text" name="subject_' . $j . '_' . $i . '" 
                                placeholder="Fach" value="' . $fill['Fach_Id'] . '">
                        </td>
                        <td>
                            <input type="text" name="teacher_'. $j . '_' . $i . '"
                                placeholder="Lehrer" value="' . $fill['Lehrer_Id'] . '">
                        </td>
                        <td>
                            <input type="text" name="room_' . $j . '_' . $i . '"
                                placeholder="Raum" value="' . $fill['Raum_Id'] . '">
                        </td>
                        ');
                        */
                    }

                    echo('</tr>');
                }
            ?>
            <!-- Dynamische Auflistung - ENDE -->
        </table>
        <div>
            <input type="submit" name="btnAddDay" value="Neuer Tag">
            <input type="submit" name="btnDelDay" value="Lösche Tag">
            <input type="submit" name="btnAddHour" value="Neue Stunde">
            <input type="submit" name="btnDelHour" value="Lösche Stunde">
        </div>
        <div>
            <?php
                // Dieser Nutzer existiert bereits.
                if($found_timetable) {
                    echo('<input type="submit" name="btnSave" value="Stundenplan speichern">');
                    echo('<input type="submit" name="btnDelete" value="Stundenplan löschen">');

                } else { // Neuer Nutzer.
                    echo('<input type="submit" name="btnCreate" value="Stundenplan anlegen">');
                }
            ?>

            <button formaction="/timetables/" formnovalidate>Zurück</button>
        </div>
    </form>

<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');

    // Wir speichern alle Datensätze temporär in der Sitzung ab.
    // Erst, sobald der Nutzer den Stundenplan speichert, wird dieser
    // persistent in der Datenbank abgespeichert.
    $_SESSION['tmp_timetable'] = $table;
    $_SESSION['tmp_timetable_id'] = $input_id;
    $_SESSION['tmp_timetable_row'] = $numRow;
    $_SESSION['tmp_timetable_column'] = $numColumn;
?>