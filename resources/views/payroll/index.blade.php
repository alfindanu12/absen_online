@extends('layouts.dashboard')
@section('isi')
    <div class="container-fluid">
        <div class="card" style="border-radius: 20px">
            <div class="card-header">
                <a href="{{ url('/payroll/tambah') }}" class="btn btn-sm btn-primary" style="border-radius: 10px">+ Tambah Data Payroll</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="tablePayroll">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Upah Normal</th>
                            <th>Upah Target</th>
                            <th>Masuk Minggu</th>
                            <th>Lembur</th>
                            <th>Kasbon</th>
                            <th>Denda</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data payroll akan dimuat di sini -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@push('script')
<script type="text/javascript">
    // Format angka menjadi format Rupiah
    function formatRupiah(angka, prefix = 'Rp. ') {
    if (angka === null || angka === undefined) {
        return prefix + '0'; // or handle as needed, like returning an empty string
    }
    
    var number_string = angka.toString(),
        sisa  = number_string.length % 3,
        rupiah = number_string.substr(0, sisa),
        ribuan = number_string.substr(sisa).match(/\d{3}/g);
    
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return prefix + rupiah;
}


    // DataTable initialization
    var table = $('#tablePayroll').DataTable({
        processing: true,
        ajax: {
            url: '{{ route("get-salary") }}',
        },
        columns: [
            {
                data: null, name: 'no', className: 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; // Menambahkan nomor urut
                }
            },
            { data: 'nama_karyawan' },
            { data: 'tanggal' },
            {
                data: 'upah_normal', 
                render: function(data) {
                    return formatRupiah(data); // Format uang
                }
            },
            {
                data: 'upah_target', 
                render: function(data) {
                    return formatRupiah(data);
                }
            },
            {
                data: 'masuk_minggu', 
                render: function(data) {
                    return formatRupiah(data);
                }
            },
            {
                data: 'lembur', 
                render: function(data) {
                    return formatRupiah(data);
                }
            },
            {
                data: 'kasbon', 
                render: function(data) {
                    return formatRupiah(data);
                }
            },
            {
                data: 'denda', 
                render: function(data) {
                    return formatRupiah(data);
                }
            },
            {
                data: null, 
                render: function(data) {
                    return `
                        <a href="{{ url('payroll/edit') }}/${data.id}" class="btn btn-info btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-danger btn-sm delete-data" data-id="${data.id}">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    `;
                }
            }
        ],
    });

    // Event handler untuk tombol delete
    $(document).on('click', '.delete-data', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this payroll data?')) {
            // Kirim request untuk menghapus data
            $.ajax({
                url: '/payroll/delete/' + id,
                method: 'DELETE',
                success: function(response) {
                    table.ajax.reload(); // Reload table setelah delete
                    alert('Data berhasil dihapus.');
                },
                error: function(err) {
                    alert('Gagal menghapus data.');
                }
            });
        }
    });
</script>
    @endpush
@endsection