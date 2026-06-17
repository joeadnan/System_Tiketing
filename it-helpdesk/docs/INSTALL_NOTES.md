# Install Notes

## Generate Laravel project

```bash
composer create-project laravel/laravel it-helpdesk
cd it-helpdesk
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
```

## Copy starter kit

Copy folder `app`, `database`, `resources`, dan `routes` dari starter kit ini ke root project.

## Setup storage

```bash
php artisan storage:link
```

## Queue

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## Scheduler

Tambahkan cron di server:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Catatan Laravel versi lama

Jika project masih memakai `app/Console/Kernel.php`, tambahkan:

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('tickets:check-sla')->everyFiveMinutes();
}
```
