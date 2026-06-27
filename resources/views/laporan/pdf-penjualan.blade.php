<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>

    <style>

        body{
            font-family: sans-serif;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        th,td{
            border:1px solid #000;
            padding:8px;
        }

    </style>

</head>
<body>

    <h2>
        LAPORAN PENJUALAN
    </h2>

    <table>

        <thead>

            <tr>

                <th>No Nota</th>
                <th>Tanggal</th>
                <th>Channel</th>
                <th>Total</th>

            </tr>

        </thead>

        <tbody>

            @foreach($penjualan as $item)

            <tr>

                <td>{{ $item->no_nota }}</td>

                <td>{{ $item->tanggal_penjualan }}</td>

                <td>{{ $item->channel }}</td>

                <td>
                    Rp {{ number_format($item->total,0,',','.') }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    <h3>
        Total Penjualan :
        Rp {{ number_format($total,0,',','.') }}
    </h3>

</body>
</html>