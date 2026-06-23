<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kontrol Poin - {{ $student->name }} - SMKN 2 Jakarta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 11pt;
            line-height: 1.4;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .kop-text {
            text-align: center;
            flex: 1;
        }

        .kop-text h2 {
            font-size: 14pt;
            margin: 0 0 5px 0;
            font-weight: 700;
            text-transform: uppercase;
        }

        .kop-text h1 {
            font-size: 17pt;
            margin: 0 0 5px 0;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            font-size: 9pt;
            margin: 0;
            line-height: 1.3;
        }

        .title-card {
            text-align: center;
            margin-bottom: 25px;
        }

        .title-card h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: underline;
        }

        /* Profile Block */
        .student-profile {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
            border: 1px solid #000;
            padding: 15px;
            border-radius: 6px;
        }

        .profile-group {
            display: flex;
            margin-bottom: 8px;
        }

        .profile-label {
            width: 140px;
            font-weight: 600;
        }

        .profile-value {
            flex: 1;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
            font-size: 9.5pt;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .center-align {
            text-align: center;
        }

        /* Signatures */
        .signature-block {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-col {
            text-align: center;
            width: 220px;
        }

        .signature-space {
            height: 70px;
        }

        .btn-print-box {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover {
            background: #1d4ed8;
        }

        @media print {
            .btn-print-box {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="btn-print-box">
    <button onclick="window.print()" class="btn-print">
        <i class="fa-solid fa-print"></i> Cetak Dokumen
    </button>
</div>

<div class="container">
    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="kop-text">
            <h2>Pemerintah Provinsi DKI Jakarta</h2>
            <h1>SMK Negeri 2 Jakarta</h1>
            <p>Bidang Keahlian: Teknologi Informasi, Bisnis dan Manajemen, Pariwisata</p>
            <p>Jl. Gajah Mada No.139, Jakarta Pusat | Telp: (021) 6341234 | Email: info@smkn2jkt.sch.id</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title-card">
        <h3>KARTU KONTROL POIN PELANGGARAN SISWA</h3>
    </div>

    <!-- Student Profiles -->
    <div class="student-profile">
        <div>
            <div class="profile-group">
                <span class="profile-label">Nama Siswa</span>
                <span class="profile-value">: <strong>{{ $student->name }}</strong></span>
            </div>
            <div class="profile-group">
                <span class="profile-label">NISN</span>
                <span class="profile-value">: {{ $student->nisn }}</span>
            </div>
            <div class="profile-group">
                <span class="profile-label">Kelas</span>
                <span class="profile-value">: {{ $student->kelas ? $student->kelas->class_name : '-' }}</span>
            </div>
        </div>
        <div>
            <div class="profile-group">
                <span class="profile-label">Thn Ajaran</span>
                <span class="profile-value">: {{ $student->tahun_ajaran }}</span>
            </div>
            <div class="profile-group">
                <span class="profile-label">Orang Tua / Wali</span>
                <span class="profile-value">: {{ $student->parent_name }}</span>
            </div>
            <div class="profile-group">
                <span class="profile-label">WhatsApp Ortu</span>
                <span class="profile-value">: {{ $student->parent_phone }}</span>
            </div>
            <div class="profile-group">
                <span class="profile-label"><strong>Poin Akumulasi</strong></span>
                <span class="profile-value">: <strong>{{ $student->current_points }} Poin</strong></span>
            </div>
        </div>
    </div>

    <!-- Violation Logs Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 110px;">Tanggal</th>
                <th style="width: 90px;">Poin</th>
                <th>Jenis Pelanggaran</th>
                <th style="width: 120px;">Dicatat Oleh</th>
                <th>Keterangan / Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                <tr>
                    <td class="center-align">{{ $index + 1 }}</td>
                    <td class="center-align">{{ date('d-m-Y', strtotime($log->date_occurred)) }}</td>
                    <td class="center-align" style="font-weight: bold; color: #d32f2f;">+{{ $log->points_added }} Poin</td>
                    <td>{{ $log->violation->violation_name }}</td>
                    <td>{{ $log->user ? $log->user->name : '-' }}</td>
                    <td>{{ $log->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center-align" style="padding: 20px; color: #666;">
                        Belum ada riwayat pelanggaran tercatat. Siswa memiliki rekam jejak yang baik.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Fields -->
    <div class="signature-block">
        <div class="signature-col">
            <p>Orang Tua / Wali Siswa</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">( {{ $student->parent_name }} )</p>
        </div>
        
        <div class="signature-col">
            <p>Wali Kelas</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">
                ( {{ $student->kelas && $student->kelas->homeroomTeacher ? $student->kelas->homeroomTeacher->name : '........................................' }} )
            </p>
        </div>

        <div class="signature-col">
            <p>Koordinator Guru BK</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">
                ( {{ auth()->user()->role === 'guru_bk' ? auth()->user()->name : '........................................' }} )
            </p>
        </div>
    </div>
</div>

<script>
    // Trigger print layout after load
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 300);
    };
</script>
</body>
</html>
