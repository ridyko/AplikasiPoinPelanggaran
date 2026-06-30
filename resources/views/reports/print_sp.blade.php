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
            padding: 15px 30px;
            font-size: 12pt;
            line-height: 1.45;
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
            margin-bottom: 30px;
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

        /* Document Title */
        .title-sp {
            text-align: center;
            margin-bottom: 15px;
        }

        .title-sp h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .title-sp p {
            margin: 3px 0 0 0;
            font-size: 11pt;
        }

        /* Content spacing */
        p.main-paragraph {
            text-indent: 40px;
            text-align: justify;
            margin-bottom: 8px;
        }

        /* Bio Table */
        .bio-table {
            margin: 10px auto 10px 40px;
            border-collapse: collapse;
            width: 80%;
        }

        .bio-table td {
            border: none;
            padding: 3px 10px;
            font-size: 12pt;
        }

        .bio-label {
            width: 180px;
            font-weight: bold;
        }

        /* Signatures block */
        .signature-block {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .signature-col {
            text-align: center;
            width: 220px;
        }

        .signature-space {
            height: 45px;
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

    <!-- Letter title based on SP Level -->
    <div class="title-sp">
        @if($spLevel == 1)
            <h3>SURAT PERINGATAN KESATU (SP 1)</h3>
        @elseif($spLevel == 2)
            <h3>SURAT PERINGATAN KEDUA (SP 2)</h3>
        @elseif($spLevel == 3)
            <h3>SURAT PERINGATAN KETIGA (SP 3 / DROP OUT)</h3>
        @endif
        <p>Nomor : </p>
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
            <td class="bio-label">NIS</td>
            <td>: {{ $student->nis }}</td>
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

        <div class="signature-row" style="margin-top: 20px;">
            <div class="signature-col">
                <p>Wali Kelas</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">
                    ( {{ $student->kelas && $student->kelas->homeroomTeacher ? $student->kelas->homeroomTeacher->name : '........................................' }} )
                </p>
            </div>

            <div class="signature-col" style="margin-top: -15px;">
                <p>Mengetahui,</p>
                <p style="margin-top: 0; margin-bottom: 0;">Kepala SMK Negeri 2 Jakarta</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">Drs. H. M. Husin, M.Pd</p>
                <p style="font-size: 10pt; margin-top: 2px; margin-bottom: 0;">NIP. 196912051995121002</p>
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
