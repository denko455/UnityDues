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
        <h1 class="display-3 mt-5 pt-5">Izveštaj transakcija</h1>
        <h2>Člana "{{$member->full_name}}"</h2>

        <div class="card-body">
            <img style="padding-top: 100px" src="img/app/logo-small.png" width="250" />
        </div>

        <div class="card-footer report-info" style="position:fixed;bottom:0">Dokumenat je stvoren: <?php echo date("d.m.Y H:i:s"); ?></div>
    </div>
    <hr/>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <h2>Detalji</h2>
    <table class="table mb-5 pb-5">
        <thead>
            <tr>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Lični broj</th>
                <th>Email</th>
                <th>Tel</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$member->first_name ?? '-'}}</td>           
                <td>{{$member->last_name ?? '-'}}</td>
                <td>{{$member->id_number ?? '-'}}</td>
                <td>{{$member->email ?? '-'}}</td>
                <td>{{$member->tel ?? '-'}}</td>
            </tr>
        </tbody>
    </table>
    <h2 class="display-5">Svrha plačanja</h2>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Svrha plačanja</th>
                <th class="text-end" scope="col">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payment_items_sum as $key => $valueItems) { ?>
            <tr>
                <td>{{ $key }}</td>
                <td class="text-end">
                    <?php 
                    if (isset($valueItems['EUR'])) {
                        echo "<div>" . number_format($valueItems['EUR'], 2, ',', ' ') . " EUR</div>";
                    }
                    foreach ($valueItems as $currency => $value) {
                        if ($currency !== "EUR") {
                            echo "<div>" . number_format($value, 2, ',', ' ') . " " . $currency. "</div>";
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php }  ?>
        </tbody>
        <tfoot>
            <tr>
                <td><b>Ukupno: </b></td>
                <td class="text-end">
                    <b>
                        <?php 
                         if (isset($total_sum['EUR'])) {
                            echo "<div>".number_format($total_sum['EUR'], 2, ',', ' ') . " EUR</div>";
                        }
                        foreach ($total_sum as $currency => $value) {
                            if ($currency !== "EUR") {
                                echo "<div>" . number_format($value, 2, ',', ' ') . ' ' . $currency."</div>";
                            }
                        } ?>
                    </b>
                </td>
            </tr>
        </tfoot>
    </table>
<hr/>
    <h2>Transakcije</h2>
    <table class="table">
        <thead>
            <tr>
                <th class="th">Dokument</th>
                <th class="th">Član</th>
                <th class="th">Projekat</th>
                <th class="th">Svrha plačanja</th>
                <th class="th text-end">Vrijednost</th>
                <th class="th">Bilješke</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $value) { ?>
            <tr>
                <td class="td">{{ $value['document_number'] }} <br>
                    {{ date('d.m.Y', strtotime($value['document_date'])) }}</td>
                <td class="td">{{ $value['member']['first_name'] ?? '-' }} {{ $value['member']['last_name'] ?? '' }}</td>
                <td class="td">{{ $value['project']['name'] ?? '-' }}</td>
                <td class="td">{{ $value['payment_item']['name'] }}</td>
                <td class="td text-end">
                    {{ number_format($value['value'], 2, ',', ' ') }}
                    {{ $value['currency'] }}
                </td>
                <td class="td"><i><?php 
                if($value["status"] === "draft"){
                    echo "<div>Uplata nije potvrđena.</div>";
                } else if($value["status"] === "approved"){
                    echo "<div>Uplata je potvrđena.</div>";
                }
                echo "<div>".$value['remarks']."<div>"; 
                ?></i></td>
            </tr>
            <?php }
            ; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><b>Ukupno: </b></td>
                <td class="text-end">
                    <b>
                    <?php 
                         if(isset($total_sum['EUR'])){
                            echo "<div>".number_format($total_sum['EUR'], 2, ',', ' ')." EUR</div>";
                        }
                        foreach($total_sum as $currency => $value) {
                            if($currency !== "EUR"){
                                echo "<div>".number_format($value, 2, ',', ' ') . ' ' . $currency ."</div>";
                            }
                        } ?>
                    </b>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>