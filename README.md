# Tapir Test (Laravel + Docker)

## Структура
- `src/` – весь код Laravel (app, config, public, routes, vendor и т.д.)
- `nginx/default.conf` – конфигурация Nginx
- `Dockerfile`, `docker-compose.yml` – запуск окружения
- `prometheus/` – пример конфиг Prometheus (опционально)
- `README.md` – эта инструкция

## Шаги для запуска

1. Клонируйте репозиторий:
   ```bash
   git clone https://github.com/orhan17/tapir-test.git
   cd tapir-test
   ```

2. Скопируйте `.env.example` в `.env` (внутри папки `src/`):
   ```bash
   cp src/.env.example src/.env
   cp .env.example .env
   ```

3. Убедитесь, что в `src/.env` прописаны корректные настройки:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=laravel
   ```

4. Запустите контейнеры:
   ```bash
   docker compose up --build
   ```

5. Зайдите в контейнер PHP-FPM и установите Laravel-зависимости:
   ```bash
   docker compose exec app bash
   composer install
   php artisan key:generate
   php artisan migrate
   exit
   ```

6. Проверьте в браузере:
   - [http://localhost:8000](http://localhost:8000) – главная страница Laravel
   - [http://localhost:8081](http://localhost:8081) – phpMyAdmin (логин: `laravel`, пароль: `laravel`, если не меняли)
   - [http://localhost:9090](http://localhost:9090) – Prometheus
   - [http://localhost:3000](http://localhost:3000) – Grafana

## API

- **Фильтр авто:** `GET /stock?year_from=2010&price_less=1000000`
- **Создание заявки:** `POST /application` (поля: `phone`, `car_id`)

## Admin Orchid

- Панель администратора: [http://localhost:8000/admin](http://localhost:8000/admin)
- Создать пользователя:
   ```bash
   docker compose exec app php artisan orchid:admin
   ```
  Затем войдите логином/паролем, который укажете.
