<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" media="screen" href="/style/main.css" />
    <title>Digitales Klassenbuch | Anmeldung</title>
</head>

<body>
    <div class="content">
        <div>
            <h1>Digitales Klassenbuch - Anmeldung</h1>
        </div>
        <form action="process_auth.php" method="POST" name="form_login">
            <div><label>Benutzername:</label></div>
            <div><input type="text" name="username" required></div>
            <div><label>Passwort:</label></div>
            <div><input type="password" name="password" id="password" required></div>
            <div><input type="submit" value="Login"></div>
        </form>
        <?php
        // Es kamm zu Fehlern bei der vorherigen Anmeldung
        if(isset($_GET['error'])) {

            switch ($_GET['error']) {
                case 'credentials':
                    echo('<div><h3>Fehlerhafte Anmeldedaten!</h3></div>');
                    break;
                case 'auth':
                    echo('<div><h3>Bitte melden Sie sich an!</h3></div>');
                    break;
                default:
                    echo('<div><h3>Unknown error.</h3></div>');
                    break;
            }
        }
    ?>
        <div>
            <p>Diese Webseite benutzt Cookies zur optimalen Darstellung der Dienste.</p>
            <p>Sollten Sie damit nicht einverstanden sein, verlassen Sie bitte diese Webseite!</p>
            <p>Haben Sie Fragen bezüglich des Datenschutzes, so wenden Sie sich bitte an den Systemadministrator.</p>
        </div>
    </div>
</body>

</html>