<?php
    /**
     * ##################################################################
     * #              D A T A B A S E  -  M A N A G E R                 #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 20.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Lade Konfigurationsdatei für DB-Authentifizierung
    include_once('config.php');

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
?>