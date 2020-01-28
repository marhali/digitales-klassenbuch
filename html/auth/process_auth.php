<?php
    /**
     * ##################################################################
     * #       A U T H E N T I F I Z I E R U N G S H A N D L E R        #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 22.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Binde die benötigten Funktionen ein
    include_once('../../includes/database.php');
    include_once('../../includes/authenticator.php');

    // Starte die sichere Sitzung
    sec_session_start();

    if(!(isset($_POST['username'], $_POST['password']))) {
        echo 'Invalid request.';
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Login fehlgeschlagen
    if(!(login($username, $password, $mysqli))) {
        header('Location: /auth/?error=credentials');
        exit();
    }

    // !!! Erfolgreich eingeloggt !!!
    header('Location: ../overview/');
?>