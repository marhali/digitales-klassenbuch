<?php
    /**
     * ##################################################################
     * #   S T U N D E N P L A N - V E R A R B E I T U N G S S E I T E  #
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

    // Importiere DB-Hilfsfunktionen
    include_once(INC_ROOT . 'db-helper.php');

    // !!! Geschützter Bereich !!!

    // Protokolliere
    write_log($mysqli, 'INFO', 'Hat einen Studenplan editiert.');

    // Es wurde keine Klassen-Id übergeben.
    if(!(isset($_POST['class']))) {
        echo('Invalid request. No id given.');
        exit();
    }

    // Im folgenden wird das Formular ausgelesen.
    $table = array();
    $id = $_POST['class'];

    // Wir sollten erstmal alle Tage und Stunden herausfinden
    $days = array();
    $days_id = array();
    $hours = array();
    $hours_id = array();

    foreach($_POST AS $key => $item) {

        if(substr($key, 0, 4) === 'day_') { // day_0..x
            $day_id = substr($key, 4);
            $day = $_POST[$key];

            if(!in_array($day, $days)) {
                $days[] = $day;
                $days_id[] = $day_id;
            }

        } elseif(substr($key, 0, 5) === 'hour_') { // hour_0..x
            $hour_id = substr($key, 5);
            $hour = $_POST[$key];

            if(!in_array($hour, $hours)) {
                $hours[] = $hour;
                $hours_id[] = $hour_id;
            }
        }
    }

    $i = 0;
    foreach($days as $day) {
        $j = 0;

        foreach($hours as $hour) {

            $table[] = array(
                'Tag' => $day,
                'Stunde' => $hour,
                'Fach_Id' => $_POST['subject_' . $days_id[$i] . '_' . $hours_id[$j]],
                'Lehrer_Id' => $_POST['teacher_' . $days_id[$i] . '_' . $hours_id[$j]],
                'Raum_Id' => $_POST['room_' . $days_id[$i] . '_' . $hours_id[$j]],
            );

            $j++;
        }
        $i++;
    }

    // Ende :Auslesen:

    $_SESSION['tmp_timetable'] = $table;
    $_SESSION['tmp_timetable_id'] = $id;

    // Der Nutzer möchte einen neuen Tag hinzufügen
    if(isset($_POST['btnAddDay'])) {
        $column = $_SESSION['tmp_timetable_column'];
        $column++;
        $_SESSION['tmp_timetable_column'] = $column;
        header('Location: /timetables/timetable/');

    // Der Nutzer möchte den letzten Tag löschen
    } elseif(isset($_POST['btnDelDay'])) {
        $column = $_SESSION['tmp_timetable_column'];

        if($column > 0) {
            $column--;
        }

        $_SESSION['tmp_timetable_column'] = $column;
        header('Location: /timetables/timetable/');

    // Der Nutzer möchte eine neue Stunde hinzufügen
    } elseif(isset($_POST['btnAddHour'])) {
        $row = $_SESSION['tmp_timetable_row'];
        $row++;
        $_SESSION['tmp_timetable_row'] = $row;
        header('Location: /timetables/timetable/');

    // Der Nutzr möchte die letzte Stunde entfernen
    } elseif(isset($_POST['btnDelHour'])) {
        $row = $_SESSION['tmp_timetable_row'];

        if($row > 0) {
            $row--;
        }

        $_SESSION['tmp_timetable_row'] = $row;
        header('Location: /timetables/timetable/');

    // Der Nutzer möchte den Stundenplan löschen.
    } elseif(isset($_POST['btnDelete'])) {
        deleteTimetable($mysqli, $id);

    // Der Nutzer möchte den Stundenplan anlegen.
    } elseif(isset($_POST['btnCreate'])) {
        createTimetable($mysqli, $id, $table);

    // Der Nutzer möchte den Stundenplan speichern.
    } elseif(isset($_POST['btnSave'])) {
        saveTimetable($mysqli, $id, $table);
    }

    /**
     * Löscht den aktuellen Timetable.
     */
    function deleteTimetable(mysqli $mysqli, $id) {
        // Bereite SQL-Query vor.
        $sql = 'DROP Table Stundenplan_' . $mysqli->real_escape_string($id);
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/timetables/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {

            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Stundenplan konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Stundenplan der Klasse ' . $id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /timetables/');
                exit();

            } else {
                // Stundenplan konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Der Stundenplan der Klasse ' . $id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /timetables/');
                exit();
            }
        }
    }

    /**
     * Legt den Stundenplan an.
     */
    function createTimetable(mysqli $mysqli, string $id, $table) {

        // Bereite SQL-Query vor.
        $sqlCreate = '
        CREATE TABLE Stundenplan_' . $mysqli->real_escape_string($id) . ' (
            Tag VARCHAR(10) NOT NULL,
            Stunde INT NOT NULL,
            Fach_Id INT NOT NULL,
            Lehrer_Id INT NOT NULL,
            Raum_Id INT NOT NULL,
            PRIMARY KEY (Tag, Stunde),
            FOREIGN KEY (Fach_Id) REFERENCES Fach(Id),
            FOREIGN KEY (Lehrer_Id) REFERENCES Lehrer(Id),
            FOREIGN KEY (Raum_Id) REFERENCES Raum(Id)
        );';
        
        $stmt = $mysqli->prepare($sqlCreate);

        // Query ausführen.
        if($stmt === FALSE || $stmt->execute() === FALSE) {

            // Stundenplan konnte nicht erstellt werden. Leite auf Übersichtsseite weiter.
            $_SESSION['process_status'] = 'Der Stundenplan der Klasse ' . $id . 
                ' konnte nicht erstellt werden! (Fehler: SQL-CREATE)';
            header('Location: /timetables/');
            exit();
        }

        // Tabelle ausfüllen.
        foreach($table as $row) {
            $sql = 'INSERT INTO Stundenplan_' . $mysqli->real_escape_string($id) .
                ' (Tag, Stunde, Fach_Id, Lehrer_Id, Raum_Id) VALUES (
                    "' . $mysqli->real_escape_string($row['Tag']) . '", 
                    "' . $mysqli->real_escape_string($row['Stunde']) . '", 
                    "' . $mysqli->real_escape_string($row['Fach_Id']) . '", 
                    "' . $mysqli->real_escape_string($row['Lehrer_Id']) . '", 
                    "' . $mysqli->real_escape_string($row['Raum_Id']) . '"
                );';

            $stmt = $mysqli->prepare($sql);

             // Query ausführen.
            if($stmt === FALSE || $stmt->execute() === FALSE) {

                // Stundenplan konnte nicht erstellt werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Stundenplan der Klasse ' . $id . 
                    ' konnte nicht erstellt werden! (Fehler: SQL-INSERT)';
                header('Location: /timetables/');
                exit();
            }
        }

        // Stundenplan konnte erfolgreich gespeichert werden.
        $_SESSION['process_status'] = 'Der Stundenplan der Klasse ' . $id . ' 
            wurde erfolgreich angelegt!';
        header('Location: /timetables/');
        exit();
    }

    /**
     * Speichere den aktuellen Stundenplan.
     */
    function saveTimetable(mysqli $mysqli, $id, $table) {
        $sql = 'TRUNCATE Stundenplan_' . $mysqli->real_escape_string($id);
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();

        // Tabelle ausfüllen.
        foreach($table as $row) {
            $sql = 'INSERT INTO Stundenplan_' . $mysqli->real_escape_string($id) .
                ' (Tag, Stunden, Fach_Id, Lehrer_Id, Raum_Id) VALUES (
                    ' . $row['Tag'] . ', 
                    ' . $row['Stunde'] . ', 
                    ' . $row['Fach_Id'] . ', 
                    ' . $row['Lehrer_Id'] . ', 
                    ' . $row['Raum_Id'] . '
                );';

            $stmt = $mysqli->prepare($sql);

             // Query ausführen.
            if($stmt === FALSE || $stmt->execute() === FALSE) {

                // Stundenplan konnte nicht gespeichert werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Stundenplan der Klasse ' . $id . 
                    ' konnte nicht gespeichert werden! (Fehler: SQL-INSERT)';
                header('Location: /timetables/');
                exit();
            }
        }

        // Stundenplan konnte erfolgreich gespeichert werden.
        $_SESSION['process_status'] = 'Der Stundenplan der Klasse ' . $id . ' 
            wurde erfolgreich gespeichert.';
        header('Location: /timetables/');
        exit();
    }
?>