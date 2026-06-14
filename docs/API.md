# API Reference

Base URL: `/api`

All responses are JSON. Authenticated endpoints require a Sanctum bearer token:

```
Authorization: Bearer <token>
Accept: application/json
```

Tokens are returned by `/register` and `/login`. The Vue frontend stores the token in
`localStorage` and attaches it to every request.

---

## Authentication

### `POST /api/register`
Register a new accountant and receive an API token.

**Body**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "headline": "Senior Tax Accountant · 5 yrs",
  "password": "password123",
  "password_confirmation": "password123"
}
```
`headline` is optional.

**201 Created**
```json
{
  "user": { "id": 10, "name": "Jane Doe", "email": "jane@example.com", "headline": "Senior Tax Accountant · 5 yrs" },
  "token": "11|plainTextTokenHere"
}
```

**422** — validation errors (`name`, `email`, `password`).

---

### `POST /api/login`
**Body**
```json
{ "email": "accountant@demo.test", "password": "password" }
```

**200 OK** — same shape as register (`user` + `token`).

**422** — `{ "message": "...", "errors": { "email": ["The provided credentials are incorrect."] } }`

---

### `POST /api/logout` 🔒
Revokes the token used for the current request.

**200 OK** — `{ "message": "Logged out successfully." }`

---

### `GET /api/user` 🔒
Returns the authenticated accountant.

**200 OK**
```json
{ "data": { "id": 1, "name": "Mahmoud Elzohairy", "email": "accountant@demo.test", "headline": "Certified Public Accountant · 8 yrs" } }
```

---

## Categories

### `GET /api/categories`
All categories with a count of their **open** jobs (used by the filter UI).

**200 OK**
```json
{
  "data": [
    { "id": 3, "name": "Auditing", "slug": "auditing", "description": "Internal and external audit and assurance work.", "jobs_count": 2 }
  ]
}
```

### `GET /api/categories/{id}`
A single category with its open-job count (used as the header on the category page).

**200 OK**
```json
{ "data": { "id": 2, "name": "Bookkeeping", "slug": "bookkeeping", "description": "...", "jobs_count": 1 } }
```

**404** — when the category doesn't exist.

---

## Jobs

### `GET /api/jobs`
Paginated, filterable, sortable listing. **9 jobs per page.**

**Query parameters**

| Param        | Type    | Notes                                                              |
| ------------ | ------- | ------------------------------------------------------------------ |
| `search`     | string  | Matches job title (partial, case-insensitive)                      |
| `category`   | integer | Category id (must exist)                                           |
| `budget_min` | number  | Returns jobs whose `budget_max >= budget_min`                      |
| `budget_max` | number  | Returns jobs whose `budget_min <= budget_max`                      |
| `status`     | enum    | `open` or `closed`                                                 |
| `sort`       | enum    | `newest` (default), `budget_high`, `budget_low`, `deadline`        |
| `page`       | integer | Page number                                                        |

**200 OK** (list items omit the heavy `description`/`required_skills` fields)
```json
{
  "data": [
    {
      "id": 1,
      "title": "Prepare and File 2025 Corporate Tax Return",
      "company_name": "Brightpath Logistics",
      "short_description": "Prepare and e-file our annual corporate tax return for FY2025.",
      "category": { "id": 1, "name": "Tax Preparation", "slug": "tax-preparation", "description": "..." },
      "budget": { "min": 1200, "max": 2500 },
      "delivery_days": 14,
      "deadline": "2026-07-13",
      "status": "open",
      "bids_count": 4,
      "posted_at": "2026-06-13T16:08:13+00:00"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 2, "per_page": 9, "total": 12 }
}
```

---

### `GET /api/jobs/{id}`
Full detail for a single job. Adds `description`, `company_about`, `required_skills`, and
`attachments`. When called with a valid token, also includes `has_bid` (whether the current
accountant has already bid).

**200 OK**
```json
{
  "data": {
    "id": 1,
    "title": "Prepare and File 2025 Corporate Tax Return",
    "company_name": "Brightpath Logistics",
    "company_about": "A mid-sized freight company operating across three states with ~80 employees.",
    "short_description": "...",
    "description": "We need an experienced tax accountant to...",
    "required_skills": ["Corporate Tax", "Tax Filing", "GAAP", "Excel"],
    "attachments": [],
    "category": { "id": 1, "name": "Tax Preparation" },
    "budget": { "min": 1200, "max": 2500 },
    "delivery_days": 14,
    "deadline": "2026-07-13",
    "status": "open",
    "bids_count": 4,
    "posted_at": "2026-06-13T16:08:13+00:00",
    "has_bid": false
  }
}
```

**404** — `{ "message": "..." }` when the job doesn't exist.

---

## Bids

### `POST /api/jobs/{id}/bids` 🔒
Submit a bid on a job.

**Body**
```json
{
  "amount": 1800,
  "delivery_days": 14,
  "cover_letter": "I have prepared dozens of corporate returns and can deliver this accurately.",
  "experience_summary": "8 years in corporate tax, CPA certified."
}
```

**Validation**

| Field                | Rules                                                      |
| -------------------- | ---------------------------------------------------------- |
| `amount`             | required, numeric, **within the job's `budget_min`–`budget_max`** |
| `delivery_days`      | required, integer, 1–365                                   |
| `cover_letter`       | required, 20–5000 chars                                    |
| `experience_summary` | required, 10–2000 chars                                    |

**201 Created**
```json
{
  "message": "Your bid has been submitted successfully.",
  "data": {
    "id": 27,
    "amount": 1800,
    "delivery_days": 14,
    "cover_letter": "...",
    "experience_summary": "...",
    "submitted_at": "2026-06-13T17:02:11+00:00"
  }
}
```

**Error responses**

| Code | When                                                                 |
| ---- | -------------------------------------------------------------------- |
| 401  | Not authenticated                                                    |
| 404  | Job not found                                                        |
| 409  | `You have already submitted a bid for this job.` (one bid per job)   |
| 422  | Validation failed, **or** `This job is closed and no longer accepting bids.` |

---

### `GET /api/my-bids` 🔒
The authenticated accountant's bids (newest first, paginated), each with its job.

**200 OK**
```json
{
  "data": [
    {
      "id": 27,
      "amount": 1800,
      "delivery_days": 14,
      "cover_letter": "...",
      "experience_summary": "...",
      "submitted_at": "2026-06-13T17:02:11+00:00",
      "job": {
        "id": 1,
        "title": "Prepare and File 2025 Corporate Tax Return",
        "company_name": "Brightpath Logistics",
        "category": { "id": 1, "name": "Tax Preparation" },
        "budget": { "min": 1200, "max": 2500 },
        "status": "open"
      }
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 10, "total": 2 }
}
```

---

## cURL quick start

```bash
# Log in
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"accountant@demo.test","password":"password"}' \
  | php -r 'echo json_decode(file_get_contents("php://stdin"),true)["token"];')

# List the highest-budget open jobs
curl -s "http://localhost:8000/api/jobs?status=open&sort=budget_high" -H "Accept: application/json"

# Submit a bid
curl -s -X POST http://localhost:8000/api/jobs/1/bids \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"amount":1800,"delivery_days":14,"cover_letter":"I can deliver this accurately and on time.","experience_summary":"8 years corporate tax."}'
```

🔒 = requires authentication.
