<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class SystemBackupService
{
    public function createBackup()
    {
        $backupDir = storage_path('app/backups/temp_' . now()->timestamp);
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // 1. Database Dump
        $dbName = config('database.connections.' . config('database.default') . '.database');
        $dbUser = config('database.connections.' . config('database.default') . '.username');
        $dbPass = config('database.connections.' . config('database.default') . '.password');
        $dbHost = config('database.connections.' . config('database.default') . '.host');

        $sqlFile = $backupDir . '/database_dump.sql';

        // Try using mysqldump if available
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbName),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            // Fallback: Simple PHP-based dump for basic tables if mysqldump fails
            $this->fallbackSqlDump($sqlFile);
        }

        // 2. Configuration State
        $configState = [
            'app_version' => '1.0.0', // Could be dynamic
            'maintenance_mode' => app()->isDownForMaintenance(),
            'settings' => DB::table('settings')->get()->toArray(),
            'env_snapshot' => [
                'APP_NAME' => config('app.name'),
                'APP_ENV' => config('app.env'),
            ],
            'timestamp' => now()->toDateTimeString(),
        ];

        File::put($backupDir . '/config_state.json', json_encode($configState, JSON_PRETTY_PRINT));

        // 3. Compress into ZIP
        $zipFile = storage_path('app/backups/backup_' . now()->format('Y-m-d_His') . '.zip');
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $zip->addFile($file->getPathname(), $file->getFilename());
            }
            $zip->close();
        }

        // Cleanup temp dir
        File::deleteDirectory($backupDir);

        return $zipFile;
    }

    private function fallbackSqlDump($path)
    {
        $tables = DB::select('SHOW TABLES');
        $dbKey = 'Tables_in_' . config('database.connections.' . config('database.default') . '.database');

        $content = "-- Secure Vote PH Database Dump\n";
        $content .= "-- Date: " . now()->toDateTimeString() . "\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$dbKey;

            // Structure
            $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0];
            $content .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $content .= $createTable->{'Create Table'} . ";\n\n";

            // Data
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $row = (array) $row;
                $keys = array_keys($row);
                $values = array_values($row);

                $content .= "INSERT INTO `$tableName` (`" . implode("`, `", $keys) . "`) VALUES (";
                $content .= implode(", ", array_map(function($v) {
                    if (is_null($v)) return "NULL";
                    return DB::getPdo()->quote($v);
                }, $values));
                $content .= ");\n";
            }
            $content .= "\n";
        }

        File::put($path, $content);
    }
}
