@extends('layouts.dashboard')
@section('hide-page-header', true)

@section('isi')
    @php
        $shift = $shift_karyawan->first();
        $shiftDetail = $shift ? $shift->Shift : null;
        $hasShift = $shift_karyawan->count() > 0;

        $skid = $shift->id ?? null;
        $sktanggal = $shift->tanggal ?? null;
        $sknamas = $shiftDetail->nama_shift ?? 'Belum Ada Shift';
        $skjamas = $shiftDetail->jam_masuk ?? '--:--';
        $skjamkel = $shiftDetail->jam_keluar ?? '--:--';
        $skjamab = $shift->jam_absen ?? null;
        $skjampul = $shift->jam_pulang ?? null;
        $skstatus = $shift->status_absen ?? null;
        $tanggalTampil = $sktanggal ? \Carbon\Carbon::parse($sktanggal)->format('d M Y') : 'Belum tersedia';
        $lokasiTampil = optional(auth()->user()->Lokasi)->nama_lokasi ?? 'Lokasi belum diatur';

        $stateTitle = null;
        $stateDescription = null;
        $stateIcon = 'fas fa-calendar-check';
        $attendancePhase = null;
        $canCheckOut = true;
        $checkOutAvailableAt = null;

        if (!$hasShift) {
            $stateTitle = 'Shift belum dipetakan';
            $stateDescription = 'Hubungi admin untuk mapping shift Anda sebelum melakukan absensi.';
            $stateIcon = 'fas fa-calendar-alt';
        } elseif ($skstatus === 'Libur') {
            $stateTitle = 'Hari ini Anda libur';
            $stateDescription = 'Tidak ada absensi yang perlu dilakukan pada jadwal hari ini.';
            $stateIcon = 'fas fa-bed';
        } elseif ($skstatus === 'Cuti') {
            $stateTitle = 'Hari ini Anda cuti';
            $stateDescription = 'Status kehadiran Anda sudah tercatat sebagai cuti.';
            $stateIcon = 'fas fa-plane-departure';
        } elseif ($skjamab === null) {
            $attendancePhase = 'masuk';
        } elseif ($skjampul === null) {
            $attendancePhase = 'pulang';
            $checkOutAvailableAt = \Carbon\Carbon::parse($sktanggal . ' ' . $skjamab)->addHour();
            $canCheckOut = now()->greaterThanOrEqualTo($checkOutAvailableAt);
            if (!$canCheckOut) {
                $stateTitle = 'Absen pulang belum tersedia';
                $stateDescription = 'Anda baru bisa absen pulang mulai pukul ' . $checkOutAvailableAt->format('H:i') . ', yaitu 1 jam setelah absen masuk.';
                $stateIcon = 'fas fa-hourglass-half';
            }
        } else {
            $stateTitle = 'Absensi hari ini selesai';
            $stateDescription = 'Anda sudah melakukan absen masuk dan pulang. Tidak ada aksi tambahan.';
            $stateIcon = 'fas fa-check-circle';
        }
    @endphp

    <div class="container-fluid attendance-shell">
        <div class="row">
            <div class="col-12">
                <div class="card attendance-clock-card-panel attendance-clock-card-top">
                    <div class="card-body">
                        <p class="attendance-kicker">Jam Real-Time</p>
                        <h3 class="attendance-clock-time-large" data-live-clock>00:00:00</h3>
                        <p class="attendance-clock-date-large" data-live-date>{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card attendance-summary-card">
                    <div class="card-body">
                        <div class="attendance-summary-head">
                            <div>
                                <p class="attendance-kicker">Status Shift</p>
                                <h2 class="attendance-shift-name">{{ $sknamas }}</h2>
                                <p class="attendance-shift-window">{{ $skjamas }} - {{ $skjamkel }}</p>
                            </div>
                        </div>

                        <div class="attendance-meta-grid attendance-meta-grid-four">
                            <div class="attendance-meta-item attendance-summary-badge">
                                <span class="attendance-meta-label attendance-summary-badge-label">Status</span>
                                <strong>{{ $hasShift ? $skstatus : 'Belum Dipetakan' }}</strong>
                            </div>
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Tanggal Shift</span>
                                <strong>{{ $tanggalTampil }}</strong>
                            </div>
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Lokasi Absen</span>
                                <strong>{{ $lokasiTampil }}</strong>
                            </div>
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Status Titik Lokasi</span>
                                <strong class="attendance-location-status" data-location-status="loading">
                                    <span class="attendance-location-light" aria-hidden="true"></span>
                                    <span data-location-status-text>Titik lokasi belum terdeteksi</span>
                                </strong>
                            </div>
                        </div>

                        <form action="{{ url('/my-location') }}" method="get" class="d-none">
                            @csrf
                            <input type="hidden" name="lat" id="lat2" readonly>
                            <input type="hidden" name="long" id="long2" readonly>
                            <input type="hidden" name="userid" value="{{ auth()->user()->id }}">
                        </form>
                    </div>
                </div>
            </div>

        </div>

        @if ($stateTitle)
            <div class="card attendance-empty-card">
                <div class="card-body">
                    <div class="attendance-empty-icon">
                        <i class="{{ $stateIcon }}"></i>
                    </div>
                    <h2 class="attendance-empty-title">{{ $stateTitle }}</h2>
                    <p class="attendance-empty-text">{{ $stateDescription }}</p>
                </div>
            </div>
        @else
            <div class="card attendance-action-card">
                <div class="card-body">
                    <div class="attendance-capture-grid">
                        <div class="attendance-capture-copy">
                            <p class="attendance-kicker">{{ $attendancePhase === 'masuk' ? 'Absen Masuk' : 'Absen Pulang' }}</p>
                            <h2 class="attendance-action-title">
                                {{ $attendancePhase === 'masuk' ? 'Ambil foto kehadiran masuk' : 'Ambil foto kehadiran pulang' }}
                            </h2>
                            <p class="attendance-action-text">
                                {{ $attendancePhase === 'masuk'
                                    ? 'Pastikan wajah terlihat jelas, kamera aktif, dan Anda berada di area kantor sebelum menekan tombol masuk.'
                                    : 'Pastikan wajah terlihat jelas dan posisi Anda masih berada dalam radius lokasi kantor sebelum menekan tombol pulang.' }}
                            </p>

                            <div class="attendance-info-list">
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">Shift</span>
                                    <strong>{{ $sknamas }}</strong>
                                </div>
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">Jam Kerja</span>
                                    <strong>{{ $skjamas }} - {{ $skjamkel }}</strong>
                                </div>
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">Status Titik</span>
                                    <strong class="attendance-location-status" data-location-status="loading">
                                        <span class="attendance-location-light" aria-hidden="true"></span>
                                        <span data-location-status-text>Titik lokasi belum terdeteksi</span>
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="attendance-camera-panel">
                            <div class="attendance-camera-frame">
                                <div class="webcam attendance-webcam" id="results"></div>
                            </div>
                            <p class="attendance-camera-note">Preview kamera akan tampil di area ini. Jangan menutup izin kamera dan lokasi.</p>
                        </div>
                    </div>

                    @if ($attendancePhase === 'masuk')
                        <form method="post" action="{{ url('/absen/masuk/'.$skid) }}" class="attendance-capture-form">
                            @method('put')
                            @csrf
                            <input type="hidden" name="jam_absen">
                            <input type="hidden" name="foto_jam_absen" class="image-tag">
                            <input type="hidden" name="lat_absen" id="lat">
                            <input type="hidden" name="long_absen" id="long">
                            <input type="hidden" name="telat">
                            <input type="hidden" name="jarak_masuk">
                            <input type="hidden" name="status_absen">
                            <button type="submit" class="btn btn-primary attendance-action-btn">
                                <i class="fas fa-camera mr-2"></i> Absen Masuk
                            </button>
                        </form>
                    @elseif ($attendancePhase === 'pulang')
                        <form method="post" action="{{ url('/absen/pulang/'.$skid) }}" class="attendance-capture-form">
                            @method('put')
                            @csrf
                            <input type="hidden" name="jam_pulang">
                            <input type="hidden" name="foto_jam_pulang" class="image-tag">
                            <input type="hidden" name="lat_pulang" id="lat">
                            <input type="hidden" name="long_pulang" id="long">
                            <input type="hidden" name="pulang_cepat">
                            <input type="hidden" name="jarak_pulang">
                            <button type="submit" class="btn btn-primary attendance-action-btn">
                                <i class="fas fa-camera mr-2"></i> Absen Pulang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('script')
    @if ($attendancePhase === 'masuk' || $attendancePhase === 'pulang')
        <script type="text/javascript" src="{{ url('webcamjs/webcam.min.js') }}"></script>
        <script>
            (function () {
                Webcam.set({
                    width: 320,
                    height: 420,
                    image_format: 'jpeg',
                    jpeg_quality: 75,
                    flip_horiz: true
                });

                Webcam.attach('.attendance-webcam');
                document.querySelectorAll('.attendance-webcam').forEach(function(element) {
                    element.style.transform = 'scaleX(-1)';
                    element.style.webkitTransform = 'scaleX(-1)';
                });

                const form = document.querySelector('.attendance-capture-form');

                const takeSnapshot = function (done) {
                    Webcam.snap(function (dataUri) {
                        const imageField = document.querySelector('.image-tag');
                        const resultsNode = document.getElementById('results');

                        if (imageField) {
                            imageField.value = dataUri;
                        }

                        if (resultsNode) {
                            resultsNode.innerHTML = '<img src="' + dataUri + '" alt="Preview Absen">';
                        }

                        if (typeof done === 'function') {
                            done();
                        }
                    });
                };

                if (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.dataset.snapshotReady === 'true') {
                            return;
                        }

                        event.preventDefault();
                        form.dataset.snapshotReady = 'processing';

                        takeSnapshot(function () {
                            form.dataset.snapshotReady = 'true';
                            form.submit();
                        });
                    });
                }
            })();
        </script>
    @endif
@endpush
