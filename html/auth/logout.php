<?php
    /**
     * ##################################################################
     * #                  L O G O U T H A N D L E R                     #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 22.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Binde globale Umgebungsvariablen ein.
    include_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/globals.php');

    // Binde Das Authentifizierungssystem ein.
    include_once(INC_ROOT . 'authenticator.php');

    // Binde die Datenbank ein.
    include_once(INC_ROOT . 'database.php');

    // Importiere DB-Hilfsfunktionen.
    include_once(INC_ROOT . 'db-helper.php');

    // Starte die sichere Sitzung
    sec_session_start();

    // Protokolliere Abmeldung.
    write_log($mysqli, 'INFO', 'Nutzer hat sich abgemeldet.');

    // Bereinige alle Session-Parameter
    $_SESSION = array();

    $cookie = session_get_cookie_params();

    // Lösche alle Cookie-Parameter
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $cookie['path'],
        $cookie['domain'],
        $cookie['secure'],
        $cookie['httponly']
    );

    // Vernichte die Sitzung und leite den Benutzer auf die Anmeldeseite weiter
    session_destroy();
    header('Location: /auth/');
?>