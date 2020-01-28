<?php
    /**
     * ##################################################################
     * #               V E R W A L T U N G S S E I T E                  #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 25.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Lade Sicherheitsskript und lasse nur eingeloggte Benutzer auf diese Webseite zu.
    include_once('../../includes/site-security.php');

    // !!! Geschützter Bereich !!!

    /*
     * Dynamische Variablen der Seite festlegen.
     */
    $title = 'Digitales Klassenbuch | Verwaltung';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung';

    // Binde globalen HTML-Kopf ein.
    include('../../includes/site/global-head.php');

    //Binde globale Kopfzeile ein.
    include('../../includes/site/global-header.php');
?>

    <?php
    //Binde globale Fußzeile ein.
    include('../../includes/site/global-footer.php');
?>