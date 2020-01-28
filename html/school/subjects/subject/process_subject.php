<?php
    /**
     * ##################################################################
     * #     F Ä C H E R  - V E R A R B E I T U N G S H A N D L E R     #
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
    write_log($mysqli, 'INFO', 'Hat ein Fach bearbeitet.');

    // Fach löschen.
    if(isset($_POST['btnDelete'])) {

        // Keine Fächer_Id vorhanden.
        if(!(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        $post_id = $_POST['id'];

        // Fach aus der Datenbank löschen.

        // Bereite SQL-Query vor.
        $sql  = 'DELETE FROM Fach WHERE Id = ?;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/school/subjects/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen.
            $stmt->bind_param('i', $post_id);
            
            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Fach konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Das Fach mit der Id ' . $post_id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /school/subjects/');
                exit();

            } else {
                // Fach konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Das Fach mit der Id ' . $post_id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /school/subjects/');
                exit();
            }
        }
    
    // Fach registrieren oder Änderungen speichern.
    } elseif (isset($_POST['btnCreate']) || isset($_POST['btnSave'])) {

        // Kann Fach ohne Id nicht speichern.
        if(isset($_POST['btnSave']) && !(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        // Daten aus Formular auslesen
        $id = $_POST['id'];
        $shortname = $_POST['shortname'];
        $description = $_POST['description'];

        // Änderungen speichern.
        if(isset($_POST['btnSave'])) {
            $sql = 'UPDATE Fach SET Kuerzel = ?, Bezeichnung = ? WHERE Id = ?;';

            // Bereite SQL-Query vor.
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/subjects/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('ssi', $shortname, $description, $id);

                //Query ausführen.
                if($stmt->execute() === FALSE) {
                    // Fach konnte nicht bearbeitet werden. Leite auf Übersichtsseite weiter.
                   $_SESSION['process_status'] = 'Das Fach ' . $shortname . 
                       ' konnte nicht ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/subjects/');
                   exit();

               } else {
                   // Fach konnte erfolgreich bearbeitet werden.
                   $_SESSION['process_status'] = 'Das Fach ' . $class . 
                       ' konnte erfolgreich ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/subjects/');
                   exit();
               }
            }

        } else {
            // Neuen Eintrag vornehmen.

            // Bereite SQL-Query vor.
            $sql = 'INSERT INTO Fach (Kuerzel, Bezeichnung) VALUES (?,?);';
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/subjects/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('ss', $shortname, $description);

                // Query ausführen.
                if($stmt->execute() === FALSE) {
                     // Fach konnte nicht angelegt werden. Leite auf Übersichtsseite weiter.
                    $_SESSION['process_status'] = 'Das Fach ' . $shortname . 
                        ' konnte nicht angelegt werden!';
                    header('Location: /school/subjects/');
                    exit();

                } else {
                    // Fach konnte erfolgreich gelöscht werden.
                    $_SESSION['process_status'] = 'Das Fach ' . $shortname . ' 
                        wurde erfolgreich angelegt!';
                    header('Location: /school/subjects/');
                    exit();
                }
            }
        }
    
    // Fehlerhafte Anfrage.
    } else {
        echo('Invalid request.');
        exit();
    }
?>