<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $exceptionClass ?></title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            font-family: Arial, Verdana, sans-serif;
        }
        .wrapper {

        }
        .row {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        .status {
            background-color: rgb(217, 67, 80);
            display: inline-block;
            padding: 8px;
            color: #FFFFFF;
            border-radius: 3px;
            font-size: 32px;
        }
        .exception {
            padding: 0 16px;
            color: #242424;
        }
        .exception > h1 {
            padding: 0;
            margin: 0;
            text-decoration: underline;
        }
        .message {
            width: 100%;
            margin: 16px 0 0;
            border-radius: 3px;
            padding: 8px;
            color: #FFFFFF;
            font-size: 24px;
            background-color: rgb(217, 67, 80);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: rgb(217, 67, 80);
            color: #FFFFFF;
        }
        tr:nth-of-type(2n) {
            background-color: rgb(252, 172, 179);
        }
        td, th {
            padding: 8px;
            font-size: 16px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="row">
        <div class="status"><span><?= $statusCode ?></span></div>
        <div class="exception"><h1><?= $exceptionClass ?></h1></div>
    </div>
    <div class="row">
        <div class="message"><span><?= $message ?></span></div>
    </div>
    <div>
        <table>
            <?php foreach ($trace as $traceItem): ?>
                <tr>
                    <td><?= $traceItem["file"]; ?> <strong>(line <?= $traceItem["line"]; ?>)</strong></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>