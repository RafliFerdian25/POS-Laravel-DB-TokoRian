<?php
// function format_uang($angka)
// {
//     return number_format($angka, 0, ',', '.');
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Kecil</title>

    <?php
    $style = '
        <style>
            * {
                font-family: "consolas", sans-serif;
            }
            p {
                display: block;
                margin: 3px;
                font-size: 10pt;
            }
            table td {
                font-size: 10pt;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
    
            @media print {
                @page {
                    margin: 0mm;
        ';
    ?>
    <?php
    $style .= !empty($_COOKIE['innerHeight']) ? $_COOKIE['innerHeight'] . 'mm; }' : '}';
    ?>
    <?php
    $style .= '
                html, body {
                    width: 95%;
                }
                .btn-print {
                    display: none;
                }
            }
        </style>
        ';
    ?>

    {!! $style !!}
</head>

<body onload="window.print()">
    <button class="btn-print" style="position: absolute; right: 1rem; top: rem;" onclick="window.print()">Print</button>
    <div class="text-center">
        <h4 style="margin-bottom: 5px;">{{ strtoupper($setting->name) }}</h4>
        <p>{{ strtoupper($setting->address) }}</p>
    </div>
    <br>
    <div>
        <p style="float: left;">{{ date('d-m-Y') }}</p>
    </div>
    <div class="clear-both" style="clear: both;"></div>
    <p>No: KSR/{{ $penjualan->id }}</p>
    <p class="text-center">===================================</p>

    <br>
    <table width="100%" style="border: 0;">
        @foreach ($detail as $item)
            <tr>
                <td colspan="3">{{ $item->product->name }}</td>
            </tr>
            <tr>
                <td>{{ $item->qty }} x {{ format_uang($item->product['selling_price']) }}</td>
                <td></td>
                <td class="text-right">{{ format_uang($item->qty * $item->product['selling_price']) }}</td>
            </tr>
        @endforeach
    </table>
    <p class="text-center">-----------------------------------</p>

    <table width="100%" style="border: 0;">
        <tr>
            <td>Total Harga:</td>
            <td class="text-right">{{ format_uang($penjualan->total_price) }}</td>
        </tr>
        <tr>
            <td>Total Item:</td>
            <td class="text-right">{{ format_uang($penjualan->total_item) }}</td>
        </tr>
        <tr>
            <td>Diskon:</td>
            <td class="text-right">{{ format_uang($penjualan->discount) }}</td>
        </tr>
        <tr>
            <td>Total Bayar:</td>
            <td class="text-right">{{ format_uang($penjualan->total_price - $penjualan->discount) }}</td>
        </tr>
        <tr>
            <td>Diterima:</td>
            <td class="text-right">{{ format_uang($penjualan->pay) }}</td>
        </tr>
        <tr>
            <td>Kembali:</td>
            <td class="text-right">
                {{ format_uang($penjualan->pay - ($penjualan->total_price - $penjualan->discount)) }}</td>
        </tr>
    </table>

    <p class="text-center">===================================</p>
    <p class="text-center mb-3">-- TERIMA KASIH --</p>
    <p class="text-center mb-3"></p>
    <p class="text-center mb-3"></p>
    

    <script>
        let body = document.body;
        let html = document.documentElement;
        let height = Math.max(
            body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight
        );

        document.cookie = "innerHeight=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "innerHeight=" + ((height + 50) * 0.264583);
    </script>
</body>

</html>
