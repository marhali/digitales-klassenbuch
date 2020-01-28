<?php
    /**
     * ##################################################################
     * #                      U T I L I T I E S                         #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 21.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */


    /**
     * Bereinigt sämtliche PHP_SELF-Variablen einer Zeichenkette.
     * Gibt die bereinigten Zeichenkette wieder.
     * Ursrunglüch aus dem WordPress-CMS
     */
    function wipe_url(string $url) : string {
        
        // Die Zeichenkette enthält keine Zeichen
        if($url == '') {
            return $url;
        }

        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = (string) $url;
 
        $count = 1;
        while ($count) {
            $url = str_replace($strip, '', $url, $count);
        }
 
        $url = str_replace(';//', '://', $url);
 
        $url = htmlentities($url);
 
        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039;', $url);
 
        if ($url[0] !== '/') {
            // Nur relative Links von $_SERVER['PHP_SELF']
            return '';
        }
                
        return $url;
    }
?>