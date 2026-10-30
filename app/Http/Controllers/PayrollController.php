<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payroll;
use App\Models\StatusPtkp;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
USE DB;
class PayrollController extends Controller
{
    public function index(Request $request)
    {
        return view('payroll.index', [
            'title' => 'Data Penggajian Karyawan'
        ]);
    }

    public function getSalary(){
        $query = "
        SELECT
            p.id,
            u.name as nama_karyawan,
            j.nama_jabatan as nama_jabatan,
            g.name as nama_golongan,
            p.user_id,
            DATE(p.created_at) as tanggal,
            u.upah_normal,
            p.upah_target,
            p.masuk_minggu,
            p.lembur,
            p.kasbon,
            p.denda
        FROM payrolls p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN jabatans j ON j.id = u.jabatan_id
        LEFT JOIN golongans g ON g.id = u.golongan_id
        ORDER BY nama_karyawan, nama_jabatan, nama_golongan
        ";
        // $data = DB::table('users')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
        $data = DB::select($query);
        return response()->json([
            'data'=>$data
        ]);
    }

    public function tambah()
    {
        return view('payroll.tambah', [
            'title' => 'Tambah Data Penggajian Karyawan',
            'data_user' => User::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'data_status' => StatusPtkp::select('id', 'name')->orderBy('name', 'ASC')->get()
        ]);
    }

    public function tambahProses(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'upah_target' => 'required',
            'masuk_minggu' => 'required',
            'lembur' => 'required',
            'kasbon' => 'required',
            'denda' => 'required',
            'tanggal' => 'nullable'
        ]);

        DB::table('payrolls')->insert([
            'user_id' => $request->user_id,
            'bulan' => now()->month,
            'tahun' => now()->year,
            // 'upah_normal' => $request->upah_normal,
            'upah_target' => str_replace(',', '', $request->upah_target),
            'masuk_minggu' => str_replace(',', '',$request->masuk_minggu),
            'lembur' => str_replace(',', '',$request->lembur),
            'kasbon' => str_replace(',', '',$request->kasbon),
            'denda' => str_replace(',', '',$request->denda),
            'status_id' => 2,
            'created_at' => $request->tanggal
        ]);
        return redirect('/payroll')->with('success', 'Data Berhasil di Tambahkan');
    }

    public function edit($id)
    {
        return view('payroll.edit', [
            'title' => 'Edit Data Penggajian Karyawan',
            'data' => Payroll::find($id),
            'data_user' => User::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'data_status' => StatusPtkp::select('id', 'name')->orderBy('name', 'ASC')->get(),
        ]);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'status_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'gaji' => 'required',
            'setoran_bpjs_kes' => 'nullable',
            'tunjangan_bpjs_kes' => 'nullable',
            'setoran_bpjs_tk' => 'nullable',
            'tunjangan_bpjs_tk' => 'nullable',
            'tunjangan_pensiun' => 'nullable',
            'tunjangan_komunikasi' => 'nullable',
            'tunjangan_pph_21' => 'nullable',
            'pot_lainnya' => 'nullable',
            'lembur' => 'nullable'
        ]);

        if(!$validated['gaji']){
            $validated['gaji'] = 0;
        }

        if(!$validated['setoran_bpjs_kes']){
            $validated['setoran_bpjs_kes'] = 0;
        }

        if(!$validated['tunjangan_bpjs_kes']){
            $validated['tunjangan_bpjs_kes'] = 0;
        }

        if(!$validated['setoran_bpjs_tk']){
            $validated['setoran_bpjs_tk'] = 0;
        }
        
        if(!$validated['tunjangan_bpjs_tk']){
            $validated['tunjangan_bpjs_tk'] = 0;
        }

        if(!$validated['tunjangan_pensiun']){
            $validated['tunjangan_pensiun'] = 0;
        }

        if(!$validated['tunjangan_komunikasi']){
            $validated['tunjangan_komunikasi'] = 0;
        }

        if(!$validated['tunjangan_pph_21']){
            $validated['tunjangan_pph_21'] = 0;
        }

        if(!$validated['pot_lainnya']){
            $validated['pot_lainnya'] = 0;
        }

        if(!$validated['lembur']){
            $validated['lembur'] = 0;
        }

        $validated['gaji'] = str_replace(',', '', $validated['gaji']);
        $validated['setoran_bpjs_kes'] = str_replace(',', '', $validated['setoran_bpjs_kes']);
        $validated['tunjangan_bpjs_kes'] = str_replace(',', '', $validated['tunjangan_bpjs_kes']);
        $validated['setoran_bpjs_tk'] = str_replace(',', '', $validated['setoran_bpjs_tk']);
        $validated['tunjangan_bpjs_tk'] = str_replace(',', '', $validated['tunjangan_bpjs_tk']);
        $validated['tunjangan_pensiun'] = str_replace(',', '', $validated['tunjangan_pensiun']);
        $validated['tunjangan_komunikasi'] = str_replace(',', '', $validated['tunjangan_komunikasi']);
        $validated['tunjangan_pph_21'] = str_replace(',', '', $validated['tunjangan_pph_21']);
        $validated['pot_lainnya'] = str_replace(',', '', $validated['pot_lainnya']);
        $validated['lembur'] = str_replace(',', '', $validated['lembur']);

        Payroll::where('id', $id)->update($validated);
        return redirect('/payroll')->with('success', 'Data Berhasil di Update');
    }
    
    public function delete($id)
    {
        Payroll::where('id', $id)->delete();
        return redirect('/payroll')->with('success', 'Data Berhasil di Hapus');
    }
    
    public function download($id)
    {
        $pdf = Pdf::loadView('payroll.download', [
            'title' => 'Kartu Pasien',
            'data' => Payroll::find($id)
        ]);

        return $pdf->stream();
    }
}
