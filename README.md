# Translation Management Service

## Setup

1. **Clone the repository**

```bash
git clone https://github.com/bruzp/translation-service-api.git
cd translation-service-api
```

2. **Build Docker images**

```bash
docker compose build --no-cache
```

3. **Install dependencies**

```bash
docker compose run --rm app composer install
```

4. **Create environment file**

```bash
cp .env.example .env
```

5. **Start containers**

```bash
docker compose up -d
```

6. **Generate application key**

```bash
docker compose exec app php artisan key:generate
```

7. **Run migrations and seeders**

```bash
docker compose exec app php artisan migrate --seed
```

---

## Running Tests

```bash
docker compose exec app php artisan test
```

For coverage:

```bash
docker compose exec app php artisan test --coverage
```

---

## API Overview

### Public Endpoint (CDN-friendly)

```
GET /api/translations/export?locale=en&tag=web
```

- No authentication required  
- Uses `ETag` + `Cache-Control` for efficient revalidation  
- Designed to be served via CDN  

---

### Authentication

```
POST /api/auth/login
POST /api/auth/logout
```

---

### Sample User

A default user is available after seeding:

```
Email: test@example.com
Password: password
```

> Password is the default factory password.

---

### Protected Translation Endpoints

```
GET    /api/translations
POST   /api/translations
PUT    /api/translations/{id}
DELETE /api/translations/{id}
```

Authentication via **Bearer token (Sanctum)**.

---

## API Documentation

Swagger UI is available at:

```
http://localhost:8000/api/documentation
```

---

## Design Choices

- **Service + Repository pattern**  
  Keeps business logic separate from controllers and data access.

- **Explicit queries over route model binding**  
  Avoids hidden queries and keeps control over how data is retrieved.

- **Form Request classes for validation**  
  Centralizes validation and keeps controllers clean.

- **API Resources for responses**  
  Ensures consistent response structure.

- **DTOs for parameter handling**  
  Makes method contracts explicit and avoids loosely passing arrays.

- **Sanctum for authentication**  
  Lightweight and well integrated for API token handling.

- **Database optimization**  
  Applied indexes (including composite) to support search and filtering at scale.

- **PEST for testing**  
  Cleaner syntax and easier to read tests.

- **Dockerized environment**  
  Ensures consistent setup across machines.

---

## CDN Support

The export endpoint is designed to work behind a CDN:

- Public (no authentication)
- Uses `ETag` for cache validation
- Uses `Cache-Control: public, max-age=0, must-revalidate`

This ensures:
- Always up-to-date responses
- Reduced payload via `304 Not Modified`
- Compatibility with CDN caching strategies

---

## Notes

- Translations are stored per locale and can be filtered by tag.
- The export endpoint reflects the latest data using ETag validation.
- The system is designed to handle large datasets efficiently with proper indexing.
- **phpMyAdmin is included for testing only and should not be used in production.**