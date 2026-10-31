@extends('layouts.dashboard')
@section('isi')
    @push('style')
        <style>
            .face-enroll-page {
                width: 100%;
                max-width: 760px;
                margin: 0 auto;
                padding: 0 12px 24px;
            }

            .face-enroll-card {
                overflow: hidden;
                border: 0;
                border-radius: 18px;
                box-shadow: 0 10px 28px rgba(25, 42, 70, .09);
            }

            .face-enroll-body {
                padding: 24px;
            }

            .face-enroll-title {
                margin-bottom: 6px;
                font-size: 1.15rem;
                font-weight: 700;
            }

            .face-enroll-help {
                margin-bottom: 18px;
                color: #687386;
                font-size: .92rem;
            }

            .face-enroll-camera {
                overflow: hidden;
                width: 100%;
                border-radius: 14px;
                background: #111827;
                line-height: 0;
            }

            #video {
                display: block;
                width: 100%;
                height: min(62vh, 520px);
                min-height: 260px;
                object-fit: cover;
                transform: scaleX(-1);
                -webkit-transform: scaleX(-1);
            }

            .face-enroll-status {
                min-height: 24px;
                margin: 12px 0 0;
                color: #687386;
                font-size: .88rem;
            }

            #capture {
                min-height: 48px;
                padding: 10px 22px;
                border-radius: 10px;
                font-weight: 700;
            }

            @media (max-width: 575.98px) {
                .face-enroll-page {
                    padding: 0 10px 18px;
                }

                .face-enroll-card {
                    border-radius: 14px;
                }

                .face-enroll-body {
                    padding: 16px 14px 18px;
                }

                .face-enroll-help {
                    margin-bottom: 12px;
                    font-size: .86rem;
                }

                .face-enroll-camera {
                    border-radius: 12px;
                }

                #video {
                    height: min(56vh, 430px);
                    min-height: 220px;
                }

                #capture {
                    width: 100%;
                    margin-top: 10px !important;
                }
            }
        </style>
    @endpush

    <div class="face-enroll-page">
        <div class="card face-enroll-card">
            <div class="face-enroll-body">
                <h2 class="face-enroll-title">Daftarkan Wajah</h2>
                <p class="face-enroll-help">Posisikan wajah di tengah kamera dengan pencahayaan yang cukup, lalu tekan tombol di bawah.</p>
                <div class="form-group mb-3">
                    <label for="name">Nama Pegawai</label>
                    <input type="text" class="form-control" value="{{ $karyawan->name }}" disabled id="name">
                </div>
                <input type="hidden" name="username" id="username" value="{{ $karyawan->username }}">
                <div class="face-enroll-camera">
                    <video id="video" autoplay muted playsinline></video>
                </div>
                <p id="camera-status" class="face-enroll-status" role="status">Menyiapkan kamera...</p>
                <button id="capture" class="btn btn-primary mt-2" type="button" disabled>
                    <i class="fas fa-camera mr-2" aria-hidden="true"></i>Ambil Foto Wajah
                </button>
            </div>
        </div>
    </div>
    @push('script')
        <script src="{{ url('/face-runtime/dist/face-api.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let video = document.getElementById("video");
            let width = 800;
            let height = 600;
            const statusEl = document.getElementById('camera-status');
            const captureButton = document.getElementById('capture');
            const captureButtonLabel = '<i class="fas fa-camera mr-2" aria-hidden="true"></i>Ambil Foto Wajah';

            const setButtonBusy = (label) => {
                captureButton.disabled = true;
                captureButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + label;
            };

            const setButtonReady = () => {
                captureButton.disabled = false;
                captureButton.innerHTML = captureButtonLabel;
            };

            const startSteam = () => {
                navigator.mediaDevices.getUserMedia({
                    video: {facingMode: "user", width: {ideal: width}, height: {ideal: height}},
                    audio : false
                }).then((steam) => {
                    video.srcObject = steam;
                    video.style.transform = 'scaleX(-1)';
                    video.style.webkitTransform = 'scaleX(-1)';
                    video.onloadedmetadata = () => {
                        video.play();
                        captureButton.disabled = false;
                        statusEl.textContent = 'Kamera siap. Pastikan wajah terlihat jelas.';
                    };
                }).catch(() => {
                    statusEl.textContent = 'Kamera tidak dapat dibuka. Periksa izin kamera browser dan coba muat ulang halaman.';
                });
            }

            const modelPath = "{{ url('/face-runtime/weights') }}";
            const prepareFaceRecognition = async () => {
                statusEl.textContent = 'Memuat model deteksi wajah...';
                await faceapi.nets.tinyFaceDetector.loadFromUri(modelPath);
                statusEl.textContent = 'Memuat model titik wajah...';
                await faceapi.nets.faceLandmark68Net.loadFromUri(modelPath);
                statusEl.textContent = 'Memuat model pengenalan wajah...';
                await faceapi.nets.faceRecognitionNet.loadFromUri(modelPath);
                statusEl.textContent = 'Membuka kamera...';
                startSteam();
            };

            prepareFaceRecognition().catch(() => {
                statusEl.textContent = 'Model pengenalan wajah gagal dimuat. Periksa koneksi lalu muat ulang halaman.';
            });

            $(document).ready(async function(){
                $("#capture").click(async function(){
                    if (captureButton.disabled || !video.videoWidth || !video.videoHeight) return;
                    setButtonBusy('Memeriksa wajah...');
                    statusEl.textContent = 'Memeriksa wajah...';
                    try {
                        const username = $('#username').val();
                        const canvas = document.createElement('canvas');
                        canvas.width = 800;
                        canvas.height = 600;
                        const context = canvas.getContext('2d');
                        const sourceWidth = video.videoWidth;
                        const sourceHeight = video.videoHeight;
                        const sourceRatio = sourceWidth / sourceHeight;
                        const targetRatio = canvas.width / canvas.height;
                        let cropX = 0;
                        let cropY = 0;
                        let cropWidth = sourceWidth;
                        let cropHeight = sourceHeight;
                        if (sourceRatio > targetRatio) {
                            cropWidth = sourceHeight * targetRatio;
                            cropX = (sourceWidth - cropWidth) / 2;
                        } else {
                            cropHeight = sourceWidth / targetRatio;
                            cropY = (sourceHeight - cropHeight) / 2;
                        }
                        context.save();
                        context.translate(canvas.width, 0);
                        context.scale(-1, 1);
                        context.drawImage(video, cropX, cropY, cropWidth, cropHeight, 0, 0, canvas.width, canvas.height);
                        context.restore();
                        const img = document.createElement('img');
                        img.src = canvas.toDataURL('image/png');
                        await img.decode();

                        statusEl.textContent = 'Mencari wajah pada foto...';
                        setButtonBusy('Mencari Wajah...');
                        const detections = await faceapi
                            .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({inputSize: 320, scoreThreshold: 0.5}))
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                        if (!detections) {
                            setButtonReady();
                            statusEl.textContent = 'Wajah belum terdeteksi. Dekatkan wajah, hadap kamera, lalu coba lagi.';
                            return;
                        }

                        $.ajaxSetup({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                        });
                        statusEl.textContent = 'Menyimpan foto wajah...';
                        setButtonBusy('Menyimpan Foto...');
                        await $.ajax({
                            type: 'POST',
                            url: "{{ url('/pegawai/face/ajaxPhoto') }}",
                            data: {image: img.src, path: username},
                            cache: false
                        });

                        statusEl.textContent = 'Menyimpan data pengenalan wajah...';
                        setButtonBusy('Menyimpan Data...');
                        const postData = new faceapi.LabeledFaceDescriptors(username, [detections.descriptor]);
                        await $.ajax({
                            type: 'POST',
                            url: "{{ url('/pegawai/face/ajaxDescrip') }}",
                            data: {myData: JSON.stringify(postData)},
                            dataType: 'json',
                            cache: false
                        });

                        statusEl.textContent = 'Wajah berhasil didaftarkan. Mengalihkan halaman...';
                        Swal.fire('Berhasil Daftar Wajah!', '', 'success');
                        setTimeout(function() {
                            window.location.href = "{{ url('/pegawai') }}";
                        }, 1800);
                    } catch (error) {
                        setButtonReady();
                        statusEl.textContent = 'Proses gagal. Periksa koneksi internet dan izin kamera, lalu coba lagi.';
                    }
                });
            });
        </script>
    @endpush
@endsection
