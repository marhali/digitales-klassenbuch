<?php
    /**
     * ##################################################################
     * #           S C H Ü L E R - E I N Z E L A N S I C H T            #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 26.01.2019                         #
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
    $title = 'Digitales Klassenbuch | Schülerverwaltung';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Schüler > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Formular-Variablen
    $found_student = FALSE;

    $far_id;
    $far_username;
    $far_forename;
    $far_surname;
    $far_class;
    $far_company;
    $far_email;
    $far_birthday;
    $far_role_id;
    $far_password;

    // Es wurde eine Schüler_Id übergeben. -> Benutzer möchte Einzelansicht über diesen Schüler.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_NUMBER_INT);
    if($input_id !== null) {

        // Bereite SQL-Query vor.
        $sql = 'SELECT s.Id, Benutzername, Vorname, Nachname, Klasse_Id, Betrieb_Id, Email, 
            Geburtsdatum, Rolle_Id FROM Schueler s JOIN Nutzer n ON (s.Id = n.Id) 
            WHERE s.Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/school/users/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen und Query ausführen.
            $stmt->bind_param('i', $input_id);
            $stmt->execute();
            $stmt->store_result();

            // Ein Benutzer mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->num_rows == 0) {
                
                echo('<p>Es wurde kein Schüler mit der Id ' . $input_id . ' gefunden.</p>');
                echo('<a href="/school/students/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_student = TRUE;

                // Result-Parameter binden
                $stmt->bind_result($far_id, $far_username, $far_forename, $far_surname, $far_class,
                    $far_company, $far_email, $far_birthday, $far_role_id);
                
                $stmt->fetch();
            }
        }
    }
?>
<div class="content">
    <form action="process_student.php" method="POST" name="student-form">
        <div><h1>Schüler</h1></div>

        <div><label>Interne IdentNr.:</label></div>
        <div>
            <input name="id" type="number" required readonly value="<?php echo($far_id); ?>">
        </div>

        <div><label>Benutzername:</label></div>
        <div>
            <input name="username" type="text" maxlength="32" required value="<?php echo($far_username); ?>">
        </div>

        <div><label>Vorname:</label></div>
        <div>
            <input name="forename" type="text" maxlength="32" required value="<?php echo($far_forename); ?>">
        </div>

        <div><label>Nachname:</label></div>
        <div>
            <input name="surname" type="text" maxlength="32" required value="<?php echo($far_surname); ?>">
        </div>

        <div><label>Klasse:</label></div>
        <div>
            <select name="class" required>
                <?php
                    foreach(get_classes($mysqli) as $row) {
                        foreach($row as $id => $shortname) {
                            echo('<option ' . ($id == $far_class?'selected':'') . ' value="' . $id . '">' . $shortname . '</option>');
                        }
                    }
                ?>

            </select>
        </div>

        <div><label>Betrieb:</label></div>
        <div>
            <select name="company">
                <option value="1">BWI GmbH</option>
            </select>
        </div>

        <div><label>E-Mail:</label></div>
        <div>
            <input name="email" type="email" maxlength="254" required value="<?php echo($far_email); ?>">
        </div>

        <div><label>Geburtsdatum:</label></div>
        <div>
            <input name="birthday" type="date" required value="<?php echo($far_birthday); ?>">
        </div>

        <div><label>Berechtiungsrolle:</label></div>
        <div>
            <select name="role">
                <option value="1">Admin</option>
            </select>
        </div>

        <div><label>Passwort:</label></div>
        <div>
            <input name="password" type="password">
        </div>

        <div>
            <?php
                // Dieser Nutzer existiert bereits.
                if($found_student) {
                    echo('<input type="submit" name="btnSave" value="Schüler speichern">');
                    echo('<input type="submit" name="btnDelete" value="Schüler löschen">');

                } else { // Neuer Nutzer.
                    echo('<input type="submit" name="btnCreate" value="Schüler anlegen">');
                }
            ?>

            <button formaction="/school/students/" formnovalidate>Zurück</button>
        </div>
    </form>
</div>
<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>