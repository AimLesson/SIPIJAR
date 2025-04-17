<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Daftar Kegiatan</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Ruangan</th>
                <th>Asal Bidang</th>
                <th>Tanggal</th>
                <th>Waktu</th>
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
                    <td>{{ $event->guest_count }}</td>
                    <td>{{ $event->is_approve ? 'Disetujui' : 'Belum Disetujui' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
