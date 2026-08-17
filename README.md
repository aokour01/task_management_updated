# Task Manager

A small multi-user task manager: registration, login, and per-user task
CRUD with ownership checks, built up over a series of steps and finished
here with Composer, PSR-4 autoloading, real namespaces, and a Pest test
suite.

## Project layout

```
app/
  Core/               Container, Router, Database, Validator
  Core/Exceptions/    RedirectException, AbortException
  Controllers/        Controller, AuthController, TaskController
  Middleware/         Middleware, AuthMiddleware, GuestMiddleware
  Repositories/       UserRepository, TaskRepository
bootstrap/app.php      Loads .env, starts the session, builds the container + router
routes/web.php         All route definitions
public/index.php       Front controller
views/                 Plain PHP templates
database/schema.sql        MySQL schema for the real app
database/schema.sqlite.sql SQLite schema used only by tests
tests/                  Pest test suite
```

## Setup

```bash
composer install
cp .env.example .env
# edit .env with your MySQL credentials, then create the database and run:
mysql -u root -p task_manager < database/schema.sql

php -S localhost:8000 -t public
```

Visit `http://localhost:8000/register` to create an account.

## Running the tests

```bash
composer install
vendor/bin/pest
```
- `tests/Feature/TaskCrudTest.php` — create, list, filter, update, delete
- `tests/Feature/TaskAuthorizationTest.php` — a user can't view, edit, or
  delete another user's task (403), and missing/invalid ids return 404
- `tests/Feature/MiddlewareTest.php` — `AuthMiddleware` and
  `GuestMiddleware` redirect (or don't) as expected
