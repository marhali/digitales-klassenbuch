<?php
    /**
     * ##################################################################
     * #     L E H R E R - V E R A R B E I T U N G S H A N D L E R      #
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
    write_log($mysqli, 'INFO', 'Hat einen Lehrer bearbeitet.');

    // Lehrer löschen.
    if(isset($_POST['btnDelete'])) {

        // Keine Lehrer_Id vorhanden.
        if(!(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        $post_id = $_POST['id'];

        // Lehrer aus der Datenbank löschen.

        // Bereite SQL-Query vor.
        $sql  = 'DELETE FROM Nutzer WHERE Id = ?;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/school/teachers/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen.
            $stmt->bind_param('i', $post_id);
            
            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Nutzer konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Lehrer mit der Id ' . $post_id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /school/teachers/');
                exit();

            } else {
                //Nutzer konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Der Lehrer mit der Id ' . $post_id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /school/teachers/');
                exit();
            }
        }
    
    // Lehrer registrieren oder Änderungen speichern.
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
        $surname = $_POST['surname'];
        $shortname = $_POST['shortname'];
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
                $sql = 'UPDATE Lehrer l JOIN Nutzer n ON (l.Id = n.Id) SET Benutzername = ?, 
                    Vorname = ?, Nachname = ?, Kuerzel = ?, Email = ?, Geburtsdatum = ?, 
                    Rolle_Id = ?, Passwort = ? WHERE l.Id = ?;';
            } else { // Ohne Pw-Änderung.
                $sql = 'UPDATE Lehrer l JOIN Nutzer n ON (l.Id = n.Id) SET Benutzername = ?, 
                    Vorname = ?, Nachname = ?, Kuerzel = ?, Email = ?, Geburtsdatum = ?, 
                    Rolle_Id = ? WHERE l.Id = ?;';
            }

            // Bereite SQL-Qury vor.
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/teachers/">Zurück zur Übersichtsseite</a>');
                echo(mysqli_error($mysqli));
                exit();

            } else {
                // Parameter setzen.
                if($password === null) {
                    $stmt->bind_param('ssssssii', $username, $forename, $surname, $shortname,
                    $email, $birthday, $role, $id);
                } else { // Mit Passwort
                    $stmt->bind_param('ssssssisi', $username, $forename, $surname, 
                    $shortname, $email, $birthday, $role, $password, $id);
                }

                //Query ausführen.
                if($stmt->execute() === FALSE) {
                    // Nutzer konnte nicht bearbeitet werden. Leite auf Übersichtsseite weiter.
                   $_SESSION['process_status'] = 'Der Lehrer ' . $username . 
                       ' konnte nicht ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/teachers/');
                   exit();

               } else {
                   //Nutzer konnte erfolgreich bearbeitet werden.
                   $_SESSION['process_status'] = 'Der Lehrer ' . $username . 
                       ' konnte erfolgreich ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/teachers/');
                   exit();
               }
            }

        } else {
            // Neuen Eintrag vornehmen.

            // Bereite SQL-Query vor.
            $sql_n = 'INSERT INTO Nutzer (Benutzername, Vorname, Nachname, Email, 
                Geburtsdatum, Rolle_Id, Passwort) VALUES (?, ?, ?, ?, ?, ?, ?);';
            $stmt_n = $mysqli->prepare($sql_n);

            $sql_l = 'INSERT INTO Lehrer (Id, Kuerzel) VALUES (
                (SELECT Id FROM Nutzer WHERE Benutzername = ?),?);';
            $stmt_l = $mysqli->prepare($sql_l);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt_n === FALSE || $stmt_l === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/teachers/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt_n->bind_param('sssssis', $username, $forename, $surname, $email, 
                    $birthday, $role, $password);

                $stmt_l->bind_param('ss', $username, $shortname);

                //Query ausführen.
                if($stmt_n->execute() === FALSE || $stmt_l->execute() === FALSE) {
                     // Nutzer konnte nicht angelegt werden. Leite auf Übersichtsseite weiter.
                    $_SESSION['process_status'] = 'Der Lehrer ' . $username . 
                        ' konnte nicht angelegt werden!';
                    header('Location: /school/teachers/');
                    exit();

                } else {
                    //Nutzer konnte erfolgreich gelöscht werden.
                    $_SESSION['process_status'] = 'Der Lehrer ' . $username . ' 
                        wurde erfolgreich angelegt!';
                    header('Location: /school/teachers/');
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