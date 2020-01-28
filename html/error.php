<?php
    $error = filter_input(INPUT_GET, 'err', $filter = FILTER_SANITIZE_STRING);

    //Es wurde keine Fehlernachricht übergeben.
    if($error === null) {
        $error = "Unbekannter Fehler!!!!";
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Digitales Klassenbuch | Fehler</title>
    </head>
    <body>
        <h1>Es ist ein Fehler aufgetreten:</h1>
        <p>
            <?php
                echo $error;
            ?>
        </p>
    </body>
</html>