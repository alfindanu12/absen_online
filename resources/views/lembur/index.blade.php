@extends('layouts.dashboard')

@push('style')
    <style>
        .lembur-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 118px;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .lembur-status-pill.is-pending {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
        }

        .lembur-status-pill.is-active {
            background: rgba(20, 184, 166, 0.14);
            color: #0f766e;
        }

        .lembur-status-pill.is-done {
            background: rgba(34, 197, 94, 0.14);
            color: #15803d;
        }

        .lembur-status-pill.is-neutral {
            background: rgba(100, 116, 139, 0.12);
            color: #475569;
        }

        .lembur-summary-note {
            margin: 0.9rem 0 0;
            color: #5f7280;
            font-size: 0.96rem;
            line-height: 1.7;
            max-width: 640px;
        }

        .lembur-photo-note {
            margin-top: 1.1rem;
        }

        html[data-theme="dark"] .lembur-status-pill.is-pending {
            background: rgba(245, 158, 11, 0.18);
            color: #fbbf24;
        }

        html[data-theme="dark"] .lembur-status-pill.is-active {
            background: rgba(20, 184, 166, 0.2);
            color: #7ce7d9;
        }

        html[data-theme="dark"] .lembur-status-pill.is-done {
            background: rgba(34, 197, 94, 0.18);
            color: #86efac;
        }

        html[data-theme="dark"] .lembur-status-pill.is-neutral {
            background: rgba(120, 146, 160, 0.16);
            color: #d3e2ec;
        }

        html[data-theme="dark"] .lembur-summary-note {
            color: #97aab9;
        }
    </style>
@endpush

@section('isi')
    @php
        $overtime = $lembur->first();

        $overtimeId = optional($overtime)->id;
        $jamMasuk = optional($overtime)->jam_masuk;
        $jamKeluar = optional($overtime)->jam_keluar;
        $tanggalLembur = optional($overtime)->tanggal ?? now()->format('Y-m-d');
        $tanggalTampil = \Carbon\Carbon::parse($tanggalLembur)->translatedFormat('d M Y');
        $lokasiTampil = optional(auth()->user()->Lokasi)->nama_lokasi ?? 'Lokasi belum diatur';
        $statusApproval = optional($overtime)->status ?? 'Belum Diajukan';
        $jamMasukTampil = $jamMasuk ? \Carbon\Carbon::parse($jamMasuk)->format('H:i') : '--:--';
        $jamKeluarTampil = $jamKeluar ? \Carbon\Carbon::parse($jamKeluar)->format('H:i') : '--:--';

        $totalLemburTampil = '--';
        if ($overtime && optional($overtime)->total_lembur !== null) {
            $totalDetik = (int) optional($overtime)->total_lembur;
            $jamTotal = intdiv($totalDetik, 3600);
            $menitTotal = intdiv($totalDetik - ($jamTotal * 3600), 60);
            $totalLemburTampil = trim($jamTotal . ' Jam ' . $menitTotal . ' Menit');
        }

        $stateTitle = null;
        $stateDescription = null;
        $stateIcon = 'fas fa-check-circle';
        $actionPhase = null;

        $sessionStatusText = 'Siap Masuk';
        $sessionStatusClass = 'is-pending';

        if (!$overtime) {
            $actionPhase = 'masuk';
        } elseif ($jamMasuk && !$jamKeluar) {
            $actionPhase = 'pulang';
            $sessionStatusText = 'Sedang Lembur';
            $sessionStatusClass = 'is-active';
        } else {
            $sessionStatusText = 'Selesai';
            $sessionStatusClass = 'is-done';
            $stateTitle = 'Sesi lembur hari ini sudah selesai';
            $stateDescription = 'Absen masuk dan pulang lembur sudah tercatat. Status approval saat ini: ' . $statusApproval . '.';
        }

        $approvalClass = 'is-neutral';
        if (strcasecmp($statusApproval, 'Pending') === 0) {
            $approvalClass = 'is-pending';
        } elseif (in_array(strtolower($statusApproval), ['approve', 'approved'], true)) {
            $approvalClass = 'is-done';
        } elseif (strcasecmp($statusApproval, 'Belum Diajukan') !== 0) {
            $approvalClass = 'is-neutral';
        }
    @endphp

    <div class="container-fluid attendance-shell">
        <div class="row">
            <div class="col-xl-8">
                <div class="card attendance-summary-card">
                    <div class="card-body">
                        <div class="attendance-summary-head">
                            <div>
                                <p class="attendance-kicker">Absensi Lembur</p>
                                <h2 class="attendance-shift-name">Absen Lembur</h2>
                                <p class="attendance-shift-window">{{ $tanggalTampil }}</p>
                            </div>

                            <div class="attendance-summary-badge">
                                <span class="attendance-summary-badge-label">Status Sesi</span>
                                <span class="lembur-status-pill {{ $sessionStatusClass }}">{{ $sessionStatusText }}</span>
                            </div>
                        </div>

                        <div class="attendance-meta-grid">
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Lokasi Kantor</span>
                                <strong>{{ $lokasiTampil }}</strong>
                            </div>
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Jam Masuk</span>
                                <strong>{{ $jamMasukTampil }}</strong>
                            </div>
                            <div class="attendance-meta-item">
                                <span class="attendance-meta-label">Jam Pulang</span>
                                <strong>{{ $jamKeluarTampil }}</strong>
                            </div>
                        </div>

                        <p class="lembur-summary-note">
                            Pengajuan lembur membutuhkan kamera dan lokasi aktif. Ambil foto dengan posisi wajah jelas sebelum menekan tombol aksi.
                        </p>

                        <form action="{{ url('/my-location') }}" method="get" class="d-none">
                            @csrf
                            <input type="hidden" name="lat" id="lat2" readonly>
                            <input type="hidden" name="long" id="long2" readonly>
                            <input type="hidden" name="userid" value="{{ auth()->user()->id }}">
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card attendance-clock-card-panel">
                    <div class="card-body">
                        <p class="attendance-kicker">Jam Real-Time</p>
                        <h3 class="attendance-clock-time-large" data-live-clock>00:00:00</h3>
                        <p class="attendance-clock-note-small">Status approval: {{ $statusApproval }}. Total lembur: {{ $totalLemburTampil }}.</p>
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
                            <p class="attendance-kicker">{{ $actionPhase === 'masuk' ? 'Masuk Lembur' : 'Pulang Lembur' }}</p>
                            <h2 class="attendance-action-title">
                                {{ $actionPhase === 'masuk' ? 'Ambil foto masuk lembur' : 'Ambil foto pulang lembur' }}
                            </h2>
                            <p class="attendance-action-text">
                                {{ $actionPhase === 'masuk'
                                    ? 'Mulai sesi lembur dengan foto yang jelas dan pastikan Anda masih berada di area kantor.'
                                    : 'Selesaikan sesi lembur dengan foto terbaru sebelum meninggalkan area kerja.' }}
                            </p>

                            <div class="attendance-info-list">
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">Tanggal</span>
                                    <strong>{{ $tanggalTampil }}</strong>
                                </div>
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">Status Approval</span>
                                    <strong><span class="lembur-status-pill {{ $approvalClass }}">{{ $statusApproval }}</span></strong>
                                </div>
                                <div class="attendance-info-item">
                                    <span class="attendance-info-label">{{ $actionPhase === 'masuk' ? 'Mode' : 'Jam Masuk' }}</span>
                                    <strong>{{ $actionPhase === 'masuk' ? 'Kamera dan lokasi aktif' : $jamMasukTampil }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="attendance-camera-panel">
                            <div class="attendance-camera-frame">
                                <div class="webcam attendance-webcam" id="results"></div>
                            </div>
                            <p class="attendance-camera-note lembur-photo-note">
                                Preview kamera akan tampil di area ini. Jangan menutup izin kamera dan lokasi selama proses lembur.
                            </p>
                        </div>
                    </div>

                    @if ($actionPhase === 'masuk')
                        <form method="post" action="{{ url('/lembur/masuk') }}" class="attendance-capture-form lembur-capture-form">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                            <input type="hidden" name="jam_masuk" value="{{ date('Y-m-d H:i') }}">
                            <input type="hidden" name="lat_masuk" id="lat">
                            <input type="hidden" name="long_masuk" id="long">
                            <input type="hidden" name="jarak_masuk">
                            <input type="hidden" name="status" value="Pending">
                            <input type="hidden" name="foto_jam_masuk" class="image-tag">
                            <button type="submit" class="btn btn-primary attendance-action-btn">
                                <i class="fas fa-camera mr-2"></i> Masuk Lembur
                            </button>
                        </form>
                    @elseif ($actionPhase === 'pulang' && $overtimeId)
                        <form method="post" action="{{ url('/lembur/pulang/'.$overtimeId) }}" class="attendance-capture-form lembur-capture-form">
                            @method('put')
                            @csrf
                            <input type="hidden" name="jam_keluar" value="{{ date('Y-m-d H:i') }}">
                            <input type="hidden" name="lat_keluar" id="lat">
                            <input type="hidden" name="long_keluar" id="long">
                            <input type="hidden" name="jarak_keluar">
                            <input type="hidden" name="foto_jam_keluar" class="image-tag">
                            <input type="hidden" name="total_lembur">
                            <button type="submit" class="btn btn-primary attendance-action-btn">
                                <i class="fas fa-camera mr-2"></i> Pulang Lembur
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('script')
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
                document.querySelectorAll('.attendance-webcam').forEach(function(element) {
                    element.style.transform = 'scaleX(-1)';
                    element.style.webkitTransform = 'scaleX(-1)';
                });

                const form = document.querySelector('.lembur-capture-form');

                const takeSnapshot = function (done) {
                    Webcam.snap(function (dataUri) {
                        const imageField = document.querySelector('.image-tag');
                        const resultsNode = document.getElementById('results');

                        if (imageField) {
                            imageField.value = dataUri;
                        }

                        if (resultsNode) {
                            resultsNode.innerHTML = '<img src="' + dataUri + '" alt="Preview Lembur">';
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
