<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            "name" => $row[0],
            "email" => $row[1],
            "telepon" => $row[2],
            "username" => $row[3],
            "password" => Hash::make($row[4]),
            "tgl_lahir" => $row[5],
            "gender" => $row[6],
            "tgl_join" => $row[7],
            "status_nikah" => $row[8],
            "alamat" => $row[9],
            "cuti_dadakan" => $row[10],
            "cuti_bersama" => $row[11],
            "cuti_menikah" => $row[12],
            "cuti_diluar_tanggungan" => $row[13],
            "cuti_khusus" => $row[14],
            "cuti_melahirkan" => $row[15],
            "izin_telat" => $row[16],
            "izin_pulang_cepat" => $row[17],
            "is_admin" => $row[18],
            "jabatan_id" => $row[19],
            "lokasi_id" => $row[20],
            "golongan_id" => $row[21],
        ]);
    }
}
