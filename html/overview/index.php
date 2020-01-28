<?php
    /**
     * ##################################################################
     * #       D Y N A M I S C H E  Ü B E R S I C H T S S E I T E       #
     * #                    Digitales Klassenbuch                       #
     * #                      Datum: 24.01.2019                         #
     * #                                                                #
     * #                 Developed by Marcel Haßlinger                  #
     * ##################################################################
     */

    // Binde globale Umgebungsvariablen ein.
    include_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/globals.php');

    // Lade Sicherheitsskript und lasse nur eingeloggte Benutzer auf diese Webseite zu.
    include_once(INC_ROOT . 'site-security.php');

    // !!! Geschützter Bereich !!!

    // Importiere unsere Hilfsklasse für DB-Funktionen
    include_once(INC_ROOT . 'db-helper.php');

    /*
     * Dynamische Variablen der Seite festlegen.
     */
    $title = 'Digitales Klassenbuch | Übersicht';
    $styles = array();
    $scripts = array();
    $section = 'Übersicht';

    // Binde globalen HTML-Kopf ein.
    include('../../includes/site/global-head.php');

    //Binde globale Kopfzeile ein.
    include('../../includes/site/global-header.php');

    //print_r(get_teacher($mysqli));

    // Statusinformation wurde gesetzt.
    if(isset($_SESSION['process_status'])) {
        echo('<p>' . $_SESSION['process_status'] . '</p>');
        unset($_SESSION['process_status']);
    }
?>

    <div class="content">
        <div class="align-right">
            <form action="entries/" method="GET" name="list-entries-form">
                <h1>Klassenbuch einer Klasse aufrufen</h1>
                <div><label>Klasse:</label></div>
                <div>
                    <select name="id" required>
                        <?php
                            foreach(get_classes($mysqli) as $row) {
                                foreach($row as $id => $shortname) {
                                    echo('<option value="' . $shortname . '">' . 
                                        $shortname . '</option>');
                                }
                            }
                        ?>

                    </select>
                </div>
                <div><input type="submit" value="Einträge anzeigen"></div>
            </form>
        </div>

        <div class="align-left">
            <form action="add_entry.php" method="POST" name="add-entry-form">
                <h1>Einen neuen Eintrag verfassen</h1>
                <div><label>Klasse:</label></div>
                <div>
                    <select name="class" required>
                        <?php
                            foreach(get_classes($mysqli) as $row) {
                                foreach($row as $id => $shortname) {
                                    echo('<option value="' . $shortname . '">' . 
                                            $shortname . '</option>');
                                }
                            }
                        ?>

                    </select>
                </div>
                <div><label>Datum:</label></div>
                <div><input type="date" name="date" required></div>
                <div><label>Stunde:</label></div>
                <div><input type="number" name="hour" required></div>
                <div><label>Fach Soll:</label></div>
                <div>
                    <select name="subject_should" required>
                        <?php
                            foreach(get_subjects($mysqli) as $row) {
                                foreach($row as $id => $shortname) {
                                    echo('<option value="' . $id . '">' . 
                                        $shortname . '</option>');
                                }
                            }
                        ?>

                    </select>
                </div>
                <div><label>Fach Ist:</label></div>
                <div>
                    <select name="subject_is" required>
                        <?php
                            foreach(get_subjects($mysqli) as $row) {
                                foreach($row as $id => $shortname) {
                                    echo('<option value="' . $id . '">' . 
                                        $shortname . '</option>');
                                }
                            }
                        ?>
    
                    </select>
                </div>
                <div><label>Lehrer:</label></div>
                <div>
                    <select name="teacher" required>
                        <?php
                            foreach(get_teacher($mysqli) as $row) {
                                foreach($row as $id => $value) {
                                    echo('<option value="' . $id . '">' . $value['Kuerzel'] . ' - ' . 
                                        $value['Vorname'] . ' ' . $value['Nachname'] . '</option>');
                                }
                            }
                        ?>
    
                    </select>
                </div>
                <div><label>Thema:</label></div>
                <div><input type="text" name="topic" required maxlength="255"></div>
                <div><label>Hausaufgabe:</label></div>
                <div><input type="text" name="homework" required maxlength="255"></div>
                <div><label>Ihre Unterschrift</label></div>
                <div>
                    <input type="text" name="signature" required readonly 
                    value="<?php echo(get_user($mysqli)['Id']); ?>">
                </div>
                <div><input type="submit" name="btnSave" value="Eintrag speichern"></div>
            </form>
        </div>
    </div>

    <?php
    //Binde globale Fußzeile ein.
    include('../../includes/site/global-footer.php');
?>