# Clean City API — Endpoint Documentation

> **Base URL:** `{{base_url}}` → `http://localhost:8000/api`  
> **Authentication:** Laravel Sanctum (Bearer Token)  
> **Content-Type:** `application/json` (unless noted otherwise)

---

## Table of Contents

- [Environment Variables](#environment-variables)
- [Authentication](#authentication)
  - [Register](#1-register)
  - [Login](#2-login)
  - [Get Profile](#3-get-profile)
  - [Logout](#4-logout)
- [Reports](#reports)
  - [Get Categories](#5-get-categories)
  - [Get All Reports](#6-get-all-reports)
  - [Submit Report](#7-submit-report)
  - [Get My Reports](#8-get-my-reports)
  - [Get Report Details](#9-get-report-details)
  - [Upvote Report](#10-upvote-report)
- [Error Responses](#error-responses)
- [Collection Runner Order](#collection-runner-order)

---

## Environment Variables

| Variable       | Description                                      | Set By              |
|----------------|--------------------------------------------------|---------------------|
| `base_url`     | API base URL (e.g. `http://localhost:8000/api`)  | Manual              |
| `auth_token`   | Bearer token for authenticated requests          | Auto-set on login/register |
| `report_id`    | ID of the last submitted report                  | Auto-set on submit  |
| `test_email`   | Dynamically generated email for test runs        | Auto-set on register |
| `test_password`| Password used during test runs                   | Auto-set on register |

---

## Authentication

All protected endpoints require the following header:

```
Authorization: Bearer {{auth_token}}
```

---

### 1. Register

Create a new user account.

| Property    | Value                    |
|-------------|--------------------------|
| **Method**  | `POST`                   |
| **URL**     | `{{base_url}}/register`  |
| **Auth**    | None (Public)            |

#### Request Headers

| Key            | Value              |
|----------------|--------------------|
| `Content-Type` | `application/json` |
| `Accept`       | `application/json` |

#### Request Body (JSON)

```json
{
  "name": "Test User",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone_number": "1234567890",
  "role": "citizen"
}
```

#### Body Field Validation

| Field                    | Type   | Required | Rules                                      |
|--------------------------|--------|----------|--------------------------------------------|
| `name`                   | string | ✅ Yes   | max:255                                    |
| `email`                  | string | ✅ Yes   | valid email, unique, max:255               |
| `password`               | string | ✅ Yes   | min:8, must match `password_confirmation`  |
| `password_confirmation`  | string | ✅ Yes   | must match `password`                      |
| `phone_number`           | string | ❌ No    | —                                          |
| `role`                   | string | ❌ No    | one of: `citizen`, `admin` (default: `citizen`) |

#### Success Response — `201 Created`

```json
{
  "access_token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "user@example.com",
    "phone_number": "1234567890",
    "role": "citizen",
    "created_at": "2026-04-29T07:00:00.000000Z",
    "updated_at": "2026-04-29T07:00:00.000000Z"
  }
}
```

#### Error Response — `422 Unprocessable Entity`

```json
{
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

#### Postman Tests
- ✅ Status code is 201
- ✅ Response has `access_token`
- 🔁 Auto-saves `auth_token` to environment

---

### 2. Login

Authenticate an existing user and receive a Bearer token.

| Property    | Value                  |
|-------------|------------------------|
| **Method**  | `POST`                 |
| **URL**     | `{{base_url}}/login`   |
| **Auth**    | None (Public)          |

#### Request Headers

| Key            | Value              |
|----------------|--------------------|
| `Content-Type` | `application/json` |
| `Accept`       | `application/json` |

#### Request Body (JSON)

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

#### Body Field Validation

| Field      | Type   | Required | Rules        |
|------------|--------|----------|--------------|
| `email`    | string | ✅ Yes   | valid email  |
| `password` | string | ✅ Yes   | —            |

#### Success Response — `200 OK`

```json
{
  "access_token": "2|xyz789...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "user@example.com",
    "phone_number": "1234567890",
    "role": "citizen",
    "created_at": "2026-04-29T07:00:00.000000Z",
    "updated_at": "2026-04-29T07:00:00.000000Z"
  }
}
```

#### Error Responses

| Status | Body                                                    |
|--------|---------------------------------------------------------|
| `401`  | `{ "message": "Invalid login credentials" }`           |
| `422`  | `{ "errors": { "email": ["The email field is required."] } }` |

#### Postman Tests
- ✅ Status code is 200
- ✅ Response has `access_token`
- 🔁 Auto-saves `auth_token` to environment

---

### 3. Get Profile

Retrieve the authenticated user's profile.

| Property    | Value                |
|-------------|----------------------|
| **Method**  | `GET`                |
| **URL**     | `{{base_url}}/user`  |
| **Auth**    | 🔒 Bearer Token      |

#### Request Headers

| Key             | Value                    |
|-----------------|--------------------------|
| `Accept`        | `application/json`       |
| `Authorization` | `Bearer {{auth_token}}`  |

#### Success Response — `200 OK`

```json
{
  "id": 1,
  "name": "Test User",
  "email": "user@example.com",
  "phone_number": "1234567890",
  "role": "citizen",
  "created_at": "2026-04-29T07:00:00.000000Z",
  "updated_at": "2026-04-29T07:00:00.000000Z"
}
```

#### Postman Tests
- ✅ Status code is 200
- ✅ Response has `email` field

---

### 4. Logout

Revoke the current Bearer token and log out the user.

| Property    | Value                  |
|-------------|------------------------|
| **Method**  | `POST`                 |
| **URL**     | `{{base_url}}/logout`  |
| **Auth**    | 🔒 Bearer Token        |

#### Request Headers

| Key             | Value                    |
|-----------------|--------------------------|
| `Accept`        | `application/json`       |
| `Authorization` | `Bearer {{auth_token}}`  |

#### Success Response — `200 OK`

```json
{
  "message": "Logged out successfully"
}
```

#### Postman Tests
- ✅ Status code is 200
- ✅ Response message is `"Logged out successfully"`

---

## Reports

---

### 5. Get Categories

Retrieve all active report categories. No authentication required.

| Property    | Value                      |
|-------------|----------------------------|
| **Method**  | `GET`                      |
| **URL**     | `{{base_url}}/categories`  |
| **Auth**    | None (Public)              |

#### Request Headers

| Key      | Value              |
|----------|--------------------|
| `Accept` | `application/json` |

#### Success Response — `200 OK`

```json
[
  {
    "id": 1,
    "name": "Waste Dumping",
    "is_active": true,
    "created_at": "2026-04-01T00:00:00.000000Z",
    "updated_at": "2026-04-01T00:00:00.000000Z"
  },
  {
    "id": 2,
    "name": "Pothole",
    "is_active": true,
    "created_at": "2026-04-01T00:00:00.000000Z",
    "updated_at": "2026-04-01T00:00:00.000000Z"
  }
]
```

#### Postman Tests
- ✅ Status code is 200
- ✅ Response is an array

---

### 6. Get All Reports

Retrieve all active reports with status `pending` or `in_progress`, including category and image data. Used for map view.

| Property    | Value                   |
|-------------|-------------------------|
| **Method**  | `GET`                   |
| **URL**     | `{{base_url}}/reports`  |
| **Auth**    | 🔒 Bearer Token         |

#### Request Headers

| Key             | Value                    |
|-----------------|--------------------------|
| `Accept`        | `application/json`       |
| `Authorization` | `Bearer {{auth_token}}`  |

#### Success Response — `200 OK`

```json
[
  {
    "id": 33,
    "user_id": 5,
    "category_id": 1,
    "title": "Waste Report #1714000000",
    "description": "There is a large pothole on the main road",
    "latitude": "3.8480",
    "longitude": "11.5021",
    "status": "pending",
    "priority_score": 2,
    "created_at": "2026-04-29T07:00:00.000000Z",
    "updated_at": "2026-04-29T07:00:00.000000Z",
    "category": { "id": 1, "name": "Waste Dumping", "is_active": true },
    "images": []
  }
]
```

> **Note:** Only reports with status `pending` or `in_progress` are returned.

#### Postman Tests
- ✅ Status code is 200
- ✅ Response is an array

---

### 7. Submit Report

Submit a new city issue report with location, category, description, and optional photos.

| Property    | Value                   |
|-------------|-------------------------|
| **Method**  | `POST`                  |
| **URL**     | `{{base_url}}/reports`  |
| **Auth**    | 🔒 Bearer Token         |

#### Request Headers

| Key             | Value                    |
|-----------------|--------------------------|
| `Accept`        | `application/json`       |
| `Authorization` | `Bearer {{auth_token}}`  |

#### Request Body (form-data)

| Key           | Type   | Required | Description                                      |
|---------------|--------|----------|--------------------------------------------------|
| `category_id` | text   | ✅ Yes   | Must exist in the `categories` table             |
| `description` | text   | ❌ No    | Description of the issue                         |
| `title`       | text   | ❌ No    | Auto-generated as `"Waste Report #<timestamp>"` if omitted |
| `latitude`    | text   | ✅ Yes   | Numeric latitude coordinate                      |
| `longitude`   | text   | ✅ Yes   | Numeric longitude coordinate                     |
| `photos`      | file[] | ❌ No    | Array of images (jpeg/png/jpg, max 5MB each)     |

#### Success Response — `201 Created`

```json
{
  "id": 33,
  "user_id": 5,
  "category_id": 1,
  "title": "Waste Report #1714000000",
  "description": "There is a large pothole on the main road",
  "latitude": "3.8480",
  "longitude": "11.5021",
  "status": "pending",
  "priority_score": 1,
  "created_at": "2026-04-29T07:00:00.000000Z",
  "updated_at": "2026-04-29T07:00:00.000000Z",
  "images": [],
  "category": { "id": 1, "name": "Waste Dumping", "is_active": true },
  "status_history": [
    {
      "id": 1,
      "report_id": 33,
      "changed_by": 5,
      "old_status": "pending",
      "new_status": "pending",
      "note": "Report submitted successfully.",
      "created_at": "2026-04-29T07:00:00.000000Z"
    }
  ]
}
```

#### Error Response — `422 Unprocessable Entity`

```json
{
  "errors": {
    "category_id": ["The selected category id is invalid."],
    "latitude": ["The latitude field is required."]
  }
}
```

#### Priority Score Logic

The `priority_score` is automatically calculated on submission:
- **+2** for each upvote the report receives
- **+1** for each other report within **500 metres** of this report's location

#### Postman Tests
- ✅ Status code is 201 or 422
- 🔁 Auto-saves `report_id` to environment on 201

---

### 8. Get My Reports

Retrieve all reports submitted by the currently authenticated user, ordered by newest first.

| Property    | Value                      |
|-------------|----------------------------|
| **Method**  | `GET`                      |
| **URL**     | `{{base_url}}/my-reports`  |
| **Auth**    | 🔒 Bearer Token            |

#### Request Headers

| Key             | Value                    |
|-----------------|--------------------------|
| `Accept`        | `application/json`       |
| `Authorization` | `Bearer {{auth_token}}`  |

#### Success Response — `200 OK`

```json
[
  {
    "id": 33,
    "user_id": 5,
    "category_id": 1,
    "title": "Waste Report #1714000000",
    "description": "There is a large pothole on the main road",
    "latitude": "3.8480",
    "longitude": "11.5021",
    "status": "pending",
    "priority_score": 2,
    "created_at": "2026-04-29T07:00:00.000000Z",
    "updated_at": "2026-04-29T07:00:00.000000Z",
    "category": { "id": 1, "name": "Waste Dumping", "is_active": true },
    "images": [],
    "status_history": []
  }
]
```

#### Postman Tests
- ✅ Status code is 200
- ✅ Response is an array

---

### 9. Get Report Details

Retrieve full details of a single report including category, images, and full status history with the names of staff who changed each status.

| Property    | Value                                    |
|-------------|------------------------------------------|
| **Method**  | `GET`                                    |
| **URL**     | `{{base_url}}/reports/{{report_id}}`     |
| **Auth**    | 🔒 Bearer Token                          |

#### URL Parameters

| Parameter   | Description                  |
|-------------|------------------------------|
| `report_id` | The ID of the report to fetch |

#### Request Headers

| Key             | Value                    |
|-----------------|--------------------------|
| `Accept`        | `application/json`       |
| `Authorization` | `Bearer {{auth_token}}`  |

#### Success Response — `200 OK`

```json
{
  "id": 33,
  "user_id": 5,
  "category_id": 1,
  "title": "Waste Report #1714000000",
  "description": "There is a large pothole on the main road",
  "latitude": "3.8480",
  "longitude": "11.5021",
  "status": "pending",
  "priority_score": 2,
  "created_at": "2026-04-29T07:00:00.000000Z",
  "updated_at": "2026-04-29T07:00:00.000000Z",
  "category": { "id": 1, "name": "Waste Dumping", "is_active": true },
  "images": [
    {
      "id": 1,
      "report_id": 33,
      "image_path": "reports/photo.jpg"
    }
  ],
  "status_history": [
    {
      "id": 1,
      "report_id": 33,
      "old_status": "pending",
      "new_status": "pending",
      "note": "Report submitted successfully.",
      "changed_by": {
        "id": 5,
        "name": "Test User"
      },
      "created_at": "2026-04-29T07:00:00.000000Z"
    }
  ]
}
```

#### Error Response — `404 Not Found`

```json
{
  "message": "No query results for model [App\\Models\\Report] 999"
}
```

#### Postman Tests
- ✅ Status code is 200
- ✅ Response has `id` field

---

### 10. Upvote Report

Cast an upvote on a report to increase its priority score. Each user can only upvote a report once.

| Property    | Value                                          |
|-------------|------------------------------------------------|
| **Method**  | `POST`                                         |
| **URL**     | `{{base_url}}/reports/{{report_id}}/upvote`    |
| **Auth**    | 🔒 Bearer Token                                |

#### URL Parameters

| Parameter   | Description                    |
|-------------|--------------------------------|
| `report_id` | The ID of the report to upvote |

#### Request Headers

| Key             | Value                    |
|-----------------|--------------------------|
| `Accept`        | `application/json`       |
| `Authorization` | `Bearer {{auth_token}}`  |

#### Success Response — `200 OK`

```json
{
  "message": "Report upvoted successfully",
  "priority_score": 2
}
```

#### Error Response — `400 Bad Request` (already upvoted)

```json
{
  "message": "You have already upvoted this report"
}
```

#### Priority Score Formula

```
priority_score = (total_upvotes × 2) + nearby_reports_within_500m
```

#### Postman Tests
- ✅ Status code is 200 or 400
- ✅ Response has `message` field

---

## Error Responses

| Status Code | Meaning                                                  |
|-------------|----------------------------------------------------------|
| `200`       | OK — Request succeeded                                   |
| `201`       | Created — Resource created successfully                  |
| `400`       | Bad Request — Business logic error (e.g. already upvoted)|
| `401`       | Unauthorized — Invalid credentials or missing token      |
| `404`       | Not Found — Resource does not exist                      |
| `422`       | Unprocessable Entity — Validation failed                 |
| `500`       | Internal Server Error — Unexpected server error          |

---

## Collection Runner Order

The collection is configured to run in the following order for end-to-end testing:

### Auth Folder

| Order | Request      |
|-------|--------------|
| 1000  | Register     |
| 2000  | Login        |
| 3000  | Get Profile  |
| 9000  | Logout       |

### Reports Folder

| Order | Request            |
|-------|--------------------|
| 1000  | Get Categories     |
| 2000  | Get All Reports    |
| 3000  | Submit Report      |
| 4000  | Get My Reports     |
| 5000  | Get Report Details |
| 6000  | Upvote Report      |

> **Tip:** Run the Auth folder first to populate `auth_token`, then run the Reports folder. The Register request auto-generates a unique email on each run to avoid duplicate errors.

---

*Generated for the Clean City API — Laravel + Sanctum*
