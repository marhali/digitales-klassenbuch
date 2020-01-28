<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo($title); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="/style/main.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="/style/nav.css" />

    <?php
        // Binde alle übergebene Stylesheets ein.
        foreach($styles as $style) {
            echo($style);
        }
    ?>

    <script src="/script/jquery.min.js"></script>
    <script src="/script/nav.js"></script>

    <?php
        // Binde alle übergebenen Skripte ein.
        foreach($scripts as $script) {
            echo($script);
        }
    ?>
</head>
<body>
