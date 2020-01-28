<?php
    /**
     * ##################################################################
     * #    E I N T R Ä G E - V E R A R B E I T U N G S H A N D L E R   #
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

    // Importiere DB-Hilfsfunktionen
    include_once(INC_ROOT . 'db-helper.php');

    // !!! Geschützter Bereich !!!

    // Protokolliere
    write_log($mysqli, 'INFO', 'Hat einen Klassenbuch-Eintrag hinzugefügt.');

    // Formuluar ist fehlerhaft.
    if(!(isset($_POST['btnSave']))) {
        echo('Invalid request. No form given.');
        exit();
    }

    // Parameter aus Formular auslesen.
    $class = $_POST['class'];
    $date = $_POST['date'];
    $hour = $_POST['hour'];
    $subject_should = $_POST['subject_should'];
    $subject_is = $_POST['subject_is'];
    $teacher = $_POST['teacher'];
    $topic = $_POST['topic'];
    $homework = $_POST['homework'];
    $signature = $_POST['signature'];

    // Eintrag abspeichern.

    $sql = 'INSERT INTO Klassenbuch_' . mysqli_real_escape_string($mysqli, $class) . '_Tag (
        Datum, Stunde, Fach_Id_Soll, Fach_Id_Ist, Lehrer_Id, Thema, Hausaufgabe, Best_Lehrer_Id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?);';
    $stmt = $mysqli->prepare($sql);

    // Fehler im SQL-Syntax.
    if($stmt === FALSE) {
        echo('<p>SQL-Syntax konnte nicht erstellt werden.</p>');
        echo('<a href="/overview/">Zurück zur Übersichtsseite</a>');
        print_r(mysqli_error($mysqli));
        exit();
    }

    $stmt->bind_param('siiiissi', $date, $hour, $subject_should, $subject_is, $teacher, 
        $topic, $homework, $signature);

    // Eintrag konnte nicht vorgenommen werden. Eingaben überprüfen
    if($stmt->execute() === FALSE) {
        $_SESSION['process_status'] = 'Der Eintrag konnte nicht vorgenommen werden! Bitte erneut versuchen.';
        header('Location: /overview/');
        exit();

    } else {

        // Eintrag war erfolgreich.
        $_SESSION['process_status'] = 'Der Eintrag wurde hinzugefügt.';
        header('Location: /overview/entries/?id=' . $class);
        exit();
    }
?>