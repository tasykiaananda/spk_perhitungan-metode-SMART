<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\Penilaian;
use App\Models\CalculationHistory;
use App\Models\WebsiteSetting;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup.index');
    }

    /**
     * Export all data tables to a downloadable JSON file.
     */
    public function backup()
    {
        $backupData = [
            'kriterias' => Kriteria::all()->toArray(),
            'alternatifs' => Alternatif::all()->toArray(),
            'penilaians' => Penilaian::all()->toArray(),
            'calculation_histories' => CalculationHistory::all()->toArray(),
            'website_settings' => WebsiteSetting::all()->toArray(),
            'activity_logs' => ActivityLog::all()->toArray(),
        ];

        $jsonContent = json_encode($backupData, JSON_PRETTY_PRINT);
        $fileName = 'backup_spk_coffee_' . date('Y-m-d_H-i-s') . '.json';

        ActivityLog::log("Melakukan backup database ke file: {$fileName}");

        return response($jsonContent, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Restore database tables from an uploaded JSON file.
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json',
        ]);

        try {
            $file = $request->file('backup_file');
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (!$data || !isset($data['kriterias']) || !isset($data['alternatifs'])) {
                return back()->withErrors(['backup_file' => 'File backup JSON tidak valid atau struktur tidak cocok.']);
            }

            DB::transaction(function () use ($data) {
                // Disable foreign key checks to prevent delete conflicts
                Schema::disableForeignKeyConstraints();

                DB::table('penilaians')->truncate();
                DB::table('alternatifs')->truncate();
                DB::table('kriterias')->truncate();
                DB::table('calculation_histories')->truncate();
                DB::table('activity_logs')->truncate();
                DB::table('website_settings')->truncate();

                // Re-insert data
                foreach ($data['kriterias'] as $row) {
                    DB::table('kriterias')->insert($row);
                }
                foreach ($data['alternatifs'] as $row) {
                    DB::table('alternatifs')->insert($row);
                }
                foreach ($data['penilaians'] as $row) {
                    DB::table('penilaians')->insert($row);
                }
                if (isset($data['calculation_histories'])) {
                    foreach ($data['calculation_histories'] as $row) {
                        DB::table('calculation_histories')->insert($row);
                    }
                }
                if (isset($data['website_settings'])) {
                    foreach ($data['website_settings'] as $row) {
                        DB::table('website_settings')->insert($row);
                    }
                }
                if (isset($data['activity_logs'])) {
                    foreach ($data['activity_logs'] as $row) {
                        DB::table('activity_logs')->insert($row);
                    }
                }

                Schema::enableForeignKeyConstraints();
            });

            ActivityLog::log("Berhasil memulihkan database dari file backup");

            return redirect()->route('admin.backup.index')->with('success', 'Database berhasil dipulihkan dari file backup!');
        } catch (\Exception $e) {
            return back()->withErrors(['backup_file' => 'Terjadi kesalahan saat memulihkan database: ' . $e->getMessage()]);
        }
    }

    /**
     * Wipe everything and re-seed from database seeders.
     */
    public function reset()
    {
        try {
            DB::transaction(function () {
                Schema::disableForeignKeyConstraints();
                
                DB::table('penilaians')->truncate();
                DB::table('alternatifs')->truncate();
                DB::table('kriterias')->truncate();
                DB::table('calculation_histories')->truncate();
                DB::table('activity_logs')->truncate();
                DB::table('website_settings')->truncate();
                
                Schema::enableForeignKeyConstraints();
            });

            // Re-run the seeders
            Artisan::call('db:seed', ['--force' => true]);

            ActivityLog::log("Mereset database ke data awal bawaan");

            return redirect()->route('admin.backup.index')->with('success', 'Database berhasil di-reset ke data awal bawaan!');
        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')->with('error', 'Terjadi kesalahan saat mereset database: ' . $e->getMessage());
        }
    }
}
