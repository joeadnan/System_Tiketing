# IT Helpdesk Ticketing System - Laravel Starter Kit

Starter kit ini berisi modul dasar aplikasi IT Helpdesk Ticketing System menggunakan Laravel + MySQL.

Fitur utama:

- Ticket intake dari web, email, WhatsApp, dan phone/manual.
- Nomor tiket otomatis: `TKT-YYYYMM-0001`.
- Priority matrix otomatis: impact x urgency.
- SLA response dan resolution.
- Pause SLA saat status `pending_user`.
- Resume SLA saat status kembali aktif.
- SLA warning saat sisa waktu 20%.
- SLA breach otomatis.
- Auto assignment berdasarkan load balancing agent aktif dengan tiket aktif paling sedikit.
- Eskalasi L1 -> L2 -> L3 -> vendor eksternal.
- Handover note wajib saat eskalasi.
- Lifecycle status: `open`, `in_progress`, `pending_user`, `resolved`, `closed`, `reopened`, `cancelled`.
- Reopen maksimal 72 jam setelah closed.
- Resolution note, root cause, prevention note, dan user confirmation.
- CSAT survey setelah closed.
- Dashboard dan laporan KPI sederhana.

## Cara Pakai

1. Buat project Laravel baru.
2. Install auth starter kit, misalnya Laravel Breeze.
3. Copy isi folder ini ke root project Laravel.
4. Sesuaikan `.env` database MySQL.
5. Jalankan:

```bash
php artisan migrate
php artisan db:seed
php artisan queue:table
php artisan migrate
php artisan queue:work
```

6. Jalankan scheduler server:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Untuk Laravel 11+, scheduler didefinisikan di `routes/console.php`.

## Akun Demo

Semua password: `password`

- superadmin@demo.local
- manager@demo.local
- l1@demo.local
- l2@demo.local
- l3@demo.local
- user@demo.local

## Catatan

- SLA P3 dan P4 pada starter ini menggunakan hitungan menit kalender. Jika ingin benar-benar hari kerja, extend `TicketSlaService` dengan business hours calendar.
- WhatsApp dan email parser dibuat sebagai field `source`, integrasi gateway-nya bisa ditambahkan di tahap berikutnya.
- Role dibuat sederhana sebagai kolom `users.role`, belum memakai Spatie Permission.
