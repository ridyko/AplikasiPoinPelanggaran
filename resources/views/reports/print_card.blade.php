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
            border-bottom: 2.5px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 25px;
            font-family: Arial, Helvetica, sans-serif !important;
            color: #000000;
        }

        .kop-logo {
            width: 85px;
            height: auto;
            margin-right: 15px;
            filter: grayscale(1) contrast(1.1);
        }

        .kop-text {
            flex: 1;
            text-align: center;
            position: relative;
            padding-right: 85px; /* balances the logo margin on the left for perfect centering */
        }

        .kop-provinsi {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .kop-sekolah {
            font-size: 15pt;
            font-weight: bold;
            margin: 2px 0;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .kop-detail {
            font-size: 9.5pt;
            margin: 1px 0;
            line-height: 1.3;
        }

        .kop-kota {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }

        .kop-kodepos {
            font-size: 9.5pt;
            text-align: right;
            margin-top: 4px;
            margin-right: -85px; /* aligns to the edge of the container */
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
        <img class="kop-logo" src="{{ asset('images/jakarta-logo.png') }}" alt="Logo DKI Jakarta">
        <div class="kop-text">
            <div class="kop-provinsi">PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA</div>
            <div class="kop-sekolah">SEKOLAH MENENGAH KEJURUSAN NEGERI 2</div>
            <div class="kop-detail">Bidang Keahlian : Bisnis, Manajemen dan Teknik Informatika</div>
            <div class="kop-detail">Jalan Batu No. 3 Gambir, Telp. 3846219, 3520860 Fax 3520860</div>
            <div class="kop-detail">Website:http://www.smkn2jkt.sch.id Email:humas@smkn2jkt.sch.id</div>
            <div class="kop-kota">JAKARTA</div>
            <div class="kop-kodepos">Kode Pos : 10110</div>
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
