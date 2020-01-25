<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $statusCode ?></title>
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, Verdana, sans-serif;
            background-color: #DEDEDE;
        }
        .message {
            color: #343434;
            font-size: 48px;
        }
    </style>
</head>
<body>
<div class="message">
    <span><?= $statusCode ?></span>
</div>
</body>
</html>