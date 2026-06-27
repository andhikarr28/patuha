<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembelian</title>

    <style>

        body{
            font-family:sans-serif;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,td{
            border:1px solid #000;
            padding:8px;
        }

    </style>

</head>
<body>

<h2>
    LAPORAN PEMBELIAN
</h2>

<table>

    <thead>

        <tr>

            <th>Faktur</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Netto</th>

        </tr>

    </thead>

    <tbody>

        @foreach($pembelian as $item)

        <tr>

            <td>{{ $item->no_faktur }}</td>

            <td>{{ $item->tanggal_pembelian }}</td>

            <td>{{ $item->supplier->nama_supplier }}</td>

            <td>
                Rp {{ number_format($item->total_netto,0,',','.') }}
            </td>

        </tr>

        @endforeach

    </tbody>

</table>

<h3>
    Total Pembelian :
    Rp {{ number_format($total,0,',','.') }}
</h3>

</body>
</html>