<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\MappingShift;
use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\Payroll;
use App\Models\Daily;
use App\Models\ResetCuti;
use DB;

class DailyController extends Controller
{
    public function index(Request $request)
    {
        // $data = DB::table('salary_daily as sd')
        //     ->join('users as u', 'u.id', '=', 'sd.user_id')
        //     ->join('golongans as g', 'g.id', '=', 'u.golongan_id')
        //     ->join('lokasis as l', 'l.id', '=', 'u.lokasi_id')
        //     ->select(
        //         'sd.id as id',
        //         'u.name as name',
        //         'l.nama_lokasi as nama_cabang',
        //         'g.name as divisi',
        //         'sd.start_date as start_date', 'sd.end_date as end_date', 'sd.salary_per_day as spd'
        //     )
        //     ->orderBy('divisi','ASC')
        //     ->orderBy('id','ASC')
        //     ->get();
        $data_cabang = DB::select('SELECT * FROM lokasis');

        return view('daily.index', [
            'title' => 'Data Gaji Harian',
            'cabang' =>$data_cabang
        ]);
    }
    public function setDailySalary(Request $request){
        $validatedData = $request->validate([
            'user_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'spd' => 'required',
        ]);

        // Temukan record yang sesuai berdasarkan ID
        $salary = New Daily();
        $salary->user_id = $request->user_id;
        $salary->start_date = $request->start_date;
        $salary->end_date = $request->end_date;
        $salary->salary_per_day = $request->spd;
        $salary->save();
        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }
    public function getDailySalary(){
        $data = DB::table('salary_daily as sd')
            ->join('users as u', 'u.id', '=', 'sd.user_id')
            ->join('golongans as g', 'g.id', '=', 'u.golongan_id')
            ->join('lokasis as l', 'l.id', '=', 'u.lokasi_id')
            ->select(
                'sd.id as id',
                'u.name as name',
                'l.nama_lokasi as nama_cabang',
                'g.name as divisi',
                'sd.start_date as start_date', 'sd.end_date as end_date', 'sd.salary_per_day as spd'
            )
            ->orderBy('divisi','ASC')
            ->orderBy('id','ASC')
            ->get();
            return response()->json([
                'data' => $data
            ]);
    }

    public function getDailySalaryById($id){
        $data = DB::table('salary_daily as sd')
            ->join('users as u', 'u.id', '=', 'sd.user_id')
            ->join('golongans as g', 'g.id', '=', 'u.golongan_id')
            ->join('lokasis as l', 'l.id', '=', 'u.lokasi_id')
            ->select(
                'sd.id as id',
                'u.name as name',
                'l.nama_lokasi as nama_cabang',
                'g.name as divisi',
                'sd.start_date as start_date',
                'sd.end_date as end_date',
                'sd.salary_per_day as spd'
            )
            ->where('sd.id', $id)
            ->orderBy('divisi','ASC')
            ->orderBy('sd.id','ASC')
            ->first();
            return response()->json([
                'data' => $data
            ]);
    }
    public function updateDailySalary(Request $request, $id){
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'spd' => 'required',
        ]);

        // Temukan record yang sesuai berdasarkan ID
        $salary = Daily::find($id);

        if ($salary) {
            // Update data
            $salary->start_date = $request->start_date;
            $salary->end_date = $request->end_date;
            $salary->salary_per_day = $request->spd;
            $salary->save();

            return response()->json(['message' => 'Data berhasil diupdate'], 200);
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }
    public function deleteDailySalary(Request $request, $id){
        $salary = Daily::find($id);
        if ($salary) {
            $salary->delete();
            return response()->json(['message' => 'Data berhasil didelete'], 200);
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }


}
