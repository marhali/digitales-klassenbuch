<?php
    /**
     * ##################################################################
     * #           N U T Z E R - E I N Z E L A N S I C H T              #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 25.01.2019                         #
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
    $title = 'Digitales Klassenbuch | Nutzerverwaltung';
    $styles = array();
    $scripts = array();
    $section = 'Verwaltung > Nutzer > Einzelansicht';

    // Binde globalen HTML-Kopf ein.
    include(INC_ROOT . 'site/global-head.php');

    //Binde globale Kopfzeile ein.
    include(INC_ROOT . 'site/global-header.php');

    // Formular-Variablen
    $found_user = FALSE;

    $far_id;
    $far_username;
    $far_forename;
    $far_surname;
    $far_email;
    $far_birthday;
    $far_role_id;
    $far_password;

    // Es wurde eine Nutzer_Id übergeben. -> Benutzer möchte Einzelansicht über diesen Nutzer.
    $input_id = filter_input(INPUT_GET, 'id', $filter = FILTER_SANITIZE_NUMBER_INT);
    if($input_id !== null) {

        // Bereite SQL-Query vor.
        $sql = 'SELECT Id, Benutzername, Vorname, Nachname, Email, Geburtsdatum, Rolle_Id 
            FROM Nutzer WHERE Id = ? LIMIT 1;';
        $stmt = $mysqli->prepare($sql);

        // Statement konnte nicht erstellt werden. Möglicherweise DB-Verbindungsfehler.
        if($stmt === FALSE) {
            echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
            echo('<a href="/management/users/">Zurück zur Übersichtsseite</a>');
            exit();

        } else {
            // Parameter setzen und Query ausführen.
            $stmt->bind_param('i', $input_id);
            $stmt->execute();
            $stmt->store_result();

            // Ein Benutzer mit der Id $input_id konnte nicht gefunden werden.
            if($stmt->num_rows == 0) {
                
                echo('<p>Es wurde kein Nutzer mit der Id ' . $input_id . ' gefunden.</p>');
                echo('<a href="/management/users/">Zurück zur Übersichtsseite</a>');
                exit();

            } else {
                $found_user = TRUE;

                // Result-Parameter binden
                $stmt->bind_result($far_id, $far_username, $far_forename, $far_surname, 
                    $far_email, $far_birthday, $far_role_id);
                
                $stmt->fetch();
            }
        }
    }
?>
<div class="content">
    <form action="process_user.php" method="POST" name="user-form">
        <div><h1>Nutzer</h1></div>
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
                if($found_user) {
                    echo('<input type="submit" name="btnSave" value="Nutzer speichern">');
                    echo('<input type="submit" name="btnDelete" value="Nutzer löschen">');

                } else { // Neuer Nutzer.
                    echo('<input type="submit" name="btnCreate" value="Nutzer anlegen">');
                }
            ?>

            <button formaction="/management/users/" formnovalidate>Zurück</button>
        </div>
    </form>
</div>
<?php
    //Binde globale Fußzeile ein.
    include(INC_ROOT . 'site/global-footer.php');
?>