<?php
    /**
     * ##################################################################
     * #        H I L F S K L A S S E  Z U R  D A T E N B A N K         #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 29.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    /**
     * Übergibt eine Liste aller Klassen, welche in der Datenbank
     * gespeichert sind.
     */
    function get_classes(mysqli $mysqli) : array {
        $classes = array();

        $sql = 'SELECT Id, Kuerzel FROM Klasse;';
        $stmt = $mysqli->prepare($sql);

        // Fehler in der DB-Verbindung
        if($stmt === FALSE) {
            return null;
        }

        // Query ausführen.
        $stmt->execute();
        $stmt->store_result();

        $fetch_id;
        $fetch_shortname;

        $stmt->bind_result($fetch_id, $fetch_shortname);

        while($stmt->fetch()) {
            $classes[] = array($fetch_id => $fetch_shortname);
        }

        return $classes;
    }

    /**
     * Übergibt eine Liste aller Fächer.
     */
    function get_subjects(mysqli $mysqli) : array {
        $subjects = array();

        $sql = 'SELECT Id, Kuerzel FROM Fach;';
        $stmt = $mysqli->prepare($sql);

        // Fehler in der DB-Verbindung
        if($stmt === FALSE) {
            return null;
        }

        // Query ausführen.
        $stmt->execute();
        $stmt->store_result();

        $fetch_id;
        $fetch_shortname;

        $stmt->bind_result($fetch_id, $fetch_shortname);

        while($stmt->fetch()) {
            $subjects[] = array($fetch_id => $fetch_shortname);
        }

        return $subjects;
    }

    /**
     * Übergibt eine Liste aller Räume.
     */
    function get_rooms(mysqli $mysqli) : array {
        $rooms = array();

        $sql = 'SELECT Id, Raum FROM Raum;';
        $stmt = $mysqli->prepare($sql);

        // Fehler in der DB-Verbindung
        if($stmt === FALSE) {
            return null;
        }

        // Query ausführen.
        $stmt->execute();
        $stmt->store_result();

        $fetch_id;
        $fetch_room;

        $stmt->bind_result($fetch_id, $fetch_room);

        while($stmt->fetch()) {
            $rooms[] = array($fetch_id => $fetch_room);
        }

        return $rooms;
    }

    /**
     * Übergibt eine Liste aller Lehrer.
     */
    function get_teacher(mysqli $mysqli) : array {
        $teacher = array();

        $sql = 'SELECT l.Id, Kuerzel, Vorname, Nachname FROM Lehrer l 
            JOIN Nutzer n ON (l.Id = n.Id);';
        $stmt = $mysqli->prepare($sql);

        // Fehler in der DB-Verbindung
        if($stmt === FALSE) {
            return null;
        }

        // Query ausführen.
        $stmt->execute();
        $stmt->store_result();

        $fetch_id;
        $fetch_shortname;
        $fetch_forename;
        $fetch_surname;

        $stmt->bind_result($fetch_id, $fetch_shortname, $fetch_forename, $fetch_surname);

        while($stmt->fetch()) {
            $teacher[] = array($fetch_id => array('Kuerzel' => $fetch_shortname, 
                'Vorname' => $fetch_forename, 'Nachname' => $fetch_surname));
        }

        return $teacher;
    }

    /**
     * Ruft alle Informationen über den Nutzer der aktuellen Sitzung ab.
     */
    function get_user(mysqli $mysqli) : array {
        $sql = 'SELECT Id, Benutzername, Vorname, Nachname, Email, 
            Geburtsdatum, Rolle_Id FROM Nutzer WHERE Benutzername = ?';
        $stmt = $mysqli->prepare($sql);

        // Fehler in der DB-Verbindung
        if($stmt === FALSE) {
            return null;
        }

        // Parameter binden
        $stmt->bind_param('s', $_SESSION['USERNAME']);

        // Query ausführen.
        $stmt->execute();
        $stmt->store_result();

        $fetch_id;
        $fetch_username;
        $fetch_forename;
        $fetch_surname;
        $fetch_email;
        $fetch_birthday;
        $fetch_role_id;

        $stmt->bind_result($fetch_id, $fetch_username, $fetch_forename, $fetch_surname, 
            $fetch_email, $fetch_birthday, $fetch_role_id);

        $stmt->fetch();

        return array('Id' => $fetch_id, 'Benutzername' => $fetch_username,
            'Vorname' => $fetch_forename, 'Nachname' => $fetch_surname,
            'Email' => $fetch_email, 'Geburtsdatum' => $fetch_birthday,
            'Rolle_Id' => $fetch_role_id);
    }

    /**
     * Protokolliert eine bestimmte Aktion.
     */
    function write_log(mysqli $mysqli, string $type, string $info) {
        $sql = 'INSERT INTO Protokoll(Zeitstempel, Nutzer_Id, Protokolltyp_Id, Info) 
            VALUES (NOW(),?,1,?);';
        $stmt = $mysqli->prepare($sql);

        // Fehler in der DB-Verbindung
        if($stmt === FALSE) {
            return;
        }

        // TODO: Protokolltyp filtern

        // Parameter binden
        $stmt->bind_param('is', get_user($mysqli)['Id'], $info);

        // Query ausführen.
        $stmt->execute();
    }
?>