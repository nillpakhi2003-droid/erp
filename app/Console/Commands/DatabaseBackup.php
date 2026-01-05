<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--telegram : Send backup to Telegram}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database and optionally send to Telegram';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        // Get database configuration
        $dbHost = env('DB_HOST');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbConnection = env('DB_CONNECTION', 'mysql');

        // Create backup directory if not exists
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Generate backup filename
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$dbName}_{$timestamp}.sql";
        $filepath = $backupDir . '/' . $filename;

        // Perform backup based on database type
        $success = false;
        
        if ($dbConnection === 'mysql') {
            $success = $this->mysqlBackup($dbHost, $dbPort, $dbUser, $dbPass, $dbName, $filepath);
        } elseif ($dbConnection === 'pgsql') {
            $success = $this->postgresBackup($dbHost, $dbPort, $dbUser, $dbPass, $dbName, $filepath);
        } else {
            $this->error("Unsupported database connection: {$dbConnection}");
            return 1;
        }

        if (!$success) {
            $this->error('Backup failed!');
            return 1;
        }

        $this->info("Backup created successfully: {$filename}");

        // Compress the backup
        $gzFilepath = $filepath . '.gz';
        $this->info('Compressing backup...');
        
        exec("gzip -c {$filepath} > {$gzFilepath}");
        
        if (file_exists($gzFilepath)) {
            // Remove uncompressed file
            unlink($filepath);
            $filepath = $gzFilepath;
            $filename = $filename . '.gz';
            $this->info('Backup compressed successfully.');
        }

        // Send to Telegram if option is set
        if ($this->option('telegram')) {
            $this->info('Sending backup to Telegram...');
            
            $fileSize = filesize($filepath);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);
            
            $caption = "🗄️ <b>Database Backup</b>\n\n";
            $caption .= "📅 Date: " . Carbon::now()->format('d M Y, H:i:s') . "\n";
            $caption .= "💾 Database: {$dbName}\n";
            $caption .= "📦 Size: {$fileSizeMB} MB\n";
            $caption .= "🏢 App: " . env('APP_NAME', 'ERP System');

            if ($this->telegramService->sendDocument($filepath, $caption)) {
                $this->info('✅ Backup sent to Telegram successfully!');
            } else {
                $this->error('❌ Failed to send backup to Telegram.');
            }
        }

        // Clean old backups (keep last 7 days)
        $this->cleanOldBackups($backupDir, 7);

        $this->info('Backup process completed!');
        return 0;
    }

    /**
     * Perform MySQL backup
     */
    protected function mysqlBackup($host, $port, $user, $pass, $dbName, $filepath)
    {
        $passOption = $pass ? "-p'{$pass}'" : '';
        $command = "mysqldump -h {$host} -P {$port} -u {$user} {$passOption} {$dbName} > {$filepath} 2>&1";
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            $this->error('MySQL backup error: ' . implode("\n", $output));
            return false;
        }
        
        return file_exists($filepath) && filesize($filepath) > 0;
    }

    /**
     * Perform PostgreSQL backup
     */
    protected function postgresBackup($host, $port, $user, $pass, $dbName, $filepath)
    {
        // Set password via environment variable
        $env = $pass ? "PGPASSWORD='{$pass}' " : '';
        $command = "{$env}pg_dump -h {$host} -p {$port} -U {$user} {$dbName} > {$filepath} 2>&1";
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            $this->error('PostgreSQL backup error: ' . implode("\n", $output));
            return false;
        }
        
        return file_exists($filepath) && filesize($filepath) > 0;
    }

    /**
     * Clean old backup files
     */
    protected function cleanOldBackups($backupDir, $daysToKeep = 7)
    {
        $this->info("Cleaning backups older than {$daysToKeep} days...");
        
        $files = glob($backupDir . '/backup_*.sql*');
        $now = time();
        $deletedCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $daysToKeep) {
                    unlink($file);
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} old backup(s).");
        }
    }
}
