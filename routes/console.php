<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Daily database backup to Telegram
Schedule::command('db:backup --telegram')
    ->dailyAt('02:00')
    ->timezone('Asia/Dhaka')
    ->name('Daily Database Backup')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('Database backup completed successfully');
    })
    ->onFailure(function () {
        Log::error('Database backup failed');
    });
