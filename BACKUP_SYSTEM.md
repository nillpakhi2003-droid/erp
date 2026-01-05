# Database Backup System

## Overview
This system automatically backs up the database daily and sends it to a Telegram channel.

## Setup Instructions

### 1. Create a Telegram Bot

1. Open Telegram and search for **@BotFather**
2. Send `/newbot` command
3. Follow the instructions to create your bot
4. Copy the **Bot Token** (looks like: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

### 2. Get Your Chat ID

**Option A: For Personal Chat**
1. Send a message to your bot
2. Visit: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
3. Look for `"chat":{"id":123456789}` and copy the ID

**Option B: For Channel**
1. Create a channel
2. Add your bot as an administrator
3. Send a message to the channel
4. Visit: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
5. Look for the channel chat ID (starts with `-100`)

### 3. Configure Environment Variables

Edit your `.env` file and add:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```

### 4. Setup Cron Job (Important!)

Laravel's scheduler requires a single cron entry. Add this to your server's crontab:

```bash
# Edit crontab
crontab -e

# Add this line:
* * * * * cd /path/to/your/erp && php artisan schedule:run >> /dev/null 2>&1
```

Replace `/path/to/your/erp` with your actual project path (e.g., `/var/www/html/erp` or `/workspaces/ERP`).

## Usage

### Manual Backup

Create a backup without sending to Telegram:
```bash
php artisan db:backup
```

Create a backup and send to Telegram:
```bash
php artisan db:backup --telegram
```

### Automatic Daily Backup

Once the cron job is set up, backups will automatically run every day at **2:00 AM (Bangladesh Time)**.

You can change the time in [routes/console.php](routes/console.php):
```php
Schedule::command('db:backup --telegram')
    ->dailyAt('02:00')  // Change this time
    ->timezone('Asia/Dhaka');
```

## Features

✅ **Automatic Daily Backups** - Runs every day at 2 AM  
✅ **Telegram Integration** - Sends backup to your channel/chat  
✅ **Compression** - Automatically compresses backups with gzip  
✅ **Auto Cleanup** - Keeps only last 7 days of backups  
✅ **MySQL & PostgreSQL Support** - Works with both databases  
✅ **Detailed Logging** - Tracks success and failures  

## Backup Details

- **Location**: `storage/app/backups/`
- **Format**: `backup_dbname_YYYY-MM-DD_HH-MM-SS.sql.gz`
- **Retention**: 7 days (configurable)
- **Compression**: gzip

## Troubleshooting

### Backup not running automatically?
- Check if cron job is set up correctly: `crontab -l`
- Check Laravel logs: `storage/logs/laravel.log`
- Test manually: `php artisan db:backup --telegram`

### Telegram not working?
- Verify bot token and chat ID in `.env`
- Make sure bot is administrator in channel
- Check logs: `storage/logs/laravel.log`

### Database backup fails?
- Check database credentials in `.env`
- Ensure `mysqldump` or `pg_dump` is installed
- Verify disk space: `df -h`

## Security Notes

⚠️ **Important**: 
- Never commit `.env` file to git
- Keep your bot token secret
- Consider encrypting backups for sensitive data
- Restrict backup directory permissions: `chmod 700 storage/app/backups`

## Monitoring

Check recent backups:
```bash
ls -lh storage/app/backups/
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

Test schedule (without waiting):
```bash
php artisan schedule:run
```
