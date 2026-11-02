<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PurgeAttendancePhotos extends Command
{
    protected $signature = 'attendance:purge-photos {--dry-run : Show eligible photos without deleting them}';

    protected $description = 'Delete attendance photos older than three months while preserving attendance records';

    public function handle()
    {
        $cutoff = Carbon::today('Asia/Jakarta')->subMonthsNoOverflow(3)->toDateString();
        $photoColumns = [
            'mapping_shifts' => [
                'foto_jam_absen' => ['foto_jam_absen'],
                'foto_jam_pulang' => ['foto_jam_pulang'],
            ],
            'dinas_luars' => [
                'foto_jam_absen' => ['foto_dinas_luar_masuk'],
                'foto_jam_pulang' => ['foto_dinas_luar_pulang'],
            ],
            'lemburs' => [
                'foto_jam_masuk' => ['foto_jam_masuk_lembur'],
                'foto_jam_keluar' => ['foto_jam_keluar_lembur'],
            ],
        ];
        $dryRun = $this->option('dry-run');
        $eligible = 0;
        $deleted = 0;
        $failed = 0;

        $this->info(($dryRun ? 'Dry run' : 'Purging') . ' attendance photos dated before ' . $cutoff . '.');

        foreach ($photoColumns as $table => $columns) {
            foreach ($columns as $column => $allowedFolders) {
                $query = DB::table($table)
                    ->whereDate('tanggal', '<', $cutoff)
                    ->whereNotNull($column)
                    ->where($column, '!=', '');

                if ($dryRun) {
                    $count = $query->count();
                    $eligible += $count;
                    $this->line($table . '.' . $column . ': ' . $count . ' photo(s) eligible');
                    continue;
                }

                $query->orderBy('id')->chunkById(100, function ($rows) use ($table, $column, $allowedFolders, &$eligible, &$deleted, &$failed) {
                    foreach ($rows as $row) {
                        $eligible++;
                        $path = $row->{$column};

                        if (!$this->isAllowedPhotoPath($path, $allowedFolders) || !$this->deletePhotoFromStorage($path)) {
                            $failed++;
                            $this->error('Could not safely remove ' . $table . '.' . $column . ' record ' . $row->id . '; keeping its database reference.');
                            continue;
                        }

                        DB::table($table)
                            ->where('id', $row->id)
                            ->where($column, $path)
                            ->update([$column => $table === 'lemburs' && $column === 'foto_jam_masuk' ? '' : null]);
                        $deleted++;
                    }
                });

                $this->line($table . '.' . $column . ': processed');
            }
        }

        $this->info(($dryRun ? 'Eligible photo references: ' : 'Photo references cleared: ') . ($dryRun ? $eligible : $deleted));
        if (!$dryRun && $failed > 0) {
            $this->warn('Photos that could not be removed were left referenced in the database: ' . $failed);
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function isAllowedPhotoPath($path, array $allowedFolders): bool
    {
        if (!is_string($path)) {
            return false;
        }

        foreach ($allowedFolders as $folder) {
            $prefix = $folder . '/';
            if (strpos($path, $prefix) === 0) {
                $filename = substr($path, strlen($prefix));
                return $filename !== ''
                    && strpos($filename, '/') === false
                    && preg_match('/^[A-Za-z0-9._-]+\.(png|jpe?g|webp)$/i', $filename) === 1;
            }
        }

        return false;
    }

    private function deletePhotoFromStorage(string $path): bool
    {
        $disks = array_unique(['public', 'local', config('filesystems.default')]);
        foreach ($disks as $diskName) {
            try {
                $disk = Storage::disk($diskName);
                if (!$disk->exists($path)) {
                    continue;
                }

                if (!$disk->delete($path)) {
                    return false;
                }
            } catch (Throwable $exception) {
                report($exception);
                return false;
            }
        }

        // Clear stale database paths even if a prior cleanup already removed the file.
        return true;
    }
}
