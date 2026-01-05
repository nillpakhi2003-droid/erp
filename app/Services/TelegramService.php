<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
    }

    /**
     * Send a message to Telegram
     */
    public function sendMessage(string $message): bool
    {
        try {
            if (empty($this->botToken) || empty($this->chatId)) {
                Log::error('Telegram credentials not configured');
                return false;
            }

            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram message failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a file to Telegram
     */
    public function sendDocument(string $filePath, string $caption = ''): bool
    {
        try {
            if (empty($this->botToken) || empty($this->chatId)) {
                Log::error('Telegram credentials not configured');
                return false;
            }

            if (!file_exists($filePath)) {
                Log::error('File not found: ' . $filePath);
                return false;
            }

            $response = Http::attach(
                'document',
                file_get_contents($filePath),
                basename($filePath)
            )->post("https://api.telegram.org/bot{$this->botToken}/sendDocument", [
                'chat_id' => $this->chatId,
                'caption' => $caption,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram document upload failed: ' . $e->getMessage());
            return false;
        }
    }
}
