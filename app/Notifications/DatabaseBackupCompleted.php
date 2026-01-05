<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DatabaseBackupCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $backupInfo;

    public function __construct(array $backupInfo)
    {
        $this->backupInfo = $backupInfo;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Database Backup Completed - ' . config('app.name'))
            ->greeting('Hello!')
            ->line('Your database backup has been completed successfully.')
            ->line('Backup Details:')
            ->line('📅 Date: ' . $this->backupInfo['date'])
            ->line('💾 Database: ' . $this->backupInfo['database'])
            ->line('📦 File Size: ' . $this->backupInfo['size'])
            ->line('📁 Location: ' . $this->backupInfo['location'])
            ->line('Thank you for using our system!');
    }
}
