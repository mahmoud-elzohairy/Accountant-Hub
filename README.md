# Accountant Hub

A small Upwork-style marketplace where companies post accounting jobs and accountants browse, view details, and apply with a bid.

Built as a single-page Vue application backed by a Laravel JSON API.

### 📚 Documentation
- [`docs/API.md`](docs/API.md) — full API reference with request/response examples
- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — layered design, request lifecycle, decisions
- [`docs/DATABASE.md`](docs/DATABASE.md) — schema, ERD, relationships, constraints

---

## Project overview

Accountant Hub lets logged-in accountants:

- **Browse** a paginated, filterable list of accounting jobs.
- **Browse by category** — a categories page, plus per-category paginated job listings.
- **View** full job details — description, required skills, client info, budget, deadline, bid count.
- **Apply** by submitting a bid (proposed price, delivery time, cover letter, experience).
- **Track** their submitted bids on a personal dashboard.

Business rules enforced by the backend:

- Only authenticated accountants can submit bids.
- An accountant can submit **only one bid per job** (enforced in the service layer *and* by a DB unique constraint).
- Bids cannot be placed on **closed** jobs.

---

## Tech stack

| Layer        | Choice                                              |
| ------------ | --------------------------------------------------- |
| Frontend     | Vue 3 (`<script setup>`), Vue Router, Pinia         |
| Styling      | Tailwind CSS v4 (brand palette: **black + #019a51**)|
| Build tool   | Vite                                                |
| Backend      | Laravel 13 (PHP 8.3)                                |
| Auth         | Laravel Sanctum (Bearer token API auth)             |
| Database     | MySQL 8 / 9                                          |
| Tests        | PHPUnit (feature tests, SQLite in-memory)           |

### Why Vue 3 instead of React/Next.js
The brief preferred **React/Next.js** but allows another stack *with justification*. I chose **Vue 3**:

1. **One app, one deploy.** Laravel ships first-class Vite + Vue support out of the box. The SPA
   builds straight into `public/build` and Laravel serves it — so this is a **single deployable
   unit** with no separate Node runtime, no second host, and no CORS layer to manage. A Next.js
   choice would have meant running and deploying a second server alongside Laravel for no benefit
   here (the app needs SPA interactivity, not SSR/SEO).
2. **The backend is the real evaluation surface.** The API is plain, framework-agnostic JSON, so
   none of the backend criteria (API design, validation, auth, relationships, business logic)
   depend on whether the client is React or Vue. The frontend choice is cosmetic to the API.
3. **Lower overhead for a solo build.** Vue's `<script setup>` SFCs keep components compact, and
   Pinia + Vue Router cover state and routing with minimal boilerplate — a good fit for shipping a
   complete, polished product quickly as a single developer.

The same UI/UX, component reuse, and responsiveness goals are fully met in Vue; the concepts map
1:1 to React if a rewrite were ever needed.

> **Note:** React/Next.js was the preferred stack, and I'm fully comfortable picking it up — the
> core ideas (component model, props/state, hooks ≈ composition API, client routing, fetch-based
> data layer) carry directly over from what's built here. I chose Vue for this build to ship a
> clean, single-deploy product fast, and I'm happy to learn and adopt React on the job as the
> team's stack requires.

---

## Architecture

The backend follows a layered **Controller → Service → Repository** pattern:

```
HTTP request
  └─ Controller        (Http/Controllers/Api)      thin — validates input, returns Resources
       └─ Service      (Services)                  business rules (duplicate bids, closed jobs)
            └─ Repository (Repositories/Eloquent)  data access, bound via interfaces
                 └─ Model (Models)                 Eloquent relationships + query scopes
```

- Repository **interfaces** live in `app/Repositories/Contracts` and are bound to Eloquent
  implementations in `app/Providers/RepositoryServiceProvider.php` — the data layer can be
  swapped without touching services.
- Responses are shaped by **API Resources** (`app/Http/Resources`) for consistent JSON.
- Validation lives in **Form Requests** (`app/Http/Requests`).
- Domain errors use dedicated exceptions (`DuplicateBidException` → 409, `JobClosedException` → 422)
  that render themselves as clean JSON.

> **Note on the `jobs` table:** Laravel's queue uses a table literally named `jobs`, which would
> collide with our domain *Jobs* entity. Since this app needs no background queue, `QUEUE_CONNECTION`
> is set to `sync` and the queue migration was removed, freeing the `jobs` table name for the domain.

### Database schema

```
users ──< bids >── jobs >── categories
```

| Table        | Key columns                                                                                  |
| ------------ | -------------------------------------------------------------------------------------------- |
| `users`      | name, email (unique), headline, password                                                     |
| `categories` | name, slug (unique), description                                                             |
| `jobs`       | category_id (FK), title, company_name, company_about, short_description, description, required_skills (json), budget_min, budget_max, delivery_days, deadline, attachments (json), status (`open`/`closed`) |
| `bids`       | job_id (FK), user_id (FK), amount, delivery_days, cover_letter, experience_summary — **unique(job_id, user_id)** |

Relationships: a job *belongs to* a category and *has many* bids; a user *has many* bids; a user can place *only one* bid per job.

---

## Setup instructions

### Prerequisites
- PHP 8.3+, Composer
- Node.js 20+ and npm
- MySQL 8 or 9 running locally

### 1. Clone & install dependencies
```bash
git clone <your-repo-url> accountant-hub
cd accountant-hub
composer install
npm install
```

### 2. Environment
```bash
cp .env.example .env
php artisan key:generate
```
Edit `.env` and set your MySQL credentials:
```dotenv
DB_CONNECTION=mysql
DB_DATABASE=db_accountant_hub
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Create the database
```bash
mysql -u root -p -e "CREATE DATABASE db_accountant_hub;"
```

### 4. Migrate & seed demo data
```bash
php artisan migrate:fresh --seed
```
This creates the schema, 8 categories, 12 realistic jobs, a demo accountant, and sample bids.

### 5. Build the frontend
```bash
npm run build      # production build
# or, for live reload during development:
npm run dev
```

---

## How to run the project locally

Open two terminals (or use `composer dev` which runs both):

```bash
# Terminal 1 — Laravel API + SPA
php artisan serve            # http://localhost:8000

# Terminal 2 — Vite dev server (hot reload)
npm run dev
```

Then visit **http://localhost:8000**.

Run the test suite:
```bash
php artisan test
```

---

## Test accountant credentials

```
Email:    accountant@demo.test
Password: password
```
The demo accountant comes pre-seeded with a couple of bids so the dashboard isn't empty.
You can also click **"Use demo accountant credentials"** on the login page to auto-fill them.

---

## API endpoints

Base URL: `/api`. Protected routes require an `Authorization: Bearer <token>` header.

### Public

| Method | Endpoint              | Description                                              |
| ------ | --------------------- | ------------------------------------------------------- |
| POST   | `/register`           | Register an accountant; returns user + token            |
| POST   | `/login`              | Log in; returns user + token                            |
| GET    | `/categories`         | List categories with open-job counts                    |
| GET    | `/categories/{id}`    | A single category with its open-job count               |
| GET    | `/jobs`               | List jobs (filter, sort, paginate)                      |
| GET    | `/jobs/{id}`          | Full details for one job                                |

**`GET /jobs` query parameters**

| Param         | Values                                              |
| ------------- | --------------------------------------------------- |
| `search`      | text — matches job title                            |
| `category`    | category id                                         |
| `budget_min`  | number                                              |
| `budget_max`  | number                                              |
| `status`      | `open` \| `closed`                                  |
| `sort`        | `newest` (default) \| `budget_high` \| `budget_low` \| `deadline` |
| `page`        | page number (9 jobs per page)                       |

### Protected (auth:sanctum)

| Method | Endpoint              | Description                                              |
| ------ | --------------------- | ------------------------------------------------------- |
| GET    | `/user`               | Current authenticated accountant                        |
| POST   | `/logout`             | Revoke the current token                                |
| POST   | `/jobs/{id}/bids`     | Submit a bid on a job                                    |
| GET    | `/my-bids`            | The authenticated accountant's bids (dashboard)         |

**`POST /jobs/{id}/bids` body**
```json
{
  "amount": 1800,
  "delivery_days": 14,
  "cover_letter": "Why I'm a great fit…",
  "experience_summary": "Relevant experience…"
}
```

**Status codes used**

| Code | Meaning                                            |
| ---- | -------------------------------------------------- |
| 201  | Created (registration, bid submitted)              |
| 401  | Unauthenticated (bidding while logged out)         |
| 404  | Job not found                                      |
| 409  | Duplicate bid — already applied to this job        |
| 422  | Validation error / job closed                      |

---

## Deployment

The app is a single Laravel deployable (Laravel serves the built Vue SPA). General steps for
any host (VPS / Render / Railway / shared hosting with PHP 8.3 + MySQL):

1. Set production env: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, and
   the production MySQL credentials.
2. Install & build:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan key:generate
   php artisan migrate --force --seed
   php artisan config:cache && php artisan route:cache
   ```
3. Point the web server's document root at `public/`.
4. Ensure `storage/` and `bootstrap/cache/` are writable.

---

## Assumptions made

- **Accountants only.** Per the brief, client/company authentication isn't required — jobs are
  seeded and store the company name/description as plain fields rather than belonging to a client account.
- **Token auth over cookies.** Sanctum personal-access tokens (stored in `localStorage`) keep the
  API stateless and simple to consume; no CSRF/cookie-domain setup needed.
- **Attachments are a placeholder.** The schema has an `attachments` JSON column and the UI shows an
  empty-state placeholder, but no file upload is implemented (out of scope for the brief).
- **Currency is USD**, formatted client-side; budgets are stored as decimals.
- **Vue instead of React** — explained in *Why Vue 3 instead of React/Next.js* above.
- **Job status** is toggled in data (seeded open/closed); there's no admin UI to change it since
  client/admin accounts are out of scope.
