<?php
    /**
     * ##################################################################
     * #       A U T H E N T I F I Z I E R U N G S S Y S T E M          #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 19.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Lade die Konfigurationsdatei.
    include_once('config.php');

    // Lade die Umgebungsvariablen.
    include_once('globals.php');

    // Lade DB-Hilfsfunktionen.
    include_once('db-helper.php');

    /**
     * Ähnlich zu @start_session() aber mit erhöhter Sicherheit.
     */
    function sec_session_start() {
        
        // Die Sitzung darf keine Cookies setzen -> Fehler
        if(ini_set('session.use_only_cookies', 1) === FALSE) {
            header("Location: error.php?err=Cookie konnte nicht gesetzt werden.");
            exit();
        }

        // Cookie auslesen
        $cookie = session_get_cookie_params();

        // Cookie um sichere Optionen erweitern
        session_set_cookie_params(
            $cookie['lifetime'],
            $cookie['path'],
            $cookie['domain'],
            USE_HTTPS,
            true
        );
        
        // Sitzung überschreiben und neu starten
        session_name('DK_AUTH_SESSION');
        session_start();
        session_regenerate_id();
    }

    /**
     * Überprüft, ob die übergebenen Parameter (Nutzername, Passwort)
     * mit denen aus der Datenbank übereinstimmen.
     * Bei Übereinstimmung wird FALSE, andernfalls FALSE zurückgegeben.
     */
    function login(string $username, string $password, mysqli $mysqli) : bool {

        $sql = 'SELECT Id, Benutzername, Passwort FROM Nutzer WHERE Benutzername = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement lässt sich nicht erstellen. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            return false;
        }

        // Parameter setzen und Query ausführen.
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        // Der Benutzer mit dem Nutzernamen $username existiert nicht.
        if($stmt->num_rows != 1) {
            return false;
        }

        // Result-Parameter binden
        $stmt->bind_result($db_user_id, $db_username, $db_password);
        $stmt->fetch();

        //TODO: Anzahl an Login-Versuchen limitieren.

        $pa_hashed = hash('sha512', $password);

        // Die Passwörter stimmen nicht überein.
        if($pa_hashed != $db_password) {
            return false;
        }

        // !!! Authentifizierung erfolgreich !!!

        $agent = $_SERVER['HTTP_USER_AGENT'];

        // XSS-Schutz auf Sitzungsvariablen
        $db_user_id = preg_replace("/[^0-9]+/", "", $db_user_id);
        $_SESSION['USER_ID'] = $db_user_id;

        $db_username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $db_username);
        $_SESSION['USERNAME'] = $db_username;

        $_SESSION['AUTH_KEY'] = hash('sha512', $db_password . $agent);

        // Erfolgreichen Login protokollieren.
        write_log($mysqli, 'INFO', 'Nutzer hat sich angemeldet.');
        return true;
    }

    /**
     * Überprüft die Sitzungsinformationen des Benutzers anhand der
     * zuvor gesetzten Sitzungsvariablen.
     * Bei aktiver Sitzung wird TRUE, andernfalls FALSE zurückgegeben.
     */
    function check_login(mysqli $mysqli) : bool {

        // Keine aktive Sitzung, da keine Sitzungsvariablen vorhanden sind.
        if(!(isset($_SESSION['USER_ID'], $_SESSION['USERNAME'], $_SESSION['AUTH_KEY']))) {
            return false;
        }

        $user_id = $_SESSION['USER_ID'];
        $username = $_SESSION['USERNAME'];
        $auth_key = $_SESSION['AUTH_KEY'];

        $agent = $_SERVER['HTTP_USER_AGENT'];

        $sql = 'SELECT Passwort FROM Nutzer WHERE Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement lässt sich nicht erstellen. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            return false;
        }

        // Parameter setzen und Query ausführen.
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->store_result();

        // Es existiert kein Benutzer mit der Id $user_id.
        if($stmt->num_rows != 1) {
            return false;
        }

        // Result-Parameter binden
        $stmt->bind_result($db_password);
        $stmt->fetch();

        $origin_auth_key = hash('sha512', $db_password . $agent);

        //Authentifizierungsschlüssel stimmt überein > Benutzer ist bereits eingeloggt.
        if($auth_key == $origin_auth_key) {
            return true;
        }

        return false;
    }
?>