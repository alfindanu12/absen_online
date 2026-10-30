@extends('layouts.dashboard')
@section('isi')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <center>
                <a class="btn btn-primary" href="{{ url('/data-cuti/tambah') }}">+ Tambah Cuti Pegawai</a>
            </center>
        </div>
        <div class="card-body p-3">
                <table class="table table-striped" id="tableprint" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pegawai</th>
                            <th>Nama Cuti</th>
                            <th>Tanggal</th>
                            <th>Alasan Cuti</th>
                            <th>Foto</th>
                            <th>Status Cuti</th>
                            <th>Catatan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_cuti as $dc)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dc->name ?? 'Pegawai tidak ditemukan (ID: '.$dc->user_id.')' }}</td>
                            <td>{{ $dc->nama_cuti }}</td>
                            <td>{{ $dc->tanggal_awal}} s/d {{ $dc->tanggal_akhir }}</td>
                            <td>{{ $dc->alasan_cuti}}</td>
                            <td>
                                @if(!empty($dc->foto_cuti))
                                    <img src="{{ $dc->foto_cuti }}" style="width:150px" alt="">
                                @endif
                                <!-- <img src="{{ url('storage/'.$dc->foto_cuti) }}" style="width: 200px" alt=""> -->
                            </td>
                            <td>
                                @if($dc->status_cuti == "Diterima")
                                    <span class="badge badge-success">{{ $dc->status_cuti }}</span>
                                @elseif($dc->status_cuti == "Ditolak")
                                    <span class="badge badge-danger">{{ $dc->status_cuti }}</span>
                                @else
                                    <span class="badge badge-warning">{{ $dc->status_cuti }}</span>
                                @endif
                            </td>
                            <td>{{ $dc->catatan}}</td>
                            <td>
                                @if($dc->status_cuti == "Diterima")
                                    <span class="badge badge-success">Sudah Approve</span>
                                @else
                                    <a href="{{ url('/data-cuti/edit_induk/'.$dc->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-triangle"></i></a>
                                @endif

                                @if($dc->status_cuti == "Diterima")
                                    <span class="badge badge-success">Sudah Approve</span>
                                @else
                                    <form action="{{ url('/data-cuti/delete_induk/'.$dc->id) }}" method="post" class="d-inline">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-danger btn-sm" onClick="return confirm('Are You Sure')"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>
</div>
<br>
@endsection
