<?php
    /**
     * ##################################################################
     * #            F Ä C H E R - E I N Z E L A N S I C H T             #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 27.01.2019                         #
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
    $title = 'Digitales Klassenbuch | Fächerverwaltung';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Fächer > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Formular-Variablen
    $found_subject = FALSE;

    $far_id;
    $far_shortname;
    $far_descrption;

    // Es wurde eine Fächer_Id übergeben. -> Benutzer möchte Einzelansicht über dieses Fach.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_NUMBER_INT);
    if($input_id !== null) {

        // Bereite SQL-Query vor.
        $sql = 'SELECT Id, Kuerzel, Bezeichnung FROM Fach WHERE Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/school/subjects/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen und Query ausführen.
            $stmt->bind_param('i', $input_id);
            $stmt->execute();
            $stmt->store_result();

            // Ein Fach mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->num_rows == 0) {
                
                echo('<p>Es wurde kein Fach mit der Id ' . $input_id . ' gefunden.</p>');
                echo('<a href="/school/subjects/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_subject = TRUE;

                // Result-Parameter binden
                $stmt->bind_result($far_id, $far_shortname, $far_description);

                $stmt->fetch();
            }
        }
    }
?>
<div class="content">
    <form action="process_subject.php" method="POST" name="subject-form">
        <div><h1>Fach</h1></div>

        <div><label>Interne IdentNr.:</label></div>
        <div>
            <input name="id" type="number" required readonly value="<?php echo($far_id); ?>">
        </div>

        <div><label>Kürzel:</label></div>
        <div>
            <input name="shortname" type="text" maxlength="8" required 
                value="<?php echo($far_shortname); ?>">
        </div>

        <div><label>Bezeichnung:</label></div>
        <div>
            <input name="description" type="text" maxlength="32" required 
                value="<?php echo($far_description); ?>">
        </div>

        <div>
            <?php
                // Dieses Fach existiert bereits.
                if($found_subject) {
                    echo('<input type="submit" name="btnSave" value="Fach speichern">');
                    echo('<input type="submit" name="btnDelete" value="Fach löschen">');

                } else { // Neue Klasse.
                    echo('<input type="submit" name="btnCreate" value="Fach anlegen">');
                }
            ?>

            <button formaction="/school/subjects/" formnovalidate>Zurück</button>
        </div>
    </form>
</div>
<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>