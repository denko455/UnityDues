<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transaction Report</title>
    <style>
        body {
            font-family: 'Century Gothic', Arial, Helvetica, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 20px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .th,
        .td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .td-right {
            text-align: right;
        }
    </style>
</head>

<body>
<h1>Svrha placanja</h1>
    <table class="table">
        <thead>
            <tr bgcolor="#536878" color="#F5F5F5">
               <th class="th" style="width:50%"><b>Svrha placanja</b></th>
                <th class="th td-right"><b>Saldo</b></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payment_items_sum as $key=>$valueItems) { ?>
            <tr>                
                <td class="td">{{ $key }}</td>
                <td class="td td-right">
                    <?php 
                    if(isset($valueItems['EUR'])){
                        echo number_format($valueItems['EUR'], 2, ',', ' ')." EUR";
                    }
                    foreach ($valueItems as $currency => $value) {
                        if($currency !== "EUR"){
                            echo "<br>" . number_format($value, 2, ',', ' ')." ".$currency;
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php }
; ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="border-top: 1px solid #536878;"><b>Ukupno: </b></td>
                <td style="text-align:right;border-top: 1px solid #536878;">
                    <b>
                        <?php 
                         if(isset($total_sum['EUR'])){
                            echo number_format($total_sum['EUR'], 2, ',', ' ')." EUR";
                        }
                        foreach($total_sum as $currency => $value) {
                            if($currency !== "EUR"){
                                echo "<br>".number_format($value, 2, ',', ' ') . ' ' . $currency;
                            }
                        } ?>
                    </b>
                </td>
            </tr>
        </tfoot>
    </table>





    <h1>Tok novca</h1>
    <table class="table">
        <thead>
            <tr bgcolor="#536878" color="#F5F5F5">
                <th class="th"><span style="margin:5px"><b>Dokument</b></span></th>
                <th class="th"><b>Član</b></th>
                <th class="th"><b>Projekat</b></th>
                <th class="th"><b>Svrha plačanja</b></th>
                <th class="th"><b>Vrijednost</b></th>
                <th class="th"><b>Bilješke</b></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $value) { ?>
            <tr>
                <td class="td">{{ $value['document_number'] }} <br>
                    {{ date('d.m.Y', strtotime($value['document_date'])) }}</td>
                <td class="td">{{ $value['member']['first_name'] ?? '-' }} {{ $value['member']['last_name'] ?? '' }}</td>
                <td class="td">{{ $value['project']['name'] ?? '-' }}</td>
                <td class="td">{{ $value['payment_item']['name'] }}</td>
                <td class="td td-right" style="vertical-align: bottom;">
                    {{ number_format($value['value'], 2, ',', ' ') }}
                    {{ $value['currency'] }}
                </td>
                <td class="td"><i>{{ $value['remarks'] }}</i></td>
            </tr>
            <?php }
; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="border-top: 1px solid #536878;"><b>Ukupno: </b></td>
                <td colspan="1" style="text-align:right;border-top: 1px solid #536878;">
                    <b>
                    <?php 
                         if(isset($total_sum['EUR'])){
                            echo number_format($total_sum['EUR'], 2, ',', ' ')." EUR";
                        }
                        foreach($total_sum as $currency => $value) {
                            if($currency !== "EUR"){
                                echo "<br>";
                                echo number_format($value, 2, ',', ' ') . ' ' . $currency;
                            }
                        } ?>
                    </b>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>