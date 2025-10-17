# Бронирование-мини

Laravel 12 + Inertia/Vue 3. Мини‑сервис бронирования с недельным календарём и защитой от гонок.

### Стек
- PHP 8.2, Laravel 12
- MySQL 8
- Inertia.js + Vue 3, Vite, Tailwind
- Nginx + php-fpm (Docker)

### Ключевые правила
- Рабочие дни: Пн–Сб, Вс — закрыто
- Окно бронирования: 10:00–20:00 (МСК)
- Фактическая продолжительность: опция + 30 мин буфер
- Время в БД — UTC; расчёты/интерфейс — МСК
- Пересечения по `service_id`, статус `booked`

### Доменные сущности
- Venue 1–N Service
- Service 1–N ServiceOption | 1–N ServiceSchedule | 1–N Booking

### Эндпоинты
- Web: `/` (список услуг), `/services/{service}` (бронирование)
- API:
  - GET `/services/{service}/slots?date=YYYY-MM-DD&option_id=ID` → `{ slots: ["HH:MM"] }`
  - POST `/bookings` → `201 { booking }` или `409 { message: "Слот занят" }`

### Гонки (race condition)
Транзакция с диапазонной блокировкой:
```
SELECT id FROM bookings
WHERE service_id = :sid AND status='booked'
  AND starts_at < :new_end AND ends_at > :new_start
FOR UPDATE;
```
Если пусто — INSERT, иначе 409.

### Seed
- Площадка + услуги: «Поездка на квадроцикле», «Тур на эндуро»
- Опции: 30/60/120 мин
- Расписание: Пн–Сб 10:00–20:00, Вс закрыт
- Предзаполненные брони по ТЗ

---

## Запуск локально
```
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm i
npm run dev
php artisan serve
```

## Запуск в Docker
```
cp .env.example .env
# DB_*= booking/root как в compose по умолчанию
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```
Откройте: http://localhost:8000

## .env (пример БД)
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=booking
DB_USERNAME=root
DB_PASSWORD=root

APP_TIMEZONE=Europe/Moscow
```

