<?php
    /**
     * ##################################################################
     * #     K L A S S E N - V E R A R B E I T U N G S H A N D L E R    #
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
    write_log($mysqli, 'INFO', 'Hat eine Klasse geändert.');

    // Klasse löschen.
    if(isset($_POST['btnDelete'])) {

        // Keine Klassen_Id vorhanden.
        if(!(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        $post_id = $_POST['id'];

        // Klasse aus der Datenbank löschen.
        removeAdditionalTables($mysqli, $_POST['shortname']);

        // Bereite SQL-Query vor.
        $sql  = 'DELETE FROM Klasse WHERE Id = ?;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/school/classes/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen.
            $stmt->bind_param('i', $post_id);
            
            //Query ausführen.
            if($stmt->execute() === FALSE) {

                // Klasse konnte nicht gelöscht werden. Leite auf Übersichtsseite weiter.
                $_SESSION['process_status'] = 'Die Klasse mit der Id ' . $post_id . 
                    ' konnte nicht gelöscht werden!';
                header('Location: /school/classes/');
                exit();

            } else {
                // Klasse konnte erfolgreich gelöscht werden.
                $_SESSION['process_status'] = 'Die Klasse mit der Id ' . $post_id . ' 
                    wurde erfolgreich gelöscht.';
                header('Location: /school/classes/');
                exit();
            }
        }
    
    // Klasse registrieren oder Änderungen speichern.
    } elseif (isset($_POST['btnCreate']) || isset($_POST['btnSave'])) {

        // Kann Klasse ohne Id nicht speichern.
        if(isset($_POST['btnSave']) && !(isset($_POST['id']))) {
            echo('Invalid request. No id given.');
            exit();
        }

        // Daten aus Formular auslesen
        $id = $_POST['id'];
        $shortname = $_POST['shortname'];
        $teacher = $_POST['teacher'];

        // Änderungen speichern.
        if(isset($_POST['btnSave'])) {
            $sql = 'UPDATE Klasse SET Kuerzel = ?, Lehrer_Id = ? WHERE Id = ?;';

            // Bereite SQL-Query vor.
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/classes/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('sii', $shortname, $teacher, $id);

                //Query ausführen.
                if($stmt->execute() === FALSE) {
                    // Klasse konnte nicht bearbeitet werden. Leite auf Übersichtsseite weiter.
                   $_SESSION['process_status'] = 'Die Klasse ' . $shortname . 
                       ' konnte nicht ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/classes/');
                   exit();

               } else {
                   // Nutzer konnte erfolgreich bearbeitet werden.
                   $_SESSION['process_status'] = 'Die Klasse ' . $class . 
                       ' konnte erfolgreich ' . ((isset($_POST['btnSave'])) ? 'gespeichert' : 'angelegt') . ' werden!';
                   header('Location: /school/classes/');
                   exit();
               }
            }

        } else {
            // Neuen Eintrag vornehmen.
            addAdditionalTables($mysqli, $shortname);

            // Bereite SQL-Query vor.
            $sql = 'INSERT INTO Klasse (Kuerzel, Lehrer_Id) VALUES (?,?);';
            $stmt = $mysqli->prepare($sql);

            // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
            if($stmt === FALSE) {
                echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
                echo('<a href="/school/classes/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                // Parameter setzen.
                $stmt->bind_param('si', $shortname, $teacher);

                // Query ausführen.
                if($stmt->execute() === FALSE) {
                     // Klasse konnte nicht angelegt werden. Leite auf Übersichtsseite weiter.
                    $_SESSION['process_status'] = 'Die Klasse ' . $shortname . 
                        ' konnte nicht angelegt werden!';
                    header('Location: /school/classes/');
                    exit();

                } else {
                    //Nutzer konnte erfolgreich gelöscht werden.
                    $_SESSION['process_status'] = 'Die Klasse ' . $shortname . ' 
                        wurde erfolgreich angelegt!';
                    header('Location: /school/classes/');
                    exit();
                }
            }
        }
    
    // Fehlerhafte Anfrage.
    } else {
        echo('Invalid request.');
        exit();
    }

    function removeAdditionalTables(mysqli $mysqli, string $id) {
        $sql = 'DROP TABLE Klassenbuch_'.$id.'_Woche';
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();

        $sql = 'DROP TABLE Klassenbuch_'.$id.'_Tag';
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();

        $sql = 'DROP TABLE Klassenbuch_'.$id.'_Fehlzeit';
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
    }

    function addAdditionalTables(mysqli $mysqli, string $id) {
        $sql = '
        CREATE TABLE Klassenbuch_'.$id.'_Woche (
            Block_Nr INT NOT NULL,
            Wochen_Nr INT NOT NULL,
            Datum_Von DATE NOT NULL,
            Datum_Bis DATE NOT NULL,
            KW INT NOT NULL,
            Signiert_KL_Id INT,
            Signiert_SL_Id INT,
            Fehlzeiten_Best_Id INT,
            PRIMARY KEY (Block_Nr, Wochen_NR),
            FOREIGN KEY (Signiert_KL_Id) REFERENCES Nutzer(Id),
            FOREIGN KEY (Signiert_SL_Id) REFERENCES Nutzer(Id),
            FOREIGN KEY (Fehlzeiten_Best_Id) REFERENCES Nutzer(Id)
        );';

        $stmt = $mysqli->prepare($sql);
        $stmt->execute();

        $sql = '
        CREATE TABLE Klassenbuch_'.$id.'_Tag (
            Datum DATE NOT NULL,
            Stunde INT NOT NULL,
            Fach_Id_Soll INT NOT NULL,
            Fach_Id_Ist INT NOT NULL,
            Lehrer_Id INT NOT NULL,
            Thema VARCHAR(255),
            Hausaufgabe VARCHAR(255),
            Best_Lehrer_Id INT,
            PRIMARY KEY (Datum, Stunde),
            FOREIGN KEY (Fach_Id_Soll) REFERENCES Fach(Id),
            FOREIGN KEY (Fach_Id_Ist) REFERENCES Fach(Id),
            FOREIGN KEY (Lehrer_Id) REFERENCES Lehrer(Id),
            FOREIGN KEY (Best_Lehrer_Id) REFERENCES Nutzer(Id)
        );';

        $stmt = $mysqli->prepare($sql);
        $stmt->execute();

        $sql = '
        CREATE TABLE Klassenbuch_'.$id.'_Fehlzeit (
            Datum DATE NOT NULL,
            Stunde INT NOT NULL,
            Schueler_Id INT NOT NULL,
            Fehlzeit_Typ_Id INT NOT NULL,
            Infotext VARCHAR(255),
            PRIMARY KEY (Datum, Stunde, Schueler_Id),
            FOREIGN KEY (Schueler_Id) REFERENCES Schueler(Id),
            FOREIGN KEY (Fehlzeit_Typ_Id) REFERENCES Fehlzeit_Typ(Id)
        );';

        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
    }
?>