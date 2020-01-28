<?php
    /**
     * ##################################################################
     * #       R A U M  - V E R A R B E I T U N G S H A N D L E R       #
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

    // Importiere DB-Hilfsfunktionen
    include_once(INC_ROOT . 'db-helper.php');

    // !!! Geschützter Bereich !!!

    // Protokolliere Änderung
    write_log($mysqli, 'INFO', 'Hat eine Änderung an den Räumen durchgeführt.');

    // Raum löschen.
    if(isset($_POST['btnDelete'])) {

        // Keine Raum_Id vorhanden.
        if(!(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        $post_id = $_POST['id'];

        // Raum aus der Datenbank löschen.

        // Bereite SQL-Query vor.
        $sql  = 'DELETE FROM Raum WHERE Id = ?;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/management/rooms/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen.
            $stmt->bind_param('i', $post_id);
            
            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Raum konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Raum mit der Id ' . $post_id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /management/rooms/');
                exit();

            } else {
                // Raum konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Der Raum mit der Id ' . $post_id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /management/rooms/');
                exit();
            }
        }
    
    // Raum registrieren oder Änderungen speichern.
    } elseif (isset($_POST['btnCreate']) || isset($_POST['btnSave'])) {

        // Kann Raum ohne Id nicht speichern.
        if(isset($_POST['btnSave']) && !(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        // Daten aus Formular auslesen
        $id = $_POST['id'];
        $building = $_POST['building'];
        $level = $_POST['level'];
        $room = $_POST['room'];

        // Änderungen speichern.
        if(isset($_POST['btnSave'])) {
            $sql = 'UPDATE Raum SET Gebaeude = ?, Etage = ?, Raum = ? WHERE Id = ?;';

            // Bereite SQL-Query vor.
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/management/rooms/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('sssi', $building, $level, $room, $id);

                // Query ausführen.
                if($stmt->execute() === FALSE) {
                    // Raum konnte nicht bearbeitet werden. Leite auf Übersichtsseite weiter.
                   $_SESSION['process_status'] = 'Der Raum mit der Id ' . $id . 
                       ' konnte nicht ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /management/rooms/');
                   exit();

               } else {
                   // Raum konnte erfolgreich bearbeitet werden.
                   $_SESSION['process_status'] = 'Der Raum mit der Id ' . $id . 
                       ' konnte erfolgreich ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /management/rooms/');
                   exit();
               }
            }

        } else {
            // Neuen Eintrag vornehmen.

            // Bereite SQL-Query vor.
            $sql = 'INSERT INTO Raum (Gebaeude, Etage, Raum) VALUES (?,?,?);';
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/management/rooms/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('sss', $building, $level, $room);

                // Query ausführen.
                if($stmt->execute() === FALSE) {
                     // Raum konnte nicht angelegt werden. Leite auf Übersichtsseite weiter.
                    $_SESSION['process_status'] = 'Der Raum mit der Id ' . $id . 
                        ' konnte nicht angelegt werden!';
                    header('Location: /management/rooms/');
                    exit();

                } else {
                    // Raum konnte erfolgreich gelöscht werden.
                    $_SESSION['process_status'] = 'Der Raum mit der Id ' . $id . ' 
                        wurde erfolgreich angelegt!';
                    header('Location: /management/rooms/');
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