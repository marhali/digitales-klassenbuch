<?php
    /**
     * ##################################################################
     * #           K L A S S E N - E I N Z E L A N S I C H T            #
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
    $title = 'Digitales Klassenbuch | Klassenverwaltung';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Klassen > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Binde DB-Hilfsfunktionen ein.
    include_once(INC_ROOT . 'db-helper.php');
    
    // Formular-Variablen
    $found_class = FALSE;

    $far_id;
    $far_shortname;
    $far_teacher_id;

    // Es wurde eine Klassen_Id übergeben. -> Benutzer möchte Einzelansicht über diese Klasse.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_NUMBER_INT);
    if($input_id !== null) {

        // Bereite SQL-Query vor.
        $sql = 'SELECT Id, Kuerzel, Lehrer_Id FROM Klasse WHERE Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/school/classes/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen und Query ausführen.
            $stmt->bind_param('i', $input_id);
            $stmt->execute();
            $stmt->store_result();

            // Eine Klasse mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->num_rows == 0) {
                
                echo('<p>Es wurde keine Klasse mit der Id ' . $input_id . ' gefunden.</p>');
                echo('<a href="/school/classes/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_class = TRUE;

                // Result-Parameter binden
                $stmt->bind_result($far_id, $far_shortname, $far_teacher_id);

                $stmt->fetch();
            }
        }
    }
?>
<div class="content">
    <form action="process_class.php" method="POST" name="class-form">
        <div><h1>Klasse</h1></div>

        <div><label>Interne IdentNr.:</label></div>
        <div>
            <input name="id" type="number" required readonly value="<?php echo($far_id); ?>">
        </div>

        <div><label>Kürzel:</label></div>
        <div>
            <input name="shortname" type="text" maxlength="8" required value="<?php echo($far_shortname); ?>">
        </div>

        <div><label>Lehrer:</label></div>
        <div>
            <select name="teacher">
                <?php
                    foreach(get_teacher($mysqli) as $row) {
                        foreach($row as $id => $value) {
                            echo('<option ' . ($id==$far_teacher_id ? 'selected' : '') . ' value="' . $id . '">' . $value['Kuerzel'] . ' - ' . 
                                $value['Vorname'] . ' ' . $value['Nachname'] . '</option>');
                        }
                    }
                ?>

        </select>
        </div>

        <div>
            <?php
                // Diese Klasse existiert bereits.
                if($found_class) {
                    echo('<input type="submit" name="btnSave" value="Klasse speichern">');
                    echo('<input type="submit" name="btnDelete" value="Klasse löschen">');

                } else { // Neue Klasse.
                    echo('<input type="submit" name="btnCreate" value="Klasse anlegen">');
                }
            ?>

            <button formaction="/school/classes/" formnovalidate>Zurück</button>
        </div>
    </form>
</div>
<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>