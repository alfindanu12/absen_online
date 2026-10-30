<div class='row' id='bulanan-type'>
    <div class='col-5'>
        <div class='form-group'>
            <label for="startFromMonth">From</label>
            <input type="date" name="startFromMonth" id="startFromMonth" class='form-control'>
        </div>
    </div>
    <div class='col-5'>
        <div class='form-group'>
            <label for="toEndMonth">To</label>
            <input type="date" class='form-control' name="toEndMonth" id="toEndMonth">
        </div>
    </div>
    <div class='col-2'>
        <div class='form-group'>
            <label for="print">&nbsp;</label>
            <input class='btn btn-success form-control' type="button" name="print" id="printBulanan" value="Cetak">
        </div>
    </div>
</div>

<div class="card-body" id='bulanan-tabel'>
    <table class="table table-sm table-bordered table-hover" id='table-bulanan'>
        <thead class='text-center'>
            <tr>
                <th width="10px">No</th>
                <th>Divisi</th>
                <th width='150px'>Jumlah Karyawan</th>
                <th>Total Lembur</th>
                <th>Sub Total Gaji</th>
            </tr>
        </thead>
        <tbody id='data-gaji'>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Gaji</th>
                <th id="total-gaji" class="text-left">Rp. -</th>
            </tr>
        </tfoot>
    </table>
</div>
            