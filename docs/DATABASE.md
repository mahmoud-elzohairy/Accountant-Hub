# Database Schema

MySQL. Four domain tables — `users`, `categories`, `jobs`, `bids` — plus Laravel's framework
tables (`sessions`, `cache`, `personal_access_tokens`).

## Entity-relationship diagram

```
┌────────────┐         ┌─────────────────┐         ┌────────────┐
│ categories │ 1     ∞ │      jobs       │ ∞     1 │            │
│────────────│─────────│─────────────────│         │            │
│ id (PK)    │         │ id (PK)         │         │            │
│ name       │         │ category_id (FK)│         │            │
│ slug (UQ)  │         │ title           │         │            │
│ description│         │ company_name    │         │            │
└────────────┘         │ company_about   │         │            │
                       │ short_description         │            │
                       │ description     │         │            │
                       │ required_skills (json)     │            │
                       │ budget_min      │         │            │
                       │ budget_max      │         │            │
                       │ delivery_days   │         │            │
                       │ deadline        │         │            │
                       │ attachments (json)         │            │
                       │ status (open|closed)       │            │
                       └────────┬────────┘         │            │
                                │ 1                 │            │
                                │                   │            │
                                │ ∞                 │            │
                       ┌────────┴────────┐         ┌────────────┐
                       │      bids       │ ∞     1 │   users    │
                       │─────────────────│─────────│────────────│
                       │ id (PK)         │         │ id (PK)    │
                       │ job_id (FK)     │         │ name       │
                       │ user_id (FK)    │─────────│ email (UQ) │
                       │ amount          │         │ headline   │
                       │ delivery_days   │         │ password   │
                       │ cover_letter    │         └────────────┘
                       │ experience_summary
                       │ UNIQUE(job_id, user_id)   │
                       └─────────────────┘
```

## Relationships

| Relationship                         | Eloquent                                  |
| ------------------------------------ | ----------------------------------------- |
| A job **belongs to** a category      | `Job::category()` → `belongsTo`           |
| A category **has many** jobs         | `Category::jobs()` → `hasMany`            |
| A job **has many** bids              | `Job::bids()` → `hasMany`                 |
| A bid **belongs to** a job           | `Bid::job()` → `belongsTo`                |
| A user **has many** bids             | `User::bids()` → `hasMany`                |
| A bid **belongs to** a user          | `Bid::user()` → `belongsTo`               |

## Constraints & indexes

| Table   | Constraint                                  | Purpose                                       |
| ------- | ------------------------------------------- | --------------------------------------------- |
| `users` | `UNIQUE(email)`                             | One account per email                         |
| `categories` | `UNIQUE(slug)`                         | Stable, URL-safe identifier                   |
| `jobs`  | `FK category_id → categories` (cascade)     | A job always has a valid category             |
| `jobs`  | `INDEX(status)`, `INDEX(created_at)`        | Fast filtering by status / "newest" sort      |
| `bids`  | `FK job_id → jobs` (cascade)                | Bids removed if a job is deleted              |
| `bids`  | `FK user_id → users` (cascade)              | Bids removed if a user is deleted             |
| `bids`  | **`UNIQUE(job_id, user_id)`**               | **Enforces one bid per accountant per job**   |

The `UNIQUE(job_id, user_id)` index is the database-level guarantee behind the "one bid per job"
rule. The service also checks beforehand for a friendly 409, but the index is the source of truth
and handles concurrent requests safely.

## Column notes

- `jobs.required_skills` and `jobs.attachments` are **JSON** columns, cast to PHP arrays in the model.
- `jobs.status` is an enum (`open` / `closed`), defaulting to `open`.
- `jobs.budget_min` / `budget_max` and `bids.amount` are `DECIMAL(10,2)`.
- `users.headline` is a nullable accountant tagline (e.g. *"Certified Public Accountant · 8 yrs"*).
- Posted date uses the standard `created_at` timestamp (exposed as `posted_at` in the API).

## Seed data

`php artisan migrate:fresh --seed` creates:

- **8 categories** — Tax Preparation, Bookkeeping, Auditing, Payroll, Financial Reporting,
  VAT & Compliance, Management Accounting, Forensic Accounting.
- **12 jobs** — realistic, hand-written titles/descriptions across all categories (11 open, 1 closed).
- **1 demo accountant** — `accountant@demo.test` / `password`, pre-seeded with 2 bids.
- **8 additional accountants** — to give jobs realistic bid counts (1–4 bids each).
