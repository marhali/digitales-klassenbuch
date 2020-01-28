<?php
    /**
     * ##################################################################
     * #     N U T Z E R - V E R A R B E I T U N G S H A N D L E R      #
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

    // Importiere DB-Hilfsfunktionen
    include_once(INC_ROOT . 'db-helper.php');

    // !!! Geschützter Bereich !!!

    // Protokolliere
    write_log($mysqli, 'INFO', 'Hat eine Änderung an den Nutzern durchgeführt.');

    // Nutzer löschen.
    if(isset($_POST['btnDelete'])) {

        // Keine Nutzer_Id vorhanden.
        if(!(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        $post_id = $_POST['id'];

        // Nutzer aus der Datenbank löschen.

        // Bereite SQL-Query vor.
        $sql  = 'DELETE FROM Nutzer WHERE Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/management/users/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen.
            $stmt->bind_param('i', $post_id);
            
            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Nutzer konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Nutzer mit der Id ' . $post_id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /management/users/');
                exit();

            } else {
                //Nutzer konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Der Nutzer mit der Id ' . $post_id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /management/users/');
                exit();
            }
        }
    
    // Nutzer registrieren oder Änderungen speichern.
    } elseif (isset($_POST['btnCreate']) || isset($_POST['btnSave'])) {

        // Kann Nutzer ohne Id nicht speichern.
        if(isset($_POST['btnSave']) && !(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        // Daten aus Formular auslesen
        $id = $_POST['id'];
        $username = $_POST['username'];
        $forename = $_POST['forename'];
        $surname= $_POST['surname'];
        $email = $_POST['email'];
        $birthday = $_POST['birthday'];
        $role = $_POST['role'];
        $password = $_POST['password'];

        // Hashe das Passwort, wenn eines übergeben wurde.
        if($password !== null) {
            $password = hash('sha512', $password);
        }

        // Änderungen speichern.
        if(isset($_POST['btnSave'])) {
            $sql;

            // Ändere auch das Passwort.
            if($password !== null) {
                $sql = 'UPDATE Nutzer SET Benutzername = ?, Vorname = ?, Nachname = ?, 
                    Email = ?, Geburtsdatum = ?, Rolle_Id = ?, Passwort = ? WHERE Id = ?;';
            } else { // Ohne Pw-Änderung.
                $sql = 'UPDATE Nutzer SET Benutzername = ?, Vorname = ?, Nachname = ?, 
                    Email = ?, Geburtsdatum = ?, Rolle_Id = ? WHERE Id = ?;';
            }

            // Bereite SQL-Qury vor.
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/management/users/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                if($password === null) {
                    $stmt->bind_param('sssssii', $username, $forename, $surname, $email, 
                    $birthday, $role, $id);
                } else { // Mit Passwort
                    $stmt->bind_param('sssssisi', $username, $forename, $surname, $email, 
                    $birthday, $role, $password, $id);
                }

                //Query ausführen.
                if($stmt->execute() === FALSE) {
                    // Nutzer konnte nicht bearbeitet werden. Leite auf Übersichtsseite weiter.
                   $_SESSION['process_status'] = 'Der Nutzer ' . $username . 
                       ' konnte nicht ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /management/users/');
                   exit();

               } else {
                   //Nutzer konnte erfolgreich bearbeitet werden.
                   $_SESSION['process_status'] = 'Der Nutzer ' . $username . 
                       ' konnte erfolgreich ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /management/users/');
                   exit();
               }
            }

        } else {
            // Neuen Eintrag vornehmen.

            // Bereite SQL-Query vor.
            $sql = 'INSERT INTO Nutzer (Benutzername, Vorname, Nachname, Email, 
                Geburtsdatum, Rolle_Id, Passwort) VALUES (?, ?, ?, ?, ?, ?, ?);';
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/management/users/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('sssssis', $username, $forename, $surname, $email, 
                    $birthday, $role, $password);

                //Query ausführen.
                if($stmt->execute() === FALSE) {
                     // Nutzer konnte nicht angelegt werden. Leite auf Übersichtsseite weiter.
                    $_SESSION['process_status'] = 'Der Nutzer ' . $username . 
                        ' konnte nicht angelegt werden!';
                    header('Location: /management/users/');
                    exit();

                } else {
                    //Nutzer konnte erfolgreich gelöscht werden.
                    $_SESSION['process_status'] = 'Der Nutzer ' . $username . ' 
                        wurde erfolgreich angelegt!';
                    header('Location: /management/users/');
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