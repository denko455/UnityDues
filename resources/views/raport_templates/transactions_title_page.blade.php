<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Transaction Report</title>
    <style>
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page,
         {
            font-family: sans-serif;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }

        .report-body {
            text-align: center;
            margin-bottom: 20px;
            height: 100vh;
        }

        .report-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 180px;
        }

        .report-info {
            font-size: 10px;
            margin-bottom: 10px;
        }

        .header {
            position: absolute;
            top: 0;
        }

        .content,
        .header {
            text-align: center;
            /* padding: 10px; */
        }

        .footer {
            padding: 10px;
            text-align: center;
            position: absolute;
            bottom: 0;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="header">
            <h1 class="report-title">Izveštaj transakcija</h1>
        </div>

        <div class="content">
            <img style="padding-top: 200px" src="img/app/logo-small.png" width="250" />
        </div>

        <div class="footer" style="padding: 10px;text-align: center;position: absolute;bottom: 0;">
            <p class="report-info">Dokumenat je stvoren: <?php echo date("d.m.Y H:i:s"); ?></p>
        </div>
    </div>
</body>

</html>