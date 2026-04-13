# Doctor Booking API

![Doctor Booking API Banner](public/images/doctor-booking-api-banner.svg)

نظام حجز أطباء مبني بـ Laravel 13 على هيئة API، ومجهز بهيكل منظم لتسهيل التطوير والصيانة والتوسع.

## نظرة سريعة

- تسجيل ودخول وخروج واسترجاع كلمة المرور
- إدارة الأطباء والتخصصات والجداول الزمنية
- الحجوزات والمدفوعات والتقييمات
- الترجمة واللغات والإعدادات
- Spatie Roles & Permissions لإدارة الصلاحيات
- طبقات منفصلة لـ Controllers وRequests وServices وRepositories وPolicies

## التقنيات المستخدمة

- PHP 8.4
- Laravel 13
- MySQL
- Vite + Tailwind CSS 4
- PHPUnit 12
- Laravel Pint
- Spatie Laravel Permission

## هيكل المشروع

أهم المجلدات الحالية داخل `app`:

- `app/Http/Controllers/Api/V1`
- `app/Http/Requests/Api/V1`
- `app/Models`
- `app/Repositories`
- `app/Repositories/Interfaces`
- `app/Services`
- `app/Events`
- `app/Listeners`
- `app/Jobs`
- `app/Policies`
- `app/Http/Middleware`

## الجداول الأساسية

- `users`
- `roles` / `permissions` / جداول Spatie المرتبطة بها
- `specialties`
- `doctors`
- `time_slots`
- `appointments`
- `payments`
- `reviews`
- `translations`
- `locales`
- `settings`

## التشغيل محليًا

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
npm install
npm run build
php artisan serve
```

ولو تريد تشغيل الواجهة أثناء التطوير:

```bash
npm run dev
```

## الاختبار

```bash
php artisan test
```

## ملاحظات مهمة

- الصفحة الرئيسية الحالية هي صفحة Laravel الافتراضية (`/`).
- الهيكل الداخلي جاهز لربط API v1 على مستوى Controllers / Requests / Services.
- تم استبعاد مجلدات البيئة الخاصة بالعمل داخل Codex من `.gitignore` حتى لا تدخل في أي commit.

## CI Workflow

يوجد Workflow واحد داخل `.github/workflows/test.yml`:

- يعمل على `push` و `pull_request`
- يستخدم MySQL
- يثبت الاعتمادات
- يشغّل `pint`
- يطبق migrations
- يشغّل الاختبارات

