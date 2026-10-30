<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesUserSyncController extends Controller
{
    public function upsert(Request $request)
    {
        $this->ensureAuthorized($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
            'location_id' => ['nullable', 'integer', Rule::exists('lokasis', 'id')->where('status', 'approved')],
            'gender' => ['nullable', Rule::in(['Laki-Laki', 'Perempuan'])],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $locationId = (int) ($validated['location_id'] ?? 0);
        if ($locationId <= 0) {
            $locationId = (int) Lokasi::query()
                ->where('status', 'approved')
                ->orderBy('id')
                ->value('id');
        }

        if ($locationId <= 0) {
            throw ValidationException::withMessages([
                'location_id' => 'Lokasi approved belum tersedia di absensi-online.',
            ]);
        }

        $jabatanId = (int) Jabatan::query()
            ->firstOrCreate(['nama_jabatan' => 'Sales'])
            ->id;
        $golonganId = (int) Golongan::query()
            ->firstOrCreate(['name' => 'SALES'])
            ->id;

        $user = User::query()
            ->where('email', $validated['email'])
            ->first();

        $payload = [
            'name' => $validated['name'],
            'foto_karyawan' => $user->foto_karyawan ?? '-',
            'foto_face_recognition' => $user->foto_face_recognition ?? null,
            'email' => $validated['email'],
            'telepon' => $validated['phone'] ?? '-',
            'username' => $this->uniqueUsername($validated['email'], $user?->id),
            'tgl_lahir' => $user->tgl_lahir ?? '2000-01-01',
            'gender' => $validated['gender'] ?? $user->gender ?? 'Laki-Laki',
            'tgl_join' => $user->tgl_join ?? now('Asia/Jakarta')->toDateString(),
            'status_nikah' => $user->status_nikah ?? 'Lajang',
            'alamat' => $validated['address'] ?? $user->alamat ?? '-',
            'cuti_dadakan' => $user->cuti_dadakan ?? '0',
            'cuti_bersama' => $user->cuti_bersama ?? '0',
            'cuti_menikah' => $user->cuti_menikah ?? '0',
            'cuti_diluar_tanggungan' => $user->cuti_diluar_tanggungan ?? '0',
            'cuti_khusus' => $user->cuti_khusus ?? '0',
            'cuti_melahirkan' => $user->cuti_melahirkan ?? '0',
            'izin_telat' => $user->izin_telat ?? '0',
            'izin_pulang_cepat' => $user->izin_pulang_cepat ?? '0',
            'is_admin' => 'user',
            'jabatan_id' => $jabatanId,
            'golongan_id' => $golonganId,
            'lokasi_id' => $locationId,
        ];

        if (!$user || !empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password'] ?? Str::random(12));
        }

        $user = User::query()->updateOrCreate(
            ['email' => $validated['email']],
            $payload
        );

        return response()->json([
            'message' => 'User sales absensi berhasil disinkronkan.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'lokasi_id' => $user->lokasi_id,
            ],
        ]);
    }

    private function ensureAuthorized(Request $request): void
    {
        $expectedToken = trim((string) env('SALES_SYNC_TOKEN', ''));
        if ($expectedToken === '') {
            return;
        }

        $token = trim((string) $request->header('X-Sales-Sync-Token', ''));
        if (!hash_equals($expectedToken, $token)) {
            abort(401, 'Token sinkronisasi sales tidak valid.');
        }
    }

    private function uniqueUsername(string $email, ?int $existingUserId = null): string
    {
        $base = strtolower(trim($email));
        $username = $base;
        $suffix = 2;

        while (User::query()
            ->where('username', $username)
            ->when($existingUserId, fn ($query) => $query->where('id', '!=', $existingUserId))
            ->exists()) {
            $username = $base . '-' . $suffix;
            $suffix++;
        }

        return $username;
    }
}
