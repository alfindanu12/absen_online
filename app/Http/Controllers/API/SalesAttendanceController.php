<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\dinasLuar as DinasLuarModel;
use App\Models\MappingShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class SalesAttendanceController extends Controller
{
    public function today(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        [$user, $attendance] = $this->resolveUserAndAttendance($validated['email']);

        if (!$user) {
            return response()->json([
                'message' => 'User absensi dengan email ini tidak ditemukan.',
            ], 404);
        }

        if (!$attendance) {
            return response()->json([
                'message' => 'Jadwal absen hari ini belum dibuat di sistem absensi.',
                'user' => $this->userPayload($user),
                'attendance' => null,
            ]);
        }

        return response()->json([
            'user' => $this->userPayload($user),
            'attendance' => $this->attendancePayload($attendance),
        ]);
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'image' => ['required', 'string'],
        ]);

        [$user, $attendance] = $this->resolveUserAndAttendance($validated['email']);
        $guard = $this->guardCheckIn($user, $attendance);
        if ($guard) {
            return $guard;
        }

        $now = Carbon::now('Asia/Jakarta');
        $checkInTime = $now->format('H:i');
        $shiftStart = Carbon::parse($attendance->tanggal . ' ' . $attendance->Shift->jam_masuk, 'Asia/Jakarta');
        $lateSeconds = max(0, $now->diffInSeconds($shiftStart, false) * -1);

        $attendance->update([
            'jam_absen' => $checkInTime,
            'telat' => $lateSeconds,
            'foto_jam_absen' => $this->storeBase64Image($validated['image'], 'foto_dinas_luar_masuk'),
            'lat_absen' => (string) $validated['latitude'],
            'long_absen' => (string) $validated['longitude'],
            'status_absen' => 'Masuk',
        ]);

        return response()->json([
            'message' => 'Absen masuk berhasil.',
            'attendance' => $this->attendancePayload($attendance->refresh()),
        ]);
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'image' => ['required', 'string'],
        ]);

        [$user, $attendance] = $this->resolveUserAndAttendance($validated['email']);
        $guard = $this->guardCheckOut($user, $attendance);
        if ($guard) {
            return $guard;
        }

        $now = Carbon::now('Asia/Jakarta');
        $shiftStartTime = strtotime($attendance->Shift->jam_masuk);
        $shiftEndTime = strtotime($attendance->Shift->jam_keluar);
        $shiftEndDate = $shiftEndTime < $shiftStartTime
            ? Carbon::parse($attendance->tanggal, 'Asia/Jakarta')->addDay()->toDateString()
            : $attendance->tanggal;
        $shiftEnd = Carbon::parse($shiftEndDate . ' ' . $attendance->Shift->jam_keluar, 'Asia/Jakarta');
        $earlyLeaveSeconds = $now->lessThan($shiftEnd) ? $shiftEnd->diffInSeconds($now) : 0;

        $attendance->update([
            'jam_pulang' => $now->format('H:i'),
            'pulang_cepat' => $earlyLeaveSeconds,
            'foto_jam_pulang' => $this->storeBase64Image($validated['image'], 'foto_dinas_luar_pulang'),
            'lat_pulang' => (string) $validated['latitude'],
            'long_pulang' => (string) $validated['longitude'],
        ]);

        return response()->json([
            'message' => 'Absen pulang berhasil.',
            'attendance' => $this->attendancePayload($attendance->refresh()),
        ]);
    }

    private function resolveUserAndAttendance(string $email): array
    {
        $user = User::query()
            ->with(['Lokasi'])
            ->where('email', $email)
            ->first();

        if (!$user) {
            return [null, null];
        }

        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $yesterday = Carbon::now('Asia/Jakarta')->subDay()->toDateString();
        $yesterdayAttendance = $this->dinasLuarForDate($user->id, $yesterday);

        if ($yesterdayAttendance && $yesterdayAttendance->jam_absen && !$yesterdayAttendance->jam_pulang) {
            return [$user, $yesterdayAttendance];
        }

        return [$user, $this->dinasLuarForDate($user->id, $today)];
    }

    private function dinasLuarForDate(int $userId, string $date): ?DinasLuarModel
    {
        $attendance = DinasLuarModel::query()
            ->with('Shift')
            ->where('user_id', $userId)
            ->where('tanggal', $date)
            ->first();

        if ($attendance) {
            return $attendance;
        }

        $mappingShift = MappingShift::query()
            ->with('Shift')
            ->where('user_id', $userId)
            ->where('tanggal', $date)
            ->first();

        if (!$mappingShift) {
            return null;
        }

        return DinasLuarModel::create([
            'user_id' => $mappingShift->user_id,
            'shift_id' => $mappingShift->shift_id,
            'tanggal' => $mappingShift->tanggal,
            'status_absen' => $mappingShift->shift_id == 1 ? 'Libur' : 'Tidak Masuk',
        ])->load('Shift');
    }

    private function guardCheckIn(?User $user, ?DinasLuarModel $attendance)
    {
        if (!$user) {
            return response()->json(['message' => 'User absensi dengan email ini tidak ditemukan.'], 404);
        }

        if (!$attendance) {
            return response()->json(['message' => 'Jadwal absen hari ini belum dibuat di sistem absensi.'], 422);
        }

        if (in_array((string) $attendance->status_absen, ['Libur', 'Cuti'], true)) {
            return response()->json(['message' => 'Jadwal hari ini berstatus ' . $attendance->status_absen . '.'], 422);
        }

        if ($attendance->jam_absen) {
            return response()->json(['message' => 'Anda sudah absen masuk hari ini.'], 422);
        }

        if (!$attendance->Shift) {
            return response()->json(['message' => 'Shift user belum lengkap.'], 422);
        }

        return null;
    }

    private function guardCheckOut(?User $user, ?DinasLuarModel $attendance)
    {
        if (!$user) {
            return response()->json(['message' => 'User absensi dengan email ini tidak ditemukan.'], 404);
        }

        if (!$attendance) {
            return response()->json(['message' => 'Jadwal absen hari ini belum dibuat di sistem absensi.'], 422);
        }

        if (!$attendance->jam_absen) {
            return response()->json(['message' => 'Anda belum absen masuk.'], 422);
        }

        if ($attendance->jam_pulang) {
            return response()->json(['message' => 'Anda sudah absen pulang hari ini.'], 422);
        }

        if (!$this->canCheckOut($attendance)) {
            return response()->json(['message' => 'Absen pulang baru bisa dilakukan 1 jam setelah absen masuk.'], 422);
        }

        if (!$attendance->Shift) {
            return response()->json(['message' => 'Shift user belum lengkap.'], 422);
        }

        return null;
    }

    private function canCheckOut(DinasLuarModel $attendance): bool
    {
        $checkInAt = Carbon::parse($attendance->tanggal . ' ' . $attendance->jam_absen, 'Asia/Jakarta');

        return Carbon::now('Asia/Jakarta')->greaterThanOrEqualTo($checkInAt->copy()->addHour());
    }

    private function attendancePayload(DinasLuarModel $attendance): array
    {
        $checkInAt = $attendance->jam_absen
            ? Carbon::parse($attendance->tanggal . ' ' . $attendance->jam_absen, 'Asia/Jakarta')->toIso8601String()
            : null;

        $checkOutAt = null;
        if ($attendance->jam_pulang) {
            $checkOutDate = $attendance->tanggal;
            if ($attendance->Shift && strtotime($attendance->Shift->jam_keluar) < strtotime($attendance->Shift->jam_masuk)) {
                $checkOutDate = Carbon::parse($attendance->tanggal)->addDay()->toDateString();
            }
            $checkOutAt = Carbon::parse($checkOutDate . ' ' . $attendance->jam_pulang, 'Asia/Jakarta')->toIso8601String();
        }

        return [
            'id' => $attendance->id,
            'date' => $attendance->tanggal,
            'status' => $attendance->status_absen,
            'shift_name' => optional($attendance->Shift)->nama_shift,
            'shift_start' => optional($attendance->Shift)->jam_masuk,
            'shift_end' => optional($attendance->Shift)->jam_keluar,
            'checkInAt' => $checkInAt,
            'checkOutAt' => $checkOutAt,
            'latitude' => $attendance->lat_pulang ?: $attendance->lat_absen,
            'longitude' => $attendance->long_pulang ?: $attendance->long_absen,
            'distance_in' => null,
            'distance_out' => null,
            'can_check_out' => $attendance->jam_absen && !$attendance->jam_pulang
                ? $this->canCheckOut($attendance)
                : false,
            'check_out_available_at' => $attendance->jam_absen
                ? Carbon::parse($attendance->tanggal . ' ' . $attendance->jam_absen, 'Asia/Jakarta')->addHour()->toIso8601String()
                : null,
        ];
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'location_name' => optional($user->Lokasi)->nama_lokasi,
        ];
    }

    private function storeBase64Image(string $dataUri, string $directory): string
    {
        $base64 = str_contains($dataUri, ';base64,')
            ? explode(';base64,', $dataUri, 2)[1]
            : $dataUri;
        $image = base64_decode($base64, true);

        if ($image === false) {
            throw ValidationException::withMessages([
                'image' => 'Format foto absen tidak valid.',
            ]);
        }

        $relativePath = $directory . '/' . uniqid('', true) . '.png';
        $targetDirectory = public_path('storage/' . $directory);

        if (!File::isDirectory($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true);
        }

        file_put_contents(public_path('storage/' . $relativePath), $image);

        return $relativePath;
    }
}
