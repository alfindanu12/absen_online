        <div class='row' id='mingguan-type' style='display:none;'>
            <div class='row col-12'>
                <div class='col-4'>
                    <div class='form-group'>
                        <label for="lokasi">Cabang</label>
                        <select name="lokasi" id="lokasi" class='form-control'>
                            <option value="0" selected>Semua</option>
                            @foreach ($cabang as $caba => $cab)
                            <option value="{{$cab->id}}">{{ $cab->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='col-4'>
                    <div class='form-group'>
                        <label for="jabatan">Divisi</label>
                        <select name="jabatan" id="jabatan" class='form-control'>
                            <option value="0" selected>Semua</option>
                            @foreach ($jabatan as $jabatan => $jab)
                            <option value="{{$jab->id}}">{{ $jab->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='col-4'>
                    <div class='form-group'>
                        <label for="karyawan">Karyawan</label>
                        <select name="karyawan" id="karyawan" class='form-control'>
                            <option value="" selected>Semua</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class='row col-12'>
                <div class='col-4'>
                    <div class='form-group'>
                        <label for="startFrom">From</label>
                        <input type="date" id='startFrom' class='form-control'/>
                    </div>
                </div>
                <div class='col-4'>
                    <div class='form-group'>
                        <label for="endTo">To</label>
                        <input class='form-control' type='date' id='endTo' />
                    </div>
                </div>
                <div class='col-4'>
                    <div class='form-group'>
                        <label for="printMingguan">&nbsp;</label>
                        <input class='btn btn-success form-control' type='button' id='printMingguan' value='Cetak'/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='card-body' id='mingguan-tabel'  style='display:none'>
        <table class="table table-sm table-bordered table-hover" id='table-mingguan' width='100%'>
            <thead class='text-center'>
                <tr>
                    <th width="10px">No</th>
                    <th>Nama Karyawan</th>
                    <th>Total Upah Harian</th>
                    <th>Jumlah Hadir</th>
                    <th>Upah Target</th>
                    <th>Upah Lembur</th>
                    <th>Masuk Minggu</th>
                    <th>Kasbon</th>
                    <th>Denda</th>
                    <th>Total Terima Gaji</th>
                    <th>Print Slip Gaji</th>
                </tr>
            </thead>
            <tbody id='data-gaji-karyawan'>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="9" class="text-right">Total Gaji</th>
                    <th id="total-gaji-karyawan" class="text-left">Rp. -</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>