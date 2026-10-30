<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\dinasLuar;
use App\Models\Jabatan;
use App\Models\Lembur;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\MappingShift;
use App\Models\ResetCuti;
use App\Models\Shift;
use App\Models\Sip;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Imports\UsersImport;
use App\Models\Golongan;
use App\Models\Cabang;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Carbon\Carbon;

class karyawanController extends Controller
{
    public function index(Request $request)
    {
        $data = User::orderBy('name', 'ASC');
        if($request['search']){
            $searchTerm = $request['search'];
            $data = $data->where('name', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('email', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('telepon', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('username', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhereHas('Jabatan', function ($query) use ($searchTerm) {
                        $query->where('nama_jabatan', 'LIKE', '%'.$searchTerm.'%');
                    })
                    ->orWhereHas('Golongan', function ($query) use ($searchTerm) {
                        $query->where('name', 'LIKE', '%'.$searchTerm.'%');
                    });
        }

        return view('karyawan.index', [
            'title' => 'Pegawai',
            'data_user' => $data->paginate(10)->withQueryString()
        ]);
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xls,xlsx,csv|max:5000'
        ]);
        $file = $request->file('file_excel');
        $nama_file = $file->getClientOriginalName();
        $file->move('DataUser', $nama_file);

        Excel::import(new UsersImport, public_path('/DataUser/'.$nama_file));
        return back()->with('success', 'Data Berhasil Di Import');
    }

    public function tambahKaryawan()
    {
        return view('karyawan.tambah',[
            "title" => 'Tambah Pegawai',
            "data_jabatan" => Jabatan::all(),
            "data_golongan" => Golongan::all(),
            "data_lokasi" => Lokasi::where('status', 'approved')->get()
        ]);
    }

    public function tambahKaryawanProses(Request $request)
    {
        if($request["cuti_dadakan"] == null) {
            $request["cuti_dadakan"] = "0";
        } else {
            $request["cuti_dadakan"];
        }

        if($request["cuti_bersama"] == null) {
            $request["cuti_bersama"] = "0";
        }  else {
            $request["cuti_bersama"];
        }

        if($request["cuti_menikah"] == null) {
            $request["cuti_menikah"] = "0";
        }  else {
            $request["cuti_menikah"];
        }

        if($request["cuti_diluar_tanggungan"] == null) {
            $request["cuti_diluar_tanggungan"] = "0";
        }  else {
            $request["cuti_diluar_tanggungan"];
        }

        if($request["cuti_khusus"] == null) {
            $request["cuti_khusus"] = "0";
        }  else {
            $request["cuti_khusus"];
        }

        if($request["cuti_melahirkan"] == null) {
            $request["cuti_melahirkan"] = "0";
        }  else {
            $request["cuti_melahirkan"];
        }

        if($request["izin_telat"] == null) {
            $request["izin_telat"] = "0";
        }  else {
            $request["izin_telat"];
        }

        if($request["izin_pulang_cepat"] == null) {
            $request["izin_pulang_cepat"] = "0";
        }  else {
            $request["izin_pulang_cepat"];
        }


        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'foto_karyawan' => 'required|image|file|max:10240',
            'email' => 'required|email:dns|unique:users',
            'telepon' => 'required',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|min:6|max:255',
            'tgl_lahir' => 'required',
            'gender' => 'required',
            'tgl_join' => 'required',
            'status_nikah' => 'required',
            'alamat' => 'required',
            'cuti_dadakan' => 'required',
            'cuti_bersama' => 'required',
            'cuti_menikah' => 'required',
            'cuti_diluar_tanggungan' => 'required',
            'cuti_khusus' => 'required',
            'cuti_melahirkan' => 'required',
            'izin_telat' => 'required',
            'izin_pulang_cepat' => 'required',
            'is_admin' => 'required',
            'jabatan_id' => 'required',
            'golongan_id' => 'required',
            'lokasi_id' => 'required',
        ]);

        if ($request->file('foto_karyawan')) {
            $validatedData['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan', 'public');
        }

        $validatedData['password'] = Hash::make($validatedData['password']);
        // $validatedData['kasbon'] = $request->kasbon;
        // $validatedData['upah_normal'] = $request->upah_normal;
        // $validatedData['upah_target'] = $request->upah_target;
        // $validatedData['masuk_minggu'] = $request->masuk_minggu;
        // $validatedData['upah_lembur'] = $request->upah_lembur;
        User::create($validatedData);
        return redirect('/pegawai')->with('success', 'Data Berhasil di Tambahkan');
    }

    public function detail($id)
    {
        return view('karyawan.editkaryawan', [
            'title' => 'Detail Pegawai',
            'karyawan' => User::find($id),
            'data_jabatan' => Jabatan::all(),
            "data_golongan" => Golongan::all(),
            'data_lokasi' => Lokasi::where('status', 'approved')->get()
        ]);
    }

    public function setUpahKaryawan(Request $request){
        
        $request->validate([
            // 'upah_normal' => 'required|numeric',
            'upah_target' => 'required|numeric',
            'masuk_minggu' => 'required|numeric',
            'lembur' => 'required|numeric',
            'kasbon' => 'required|numeric',
            'denda' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        DB::table('payrolls')->insert([
            'user_id' => $request->user_id,
            'bulan' => now()->month,
            'tahun' => now()->year,
            // 'upah_normal' => $request->upah_normal,
            'upah_target' => $request->upah_target,
            'masuk_minggu' => $request->masuk_minggu,
            'lembur' => $request->lembur,
            'kasbon' => $request->kasbon,
            'denda' => $request->denda,
            'status_id' => 2,
            'created_at' => now()
        ]);

        return response()->json(['success' => true]);
    }
    public function getDataKaryawan(){
        $data = User::where('is_admin','user')->get();
        return response()->json(['data'=>$data]);
    }
     public function getDataKaryawanFilter($id, $id1){
        $data = User::where(['is_admin'=>'user','lokasi_id'=>$id, 'golongan_id'=>$id1])->get();
        return response()->json(['data'=>$data]);
    }
    public function getUpahKaryawan(Request $request){
        $user_id = $request->get('user_id');

        $query = "SELECT
                    p.id, p.user_id, DATE(p.created_at) as tanggal, u.upah_normal, p.upah_target, p.masuk_minggu, p.lembur, p.kasbon, p.denda
                FROM payrolls p LEFT JOIN users u ON p.user_id = u.id
                    WHERE
                    p.user_id = $user_id
                ";
        // $data = DB::table('users')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
        $data = DB::select($query);
        // $footer = "";
        return response()->json([
            'data'=>$data,
        ]);
    }

    public function editKaryawanProses(Request $request, $id)
    {
        $userId = User::findOrFail($id);

        $valueOrExisting = static function (Request $request, string $key, $existingValue) {
            return $request->filled($key) ? $request->input($key) : $existingValue;
        };

        $request->merge([
            'cuti_dadakan' => $valueOrExisting($request, 'cuti_dadakan', $userId->cuti_dadakan),
            'cuti_bersama' => $valueOrExisting($request, 'cuti_bersama', $userId->cuti_bersama),
            'cuti_menikah' => $valueOrExisting($request, 'cuti_menikah', $userId->cuti_menikah),
            'cuti_diluar_tanggungan' => $valueOrExisting($request, 'cuti_diluar_tanggungan', $userId->cuti_diluar_tanggungan),
            'cuti_khusus' => $valueOrExisting($request, 'cuti_khusus', $userId->cuti_khusus),
            'cuti_melahirkan' => $valueOrExisting($request, 'cuti_melahirkan', $userId->cuti_melahirkan),
            'izin_telat' => $valueOrExisting($request, 'izin_telat', $userId->izin_telat),
            'izin_pulang_cepat' => $valueOrExisting($request, 'izin_pulang_cepat', $userId->izin_pulang_cepat),
            'gender' => $valueOrExisting($request, 'gender', $userId->gender),
            'status_nikah' => $valueOrExisting($request, 'status_nikah', $userId->status_nikah),
            'is_admin' => $valueOrExisting($request, 'is_admin', $userId->is_admin),
            'jabatan_id' => $valueOrExisting($request, 'jabatan_id', $userId->jabatan_id),
            'golongan_id' => $valueOrExisting($request, 'golongan_id', $userId->golongan_id),
            'lokasi_id' => $valueOrExisting($request, 'lokasi_id', $userId->lokasi_id),
            'password' => $valueOrExisting($request, 'password', $userId->password),
        ]);

        $rules = [
            'name' => 'required|max:255',
            'foto_karyawan' => 'image|file|max:10240',
            'telepon' => 'required',
            'password' => 'required',
            'tgl_lahir' => 'required',
            'gender' => 'required',
            'tgl_join' => 'required',
            'status_nikah' => 'required',
            'alamat' => 'required',
            'cuti_dadakan' => 'required',
            'cuti_bersama' => 'required',
            'cuti_menikah' => 'required',
            'cuti_diluar_tanggungan' => 'required',
            'cuti_khusus' => 'required',
            'cuti_melahirkan' => 'required',
            'izin_telat' => 'required',
            'izin_pulang_cepat' => 'required',
            'is_admin' => 'required',
            'jabatan_id' => 'required',
            'golongan_id' => 'required',
            'lokasi_id' => 'required',
            // 'upah_normal' => 'required'
        ];
        if ($request->email != $userId->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }

        if ($request->username != $userId->username) {
            $rules['username'] = 'required|max:255|unique:users';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        if ($request->filled('img_enc')) {
            if ($request->foto_karyawan_lama && strpos($request->foto_karyawan_lama, 'data:image') !== 0) {
                Storage::disk('public')->delete($request->foto_karyawan_lama);
            }

            $validatedData['foto_karyawan'] = $request->img_enc;
        } elseif ($request->file('foto_karyawan')) {
            if ($request->foto_karyawan_lama && strpos($request->foto_karyawan_lama, 'data:image') !== 0) {
                Storage::disk('public')->delete($request->foto_karyawan_lama);
            }

            $validatedData['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan', 'public');
        }

        // $validatedData['kasbon'] = $request->kasbon;
        $validatedData['upah_normal'] = $request->upah_normal;
        // $validatedData['upah_target'] = $request->upah_target;
        // $validatedData['masuk_minggu'] = $request->masuk_minggu;
        // $validatedData['upah_lembur'] = $request->upah_lembur;
        User::where('id', $id)->update($validatedData);
        $request->session()->flash('success', 'Data Berhasil di Update');
        return redirect('/pegawai/detail/' . $id);
    }

    public function deleteKaryawan($id)
    {
        $delete = User::find($id);
        $deleteShift = MappingShift::where('user_id', $id);
        $deleteLembur = Lembur::where('user_id', $id);
        $deleteCuti = Cuti::where('user_id', $id);
        $deleteSip = Sip::where('user_id', $id);
        $deletePayroll = Payroll::where('user_id', $id);
        if ($delete->foto_karyawan && strpos($delete->foto_karyawan, 'data:image') !== 0) {
            Storage::disk('public')->delete($delete->foto_karyawan);
        }
        $delete->delete();
        $deleteShift->delete();
        $deleteLembur->delete();
        $deleteCuti->delete();
        $deleteSip->delete();
        $deletePayroll->delete();
        return redirect('/pegawai')->with('success', 'Data Berhasil di Delete');
    }

    public function editpassword($id)
    {
        return view('karyawan.editpassword', [
            'title' => 'Edit Password',
            'karyawan' => User::find($id)
        ]);
    }

    public function face($id)
    {
        return view('karyawan.face', [
            'title' => 'Daftar Wajah',
            'karyawan' => User::find($id)
        ]);
    }

    public function ajaxDescrip(Request $request)
    {
        $faceData = json_decode($request->input('myData'), true);
        if (!is_array($faceData) || empty($faceData['label']) || !isset($faceData['descriptors']) || !is_array($faceData['descriptors'])) {
            return response()->json(['message' => 'Data wajah tidak valid.'], 422);
        }

        $path = 'private/neural.json';
        $storedData = Storage::disk('local')->exists($path)
            ? json_decode(Storage::disk('local')->get($path), true)
            : [];

        if (!is_array($storedData)) {
            $storedData = [];
        }

        $storedData[] = $faceData;
        Storage::disk('local')->put($path, json_encode($storedData, JSON_UNESCAPED_UNICODE));

        return response()->json(['message' => 'Data wajah berhasil disimpan.']);
    }

    public function ajaxPhoto(Request $request)
    {
        $image = $request["image"];

        $image_parts = explode(";base64,", $image);
    
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = 'foto_face_recognition/' . $request["path"] . '.png';
    
        Storage::put($fileName, $image_base64);

        $user = User::where('username', $request['path'])->update(["foto_face_recognition" => $fileName]);
        return $user;
    }

    public function editPasswordProses(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => 'required|min:6|max:255',
        ]);

        $validatedData['password'] = Hash::make($request->password);

        User::where('id', $id)->update($validatedData);
        $request->session()->flash('success', 'Password Berhasil Diganti');
        return redirect('/pegawai');
    }

    public function shift($id)
    {
        return view('karyawan.mappingshift', [
            'title' => 'Mapping Shift',
            'karyawan' => User::find($id),
            'shift_karyawan' => MappingShift::where('user_id', $id)->orderBy('tanggal', 'DESC')->limit(365)->get(),
            'shift' => Shift::all()
        ]);
    }

    public function dinasLuar($id)
    {
        return view('karyawan.dinasluar', [
            'title' => 'Mapping Dinas Luar',
            'karyawan' => User::find($id),
            'dinas_luar' => dinasLuar::where('user_id', $id)->orderBy('id', 'desc')->limit(1000)->get(),
            'shift' => Shift::all()
        ]);
    }

    public function prosesTambahShift(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        // if($request["tanggal_mulai"] == null) {
        //     $request["tanggal_mulai"] = $request["tanggal_akhir"];
        // } else {
        //     $request["tanggal_mulai"] = $request["tanggal_mulai"];
        // }

        // if($request["tanggal_akhir"] == null) {
        //     $request["tanggal_akhir"] = $request["tanggal_mulai"];
        // } else {
        //     $request["tanggal_akhir"] = $request["tanggal_akhir"];
        // }


        // Set default values for 'tanggal_mulai' and 'tanggal_akhir' if they are null
        $request["tanggal_mulai"] = $request["tanggal_mulai"] ?? $request["tanggal_akhir"];
        $request["tanggal_akhir"] = $request["tanggal_akhir"] ?? $request["tanggal_mulai"];

        $begin = new \DateTime($request["tanggal_mulai"]);
        $end = (new \DateTime($request["tanggal_akhir"]))->modify('+1 day');
        
        $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
        $daterange = new \DatePeriod($begin, $interval ,$end);
        $existingDates = [];

        foreach ($daterange as $date) {
            $tanggal = $date->format("Y-m-d");

            // Check if the shift already exists for this user on this date
            $existingShift = MappingShift::where('user_id', $request["user_id"])
            ->where('tanggal', $tanggal)
            ->first();

            // If the shift already exists, skip to the next date
            if ($existingShift) {
                $existingDates[] = $tanggal;
                continue;
            }

            // Determine the 'status_absen' based on 'shift_id'
            $request["status_absen"] = $request["shift_id"] == 1 ? "Libur" : "Tidak Masuk";
            $request["tanggal"] = $tanggal;

            // Validate the data before inserting
            $validatedData = $request->validate([
                'user_id' => 'required',
                'shift_id' => 'required',
                'tanggal' => 'required',
                'status_absen' => 'required',
            ]);
            // Create a new shift record
            MappingShift::create($validatedData);
        }
        $successMessage = 'Data Berhasil di Tambahkan';
        $warningMessage = '';
        if (!empty($existingDates)) {
            $warningMessage = 'Tanggal berikut sudah memiliki shift: ' . implode(', ', $existingDates);
        }

        return redirect('/pegawai/shift/' . $request["user_id"])
        ->with('success', 'Data Berhasil di Tambahkan')
        ->with('warning', $warningMessage);
    }

    public function prosesTambahDinas(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        if($request["tanggal_mulai"] == null) {
            $request["tanggal_mulai"] = $request["tanggal_akhir"];
        } else {
            $request["tanggal_mulai"] = $request["tanggal_mulai"];
        }

        if($request["tanggal_akhir"] == null) {
            $request["tanggal_akhir"] = $request["tanggal_mulai"];
        } else {
            $request["tanggal_akhir"] = $request["tanggal_akhir"];
        }

        $begin = new \DateTime($request["tanggal_mulai"]);
        $end = new \DateTime($request["tanggal_akhir"]);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
        $daterange = new \DatePeriod($begin, $interval ,$end);


        foreach ($daterange as $date) {
            $tanggal = $date->format("Y-m-d");

            if ($request["shift_id"] == 1) {
                $request["status_absen"] = "Libur";
            } else {
                $request["status_absen"] = "Tidak Masuk";
            }

            $request["tanggal"] = $tanggal;

            $validatedData = $request->validate([
                'user_id' => 'required',
                'shift_id' => 'required',
                'tanggal' => 'required',
                'status_absen' => 'required',
            ]);

            dinasLuar::create($validatedData);
        }
        return redirect('/pegawai/dinas-luar/' . $request["user_id"])->with('success', 'Data Berhasil di Tambahkan');
    }

    public function deleteShift(Request $request, $id)
    {
        $delete = MappingShift::find($id);
        $delete->delete();
        return redirect('/pegawai/shift/' . $request["user_id"])->with('success', 'Data Berhasil di Hapus');
    }

    public function deleteDinas(Request $request, $id)
    {
        $delete = dinasLuar::find($id);
        $delete->delete();
        return redirect('/pegawai/dinas-luar/' . $request["user_id"])->with('success', 'Data Berhasil di Hapus');
    }

    public function editShift($id)
    {
        return view('karyawan.editshift', [
            'title' => 'Edit Shift',
            'shift_karyawan' => MappingShift::find($id),
            'shift' => Shift::all()
        ]);
    }

    public function editDinas($id)
    {
        return view('karyawan.editdinas', [
            'title' => 'Edit Dinas',
            'dinas_luar' => dinasLuar::find($id),
            'shift' => Shift::all()
        ]);
    }

    public function prosesEditShift(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');


        if ($request["shift_id"] == 1) {
            $request["status_absen"] = "Libur";
        } else {
            $request["status_absen"] = "Tidak Masuk";
        }

        $validatedData = $request->validate([
            'shift_id' => 'required',
            'tanggal' => 'required',
            'status_absen' => 'required'
        ]);

        MappingShift::where('id', $id)->update($validatedData);
        return redirect('/pegawai/shift/' . $request["user_id"])->with('success', 'Data Berhasil di Update');
    }

    public function prosesEditDinas(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');


        if ($request["shift_id"] == 1) {
            $request["status_absen"] = "Libur";
        } else {
            $request["status_absen"] = "Tidak Masuk";
        }

        $validatedData = $request->validate([
            'shift_id' => 'required',
            'tanggal' => 'required',
            'status_absen' => 'required'
        ]);

        dinasLuar::where('id', $id)->update($validatedData);
        return redirect('/pegawai/dinas-luar/' . $request["user_id"])->with('success', 'Data Berhasil di Update');
    }

    public function myProfile()
    {
        return view('karyawan.myprofile', [
            'title' => 'My Profile',
            'data_jabatan' => Jabatan::all()
        ]);
    }

    public function myProfileUpdate(Request $request, $id)
    {
        $rules = [
            'name' => 'required|max:255',
            'foto_karyawan' => 'image|file|max:10240',
            'telepon' => 'required',
            'password' => 'required',
            'tgl_lahir' => 'required',
            'gender' => 'required',
            'tgl_join' => 'required',
            'status_nikah' => 'required',
            'alamat' => 'required',
        ];


        $userId = User::find($id);

        if ($request->email != $userId->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }

        if ($request->username != $userId->username) {
            $rules['username'] = 'required|max:255|unique:users';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('foto_karyawan')) {
            if ($request->foto_karyawan_lama && strpos($request->foto_karyawan_lama, 'data:image') !== 0) {
                Storage::disk('public')->delete($request->foto_karyawan_lama);
            }
            $validatedData['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan', 'public');
        }


        User::where('id', $id)->update($validatedData);
        $request->session()->flash('success', 'Data Berhasil di Update');
        return redirect('/my-profile');
    }

    public function editPassMyProfile()
    {
        return view('karyawan.editpassmyprofile', [
            'title' => 'Ganti Password'
        ]);
    }

    public function editPassMyProfileProses(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => 'required|min:6|max:255|confirmed',
        ]);

        $validatedData['password'] = Hash::make($request->password);

        User::where('id', $id)->update($validatedData);
        $request->session()->flash('success', 'Password Berhasil di Update');
        return redirect('/my-profile');
    }

    public function resetCuti()
    {
        return view('karyawan.masterreset', [
            'title' => 'Master Data Reset Cuti',
            'data_cuti' => ResetCuti::first()
        ]);
    }

    public function resetCutiProses(Request $request, $id)
    {
        $validatedData = $request->validate([
            'cuti_dadakan' => 'required',
            'cuti_bersama' => 'required',
            'cuti_menikah' => 'required',
            'cuti_diluar_tanggungan' => 'required',
            'cuti_khusus' => 'required',
            'cuti_melahirkan' => 'required',
            'izin_telat' => 'required',
            'izin_pulang_cepat' => 'required'
        ]);

        ResetCuti::where('id', $id)->update($validatedData);
        return redirect('/reset-cuti')->with('success', 'Master Cuti Berhasil Diupdate');
    }

    public function laporanBulanan(Request $request)
    {
        return view('karyawan.lapbul', [
            'title' => 'Laporan Gaji',
            'karyawan' => User::where('is_admin','user')->get(),
            'cabang' => Lokasi::all(),
            'jabatan' => Golongan::all(),
        ]);
    }
    public function getDataGajiBulanan(Request $request) {
        $startFrom = $request->get('startFrom');
        $endTo = $request->get('endTo');

    $query = "
        SELECT
            j.id,
            j.name AS jabatan,

            -- Menghitung jumlah karyawan pada setiap jabatan
            (
                SELECT COUNT(u.id)
                FROM users u
                WHERE u.golongan_id = j.id
                AND u.is_admin = 'user'
            ) AS jumlah_karyawan,

            -- Menghitung total lembur
            COALESCE(
                (
                    SELECT SUM(p.lembur)
                    FROM payrolls p
                    JOIN users u ON p.user_id = u.id
                    WHERE u.golongan_id = j.id
                    AND DATE(p.created_at) BETWEEN ? AND ?
                ), 0
            ) AS total_lembur,

            -- Total upah_target
            COALESCE(
                (
                    SELECT SUM(p.upah_target)
                    FROM payrolls p
                    JOIN users u ON p.user_id = u.id
                    WHERE u.golongan_id = j.id
                    AND DATE(p.created_at) BETWEEN ? AND ?
                ), 0
            ) AS total_upah_target,

            -- Total kasbon
            COALESCE(
                (
                    SELECT SUM(p.kasbon)
                    FROM payrolls p
                    JOIN users u ON p.user_id = u.id
                    WHERE u.golongan_id = j.id
                    AND DATE(p.created_at) BETWEEN ? AND ?
                ), 0
            ) AS total_kasbon,

            -- Total denda
            COALESCE(
                (
                    SELECT SUM(p.denda)
                    FROM payrolls p
                    JOIN users u ON p.user_id = u.id
                    WHERE u.golongan_id = j.id
                    AND DATE(p.created_at) BETWEEN ? AND ?
                ), 0
            ) AS total_denda,
            -- Total masuk_minggu
            COALESCE(
                (
                    SELECT SUM(p.masuk_minggu)
                    FROM payrolls p
                    JOIN users u ON p.user_id = u.id
                    WHERE u.golongan_id = j.id
                    AND DATE(p.created_at) BETWEEN ? AND ?
                ), 0
            ) AS total_masuk_minggu,

            -- Menghitung subtotal
            (
                SELECT COALESCE(SUM(
                    u.upah_normal * (
                        SELECT COUNT(m.user_id)
                        FROM mapping_shifts m
                        WHERE m.user_id = u.id
                        AND u.golongan_id = j.id
                        AND DATE(m.tanggal) BETWEEN ? AND ?
                        AND m.status_absen = 'Masuk'
                        AND u.is_admin = 'user'
                    ) 
                ), 0) 
                FROM users u
                WHERE u.golongan_id = j.id
            ) AS subtotal

        FROM golongans j
        GROUP BY j.id, j.name;
    ";

    $data = DB::select($query, [
        $startFrom, $endTo,  // Total lembur
        $startFrom, $endTo,  // Upah target
        $startFrom, $endTo,  // Kasbon
        $startFrom, $endTo,  // Denda
        $startFrom, $endTo,  // minggu masuk
        $startFrom, $endTo,   // Subtotal shifts
        // $startFrom, $endTo
    ]);

    // dd($data);
        return response()->json([
            'data' => $data,
        ]);
    }


    public function getUpahById($id)
    {
        $upah = Payroll::find($id);
        if($upah) {
            return response()->json($upah);
        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }
    public function getJabatanKaryawan($id){
        $karyawan = User::find($id);

        $jabatan = Golongan::find($karyawan->golongan_id);
        if($jabatan){
            return response()->json($jabatan);
        }else{
            return response()->json(['message' => 'Data not found'], 404);
        }
    }
    public function getGajiKaryawanById(Request $request) {
        $jabatanId = $request->get('jabatan_id');
        $lokasiId = $request->get('lokasi_id');
        $userId = $request->get('user_id');
        $startFrom = $request->get('startFrom');
        $endTo = $request->get('endTo');
        
        // Mulai query dengan LEFT JOIN ke tabel payrolls agar semua karyawan ditampilkan
        $query = "
            SELECT
                users.id AS user_id,
                users.name AS nama_karyawan,
                users.upah_normal AS upah_normal,
                
                -- Menghitung jumlah kehadiran karyawan
                (SELECT COUNT(m.user_id) 
                 FROM mapping_shifts m 
                 WHERE m.user_id = users.id 
                 AND m.tanggal BETWEEN ? AND ? 
                 AND m.status_absen = 'Masuk') AS jumlah_hadir,
                 
                 -- Menghitung upah lembur (gunakan COALESCE untuk menangani NULL)
                        (
                SELECT COALESCE(SUM(p.lembur), 0)
                FROM payrolls p
                WHERE p.user_id = users.id
            ) AS upah_lembur,
                
                -- Menghitung upah target (gunakan COALESCE untuk menangani NULL)
                -- Menghitung upah lembur
        (
            SELECT COALESCE(SUM(p.lembur), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) AS upah_lembur,

        -- Menghitung upah target
        (
            SELECT COALESCE(SUM(p.upah_target), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) AS upah_target,

        -- Menghitung masuk minggu
        (
            SELECT COALESCE(SUM(p.masuk_minggu), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) AS masuk_minggu,

        -- Menghitung kasbon
        (
            SELECT COALESCE(SUM(p.kasbon), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) AS kasbon,

        -- Menghitung denda
        (
            SELECT COALESCE(SUM(p.denda), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) AS denda,

                -- Menghitung total gaji jika ada data di payrolls atau berdasarkan kehadiran saja
                (users.upah_normal * 
                    (SELECT COUNT(m.user_id) 
                     FROM mapping_shifts m 
                     WHERE m.user_id = users.id 
                     AND DATE(m.tanggal) BETWEEN ? AND ? 
                     AND m.status_absen = 'Masuk') + (
            SELECT COALESCE(SUM(p.lembur), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) + (
            SELECT COALESCE(SUM(p.upah_target), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) + (
            SELECT COALESCE(SUM(p.masuk_minggu), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) - (
            SELECT COALESCE(SUM(p.kasbon), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        ) - (
            SELECT COALESCE(SUM(p.denda), 0)
            FROM payrolls p
            WHERE p.user_id = users.id
            AND DATE(p.created_at) BETWEEN ? AND ?
        )
                )  AS total_gaji
            FROM  users 
            LEFT JOIN
                payrolls ON payrolls.user_id = users.id
            WHERE
                users.is_admin = 'user' 
        ";
    
        // Tambahkan kondisi WHERE untuk jabatan_id dan user_id
        $params = [$startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo, $startFrom, $endTo];
        if ($lokasiId != 0) {
            $query .= " AND users.lokasi_id = ?";
            $params[] = $lokasiId;
        }
        if ($jabatanId != 0) {
            $query .= " AND users.golongan_id = ?";
            $params[] = $jabatanId;
        }
        
        if ($userId != 0) {
            $query .= " AND users.id = ?";
            $params[] = $userId;
        }
    
        $query .= " GROUP BY users.id, users.name, users.upah_normal ORDER BY users.id";
        
        // Gunakan DB::select dengan parameter binding untuk menghindari SQL injection
        $data = DB::select($query, $params);
        // dd($query);
        return response()->json([
            'data' => $data,
        ]);
    }
    
    
    public function getDivisiByLokasi(Request $req){
        $lokasiId = $req->get('lokasi_id');
        $divisi = DB::table('users as u')
        ->join('golongans as g', 'g.id', '=', 'u.golongan_id')
        ->select('u.golongan_id as id', 'g.name as name')
        ->where('u.lokasi_id', $lokasiId)
        ->distinct()
        ->orderBy('id')
        ->get();
        return response()->json([
            'data'=>$divisi
        ]);
    }
    public function getKaryawanByJabatan(Request $request){
        $jabatanId = $request->get('jabatan_id');
        if($jabatanId == 0){
            $allKaryawan = User::where('is_admin', 'user')->get();
            return response()->json([
                'allkaryawan' => $allKaryawan
            ]);
        } else {
            $karyawan = User::where(['golongan_id' => $jabatanId, 'is_admin'=>'user'])->get();
            return response()->json([
                'karyawan' => $karyawan
            ]);
        }
    }
    public function getKaryawanByJabatanLokasi(Request $request){
        $jabatanId = $request->get('jabatan_id');
        $lokasiId = $request->get('lokasi_id');
        if($jabatanId == 0){
            $allKaryawan = User::where(['is_admin'=>'user', 'lokasi_id'=>$lokasiId])->get();
            return response()->json([
                'allkaryawan' => $allKaryawan
            ]);
        } else {
            $karyawan = User::where(['golongan_id' => $jabatanId, 'is_admin'=>'user', 'lokasi_id'=>$lokasiId])->get();
            return response()->json([
                'karyawan' => $karyawan
            ]);
        }
    }
    public function getKaryawanByLokasi(Request $request){
        $lokasiId = $request->get('lokasi_id');
        $allKaryawan = User::where(['is_admin'=>'user', 'lokasi_id'=>$lokasiId])->get();
        return response()->json([
            'allkaryawan' => $allKaryawan
        ]);
    }
    
    public function updateUpahKaryawan(Request $request, $id)
    {
        // Validasi data jika diperlukan
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'upah_target' => 'required|numeric',
            'masuk_minggu' => 'required|numeric',
            'lembur' => 'required|numeric',
            'kasbon' => 'required|numeric',
            'denda' => 'required|numeric',
        ]);

        // Temukan record yang sesuai berdasarkan ID
        $upah = Payroll::find($id);

        if ($upah) {
            // Update data
            $tanggal = Carbon::parse($request->tanggal);
            $bulan = $tanggal->month; // Mendapatkan bulan
            $tahun = $tanggal->year;  // Mendapatkan tahun
            $upah->bulan = $bulan;
            $upah->tahun = $tahun;
            $upah->created_at = $request->tanggal;
            // $upah->upah_normal = $request->upah_normal;
            $upah->upah_target = $request->upah_target;
            $upah->masuk_minggu = $request->masuk_minggu;
            $upah->lembur = $request->lembur;
            $upah->kasbon = $request->kasbon;
            $upah->denda = $request->denda;
            $upah->save();

            return response()->json(['message' => 'Data berhasil diupdate'], 200);
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }
    public function deleteUpahKaryawan($id)
    {
        $upah = Payroll::find($id); // Asumsi model 'Upah'
        if($upah) {
            $upah->delete();
            return response()->json(['message' => 'Data berhasil di hapus'], 200);
        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }

}
