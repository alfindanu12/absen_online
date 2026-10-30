<!DOCTYPE html>
<html>
<head>
	<title>Laporan Gaji</title>

	<style type="text/css">
		@media print {
		  @page { margin: 0; }
		  body { margin: 1.6cm; }
		}
	</style>
</head>
<body onload="window.print()">

	<h1 align="center">Laporan Gaji</h1>
	 <div align="center">Periode Bulan {{ date('d-m-Y',strtotime(Request::get('dari'))) }} - {{ date('d-m-Y',strtotime(Request::get('sampai'))) }}</div>
	<div align="center">{{ $jabatan ? "Jabatan : $jabatan->nama_jabatan" : "Jabatan : Semua" }} </div>
	<hr>

	@if(Request::get('tipe') == "Bulanan")
                <table style="border-collapse: collapse;" border="1" width="100%">
                    <tr>
                        <th>Jabatan</th>
                        <th>Jumlah Karyawan</th>
                        <th>Total Upah Lembur</th>
                        <th>Subtotal Gaji</th>
                    </tr>
                    @php $total = 0;$target = 0;$karyawan=0; @endphp
                    @foreach($gaji as $item)
                    @php
                        $total += $item->gaji;
                        $target += $item->upah_lembur;
                        $karyawan += $item->jlh;
                    @endphp
                    <tr>
                        <td>{{ $item->nama_jabatan }}</td>
                        <td align="center">{{ number_format($item->jlh) }}</td>
                        <td align="center">{{ number_format($item->upah_lembur) }}</td>
                        <td align="center">{{ number_format($item->gaji) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td align="center" colspan="3"><b>Total</b></td>
                        <td align="center"><b>{{ number_format($total) }}</b></td>
                    </tr>
                </table>
                @endif

                @if(Request::get('tipe') == "Mingguan")
                <table style="border-collapse: collapse;" border="1" width="100%">
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah Hadir</th>
                        <th>Upah Normal</th>
                        <th>Upah Target</th>
                        <th>Upah Lembur</th>
                        <th>Masuk Minggu</th>
                        <th>Kasbon</th>
                        <th>Total Terima Gaji</th>
                    </tr>
                    @php
                    	$total = 0;
                    @endphp
                    @foreach($gaji as $item)
                    @php
                    	$total += $item->jlh_absen*$item->upah_normal+$item->upah_target+$item->upah_lembur+$item->masuk_minggu-$item->kasbon;
                    @endphp
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td align="center">{{ number_format($item->jlh_absen) }}</td>
                        <td>{{ number_format($item->upah_normal) }}</td>
                        <td>{{ number_format($item->upah_target) }}</td>
                        <td>{{ number_format($item->upah_lembur) }}</td>
                        <td>{{ number_format($item->masuk_minggu) }}</td>
                        <td>{{ number_format($item->kasbon) }}</td>
                        <td align="center">{{ number_format($item->jlh_absen*$item->upah_normal+$item->upah_target+$item->upah_lembur+$item->masuk_minggu-$item->kasbon) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                    	<td colspan="7" align="center"><b>Total</b></td>
                    	<td align="center"><b>{{ number_format($total) }}</b></td>
                    </tr>
                </table>
                @endif

</body>
</html>