<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\Golongan;
use Illuminate\Http\Request;
use DB;

class LaporanController extends Controller
{
    public function index(){}
    public function laporanBulanan(){
        return view('laporan.laporan', [
            'layout' => 'bulanan',
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
                            AND DATE(p.created_at) BETWEEN '$startFrom' AND '$endTo'
                        ), 0
                    ) AS total_lembur,

                    -- Total upah_target
                    COALESCE(
                        (
                            SELECT SUM(p.upah_target)
                            FROM payrolls p
                            JOIN users u ON p.user_id = u.id
                            WHERE u.golongan_id = j.id
                            AND DATE(p.created_at) BETWEEN '$startFrom' AND '$endTo'
                        ), 0
                    ) AS total_upah_target,

                    -- Total kasbon
                    COALESCE(
                        (
                            SELECT SUM(p.kasbon)
                            FROM payrolls p
                            JOIN users u ON p.user_id = u.id
                            WHERE u.golongan_id = j.id
                            AND DATE(p.created_at) BETWEEN '$startFrom' AND '$endTo'
                        ), 0
                    ) AS total_kasbon,

                    -- Total denda
                    COALESCE(
                        (
                            SELECT SUM(p.denda)
                            FROM payrolls p
                            JOIN users u ON p.user_id = u.id
                            WHERE u.golongan_id = j.id
                            AND DATE(p.created_at) BETWEEN '$startFrom' AND '$endTo'
                        ), 0
                    ) AS total_denda,

                    -- Total masuk_minggu
                    COALESCE(
                        (
                            SELECT SUM(p.masuk_minggu)
                            FROM payrolls p
                            JOIN users u ON p.user_id = u.id
                            WHERE u.golongan_id = j.id
                            AND DATE(p.created_at) BETWEEN '$startFrom' AND '$endTo'
                        ), 0
                    ) AS total_masuk_minggu,

                    -- Menghitung subtotal berdasarkan salary_per_day dan jumlah hari masuk
                    -- Subtotal gaji berdasarkan salary_per_day dan kehadiran
                            COALESCE(
                                (
                                    SELECT SUM(salary_on_date.salary_per_day)
                                    FROM (
                                        SELECT 
                                            m.user_id,
                                            m.tanggal,
                                            sd.salary_per_day
                                        FROM mapping_shifts m
                                        JOIN salary_daily sd 
                                            ON m.user_id = sd.user_id
                                            AND m.tanggal BETWEEN sd.start_date AND sd.end_date
                                        WHERE m.status_absen = 'Masuk'
                                        AND m.tanggal BETWEEN ? AND ?
                                    ) AS salary_on_date
                                    JOIN users u ON u.id = salary_on_date.user_id
                                    WHERE u.golongan_id = j.id
                                ), 0
                            ) AS subtotal

                FROM golongans j
                GROUP BY j.id, j.name;
        ";
        $data = DB::select($query, [$startFrom, $endTo]);

        return response()->json([
            'data' => $data,
        ]);
    }

    public function laporanMingguan(Request $request) {
        $jabatanId = $request->get('jabatan_id');
        $lokasiId = $request->get('lokasi_id');
        $userId = $request->get('user_id');
        $startFrom = $request->get('startFrom');
        $endTo = $request->get('endTo');
//         $query = "
//         SELECT
//             users.id AS user_id,
//             users.name AS nama_karyawan,
            
//             (
//                 SELECT COUNT(m.user_id) 
//                 FROM mapping_shifts m 
//                 WHERE m.user_id = users.id 
//                 AND DATE(m.tanggal) BETWEEN ? AND ? 
//                 AND m.status_absen = 'Masuk'
//             ) AS jumlah_hadir,
    
//         /*    // COALESCE(
//             //     (
//             //         SELECT SUM(salary_on_date.salary_per_day)
//             //         FROM (
//             //             SELECT 
//             //                 m.tanggal,
//             //                 sd.salary_per_day
//             //             FROM mapping_shifts m
//             //             JOIN salary_daily sd 
//             //                 ON m.user_id = sd.user_id
//             //                 AND m.tanggal BETWEEN sd.start_date AND sd.end_date
//             //             JOIN users
//             //                 ON users.id = m.user_id
                            
//             //             WHERE m.user_id = users.id
//             //             AND m.status_absen = 'Masuk'
//             //             AND m.tanggal BETWEEN ? AND ?
//             //         ) AS salary_on_date
//             //     ), 0
//             // ) AS total_gaji_harian, */
//             SELECT SUM(salary_on_date.salary_per_day)
//             FROM (
//                 SELECT 
//                     m.tanggal,
//                     sd.salary_per_day
//                 FROM mapping_shifts m
//                 JOIN salary_daily sd 
//                     ON m.user_id = sd.user_id
//                     AND m.tanggal BETWEEN sd.start_date AND sd.end_date
//                 WHERE m.user_id = users.id
//                 AND m.status_absen = 'Masuk'
//                 AND m.tanggal BETWEEN ? AND ?
//             ) AS salary_on_date

//         ) AS total_gaji_harian,
//             (
//                 SELECT COALESCE(SUM(p.lembur), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             ) AS upah_lembur,
    
//             (
//                 SELECT COALESCE(SUM(p.upah_target), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             ) AS upah_target,
    
//             (
//                 SELECT COALESCE(SUM(p.masuk_minggu), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             ) AS masuk_minggu,
    
//             (
//                 SELECT COALESCE(SUM(p.kasbon), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             ) AS kasbon,
    
//             (
//                 SELECT COALESCE(SUM(p.denda), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             ) AS denda,
//             (
//             COALESCE(
//     (
//         SELECT SUM(salary_on_date.salary_per_day)
//         FROM (
//             SELECT 
//                 m.tanggal,
//                 sd.salary_per_day
//             FROM mapping_shifts m
//             JOIN salary_daily sd 
//                 ON m.user_id = sd.user_id
//                 AND m.tanggal BETWEEN sd.start_date AND sd.end_date
//             JOIN users ON users.id = m.user_id AND users.id = sd.user_id 
//             WHERE m.user_id = users.id -- Ensure each user is considered separately
//             AND m.status_absen = 'Masuk'
//             AND m.tanggal BETWEEN ? AND ?
//         ) AS salary_on_date
//     ), 0
// ) +
//             (
//                 SELECT COALESCE(SUM(p.lembur), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             )  +
//             (
//                 SELECT COALESCE(SUM(p.upah_target), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             )  +
//             (
//                 SELECT COALESCE(SUM(p.masuk_minggu), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             )  -
//             (
//                 SELECT COALESCE(SUM(p.kasbon), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             ) -
    
//             (
//                 SELECT COALESCE(SUM(p.denda), 0)
//                 FROM payrolls p
//                 WHERE p.user_id = users.id
//                 AND DATE(p.created_at) BETWEEN ? AND ?
//             )
//             ) as total_gaji
//         FROM users
//         WHERE users.is_admin = 'user'
//         ";
        $query = "SELECT 
a.user_id, a.nama_karyawan, a.jumlah_hadir, a.total_gaji_harian, a.upah_lembur, a.upah_target, a.masuk_minggu, a.kasbon, a.denda, 
(a.total_gaji_harian + a.upah_lembur + a.upah_target + a.masuk_minggu - a.kasbon - a.denda)as total_gaji
FROM 
(
	SELECT 
			s.id AS user_id, 
			s.name AS nama_karyawan, 
			s.is_admin, 
			s.golongan_id,
			s.lokasi_id,
			COUNT(m.id) AS jumlah_hadir,
			COALESCE(SUM(sd.salary_per_day), 0) AS total_gaji_harian,
			(
				SELECT COALESCE(SUM(p.lembur), 0)
				FROM payrolls p
				WHERE p.user_id = s.id
				AND DATE(p.created_at) BETWEEN ? AND ?
			) AS upah_lembur,
			(
				SELECT COALESCE(SUM(p.upah_target), 0)
				FROM payrolls p
				WHERE p.user_id = s.id
				AND DATE(p.created_at) BETWEEN ? AND ?
			) AS upah_target,
			(
				SELECT COALESCE(SUM(p.masuk_minggu), 0)
				FROM payrolls p
				WHERE p.user_id = s.id
				AND DATE(p.created_at) BETWEEN ? AND ?
			) AS masuk_minggu,
			(
				SELECT COALESCE(SUM(p.kasbon), 0)
				FROM payrolls p
				WHERE p.user_id = s.id
				AND DATE(p.created_at) BETWEEN ? AND ?
			) AS kasbon,
			(
				SELECT COALESCE(SUM(p.denda), 0)
				FROM payrolls p
				WHERE p.user_id = s.id
				AND DATE(p.created_at) BETWEEN ? AND ?
			) AS denda
	FROM 
			users s
	LEFT JOIN 
			mapping_shifts m 
			ON s.id = m.user_id 
			AND m.tanggal BETWEEN ? AND ?
			AND m.status_absen = 'masuk'
	LEFT JOIN 
			salary_daily sd
			ON m.user_id = sd.user_id
			AND m.tanggal BETWEEN sd.start_date AND sd.end_date
	GROUP BY 
	s.id, s.name, s.lokasi_id, s.golongan_id, s.is_admin ) a WHERE a.is_admin='user'";
        
    
        $params = [
            $startFrom, $endTo, 
            $startFrom, $endTo, 
            $startFrom, $endTo,
            $startFrom, $endTo, 
            $startFrom, $endTo,
            $startFrom, $endTo, 
            // $startFrom, $endTo,
            // $startFrom, $endTo, 
            // $startFrom, $endTo, 
            // $startFrom, $endTo,
            // $startFrom, $endTo, 
            // $startFrom, $endTo,
            // $startFrom, $endTo
        ];
    
        if ($lokasiId != 0) {
            $query .= " AND a.lokasi_id = ?";
            $params[] = $lokasiId;
        }
        if ($jabatanId != 0) {
            $query .= " AND a.golongan_id = ?";
            $params[] = $jabatanId;
        }
        if ($userId != 0) {
            $query .= " AND a.user_id = ?";
            $params[] = $userId;
        }
    
        $query .= " ORDER BY a.user_id";
        $data = DB::select($query, $params);
    
        return response()->json(['data' => $data]);
    }
    
}
