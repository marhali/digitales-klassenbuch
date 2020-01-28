<?php
    /**
     * ##################################################################
     * #   H A N D L E R  F Ü R  G E S C H Ü T Z T E  B E R E I C H E   #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 24.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Binde Anmeldesystem ein.
    include_once('database.php');
    include_once('authenticator.php');

    // Starte die sichere Sitzung.
    sec_session_start();

    // Der aktuelle Benutzer ist nicht eingeloggt. -> Auf Anmeldeseite umleiten.
    if(check_login($mysqli) == false) {
        header('Location: /auth/?error=auth');
        exit();
    }
?>