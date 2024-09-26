<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Izveštaj transakcija</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        * {
            /* box-sizing: border-box;
            -moz-box-sizing: border-box; */
            font-family: Roboto, Dejavu Sans, sans-serif;
        }

        hr {
            page-break-after: always;
            border: 0;
        }

        .report-body {
            text-align: center;
            margin-bottom: 20px;
            height: 100vh;
        }

        .report-info {
            font-size: 10px;
            margin-bottom: 10px;
            position: fixed;
            bottom: 0;
        }

        .sub-title{
            font-size: x-large;
            padding: 0 0 5 5;
        }
        td, th {
            vertical-align: middle;
            
        }
        td {
            padding: 5px 5px;
            font-size: x-small;
            border-bottom: 1px solid;
            vertical-align: middle;
        }
        th {
            font-weight: normal;
            padding: 7px 5px;
            font-size: smaller;
            background-color: rgb(22, 129, 76);
            color: white;            
        }
        h1,h2 {
            font-weight: normal !important;
        }
        
        #header,
        #footer {
            position: fixed;
            left: 0;
            right: 0;
            color: #aaa;
            font-size: 0.9em;
        }

        #header {
            top: 0;
            border-bottom: 0.1pt solid #aaa;
        }

        #footer {
            bottom: 0;
            border-top: 0.1pt solid #aaa;
        }

        .page-number {
            text-align: center;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>
    
    <div class="card text-center">
        <h1 class="display-3 mt-5 pt-5">Lista Članova</h1>

        <div class="card-body">
            <img style="padding-top: 130px" src="img/app/logo-small.png" width="250" />
        </div>

        <div class="card-footer report-info" style="position:fixed;bottom:0">Dokumenat je stvoren: <?php echo date("d.m.Y H:i:s"); ?></div>
    </div>
    <hr/>
    <?php if($col->residence->name): ?>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <h2 class="display-5">Prebivalište</h2>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Prebivalište</th>
                <th class="text-end" scope="col">Broj članova</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($residences as $valueItems) { ?>
            <tr>
                <td>{{ $valueItems->name }}</td>
                <td class="text-end">
                    <div> {{ $valueItems->residence_count }}</div>
                </td>
            </tr>
            <?php }  ?>
        </tbody>
        <tfoot>
            <tr>
                <td><b>Ukupno: </b></td>
                <td class="text-end">
                    <b>
                        {{ array_sum(array_column($residences->toArray(), "residence_count")) }}
                    </b>
                </td>
            </tr>
        </tfoot>
    </table>
<hr/>
<?php endif; ?>
    <h2>Lista Članova</h2>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <?php if($col->full_name): ?><th>Ime i Prezime</th> <?php endif; ?>
                <?php if($col->id_number): ?><th>Lični broj</th> <?php endif; ?>
                <?php if($col->residence->name): ?><th>Prebivalište</th> <?php endif; ?>
                <?php if($col->email): ?><th>Email</th> <?php endif; ?>
                <?php if($col->tel): ?><th>Tel</th> <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $key=>$value) : ?>
            <tr>
                <td>{{ $key + 1 }}.</td>
                <?php if($col->full_name): ?><td>{{ $value->full_name }}</td><?php endif; ?>
                <?php if($col->id_number): ?><td>{{ $value->id_number }}</td><?php endif; ?>
                <?php if($col->residence->name): ?><td>{{ $value->residence->name }}</td><?php endif; ?>
                <?php if($col->email): ?><td>{{ $value->email }}</td><?php endif; ?>
                <?php if($col->tel): ?><td>{{ $value->tel }}</td><?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>