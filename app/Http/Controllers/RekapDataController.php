<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Lembur;
use App\Models\User;
use App\Models\MappingShift;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class RekapDataController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $tanggal_mulai = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $title = "Rekap Data Absensi Tanggal " . date('Y-m-01') . " s/d " . date('Y-m-d');
        $get_lokasi = $get_jabatan = $get_pegawai = "";
        if($request['submit']){
            $get_tanggal_mulai  = $request['mulai'];
            $get_tanggal_selsai = $request['akhir'];
            $get_lokasi         = $request['lokasi'];
            $get_jabatan        = $request['jabatan_id'];
            $get_pegawai        = $request['user_id'];
            //echo $get_pegawai."<br>".$get_jabatan."<br>".$get_lokasi."<hr>";
            
            if(!empty($get_pegawai) && $get_pegawai != "0"){
                $user = User::where('id', $get_pegawai)->get();
            } else {
                if(!empty($get_jabatan) && $get_jabatan != "0"){
                    $user = User::where('golongan_id', $get_jabatan)->get();
                } else {
                    if(!empty($get_lokasi) && $get_lokasi != "0"){
                        $user = User::where('lokasi_id', $get_lokasi)->get();
                    } else {
                        $user = User::all();
                    }
                }
            }
        } else {
            $user = User::all();
        }

        if ($request["mulai"] && $request["akhir"]) {
            $tanggal_mulai = $request["mulai"];
            $tanggal_akhir = $request["akhir"];
            $title = "Rekap Data Absensi Tanggal " . $tanggal_mulai . " s/d " . $tanggal_akhir;
        }
        
        return view('rekapdata.index', [
            'title' => $title,
            'data_user' => $user, 
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'golongan' => Golongan::select('id', 'name')->get(),
            'cabang' => Lokasi::all(),
            'lokasi_id' => $get_lokasi,
            'jabatan_id' => $get_jabatan,
            'pegawai_id' => $get_pegawai
        ]);
    }
}
