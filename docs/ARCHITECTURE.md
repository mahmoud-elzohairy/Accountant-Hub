# Architecture

Accountant Hub is a **single Laravel application** that serves both the JSON API and the compiled
Vue single-page app. There is no separate Node server in production — Laravel serves `public/` and
the SPA handles client-side routing.

```
┌──────────────────────────────────────────────────────────────┐
│  Browser                                                       │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │  Vue 3 SPA  (Vue Router · Pinia · Tailwind)              │ │
│  │   pages → components → stores → lib/api (fetch + token)  │ │
│  └───────────────────────────┬──────────────────────────────┘ │
└──────────────────────────────┼────────────────────────────────┘
                               │  JSON over HTTPS (Bearer token)
┌──────────────────────────────┼────────────────────────────────┐
│  Laravel 13                  ▼                                 │
│   routes/api.php  →  Controller  →  Service  →  Repository     │
│                          │            │            │           │
│                     FormRequest   business      Eloquent       │
│                     + Resource    rules         + scopes       │
│                                                    │           │
│                                                  MySQL         │
└────────────────────────────────────────────────────────────────┘
```

---

## Backend: Controller → Service → Repository

A deliberate layered separation keeps each class small and testable.

### 1. Controller (`app/Http/Controllers/Api`)
Thin HTTP adapters. They:
- type-hint a **Form Request** so validation runs before the method body,
- call a single **Service** method,
- wrap the result in an **API Resource**.

They contain no business logic and no direct database calls.

### 2. Service (`app/Services`)
Holds business rules and orchestration. Example — `BidService::submit()`:
- rejects bids on **closed** jobs (`JobClosedException` → 422),
- rejects a **second bid** from the same accountant (`DuplicateBidException` → 409),
- catches the DB `UniqueConstraintViolationException` as a final guard against race conditions.

Services depend on repository **interfaces**, never concrete classes.

### 3. Repository (`app/Repositories`)
The only layer that touches Eloquent.
- Contracts in `Repositories/Contracts` (e.g. `JobRepositoryInterface`).
- Implementations in `Repositories/Eloquent`.
- Bound together in `app/Providers/RepositoryServiceProvider.php` via a `$bindings` map, so the
  data source can be swapped (a fake/in-memory repo, a different ORM) without changing services.

### 4. Model (`app/Models`)
Eloquent relationships + reusable query logic as **scopes** — e.g. `Job::scopeFilter()` and
`Job::scopeSortBy()` keep filtering/sorting declarative and out of the repository.

---

## Request lifecycle (example: submitting a bid)

```
POST /api/jobs/1/bids
   │
   ├─ auth:sanctum middleware           → 401 if no/invalid token
   ├─ Route model binding {job}         → 404 if job missing
   ├─ StoreBidRequest                   → 422 on invalid input
   │
   ├─ BidController::store()
   │     └─ BidService::submit(user, job, data)
   │           ├─ job closed?           → JobClosedException (422)
   │           ├─ already bid?          → DuplicateBidException (409)
   │           └─ BidRepository::create()
   │                 └─ unique(job_id,user_id) DB guard → DuplicateBidException (409)
   │
   └─ 201 + BidResource
```

---

## Cross-cutting backend pieces

| Concern            | Where                                   |
| ------------------ | --------------------------------------- |
| Validation         | `app/Http/Requests/*`                   |
| Response shaping    | `app/Http/Resources/*`                  |
| Domain errors      | `app/Exceptions/*` (self-rendering)     |
| JSON error rendering | `bootstrap/app.php` (`shouldRenderJsonWhen`) |
| Auth               | Laravel Sanctum personal access tokens  |

---

## Frontend structure (`resources/js`)

| Path            | Responsibility                                                        |
| --------------- | --------------------------------------------------------------------- |
| `app.js`        | Boots Vue, Pinia, Router                                              |
| `App.vue`       | Layout shell — navbar, `<RouterView>`, footer, toasts                 |
| `router/`       | Routes + navigation guards (`requiresAuth`, `guestOnly`)              |
| `stores/`       | Pinia stores — `auth` (session/token), `toast` (notifications)        |
| `lib/api.js`    | `fetch` wrapper: attaches token, parses JSON, throws typed `ApiError` |
| `lib/format.js` | Money / date / relative-time formatting helpers                       |
| `components/`   | Reusable UI — `JobCard`, `JobFilters`, `Pagination`, `BaseModal`, `BidModal`, `StatusBadge`, `EmptyState`, `SkeletonCard`, `ToastContainer`, `Navbar` |
| `pages/`        | Route views — Jobs, JobDetail, Login, Register, Dashboard, NotFound   |

### Auth flow on the client
1. `login`/`register` store the token via `lib/api setToken()` (localStorage).
2. On app boot, the router guard calls `auth.fetchUser()` once to validate the session.
3. Protected routes (`/dashboard`) redirect guests to `/login?redirect=…`.
4. A 401 from any request clears the local session.

---

## Key design decisions

- **Token auth, not cookie/session** — keeps the API stateless and trivial to consume; no CSRF or
  cookie-domain configuration.
- **Repository interfaces** — the brief weights "clean code" and "code organization"; binding via
  contracts makes the data layer swappable and the services unit-testable in isolation.
- **`QUEUE_CONNECTION=sync`** — Laravel's queue reserves the `jobs` table name. Removing the queue
  migration frees that name for the domain *Jobs* entity (see README note).
- **Resources hide heavy fields in lists** — `description`/`required_skills` are only serialized on
  the detail endpoint (`routeIs('jobs.show')`), keeping the listing payload light.
