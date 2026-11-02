@extends('layouts.dashboard')

@push('style')
    <style>
        .dinas-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 148px;
            padding: 0.68rem 0.95rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.15;
        }

        .dinas-status-pill.is-neutral {
            background: linear-gradient(135deg, #dfe9e7, #f4faf8);
            color: #355560;
        }

        .dinas-status-pill.is-active {
            background: linear-gradient(135deg, #d7fbf2, #b9efe3);
            color: #0f766e;
        }

        .dinas-status-pill.is-done {
            background: linear-gradient(135deg, #ddfce7, #bbf7d0);
            color: #18794e;
        }

        .dinas-status-pill.is-off {
            background: linear-gradient(135deg, #fff3d8, #ffe2b2);
            color: #a16207;
        }

        .dinas-summary-note {
            margin: 1rem 0 0;
            color: #5f7280;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .dinas-location-form {
            margin-top: 1.15rem;
            display: flex;
            justify-content: flex-start;
        }

        .dinas-location-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.78rem 1.1rem;
            border-width: 1px;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 12px 22px rgba(15, 118, 110, 0.12);
        }

        html[data-theme="dark"] .dinas-status-pill.is-neutral {
            background: linear-gradient(135deg, #33414f, #24313c);
            color: #d8e2ea;
        }

        html[data-theme="dark"] .dinas-status-pill.is-active {
            background: linear-gradient(135deg, #114b48, #0f766e);
            color: #d9fff8;
        }

        html[data-theme="dark"] .dinas-status-pill.is-done {
            background: linear-gradient(135deg, #173a2c, #146c43);
            color: #d7ffe8;
        }

        html[data-theme="dark"] .dinas-status-pill.is-off {
            background: linear-gradient(135deg, #413118, #624b1d);
            color: #ffe5a8;
        }

        html[data-theme="dark"] .dinas-summary-note {
            color: #97aab9;
        }

        @media (max-width: 576px) {
            .dinas-location-form {
                width: 100%;
            }

            .dinas-location-btn {
                width: 100%;
            }

            .dinas-status-pill {
                min-width: 0;
                width: 100%;
            }
        }
    </style>
@endpush

@section('isi')
    @php
        $assignment = $dinas_luar->first();
        $hasAssignment = $assignment !== null;

        $dlid = optional($assignment)->id;
        $tanggalShift = optional($assignment)->tanggal;
        $shiftRelation = optional($assignment)->Shift;
        $shiftName = optional($shiftRelation)->nama_shift ?? 'Belum dipetakan';
        $shiftMasuk = optional($shiftRelation)->jam_masuk ?? '--:--';
        $shiftKeluar = optional($shiftRelation)->jam_keluar ?? '--:--';
        $jamAbsen = optional($assignment)->jam_absen;
        $jamPulang = optional($assignment)->jam_pulang;
        $statusAbsen = optional($assignment)->status_absen ?? 'Belum Dipetakan';
        $checkoutAvailableAt = ($tanggalShift && $jamAbsen)
            ? \Carbon\Carbon::parse($tanggalShift . ' ' . $jamAbsen)->addHour()
            : null;
        $checkoutReady = $checkoutAvailableAt && now()->greaterThanOrEqualTo($checkoutAvailableAt);

        $tanggalTampil = $tanggalShift
            ? \Carbon\Carbon::parse($tanggalShift)->translatedFormat('d M Y')
            : 'Belum dipetakan';
        $shiftWindow = ($shiftMasuk && $shiftKeluar && $shiftMasuk !== '--:--' && $shiftKeluar !== '--:--')
            ? $shiftMasuk . ' - ' . $shiftKeluar
            : '--:--';
        $jamAbsenTampil = $jamAbsen ? \Carbon\Carbon::parse($jamAbsen)->format('H:i') : '--:--';
        $jamPulangTampil = $jamPulang ? \Carbon\Carbon::parse($jamPulang)->format('H:i') : '--:--';

        $sessionStatusText = 'Belum Dipetakan';
        $sessionStatusClass = 'is-neutral';
        $stateTitle = null;
        $stateDescription = null;
        $stateIcon = null;
        $actionPhase = null;

        if (! $hasAssignment) {
            $stateTitle = 'Hubungi admin untuk mapping shift Anda';
            $stateDescription = 'Jadwal dinas luar hari ini belum dipetakan ke akun Anda, sehingga absen belum bisa dilakukan.';
            $stateIcon = 'fas fa-route';
        } elseif (strcasecmp((string) $statusAbsen, 'Libur') === 0) {
            $sessionStatusText = 'Libur';
            $sessionStatusClass = 'is-off';
            $stateTitle = 'Hari ini Anda libur';
            $stateDescription = 'Tidak ada jadwal dinas luar yang perlu dikerjakan hari ini.';
            $stateIcon = 'fas fa-umbrella-beach';
        } elseif (strcasecmp((string) $statusAbsen, 'Cuti') === 0) {
            $sessionStatusText = 'Cuti';
            $sessionStatusClass = 'is-off';
            $stateTitle = 'Hari ini Anda cuti';
            $stateDescription = 'Status kehadiran Anda sudah tercatat sebagai cuti, sehingga absen dinas luar tidak diperlukan.';
            $stateIcon = 'fas fa-plane-departure';
        } elseif ($jamAbsen === null) {
            $sessionStatusText = 'Siap Absen Masuk';
            $sessionStatusClass = 'is-active';
            $actionPhase = 'masuk';
        } elseif ($jamPulang === null) {
            $sessionStatusText = 'Menunggu Absen Pulang';
            $sessionStatusClass = 'is-active';
            $actionPhase = 'pulang';
        } else {
            $sessionStatusText = 'Absensi Selesai';
            $sessionStatusClass = 'is-done';
            $stateTitle = 'Anda sudah selesai absen';
            $stateDescription = 'Absensi masuk dan pulang dinas luar hari ini sudah tercatat dengan lengkap.';
            $stateIcon = 'fas fa-check-circle';
        }
    @endphp

    <div class="container-fluid attendance-shell">
        <div class="row">
            <div class="col-xl-8">
                <div class="card attendance-summary-card">
                    <div class="card-body">
                        <div class="attendance-summary-head">
                            <div>
                                <p class="attendance-kicker">Absensi Online</p>
                                <h2 class="attendance-shift-name">Absen Dinas Luar</h2>
                                <p class="attendance-shift-window">{{ $tanggalTampil }}</p>
                            </div>

                            <div class="attendance-summary-badge">
                                <span class="attendance-summary-badge-label">Status Hari Ini</span>
                                <span class="dinas-status-pill {{ $sessionStatusClass }}">{{ $sessionStatusText }}</span>
                            </div>
                        </div>

                        <div class="attendance-meta-grid">
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Tanggal Shift</span>
                                <strong>{{ $tanggalTampil }}</strong>
                            </div>
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Shift</span>
                                <strong>{{ $shiftName }}</strong>
                            </div>
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Jam Kerja</span>
                                <strong>{{ $shiftWindow }}</strong>
                            </div>
                        </div>

                        <p class="dinas-summary-note">
                            Pastikan kamera dan lokasi aktif sebelum melakukan absen dinas luar. Gunakan tombol lokasi untuk memeriksa posisi terbaru Anda.
                        </p>

                        <form action="{{ url('/my-location') }}" method="get" class="dinas-location-form">
                            @csrf
                            <input type="hidden" name="lat" id="lat2" readonly>
                            <input type="hidden" name="long" id="long2" readonly>
                            <input type="hidden" name="userid" value="{{ auth()->user()->id }}">
                            <button type="submit" class="btn btn-outline-primary dinas-location-btn">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Lihat Lokasi Saya</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card attendance-clock-card-panel">
                    <div class="card-body">
                        <p class="attendance-kicker">Jam Real-Time</p>
                        <h3 class="attendance-clock-time-large" data-live-clock>00:00:00</h3>
                        <p class="attendance-clock-note-small">
                            Absen masuk: {{ $jamAbsenTampil }}. Absen pulang: {{ $jamPulangTampil }}.
                        </p>
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
                            <p class="attendance-kicker">{{ $actionPhase === 'masuk' ? 'Masuk Dinas Luar' : 'Pulang Dinas Luar' }}</p>
                            <h2 class="attendance-action-title">
                                {{ $actionPhase === 'masuk' ? 'Ambil foto masuk dinas luar' : 'Ambil foto pulang dinas luar' }}
                            </h2>
                            <p class="attendance-action-text">
                                {{ $actionPhase === 'masuk'
                                    ? 'Mulai sesi dinas luar dengan foto yang jelas dan pastikan posisi Anda sudah sesuai sebelum menekan tombol masuk.'
                                    : 'Selesaikan sesi dinas luar dengan foto terbaru sebelum menekan tombol pulang.' }}
                            </p>
                            @if ($actionPhase === 'pulang' && ! $checkoutReady)
                                <p class="alert alert-warning py-2 px-3 mb-3" role="status">
                                    Absen pulang tersedia setelah satu jam dari absen masuk, mulai pukul {{ $checkoutAvailableAt->format('H:i') }}.
                                    Tombol akan aktif otomatis pada waktunya.
                                </p>
                            @endif

                            <div class="attendance-info-list">
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">Shift</span>
                                    <strong>{{ $shiftName }}</strong>
                                </div>
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">Tanggal</span>
                                    <strong>{{ $tanggalTampil }}</strong>
                                </div>
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">{{ $actionPhase === 'masuk' ? 'Mode' : 'Jam Masuk' }}</span>
                                    <strong>{{ $actionPhase === 'masuk' ? 'Kamera dan lokasi aktif' : $jamAbsenTampil }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="attendance-camera-panel">
                            <div class="attendance-camera-frame">
                                <div class="webcam attendance-webcam" id="results"></div>
                            </div>
                            <p class="attendance-camera-note">
                                Preview kamera akan tampil di area ini. Pastikan wajah terlihat jelas sebelum menekan tombol aksi.
                            </p>
                        </div>
                    </div>

                    @if ($actionPhase === 'masuk')
                        <form method="post" action="{{ url('/dinas-luar/masuk/'.$dlid) }}" class="attendance-capture-form dinas-capture-form">
                            @method('put')
                            @csrf
                            <input type="hidden" name="jam_absen">
                            <input type="hidden" name="foto_jam_absen" class="image-tag">
                            <input type="hidden" name="lat_absen" id="lat">
                            <input type="hidden" name="long_absen" id="long">
                            <input type="hidden" name="telat">
                            <input type="hidden" name="status_absen">
                            <button type="submit" class="btn btn-primary attendance-action-btn">
                                <i class="fas fa-camera mr-2"></i> Masuk Dinas Luar
                            </button>
                        </form>
                    @elseif ($actionPhase === 'pulang')
                        <form method="post" action="{{ url('/dinas-luar/pulang/'.$dlid) }}" class="attendance-capture-form dinas-capture-form">
                            @method('put')
                            @csrf
                            <input type="hidden" name="jam_pulang">
                            <input type="hidden" name="foto_jam_pulang" class="image-tag">
                            <input type="hidden" name="lat_pulang" id="lat">
                            <input type="hidden" name="long_pulang" id="long">
                            <input type="hidden" name="pulang_cepat">
                            <button type="submit" class="btn btn-primary attendance-action-btn" data-checkout-at="{{ $checkoutAvailableAt ? $checkoutAvailableAt->timestamp : '' }}" {{ ! $checkoutReady ? 'disabled' : '' }}>
                                <i class="fas fa-camera mr-2"></i> Pulang Dinas Luar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('script')
    <script>
        (function () {
            const checkoutButton = document.querySelector('[data-checkout-at]');
            if (checkoutButton && checkoutButton.disabled) {
                const availableAt = Number(checkoutButton.dataset.checkoutAt) * 1000;
                const remaining = availableAt - Date.now();
                if (remaining > 0) {
                    window.setTimeout(function () {
                        checkoutButton.disabled = false;
                        const waitNotice = checkoutButton.closest('.attendance-action-card').querySelector('[role="status"]');
                        if (waitNotice) {
                            waitNotice.classList.remove('alert-warning');
                            waitNotice.classList.add('alert-success');
                            waitNotice.textContent = 'Waktu tunggu selesai. Anda sekarang bisa absen pulang.';
                        }
                    }, remaining);
                } else {
                    checkoutButton.disabled = false;
                }
            }

            const clockNode = document.querySelector('[data-live-clock]');
            const dateNode = document.querySelector('[data-live-date]');

            if (!clockNode && !dateNode) {
                return;
            }

            const timeFormatter = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });

            const updateClock = function () {
                const now = new Date();

                if (clockNode) {
                    clockNode.textContent = timeFormatter.format(now);
                }

                if (dateNode) {
                    dateNode.textContent = dateFormatter.format(now);
                }
            };

            updateClock();
            window.setInterval(updateClock, 1000);
        })();
    </script>

    @if ($actionPhase === 'masuk' || $actionPhase === 'pulang')
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
                document.querySelectorAll('.attendance-webcam').forEach(function (element) {
                    element.style.transform = 'scaleX(-1)';
                    element.style.webkitTransform = 'scaleX(-1)';
                });

                const form = document.querySelector('.dinas-capture-form');

                const takeSnapshot = function (done) {
                    Webcam.snap(function (dataUri) {
                        const imageField = document.querySelector('.image-tag');
                        const resultsNode = document.getElementById('results');

                        if (imageField) {
                            imageField.value = dataUri;
                        }

                        if (resultsNode) {
                            resultsNode.innerHTML = '<img src="' + dataUri + '" alt="Preview Dinas Luar">';
                            resultsNode.style.transform = 'scaleX(1)';
                            resultsNode.style.webkitTransform = 'scaleX(1)';
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

                        if (form.dataset.snapshotReady === 'processing') {
                            event.preventDefault();
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
