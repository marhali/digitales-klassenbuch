<?php
    /**
     * ##################################################################
     * #  F E H L Z E I T T Y P  - V E R A R B E I T U N G S H N D L R  #
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

    // Protkolliere Änderung
    write_log($mysqli, 'INFO', 'Hat eine Änderung an den Fehlzeiten durchgeführt.');

    // Typen löschen.
    if(isset($_POST['btnDelete'])) {

        // Keine Typ_Id vorhanden.
        if(!(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        $post_id = $_POST['id'];

        // Typ aus der Datenbank löschen.

        // Bereite SQL-Query vor.
        $sql  = 'DELETE FROM Fehlzeit_Typ WHERE Id = ?;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/management/absence-types/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen.
            $stmt->bind_param('i', $post_id);
            
            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Typ konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Fehlzeit-Typ mit der Id ' . $post_id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /management/absence-types/');
                exit();

            } else {
                // Typ konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Der Fehlzeit-Typ mit der Id ' . $post_id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /management/absence-types/');
                exit();
            }
        }
    
    // Typ registrieren oder Änderungen speichern.
    } elseif (isset($_POST['btnCreate']) || isset($_POST['btnSave'])) {

        // Kann Typ ohne Id nicht speichern.
        if(isset($_POST['btnSave']) && !(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        // Daten aus Formular auslesen
        $id = $_POST['id'];
        $description = $_POST['description'];

        // Änderungen speichern.
        if(isset($_POST['btnSave'])) {
            $sql = 'UPDATE Fehlzeit_Typ SET Bezeichnung = ? WHERE Id = ?;';

            // Bereite SQL-Query vor.
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/management/absence-types/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('si', $description, $id);

                // Query ausführen.
                if($stmt->execute() === FALSE) {
                    // Typ konnte nicht bearbeitet werden. Leite auf Übersichtsseite weiter.
                   $_SESSION['process_status'] = 'Der Fehlzeit-Typ mit der Id ' . $id . 
                       ' konnte nicht ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /management/absence-types/');
                   exit();

               } else {
                   // Typ konnte erfolgreich bearbeitet werden.
                   $_SESSION['process_status'] = 'Der Fehlzeit-Typ mit der Id ' . $id . 
                       ' konnte erfolgreich ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /management/absence-types/');
                   exit();
               }
            }

        } else {
            // Neuen Eintrag vornehmen.

            // Bereite SQL-Query vor.
            $sql = 'INSERT INTO Fehlzeit_Typ (Bezeichnung) VALUES (?);';
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/management/absence-types/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('s', $description);

                // Query ausführen.
                if($stmt->execute() === FALSE) {
                     // Typ konnte nicht angelegt werden. Leite auf Übersichtsseite weiter.
                    $_SESSION['process_status'] = 'Der Fehlzeit-Typ mit der Id ' . $id . 
                        ' konnte nicht angelegt werden!';
                    header('Location: /management/absence-types/');
                    exit();

                } else {
                    // Typ konnte erfolgreich gelöscht werden.
                    $_SESSION['process_status'] = 'Der Fehlzeit-Typ mit der Id ' . $id . ' 
                        wurde erfolgreich angelegt!';
                    header('Location: /management/absence-types/');
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