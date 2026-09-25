# Property Listings API

A small REST API for managing property listings, built with Laravel 12 and MySQL.

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+ (or MariaDB with compatible spatial/math functions)
- PHP PDO MySQL extension

## Setup

1. Install dependencies:

	```bash
	composer install
	```

2. Create the environment file and application key:

	```bash
	copy .env.example .env
	php artisan key:generate
	```

3. Create a MySQL database, then set these values in `.env`:

	```dotenv
	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=propertylisting
	DB_USERNAME=root
	DB_PASSWORD=
	```

4. Run migrations:

	```bash
	php artisan migrate
	```

5. Start the local API:

	```bash
	php artisan serve
	```

The API is available at `http://127.0.0.1:8000/api`.

## API endpoints

All request and response bodies are JSON. Validation errors return HTTP `422` with Laravel's standard `message` and `errors` fields. Missing listings return HTTP `404`.

| Method | URI | Purpose |
| --- | --- | --- |
| `GET` | `/api/listings` | Paginated listings (`per_page` 1-100) |
| `POST` | `/api/listings` | Create a listing |
| `GET` | `/api/listings/{id}` | Get one listing |
| `PATCH` | `/api/listings/{id}` | Update a listing partially |
| `DELETE` | `/api/listings/{id}` | Delete a listing; returns `204` |
| `GET` | `/api/listings/search` | Filter and search listings |

Create/update fields:

```json
{
  "title": "Modern two-bedroom apartment",
  "price": 250000,
  "type": "rent",
  "bedrooms": 2,
  "location": "Victoria Island, Lagos",
  "latitude": 6.4281,
  "longitude": 3.4219,
  "agent_id": 1
}
```

`type` must be `rent`, `sale`, or `shortlet`. Coordinates are validated against their geographic bounds, and `agent_id` must reference an existing user.

### Search

Search accepts any combination of:

- `type=rent|sale|shortlet`
- `min_price` and `max_price`
- `bedrooms`
- `latitude`, `longitude`, and `radius_km` together
- `per_page` (default `15`, maximum `100`)

Example:

```text
GET /api/listings/search?type=rent&min_price=100000&max_price=500000&bedrooms=2&latitude=6.4281&longitude=3.4219&radius_km=10&per_page=20
```

Radius searches use the Haversine formula in MySQL and return nearest listings first. The response follows Laravel's paginator format with `data`, `links`, and `meta`; each listing includes its address, coordinates, and agent ID.

## Design choices

- Form Request classes keep validation separate from controller actions and make create, update, and search rules explicit.
- Eloquent model binding gives consistent `404` behavior for missing listing IDs.
- `ListingResource` provides a stable public response shape and avoids exposing database details directly.
- Database indexes cover common filters (`type`/`price`, `bedrooms`/`price`, and coordinates).
- MySQL performs the geospatial calculation in SQL so filtering and pagination happen in the database. SQLite is used by the automated tests and uses a portable coordinate-distance approximation for the radius predicate.
- Agents use the existing Laravel `users` table, with a foreign key preventing orphaned listings.

## Testing

Tests use SQLite in-memory and can be run with:

```bash
php artisan test
```

The feature suite covers creation, validation failures, combined search filters, radius filtering, pagination metadata, update, and deletion.

## What I would improve with more time

- Add authentication and authorization so agents can only manage their own listings.
- Add an explicit API version (`/api/v1`) and OpenAPI documentation.
- Use a spatial `POINT` column and a spatial index for larger datasets and more efficient geographic queries.
- Add rate limiting, structured request IDs, production logging, and CI checks.
- Add soft deletes, image/media storage, richer location normalization, and contract tests for MySQL specifically.
## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
