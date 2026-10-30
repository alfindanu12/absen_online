@extends('layouts.dashboard')
@section('isi')
    <div class="container-fluid">
        <div class="card col-lg-12">
            <div class="mt-4 p-4">
                <form method="post" action="{{ url('/payroll/tambah-proses') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="col">
                            <label for="user_id">Pegawai</label>
                            <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true">
                                <option value="">Pilih Pegawai</option>
                                @foreach ($data_user as $du)
                                    @if(old('user_id') == $du->id)
                                        <option value="{{ $du->id }}" selected>{{ $du->name }}</option>
                                    @else
                                        <option value="{{ $du->id }}">{{ $du->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('user_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="col">
                            <label for="upah_target">Upah Target</label>
                            <input type="text" class="form-control money @error('upah_target') is-invalid @enderror" id="upah_target" name="upah_target" value="{{ old('upah_target') }}">
                            @error('upah_target')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="col">
                            <label for="masuk_minggu">Masuk Minggu</label>
                            <input type="text" class="form-control money @error('masuk_minggu') is-invalid @enderror" id="masuk_minggu" name="masuk_minggu" value="{{ old('masuk_minggu') }}">
                            @error('masuk_minggu')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="gaji">Lembur</label>
                            <input type="text" class="form-control money @error('lembur') is-invalid @enderror" id="lembur" name="lembur" value="{{ old('lembur') }}">
                            @error('lembur')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="col">
                            <label for="kasbon">Kasbon</label>
                            <input type="text" class="form-control money @error('kasbon') is-invalid @enderror" id="kasbon" name="kasbon" value="{{ old('kasbon') }}">
                            @error('kasbon')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="col">
                            <label for="denda">Denda</label>
                            <input type="text" class="form-control money @error('denda') is-invalid @enderror" id="denda" name="denda" value="{{ old('denda') }}">
                            @error('denda')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control date @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal') }}">
                            @error('tanggal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary float-right">Submit</button>
                  </form>
                  <br>
            </div>
        </div>
    </div>
    <br>
    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
        <script>
            $(document).ready(function(){
                $('.money').mask('000,000,000,000,000', {
                    reverse: true
                });
            });
        </script>
    @endpush
@endsection
