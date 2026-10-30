@extends('layouts.dashboard')

@push('style')
    <style>
        .my-absen-page {
            overflow-x: hidden;
            padding-bottom: 1rem;
        }

        .my-absen-filter-card,
        .my-absen-results-card {
            border: 1px solid #dbe8e5;
            box-shadow: 0 18px 40px rgba(20, 56, 78, 0.08);
            overflow: hidden;
        }

        .my-absen-filter-card .card-body,
        .my-absen-results-card .card-body {
            padding: 1.35rem;
        }

        .my-absen-filter-head,
        .my-absen-results-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .my-absen-kicker {
            margin: 0 0 0.35rem;
            color: #5f857f;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .my-absen-heading,
        .my-absen-title {
            margin: 0;
            color: #0b3954;
            font-family: "Sora", sans-serif;
            font-size: 1.5rem;
            line-height: 1.2;
        }

        .my-absen-filter-note {
            margin: 0.6rem 0 0;
            color: #5d7474;
            max-width: 42rem;
        }

        .my-absen-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 96px;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.16), rgba(245, 158, 11, 0.18));
            color: #0f766e;
            font-weight: 800;
            white-space: nowrap;
        }

        .my-absen-filter-form {
            margin-top: 1.2rem;
        }

        .my-absen-filter-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr)) auto;
            gap: 1rem;
            align-items: end;
        }

        .my-absen-field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin: 0;
        }

        .my-absen-field span {
            color: #486866;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .my-absen-field .form-control,
        .my-absen-filter-actions .btn {
            height: 48px;
            border-radius: 14px;
        }

        .my-absen-filter-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .my-absen-filter-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.1rem;
            white-space: nowrap;
        }

        .my-absen-reset {
            border: 1px solid #d7e4e0;
            background: #ffffff;
            color: #486866;
        }

        .my-absen-scroll-hint {
            margin: 0 0 0.85rem;
            color: #5d7474;
            font-size: 0.86rem;
        }

        .my-absen-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e3ece9;
            border-radius: 20px;
            overscroll-behavior-x: contain;
            -webkit-overflow-scrolling: touch;
        }

        .my-absen-table {
            min-width: 1100px;
            margin-bottom: 0;
        }

        .my-absen-table th,
        .my-absen-table td {
            vertical-align: middle;
        }

        .my-absen-cell {
            min-width: 0;
        }

        .my-absen-inline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            align-items: center;
        }

        .my-absen-link-btn {
            border-radius: 999px;
            font-weight: 700;
            padding-inline: 0.8rem;
        }

        .my-absen-thumb {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid #d8e6e2;
            box-shadow: 0 10px 18px rgba(20, 56, 78, 0.12);
        }

        .my-absen-empty {
            border: 1px dashed #d7e6e1;
            border-radius: 22px;
            padding: 2.5rem 1rem;
            text-align: center;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(241, 247, 245, 0.96));
        }

        .my-absen-empty i {
            font-size: 2rem;
            color: #0f766e;
            margin-bottom: 0.85rem;
        }

        .my-absen-empty p {
            margin: 0.45rem 0 0;
            color: #5d7474;
        }

        @media (max-width: 991.98px) {
            .my-absen-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .my-absen-filter-actions {
                grid-column: 1 / -1;
            }

            .my-absen-filter-actions .btn {
                flex: 1 1 0;
            }
        }

        @media (max-width: 767.98px) {
            .my-absen-filter-card .card-body,
            .my-absen-results-card .card-body {
                padding: 1rem;
            }

            .my-absen-filter-head,
            .my-absen-results-head {
                flex-direction: column;
            }

            .my-absen-heading,
            .my-absen-title {
                font-size: 1.24rem;
            }

            .my-absen-filter-grid {
                grid-template-columns: 1fr;
            }

            .my-absen-filter-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .my-absen-filter-actions .btn,
            .my-absen-count {
                width: 100%;
            }

            .my-absen-table {
                min-width: 0;
                border: 0;
            }

            .my-absen-table thead {
                display: none;
            }

            .my-absen-table,
            .my-absen-table tbody,
            .my-absen-table tr,
            .my-absen-table td {
                display: block;
                width: 100%;
            }

            .my-absen-table tbody tr {
                margin-bottom: 1rem;
                border: 1px solid #e2ece8;
                border-radius: 20px;
                background: #ffffff;
                box-shadow: 0 14px 28px rgba(20, 56, 78, 0.08);
                overflow: hidden;
            }

            .my-absen-table tbody td {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.85rem;
                padding: 0.85rem 1rem;
                border-top: 1px solid #edf3f1;
                text-align: right;
            }

            .my-absen-table tbody td:first-child {
                border-top: 0;
            }

            .my-absen-table tbody td::before {
                content: attr(data-label);
                flex: 0 0 42%;
                color: #6b8581;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-align: left;
                text-transform: uppercase;
            }

            .my-absen-cell {
                flex: 1 1 auto;
                text-align: right;
                word-break: break-word;
            }

            .my-absen-cell .my-absen-inline-actions {
                justify-content: flex-end;
            }

            .my-absen-cell .my-absen-thumb {
                margin-left: auto;
            }
        }

        html[data-theme="dark"] .my-absen-filter-card,
        html[data-theme="dark"] .my-absen-results-card,
        html[data-theme="dark"] .my-absen-table-wrap,
        html[data-theme="dark"] .my-absen-table tbody tr {
            background: #0f1b22;
            border-color: #24363f;
            box-shadow: none;
        }

        html[data-theme="dark"] .my-absen-heading,
        html[data-theme="dark"] .my-absen-title {
            color: #e6edf4;
        }

        html[data-theme="dark"] .my-absen-kicker,
        html[data-theme="dark"] .my-absen-field span,
        html[data-theme="dark"] .my-absen-table tbody td::before {
            color: #93a7b7;
        }

        html[data-theme="dark"] .my-absen-filter-note,
        html[data-theme="dark"] .my-absen-scroll-hint,
        html[data-theme="dark"] .my-absen-empty p {
            color: #9bb0be;
        }

        html[data-theme="dark"] .my-absen-count {
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.36), rgba(245, 158, 11, 0.26));
            color: #7ce7d9;
        }

        html[data-theme="dark"] .my-absen-reset {
            background: #122830;
            border-color: #24363f;
            color: #dce7f0;
        }

        html[data-theme="dark"] .my-absen-thumb {
            border-color: #24363f;
            box-shadow: none;
        }

        html[data-theme="dark"] .my-absen-empty {
            background: linear-gradient(180deg, rgba(15, 27, 34, 0.98), rgba(11, 23, 32, 0.96));
            border-color: #24363f;
        }
    </style>
@endpush

@section('isi')
    @php
        $mulaiFilter = request('mulai') ? substr((string) request('mulai'), 0, 10) : '';
        $akhirFilter = request('akhir') ? substr((string) request('akhir'), 0, 10) : '';
    @endphp

    <div class="container-fluid my-absen-page">
        <div class="card my-absen-filter-card">
            <div class="card-body">
                <div class="my-absen-filter-head">
                    <div>
                        <p class="my-absen-kicker">Riwayat Kehadiran</p>
                        <h2 class="my-absen-heading">Filter absensi saya</h2>
                        <p class="my-absen-filter-note">
                            Pilih rentang tanggal untuk melihat riwayat absensi dengan tampilan yang tetap nyaman di layar kecil.
                        </p>
                    </div>
                    <div class="my-absen-count">{{ $data_absen->count() }} data</div>
                </div>

                <form action="{{ url('/my-absen') }}" class="my-absen-filter-form">
                    <div class="my-absen-filter-grid">
                        <label class="my-absen-field" for="mulai">
                            <span>Dari tanggal</span>
                            <input type="date" class="form-control" name="mulai" id="mulai" value="{{ $mulaiFilter }}">
                        </label>

                        <label class="my-absen-field" for="akhir">
                            <span>Sampai tanggal</span>
                            <input type="date" class="form-control" name="akhir" id="akhir" value="{{ $akhirFilter }}">
                        </label>

                        <div class="my-absen-filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-2"></i>Terapkan
                            </button>

                            @if ($mulaiFilter || $akhirFilter)
                                <a href="{{ url('/my-absen') }}" class="btn my-absen-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card my-absen-results-card mt-4">
            <div class="card-body">
                <div class="my-absen-results-head">
                    <div>
                        <p class="my-absen-kicker">Ringkasan</p>
                        <h2 class="my-absen-title">Riwayat absensi saya</h2>
                    </div>
                </div>

                @if ($data_absen->count() > 0)
                    <p class="my-absen-scroll-hint">Di mobile, setiap baris otomatis berubah jadi kartu. Di layar lebar, tabel bisa digeser untuk melihat semua kolom.</p>

                    <div class="my-absen-table-wrap">
                        <table id="tableprintabsen" class="table table-bordered table-striped my-absen-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Shift</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Telat</th>
                                    <th>Lokasi Masuk</th>
                                    <th>Foto Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Pulang Cepat</th>
                                    <th>Lokasi Pulang</th>
                                    <th>Foto Pulang</th>
                                    <th>Status Absen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_absen as $da)
                                    <tr>
                                        <td data-label="No.">
                                            <div class="my-absen-cell">{{ $loop->iteration }}</div>
                                        </td>
                                        <td data-label="Shift">
                                            <div class="my-absen-cell">
                                                {{ $da->Shift->nama_shift }} ({{ $da->Shift->jam_masuk }} - {{ $da->Shift->jam_keluar }})
                                            </div>
                                        </td>
                                        <td data-label="Tanggal">
                                            <div class="my-absen-cell">{{ $da->tanggal }}</div>
                                        </td>
                                        <td data-label="Jam Masuk">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @else
                                                    {{ $da->jam_absen }}
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Telat">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->status_absen == 'Izin Telat')
                                                    <span class="badge badge-warning">Izin Telat</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @else
                                                    @php
                                                        $telat = $da->telat;
                                                        $jam = floor($telat / (60 * 60));
                                                        $menit = $telat - ($jam * (60 * 60));
                                                        $menit2 = floor($menit / 60);
                                                    @endphp

                                                    @if($jam <= 0 && $menit2 <= 0)
                                                        <span class="badge badge-success">Tepat Waktu</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ $jam . ' Jam ' . $menit2 . ' Menit' }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Lokasi Masuk">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @else
                                                    @php
                                                        $jarak_masuk = explode('.', $da->jarak_masuk);
                                                    @endphp
                                                    <div class="my-absen-inline-actions">
                                                        <a href="{{ url('/maps/'.$da->lat_absen.'/'.$da->long_absen.'/'.$da->user_id) }}" class="btn btn-sm btn-secondary my-absen-link-btn" target="_blank">Lihat</a>
                                                        <span class="badge badge-warning">{{ $jarak_masuk[0] }} Meter</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Foto Masuk">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @else
                                                    <img src="{{ url('storage/' . $da->foto_jam_absen) }}" alt="Foto masuk {{ $da->tanggal }}" class="my-absen-thumb">
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Jam Pulang">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @elseif($da->jam_pulang == null)
                                                    <span class="badge badge-warning">Belum Pulang</span>
                                                @else
                                                    {{ $da->jam_pulang }}
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Pulang Cepat">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->status_absen == 'Izin Pulang Cepat')
                                                    <span class="badge badge-warning">Izin Pulang Cepat</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @elseif($da->jam_pulang == null)
                                                    <span class="badge badge-warning">Belum Pulang</span>
                                                @else
                                                    @php
                                                        $pulang_cepat = $da->pulang_cepat;
                                                        $jam = floor($pulang_cepat / (60 * 60));
                                                        $menit = $pulang_cepat - ($jam * (60 * 60));
                                                        $menit2 = floor($menit / 60);
                                                    @endphp

                                                    @if($jam <= 0 && $menit2 <= 0)
                                                        <span class="badge badge-success">Tidak Pulang Cepat</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ $jam . ' Jam ' . $menit2 . ' Menit' }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Lokasi Pulang">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @elseif($da->jam_pulang == null)
                                                    <span class="badge badge-warning">Belum Pulang</span>
                                                @else
                                                    @php
                                                        $jarak_pulang = explode('.', $da->jarak_pulang);
                                                    @endphp
                                                    <div class="my-absen-inline-actions">
                                                        <a href="{{ url('/maps/'.$da->lat_pulang.'/'.$da->long_pulang.'/'.$da->user_id) }}" class="btn btn-sm btn-secondary my-absen-link-btn" target="_blank">Lihat</a>
                                                        <span class="badge badge-warning">{{ $jarak_pulang[0] }} Meter</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Foto Pulang">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti')
                                                    <span class="badge badge-warning">Sedang Cuti</span>
                                                @elseif($da->jam_absen == null)
                                                    <span class="badge badge-danger">Belum Absen</span>
                                                @elseif($da->jam_pulang == null)
                                                    <span class="badge badge-warning">Belum Pulang</span>
                                                @else
                                                    <img src="{{ url('storage/' . $da->foto_jam_pulang) }}" alt="Foto pulang {{ $da->tanggal }}" class="my-absen-thumb">
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            <div class="my-absen-cell">
                                                @if($da->status_absen == 'Libur')
                                                    <span class="badge badge-info">Libur</span>
                                                @elseif($da->status_absen == 'Cuti' || $da->status_absen == 'Izin Telat' || $da->status_absen == 'Izin Pulang Cepat')
                                                    <span class="badge badge-warning">{{ $da->status_absen }}</span>
                                                @elseif($da->status_absen == 'Masuk')
                                                    <span class="badge badge-success">{{ $da->status_absen }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $da->status_absen }}</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="my-absen-empty">
                        <i class="far fa-calendar-times"></i>
                        <h3 class="h5 mb-0">Belum ada data absensi</h3>
                        <p>Ubah filter tanggal atau tunggu data absensi berikutnya muncul di halaman ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
