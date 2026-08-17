# Task Manager

A small multi-user task manager: registration, login, and per-user task
CRUD with ownership checks, built up over a series of steps and finished
here with Composer, PSR-4 autoloading, real namespaces, and a Pest test
suite.

## What changed in this step

- **Composer + PSR-4.** All the `require __DIR__ . '/...'` chains are
  gone. `composer.json` maps `App\` to `app/`, and `vendor/autoload.php`
  loads every class on demand.
- **Namespaces and a folder-per-role layout.**
  - `App\Core` — `Container`, `Router`, `Database`, `Validator`
  - `App\Core\Exceptions` — `RedirectException`, `AbortException`
  - `App\Controllers` — `Controller`, `AuthController`, `TaskController`
  - `App\Middleware` — `Middleware`, `AuthMiddleware`, `GuestMiddleware`
  - `App\Repositories` — `UserRepository`, `TaskRepository`
- **`redirect()`/`abort()` now throw instead of calling
  `header()`/`exit`/`die()` directly.** That was the one thing standing
  in the way of testing controllers directly: you can't unit-test code
  that calls `exit`. `Controller::redirect()` throws `RedirectException`,
  `Controller::abort()` throws `AbortException`, and middleware throws
  `RedirectException` too. `public/index.php` is the only place that
  turns those into a real `header()` call — tests just catch the
  exception and assert on it.
- **Pest tests** for registration, login, task CRUD, and the
  ownership/authorization checks (`tests/Feature/`), running against an
  in-memory SQLite database so they're fast and need no setup.

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

The suite doesn't touch MySQL — `tests/TestCase.php` builds a fresh
in-memory SQLite database (`database/schema.sqlite.sql`) for every test
and binds it into the container in place of the real `PDO`.

- `tests/Feature/RegistrationTest.php` — valid registration, mismatched
  passwords, short passwords, duplicate email
- `tests/Feature/LoginTest.php` — valid login, wrong password, unknown
  email, blank fields
- `tests/Feature/TaskCrudTest.php` — create, list, filter, update, delete
- `tests/Feature/TaskAuthorizationTest.php` — a user can't view, edit, or
  delete another user's task (403), and missing/invalid ids return 404
- `tests/Feature/MiddlewareTest.php` — `AuthMiddleware` and
  `GuestMiddleware` redirect (or don't) as expected
