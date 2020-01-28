<?php
    /**
     * ##################################################################
     * #            K O N F I G U R A T I O N S D A T E I               #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 19.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    /**
     * Allgemeine Optionen
     */

    // Nutzt das System sichere Verbindungen (HTTPS)?
    define('USE_HTTPS', false);
    
    
    /**
     * MySQL-Datenbank
     * Authentifzierungsparameter
     */

    // IP- oder Host-Adresse der Datenbank
    // Optional mit Angabe der Portnummer (Standard: 3306)
    // Beispiel: 192.168.1.0:3306
    // Die Felder mit < > sind auszufüllen
    define('DB_HOST', '<host-address>');

    // Benutzername der Datenbank
    define('DB_USER', '<db-user>');

    // Passwort zum Benutzer der Datenbank
    define('DB_PASSWORD', '<db-password>');

    // Der Name der zu nutzenden Datenbank
    define('DB_DATABASE', '<db-database>');
?>