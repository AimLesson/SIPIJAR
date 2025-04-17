<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-table td {
            vertical-align: top;
            border: none !important;
            font-size: 14px;
        }

        .logo {
            width: 120px;
        }

        .instansi-info {
            text-align: center;
            font-size: 11px;
        }

        .title {
            text-align: center;
            margin: 10px 0 20px;
            font-weight: bold;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f0f0f0;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
            text-align: right;
        }
    </style>
</head>
<body>
        {{-- Header: Logo + Instansi Info --}}
        <table class="header-table">
        <tr>
            <td class="logo">
                <img src="file://{{ public_path('logopdf.jpg') }}" alt="Logo" style="width: 100px;">
            </td>
            <td class="instansi-info">
                <strong>PEMERINTAH KABUPATEN BANYUMAS</strong><br>
                <strong>BADAN PERENCANAAN PEMBANGUNAN PENELITIAN DAN PENGEMBANGAN DAERAH (BAPPEDALITBANG)</strong><br>
                Jalan Prof. Dr. Suharso No. 45 Purwokerto, Banyumas, Jawa Tengah 53114<br>
                Telepon (0281) 632548, 632116 | Faks (0281) 640715<br>
                Laman: http://bappedalitbang.banyumaskab.go.id<br>
                Email: bappedalitbang@banyumaskab.go.id
            </td>
        </tr>
    </table>
    {{-- Title --}}
    <div class="title">
        LAPORAN PEMINJAMAN RUANGAN<br>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Ruangan</th>
                <th>Asal Bidang</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Tujuan Kegiatan</th>
                <th>Jumlah Tamu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->name }}</td>
                    <td>{{ $event->room->name ?? '-' }}</td>
                    <td>{{ $event->asal_bidang }}</td>
                    <td>{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</td>
                    <td>{{ $event->start_time }} - {{ $event->finish_time }}</td>
                    <td>{{ $event->info }}</td>
                    <td>{{ $event->guest_count }}</td>
                    <td>{{ $event->is_approve ? 'Disetujui' : 'Belum Disetujui' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
