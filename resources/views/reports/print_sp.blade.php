<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Peringatan {{ $spLevel }} - {{ $student->name }} - SMKN 2 Jakarta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 30px;
            font-size: 12pt;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3.5px solid #000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .kop-text {
            text-align: center;
            flex: 1;
        }

        .kop-text h2 {
            font-size: 13pt;
            margin: 0 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text h1 {
            font-size: 16pt;
            margin: 0 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            font-size: 9.5pt;
            margin: 0;
            line-height: 1.3;
        }

        /* Document Title */
        .title-sp {
            text-align: center;
            margin-bottom: 30px;
        }

        .title-sp h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .title-sp p {
            margin: 5px 0 0 0;
            font-size: 11pt;
        }

        /* Content spacing */
        p.main-paragraph {
            text-indent: 40px;
            text-align: justify;
            margin-bottom: 15px;
        }

        /* Bio Table */
        .bio-table {
            margin: 20px auto 20px 40px;
            border-collapse: collapse;
            width: 80%;
        }

        .bio-table td {
            border: none;
            padding: 5px 10px;
            font-size: 12pt;
        }

        .bio-label {
            width: 180px;
            font-weight: bold;
        }

        /* Signatures block */
        .signature-block {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
        }

        .signature-col {
            text-align: center;
            width: 220px;
        }

        .signature-space {
            height: 75px;
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
        <i class="fa-solid fa-print"></i> Cetak Surat
    </button>
</div>

<div class="container">
    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="kop-text">
            <h2>Pemerintah Provinsi DKI Jakarta</h2>
            <h1 style="font-size: 18pt;">SMK Negeri 2 Jakarta</h1>
            <p>Bidang Keahlian: Teknologi Informasi, Bisnis dan Manajemen, Pariwisata</p>
            <p>Jl. Gajah Mada No.139, Jakarta Pusat | Telp: (021) 6341234 | Email: info@smkn2jkt.sch.id</p>
        </div>
    </div>

    <!-- Letter title based on SP Level -->
    <div class="title-sp">
        @if($spLevel == 1)
            <h3>SURAT PERINGATAN KESATU (SP 1)</h3>
        @elseif($spLevel == 2)
            <h3>SURAT PERINGATAN KEDUA (SP 2)</h3>
        @elseif($spLevel == 3)
            <h3>SURAT PERINGATAN KETIGA (SP 3 / DROP OUT)</h3>
        @endif
        <p>Nomor: 421.5 / SP-{{ $spLevel }} / SMKN2 / {{ date('Y') }}</p>
    </div>

    <p class="main-paragraph">
        Sehubungan dengan dilakukannya evaluasi terhadap tata tertib dan tingkat kedisiplinan siswa di lingkungan SMK Negeri 2 Jakarta, dengan ini kami sampaikan Surat Peringatan ke-{{ $spLevel }} kepada siswa di bawah ini:
    </p>

    <!-- Bio Table -->
    <table class="bio-table">
        <tr>
            <td class="bio-label">Nama Siswa</td>
            <td>: {{ $student->name }}</td>
        </tr>
        <tr>
            <td class="bio-label">NISN</td>
            <td>: {{ $student->nisn }}</td>
        </tr>
        <tr>
            <td class="bio-label">Kelas</td>
            <td>: {{ $student->kelas ? $student->kelas->class_name : '-' }}</td>
        </tr>
        <tr>
            <td class="bio-label">Tahun Ajaran</td>
            <td>: {{ $student->tahun_ajaran }}</td>
        </tr>
        <tr>
            <td class="bio-label">Jumlah Poin Pelanggaran</td>
            <td>: <strong>{{ $student->current_points }} Poin</strong></td>
        </tr>
    </table>

    <p class="main-paragraph">
        @if($spLevel == 1)
            Surat Peringatan Pertama (SP 1) ini diberikan karena akumulasi poin pelanggaran siswa yang bersangkutan telah mencapai batas minimum pembinaan tertulis (50 Poin). Kami mengharapkan kerja sama dari Orang Tua/Wali Siswa untuk segera datang ke sekolah menemui Guru BK guna berkoordinasi dan menandatangani pakta komitmen pembinaan siswa.
        @elseif($spLevel == 2)
            Surat Peringatan Kedua (SP 2) ini diberikan karena akumulasi poin pelanggaran siswa telah mencapai ambang batas 75 Poin. Sebagai konsekuensinya, siswa dikenai sanksi berupa <strong>skorsing akademik selama 3 (tiga) hari kerja</strong> mulai tanggal {{ date('d-m-Y', strtotime('+1 day')) }} s/d {{ date('d-m-Y', strtotime('+3 days')) }}. Selama masa skorsing, siswa wajib menyelesaikan tugas-tugas di rumah di bawah pengawasan orang tua.
        @elseif($spLevel == 3)
            Surat Peringatan Ketiga (SP 3) ini merupakan keputusan akhir dikarenakan akumulasi poin pelanggaran siswa telah mencapai batas maksimal toleransi kelayakan sekolah (yaitu &ge; 100 Poin). Dengan sangat menyesal, kami menyatakan bahwa siswa tersebut <strong>dikembalikan hak pendidikannya kepada Orang Tua/Wali Siswa (Dikeluarkan / Drop Out)</strong> terhitung sejak surat ini dikeluarkan.
        @endif
    </p>

    <p class="main-paragraph">
        Demikian Surat Peringatan ini kami sampaikan agar diperhatikan dengan penuh tanggung jawab demi perbaikan perilaku dan kelancaran proses pembelajaran.
    </p>

    <!-- Signature Fields -->
    <div class="signature-block">
        <div class="signature-row">
            <div class="signature-col">
                <p>Orang Tua / Wali Siswa</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">( {{ $student->parent_name }} )</p>
            </div>
            
            <div class="signature-col">
                <p>Siswa yang Bersangkutan</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">( {{ $student->name }} )</p>
            </div>
        </div>

        <div class="signature-row" style="margin-top: 40px;">
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

        <div style="text-align: center; margin-top: 50px;">
            <p>Mengetahui,</p>
            <p style="margin-bottom: 0;">Kepala SMK Negeri 2 Jakarta</p>
            <div class="signature-space" style="height: 80px;"></div>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">Drs. H. M. Husin, M.Pd</p>
            <p style="font-size: 10pt; margin-top: 2px;">NIP. 196912051995121002</p>
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
