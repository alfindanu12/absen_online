@extends('layouts.dashboard')
@section('isi')
    <div class="container-fluid">
        <div class="card" style="border-radius: 20px">
            <div class="card-header">
                <a href="#" class="btn btn-sm btn-primary" style="border-radius: 10px" data-toggle="modal" data-target="#modal-add">+ Tambah Data Salary</a>
            </div>
            <div class="card-body">
                <table id="tableDailySalary" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Cabang</th>
                            <th>Divisi</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Akhir</th>
                            <th>Gaji Harian</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <br>
    <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddLabel">Tambah Data Upah Harian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-salary">
                        <div class='form-row'>
                            <div class='col'>
                                <label for="lokasi_id">Lokasi</label>
                                <select name="lokasi_id" id="lokasi_id" class='form-control select-picker' data-live-search='true'>
                                    <option>Pilih Lokasi</option>
                                    @foreach($cabang as $cb)
                                    <option value='{{ $cb->id }}'>{{ $cb->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                         <div class='form-row'>
                            <div class='col'>
                                <label for="divisi_id">Divisi</label>
                                <select name="divisi_id" id="divisi_id" class='form-control select-picker' data-live-search='true'>
                                    <option>Pilih Divisi</option>
                                </select>
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='col'>
                                <label for="user_id">Pegawai</label>
                                <select name="user_id" id="user_id" class='form-control select-picker' data-live-search='true'>
                                    <option value=''>Pilih Pegawai</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="datetime" class="form-control @error('start_date') is-invalid @enderror" id="start_date">
                                @error('start_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="end_date">Tanggal Selesai</label>
                                <input type="datetime" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date">
                                @error('end_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="spd">Gaji Harian</label>
                                <input type="text" class="form-control @error('spd') is-invalid @enderror" id="spd" name="spd">
                                @error('spd')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-add">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Data Upah Harian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-salary" data-id="">
                        <div class="form-row">
                            <div class="col">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="text" class="form-control @error('start_date') is-invalid @enderror" id="start_date">
                                @error('start_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="end_date">Tanggal Selesai</label>
                                <input type="text" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date">
                                @error('end_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="spd">Gaji Harian</label>
                                <input type="text" class="form-control @error('spd') is-invalid @enderror" id="spd" name="spd">
                                @error('spd')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-edit">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript">


$(document).ready(function() {
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Format SPD input field on user input
    $('#spd').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digit characters
        if (value) {
            value = formatNumber(value); // Format number with thousands separator
        }
        $(this).val(value); // Set formatted value back to the input
    });
    // Fetch employee options when the "Tambah Data Salary" button is clicked
    //$('#modal-add').on('show.bs.modal', function() {
    $("#lokasi_id").change(function(){
        var lokasiId = $("#lokasi_id").val();
        $('#divisi_id').empty();
        $('#user_id').empty();
         $.ajax({
            url: '{{ route("get-divisi-by-lokasi") }}',
            type: 'GET',
            data: {
                lokasi_id: lokasiId
            },
            success: function(response){
                $('#divisi_id').empty();
                // if(lokasiId == 0){
                    $('#divisi_id').append('<option value=0>Pilih Divisi</option>');
                    $.each(response.data, function(index, divisi){
                        $('#divisi_id').append('<option value = "'+ divisi.id + '">'+divisi.name + '</option>');
                    });
                // }
            },
            error: function(xhr){
                console.log(xhr.responseText);
            }
        });
    })
    $("#divisi_id").change(function(){
        var lokasi_id = $("#lokasi_id").val();
        var divisi_id = $("#divisi_id").val();
        //alert(lokasi_id);
        $('#user_id').empty();
        $.ajax({
           // url: "{{ url('/get-data-karyawan-filter') }}", // Adjust URL to your endpoint
            url : '/get-data-karyawan-filter/'+lokasi_id+'/'+divisi_id,
            method: "GET",
            success: function(data) {
                let employeeSelect = $('#user_id');
                employeeSelect.empty(); // Clear existing options
                employeeSelect.append('<option value="">Pilih Pegawai</option>'); // Add default option
                let dataPegawai = data.data;
                dataPegawai.forEach(function(employee) {
                    employeeSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                });

                // Refresh select-picker if necessary
                //$('.select-picker').selectpicker('refresh');
            },
            error: function(error) {
                console.log("Error fetching employees:", error);
            }
        });
    });
    // Add any remaining AJAX actions for Edit functionality here, if needed.
});

    $(document).ready(function() {
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Format input field on user input
        $('#spd').on('input', function() {
            let value = $(this).val().replace(/\D/g, ''); // Remove non-digit characters
            if (value) {
                value = formatNumber(value); // Format number with thousands separator
            }
            $(this).val(value); // Set formatted value back to the input
        });

        // Initialize DataTable
        var tableSalary = $("#tableDailySalary").DataTable({
            processing: true,
            ajax: {
                url: '{{ route("get-daily-salary") }}',
            },
            columns: [
                {
                    data: null, 
                    name: 'no', 
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1; // Display row number
                    }
                },
                {
                    data: 'name', name: 'name'
                },
                {
                    data: 'nama_cabang', name: 'nama_cabang'
                },
                {
                    data: 'divisi', name: 'divisi'
                },
                {
                    data: 'start_date', name: 'start_date', className: 'text-center'
                },
                {
                    data: 'end_date', name: 'end_date', className: 'text-center'
                },
                {
                    data: 'spd', name: 'spd', className: 'text-center', 
                    render: function(data, type, row) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(data); // Format spd as currency IDR
                    }
                },
                {
                    data: null, name: 'action', 
                    render: function(data, type, row) {
                        return `
                            <a class="btn btn-info edit-data" data-id="${data.id}">
                                <i class="fa fa-edit fa-xs"></i>
                            </a>
                            <a class="btn btn-danger delete-data" data-id="${data.id}">
                                <i class="fa fa-trash fa-xs"></i>
                            </a>
                        `;
                    }
                }
            ],
        });

        // Edit button action
        $("#tableDailySalary").on('click', '.edit-data', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route("get-daily-salary-by-id", ":id") }}'.replace(':id', id), // Dynamic ID replacement
                method: 'GET',
                success: function(response) {
                    var data = response.data;
                    // Fill the form with data from response
                    $('#form-edit-salary').data('id', id); // Store ID in form for later use
                    $('#modal-edit #start_date').val(data.start_date);
                    $('#modal-edit #end_date').val(data.end_date);
                    $('#modal-edit #spd').val(data.spd); // Set spd value
                    $('#modal-edit').modal('show'); // Show the modal
                },
                error: function(xhr) {
                    alert('Failed to retrieve data: ' + xhr.responseText);
                }
            });
        });

        // Save edited data
        $("#save-edit").on('click', function() {
            var id = $('#form-edit-salary').data('id'); // Get ID from form
            var spdValue = $('#modal-edit #spd').val().replace(/\./g, ''); // Remove thousands separator

            // Ensure SPD is a valid number
            if (isNaN(spdValue) || spdValue === '') {
                alert('Please enter a valid SPD value.');
                return;
            }

            var data = {
                start_date: $('#modal-edit #start_date').val(),
                end_date: $('#modal-edit #end_date').val(),
                spd: spdValue, // Send cleaned-up SPD value
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route("update-daily-salary", ":id") }}'.replace(':id', id), // Dynamic ID replacement
                method: 'PUT', // Use PUT for updating data
                data: data,
                success: function(response) {
                    alert('Data successfully updated!');
                    $('#tableDailySalary').DataTable().ajax.reload(); // Refresh DataTable
                    $('#modal-edit').modal('hide'); // Close modal
                },
                error: function(xhr) {
                    alert('Error occurred: ' + xhr.responseText);
                }
            });
        });

        $("#save-add").on('click', function() {
            var id = $('#form-add-salary').data('id'); // Get ID from form
            var spdValue = $('#modal-add #spd').val().replace(/\./g, ''); // Remove thousands separator

            // Ensure SPD is a valid number
            if (isNaN(spdValue) || spdValue === '') {
                alert('Please enter a valid SPD value.');
                return;
            }

            var data = {
                user_id: $('#modal-add #user_id').val(),
                start_date: $('#modal-add #start_date').val(),
                end_date: $('#modal-add #end_date').val(),
                spd: spdValue, // Send cleaned-up SPD value
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route("set-daily-salary") }}',
                method: 'POST', // Use PUT for updating data
                data: data,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Berhasil Ditambahkan!',
                        text: 'Data upah harian telah berhasil ditambahkan.',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $('#tableDailySalary').DataTable().ajax.reload(); // Refresh DataTable
                    $('#modal-add').modal('hide'); // Close modal
                },
                error: function(xhr) {
                    alert('Error occurred: ' + xhr.responseText);
                }
            });
        });
    });
    // Event listener for delete button
$('#tableDailySalary').on('click', '.delete-data', function() {
    var id = $(this).data('id'); // Get the data-id of the item to delete

    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with AJAX delete if confirmed
            $.ajax({
                url: '{{ route("delete-daily-salary", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}' // Include CSRF token
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Your data has been deleted.',
                        'success'
                    );
                    $('#tableDailySalary').DataTable().ajax.reload(); // Refresh the table
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'Failed to delete data: ' + xhr.responseText,
                        'error'
                    );
                }
            });
        }
    });
});
</script>

    @endpush
@endsection
