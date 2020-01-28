<?php
    /**
     * ##################################################################
     * #             R A U M - E I N Z E L A N S I C H T                #
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
    $title = 'Digitales Klassenbuch | Raumverwaltung';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Räume > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Formular-Variablen
    $found_room = FALSE;

    $far_id;
    $far_building;
    $far_level;
    $far_room;

    // Es wurde eine Raum_Id übergeben. -> Benutzer möchte Einzelansicht über diesen Raum.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_NUMBER_INT);
    if($input_id !== null) {

        // Bereite SQL-Query vor.
        $sql = 'SELECT Id, Gebaeude, Etage, Raum FROM Raum WHERE Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/management/rooms/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen und Query ausführen.
            $stmt->bind_param('i', $input_id);
            $stmt->execute();
            $stmt->store_result();

            // Ein Raum mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->num_rows == 0) {
                
                echo('<p>Es wurde kein Raum mit der Id ' . $input_id . ' gefunden.</p>');
                echo('<a href="/management/rooms/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_room = TRUE;

                // Result-Parameter binden
                $stmt->bind_result($far_id, $far_building, $far_level, $far_room);

                $stmt->fetch();
            }
        }
    }
?>
<div class="content">
    <form action="process_room.php" method="POST" name="room-form">
        <div><h1>Raum</h1></div>

        <div><label>Interne IdentNr.:</label></div>
        <div><input name="id" type="number" required readonly value="<?php echo($far_id); ?>"></div>

        <div><label>Gebäude:</label></div>
        <div>
            <input name="building" type="text" maxlength="32" required 
                value="<?php echo($far_building); ?>">
        </div>
        
        <div><label>Etage:</label></div>
        <div>
            <input name="level" type="text" maxlength="32" required 
                value="<?php echo($far_level); ?>">
        </div>

        <div><label>Raum:</label></div>
        <div>
            <input name="room" type="text" maxlength="32" required 
                value="<?php echo($far_room); ?>">
        </div>

        <div>
            <?php
                // Dieser Raum existiert bereits.
                if($found_room) {
                    echo('<input type="submit" name="btnSave" value="Raum speichern">');
                    echo('<input type="submit" name="btnDelete" value="Raum löschen">');

                } else { // Neuer Raum.
                    echo('<input type="submit" name="btnCreate" value="Raum anlegen">');
                }
            ?>

            <button formaction="/management/rooms/" formnovalidate>Zurück</button>
        </div>
    </form>
</div>
<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>