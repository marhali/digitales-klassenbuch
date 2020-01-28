<?php
    /**
     * ##################################################################
     * #    S C H Ü L E R - V E R A R B E I T U N G S H A N D L E R     #
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
    write_log($mysqli, 'INFO', 'Hat einen Schüler bearbeitet');

    // Schüler löschen.
    if(isset($_POST['btnDelete'])) {

        // Keine Schüler_Id vorhanden.
        if(!(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        $post_id = $_POST['id'];

        // Schüler aus der Datenbank löschen.

        // Bereite SQL-Query vor.
        $sql  = 'DELETE FROM Nutzer WHERE Id = ?;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/school/students/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen.
            $stmt->bind_param('i', $post_id);
            
            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Nutzer konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Der Schüler mit der Id ' . $post_id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /school/students/');
                exit();

            } else {
                //Nutzer konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Der Nutzer mit der Id ' . $post_id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /school/students/');
                exit();
            }
        }
    
    // Schüler registrieren oder Änderungen speichern.
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
        $class = $_POST['class'];
        $company = $_POST['company'];
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
                $sql = 'UPDATE Schueler s JOIN Nutzer n ON (s.Id = n.Id) SET Benutzername = ?, 
                    Vorname = ?, Nachname = ?, Klasse_Id = ?, Betrieb_Id = ?, Email = ?, 
                    Geburtsdatum = ?, Rolle_Id = ?, Passwort = ? WHERE s.Id = ?;';
            } else { // Ohne Pw-Änderung.
                $sql = 'UPDATE Schueler s JOIN Nutzer n ON (s.Id = n.Id) SET Benutzername = ?, 
                    Vorname = ?, Nachname = ?, Klasse_Id = ?, Betrieb_Id = ?, Email = ?, 
                    Geburtsdatum = ?, Rolle_Id = ? WHERE s.Id = ?;';
            }

            // Bereite SQL-Qury vor.
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/students/">Zurück zur Übersichtsseite</a>');
                echo(mysqli_error($mysqli));
                exit();

            } else {
                // Parameter setzen.
                if($password === null) {
                    $stmt->bind_param('sssiissii', $username, $forename, $surname, $class, 
                    $company, $email, $birthday, $role, $id);
                } else { // Mit Passwort
                    $stmt->bind_param('sssiissisi', $username, $forename, $surname, 
                    $class, $company, $email, $birthday, $role, $password, $id);
                }

                //Query ausführen.
                if($stmt->execute() === FALSE) {
                    // Nutzer konnte nicht bearbeitet werden. Leite auf Übersichtsseite weiter.
                   $_SESSION['process_status'] = 'Der Schüler ' . $username . 
                       ' konnte nicht ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/students/');
                   exit();

               } else {
                   //Nutzer konnte erfolgreich bearbeitet werden.
                   $_SESSION['process_status'] = 'Der Schüler ' . $username . 
                       ' konnte erfolgreich ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/students/');
                   exit();
               }
            }

        } else {
            // Neuen Eintrag vornehmen.

            // Bereite SQL-Query vor.
            $sql_n = 'INSERT INTO Nutzer (Benutzername, Vorname, Nachname, Email, 
                Geburtsdatum, Rolle_Id, Passwort) VALUES (?, ?, ?, ?, ?, ?, ?);';
            $stmt_n = $mysqli->prepare($sql_n);

            $sql_s = 'INSERT INTO Schueler (Id, Klasse_Id, Betrieb_Id) VALUES (
                (SELECT Id FROM Nutzer WHERE Benutzername = ?),?,?);';
            $stmt_s = $mysqli->prepare($sql_s);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt_n === FALSE || $stmt_s === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/students/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt_n->bind_param('sssssis', $username, $forename, $surname, $email, 
                    $birthday, $role, $password);

                $stmt_s->bind_param('sii', $username, $class, $company);

                //Query ausführen.
                if($stmt_n->execute() === FALSE || $stmt_s->execute() === FALSE) {
                     // Nutzer konnte nicht angelegt werden. Leite auf Übersichtsseite weiter.
                    $_SESSION['process_status'] = 'Der Schüler ' . $username . 
                        ' konnte nicht angelegt werden!';
                    header('Location: /school/students/');
                    exit();

                } else {
                    //Nutzer konnte erfolgreich gelöscht werden.
                    $_SESSION['process_status'] = 'Der Schüler ' . $username . ' 
                        wurde erfolgreich angelegt!';
                    header('Location: /school/students/');
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