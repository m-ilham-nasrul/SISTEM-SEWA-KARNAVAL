<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Pembayaran</title>

    <style>
        body {
            font-family: "Inter", Arial, sans-serif;
            font-size: 13px;
            background: #f7f7f7;
        }

        .receipt {
            width: 360px;
            background: #fff;
            margin: 20px auto;
            padding: 18px 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, .1);
        }

        h2 {
            font-size: 18px;
            margin: 0;
            margin-bottom: 4px;
            text-align: center;
            text-transform: uppercase;
        }

        small {
            display: block;
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }

        .line {
            border-top: 1px dashed #aaa;
            margin: 10px 0;
        }

        table {
            width: 100%;
        }

        td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            width: 40%;
            color: #444;
        }

        .value {
            width: 60%;
            font-weight: 500;
        }

        .total {
            font-size: 16px;
            font-weight: 700;
            color: #222;
            padding-top: 6px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #555;
        }

        .footer strong {
            display: block;
            margin-top: 5px;
            font-size: 13px;
        }

        /* Print */
        @media print {
            body {
                background: white;
            }

            .receipt {
                box-shadow: none;
                border: none;
                width: 100%;
                border-radius: 0;
            }

            .no-print {
                display: none;
            }
        }

        /* Button */
        .print-btn {
            padding: 8px 15px;
            background: #2d7cff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .link {
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
            color: #555;
        }
    </style>

</head>

<body>

    <div class="receipt">
        <h2>Nota Sewa</h2>
        <small>Sewa Karnaval</small>

        <div class="line"></div>

        <table>
            <tr>
                <td class="label">Tanggal</td>
                <td class="value">{{ date('d F Y', strtotime($sewa->tanggal_sewa)) }}</td>
            </tr>

            <tr>
                <td class="label">Pelanggan</td>
                <td class="value">
                    {{ optional(optional($sewa->penyewa)->user)->name ?? 'Penyewa dihapus' }}
                </td>
            </tr>

            <tr>
                <td class="label">Kostum</td>
                <td class="value">
                    @if ($sewa->kostum_list->isNotEmpty())
                        @foreach ($sewa->kostum_list as $kostum)
                            <div>{{ $kostum->nama_kostum ?? 'Kostum telah dihapus' }}</div>
                        @endforeach
                    @else
                        <small>Data kostum telah dihapus!</small>
                    @endif
                </td>
            </tr>

            <!-- === METODE PEMBAYARAN === -->
            <tr>
                <td class="label">Metode Pembayaran</td>
                <td class="value">
                    {{ $sewa->metode_pembayaran ? ucfirst($sewa->metode_pembayaran) : '-' }}
                </td>
            </tr>

            @if ($sewa->metode_pembayaran == 'ewallet')
                <tr>
                    <td class="label">E-Wallet</td>
                    <td class="value">{{ $sewa->nama_ewallet ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="label">No. E-Wallet</td>
                    <td class="value">{{ $sewa->nomor_ewallet ?? '-' }}</td>
                </tr>
            @endif
            <!-- === END METODE PEMBAYARAN === -->

            <tr>
                <td class="label">Lama Sewa</td>
                <td class="value">{{ $sewa->durasi }} hari</td>
            </tr>

            <tr>
                <td class="label">Harga Sewa</td>
                <td class="value">Rp {{ number_format($sewa->total_biaya) }}</td>
            </tr>

            @if ($sewa->denda > 0)
                <tr>
                    <td class="label">Denda</td>
                    <td class="value">Rp {{ number_format($sewa->denda) }}</td>
                </tr>
            @endif

            <tr>
                <td class="label total">Total Bayar</td>
                <td class="value total">
                    Rp {{ number_format($sewa->total_biaya + $sewa->denda) }}
                </td>
            </tr>
        </table>

        <div class="footer">
            Sewa Karnaval {{ date('d F Y') }}
            <strong>
                {{ Auth::check() ? Auth::user()->name : 'Petugas' }}
            </strong>
        </div>

        <div class="no-print" style="text-align:center;">
            <button class="print-btn" onclick="window.print()">🖨 Cetak Nota</button>
            <br>
            <a class="link" href="{{ url()->previous() }}">⬅ Kembali</a>
        </div>

</body>

</html>
