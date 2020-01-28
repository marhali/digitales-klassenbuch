<?php
    /**
     * ##################################################################
     * #       F E H L Z E I T T Y P - E I N Z E L A N S I C H T        #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 29.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Binde globale Umgebungsvariablen ein.
    include_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/globals.php');

    // Lade Sicherheitsskript und lasse nur eingeloggte Benutzer auf diese Webseite zu.
    include_once(INC_ROOT . 'site-security.php');

    // !!! Geschützter Bereich !!!

    /*
     * Dynamische Variablen der Seite festlegen.
     */
    $title = 'Digitales Klassenbuch | Fehlzeit-Typen';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Fehlzeit-Typen > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Formular-Variablen
    $found_type = FALSE;

    $far_id;
    $far_description;

    // Es wurde eine Typen_Id übergeben. -> Benutzer möchte Einzelansicht über diesen Typ.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_NUMBER_INT);
    if($input_id !== null) {

        // Bereite SQL-Query vor.
        $sql = 'SELECT Id, Bezeichnung FROM Fehlzeit_Typ WHERE Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/management/absence-types/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen und Query ausführen.
            $stmt->bind_param('i', $input_id);
            $stmt->execute();
            $stmt->store_result();

            // Ein Typ mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->num_rows == 0) {
                
                echo('<p>Es wurde kein Fehlzeit-Typ mit der Id ' . $input_id . ' gefunden.</p>');
                echo('<a href="/management/absence-types/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_type = TRUE;

                // Result-Parameter binden
                $stmt->bind_result($far_id, $far_description);

                $stmt->fetch();
            }
        }
    }
?>
<div class="content">
    <form action="process_absence-type.php" method="POST" name="absence-type-form">
        <div><h1>Fehlzeit-Typ</h1></div>

        <div><label>Interne IdentNr.:</label></div>
        <div><input name="id" type="number" required readonly value="<?php echo($far_id); ?>"></div>

        <div><label>Bezeichnung:</label></div>
        <div>
            <input name="description" type="text" maxlength="32" required 
                value="<?php echo($far_description); ?>">
        </div>

        <div>
            <?php
                // Dieser Typ existiert bereits.
                if($found_type) {
                    echo('<input type="submit" name="btnSave" value="Fehlzeit-Typ speichern">');
                    echo('<input type="submit" name="btnDelete" value="Fehlzeit-Typ löschen">');

                } else { // Neuer Raum.
                    echo('<input type="submit" name="btnCreate" value="Fehlzeit-Typ anlegen">');
                }
            ?>

            <button formaction="/management/absence-types/" formnovalidate>Zurück</button>
        </div>
    </form>
</div>
<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>