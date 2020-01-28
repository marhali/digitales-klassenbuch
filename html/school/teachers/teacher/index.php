<?php
    /**
     * ##################################################################
     * #            L E H R E R - E I N Z E L A N S I C H T             #
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
    $title = 'Digitales Klassenbuch | Lehrerverwaltung';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Lehrer > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    /**
     * Content
     */

    // Formular-Variablen
    $found_teacher = FALSE;

    $far_id;
    $far_username;
    $far_forename;
    $far_surname;
    $far_shortname;
    $far_email;
    $far_birthday;
    $far_role_id;
    $far_password;

    // Es wurde eine Lehrer_Id übergeben. -> Benutzer möchte Einzelansicht über diesen Lehrer.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_NUMBER_INT);
    if($input_id !== null) {

        // Bereite SQL-Query vor.
        $sql = 'SELECT l.Id, Benutzername, Vorname, Nachname, Kuerzel, Email, 
            Geburtsdatum, Rolle_Id FROM Lehrer l JOIN Nutzer n ON (l.Id = n.Id) 
            WHERE l.Id = ? LIMIT 1;';
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

            // Ein Lehrer mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->num_rows == 0) {
                
                echo('<p>Es wurde kein Lehrer mit der Id ' . $input_id . ' gefunden.</p>');
                echo('<a href="/school/teachers/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_teacher = TRUE;

                // Result-Parameter binden
                $stmt->bind_result($far_id, $far_username, $far_forename, $far_surname, 
                    $far_shortname, $far_email, $far_birthday, $far_role_id);
                
                $stmt->fetch();
            }
        }
    }
?>
<div class="content">
    <form action="process_teacher.php" method="POST" name="teacher-form">
        <div><h1>Lehrer</h1></div>

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

        <div><label>Kürzel:</label></div>
        <div>
            <input name="shortname" type="text" maxlength="8" required value="<?php echo($far_shortname); ?>">
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
                // Dieser Lehrer existiert bereits.
                if($found_teacher) {
                    echo('<input type="submit" name="btnSave" value="Lehrer speichern">');
                    echo('<input type="submit" name="btnDelete" value="Lehrer löschen">');

                } else { // Neuer Nutzer.
                    echo('<input type="submit" name="btnCreate" value="Lehrer anlegen">');
                }
            ?>

            <button formaction="/school/teachers/" formnovalidate>Zurück</button>
        </div>
    </form>
</div>
<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>