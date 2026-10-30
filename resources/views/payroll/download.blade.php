<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slip Gaji</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .container {
      max-width: 400px;
      margin: 0 auto;
      border: 1px solid #ccc;
      padding: 20px;
    }
    .header {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .employee-info {
      margin-bottom: 20px;
    }
    .employee-info span {
      font-weight: bold;
    }
    .table {
      width: 100%;
      border-collapse: collapse;
    }
    .table th, .table td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }
    .table th {
      background-color: #f2f2f2;
    }
    .total {
      margin-top: 20px;
      text-align: right;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">Slip Gaji</div>
    <div class="employee-info">
      <span>Nama:</span> {{ $data->User->name }}<br>
      <span>NIP:</span> {{ $data->User->username }}<br>
      <span>Golongan:</span> {{ $data->User->Golongan->name }}
      <br>
      <span>Jabatan:</span> {{ $data->User->jabatan->nama_jabatan }}
      <br>
      @php
          if ($data->bulan == 1) {
            $bulan = "Januari";
        } elseif ($data->bulan == 2) {
            $bulan = "Februari";
        } elseif ($data->bulan == 3) {
            $bulan = "Maret";
        } elseif ($data->bulan == 4) {
            $bulan = "April";
        } elseif ($data->bulan == 5) {
            $bulan = "Mei";
        } elseif ($data->bulan == 6) {
            $bulan = "Juni";
        } elseif ($data->bulan == 7) {
            $bulan = "Juli";
        } elseif ($data->bulan == 8) {
            $bulan = "Agustus";
        } elseif ($data->bulan == 9) {
            $bulan = "September";
        } elseif ($data->bulan == 10) {
            $bulan = "Oktober";
        } elseif ($data->bulan == 11) {
            $bulan = "November";
        } else {
            $bulan = "Desember";
        }
      @endphp
      <span>Tanggal:</span> {{ $bulan . ' ' . $data->tahun }}
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>Deskripsi</th>
          <th>Jumlah</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Kehadiran</td>
          <td>{{ $jumlah_hadir = $data->JumlahHadir($data->user_id, $data->bulan, $data->tahun, 'Masuk') + $data->JumlahHadir($data->user_id, $data->bulan, $data->tahun, 'Izin Pulang Cepat') + $data->JumlahHadir($data->user_id, $data->bulan, $data->tahun, 'Izin Telat'); }}</td>
        </tr>
        <tr>
          <td>Gaji Pokok</td>
          <td>Rp {{ number_format($data->gaji, 2, ".", ",") }}</td>
        </tr>
        <tr>
            @php
                $tunjangan_makan = $data->User->Golongan->Tunjangan->first()->tunjangan_makan * $jumlah_hadir;
                $tunjangan_transport = $data->User->Golongan->Tunjangan->first()->tunjangan_transport * $jumlah_hadir;
            @endphp
          <td>Tunjangan Makan</td>
          <td>Rp {{ number_format($tunjangan_makan, 2, ".", ",") }}</td>
        </tr>
        <tr>
          <td>Tunjangan Transport</td>
          <td>Rp {{ number_format($tunjangan_transport, 2, ".", ",") }}</td>
        </tr>
        <tr>
          <td>Tunjangan Pensiun</td>
          <td>Rp {{ number_format($data->tunjangan_pensiun, 2, ".", ",") }}</td>
        </tr>
        <tr>
          <td>Tunjangan Komunikasi</td>
          <td>Rp {{ number_format($data->tunjangan_komunikasi, 2, ".", ",") }}</td>
        </tr>
        <tr>
          <td>Tunjangan PPH 21</td>
          <td>Rp {{ number_format($data->tunjangan_pph_21, 2, ".", ",") }}</td>
        </tr>
        <tr>
          <td>Lembur</td>
          <td>Rp {{ number_format($data->lembur, 2, ".", ",") }}</td>
        </tr>
        <tr style="font-weight: bold">
            @php
                $jumlah_tambah = $data->gaji + $tunjangan_makan + $tunjangan_transport + $data->tunjangan_pensiun + $data->tunjangan_komunikasi + $data->tunjangan_pph_21 + $data->lembur;
            @endphp
          <td>Komponen Penambah</td>
          <td>Rp {{ number_format($jumlah_tambah, 2, ".", ",") }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr style="color: red">
          <td>Potongan Tunjangan</td>
          @php
            $jumlah_telat = $data->jumlahTelat($data->user_id, $data->bulan, $data->tahun);
            $pot_tunjangan_makan = ($data->User->Golongan->Tunjangan->first()->tunjangan_makan * (25/100)) * $jumlah_telat;
            $pot_tunjangan_transport = ($data->User->Golongan->Tunjangan->first()->tunjangan_transport * (25/100)) * $jumlah_telat;
          @endphp
          <td>Rp {{ number_format($pot_tunjangan_makan + $pot_tunjangan_transport, 2, ".", ",") }}</td>
        </tr>
        <tr style="color: red">
          <td>Potongan BPJS Kesehatan</td>
          <td>Rp {{ number_format($data->setoran_bpjs_kes - $data->tunjangan_bpjs_kes, 2, ".", ",") }}</td>
        </tr>
        <tr style="color: red">
          <td>Potongan BPJS Tenaga Kerja</td>
          <td>Rp {{ number_format($data->setoran_bpjs_tk - $data->tunjangan_bpjs_tk, 2, ".", ",") }}</td>
        </tr>
        <tr style="color: red">
          <td>Cicilan</td>
          <td>Rp {{ number_format($data->pot_lainnya, 2, ".", ",") }}</td>
        </tr>
        <tr style="font-weight:bold">
            @php
                $jumlah_kurang = ($pot_tunjangan_makan + $pot_tunjangan_transport) + ($data->setoran_bpjs_kes - $data->tunjangan_bpjs_kes) + ($data->setoran_bpjs_tk - $data->tunjangan_bpjs_tk) + $data->pot_lainnya;
            @endphp
          <td>Komponen Pengurang</td>
          <td>Rp {{ number_format($jumlah_kurang, 2, ".", ",") }}</td>
        </tr>
      </tbody>
    </table>
    <div class="total">
      <span>Take Home Pay:</span> {{ number_format($jumlah_tambah - $jumlah_kurang, 2, ".", ",") }}
    </div>
  </div>
</body>
</html>
