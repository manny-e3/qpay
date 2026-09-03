# API Authentication Documentation

This document describes the authentication API endpoints implemented for the central hub. These endpoints use **Laravel Sanctum** to manage user session tokens.

---

## 1. User Login
Authenticate a user with their email and password, and retrieve a Sanctum API token.

* **Endpoint**: `POST /api/auth/login`
* **Headers**: 
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Body (JSON)
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `email` | string | Yes | The registered user's email address. |
| `password` | string | Yes | The user's account password. |

### Response Example (200 Success)
```json
{
    "status": "success",
    "message": "Logged in successfully.",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "created_at": "2026-07-16T22:42:57.000000Z",
            "updated_at": "2026-07-16T22:42:57.000000Z"
        },
        "token": "1|abcdef1234567890..."
    }
}
```

### Response Example (401 Unauthorized)
```json
{
    "status": "error",
    "message": "The provided credentials do not match our records."
}
```

### Response Example (422 Validation Error)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ]
    }
}
```

---

## 2. User Logout
Revoke/delete the currently authenticated user's Sanctum access token.

* **Endpoint**: `POST /api/auth/logout`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <your_token>`

### Response Example (200 Success)
```json
{
    "status": "success",
    "message": "Logged out successfully."
}
```

### Response Example (401 Unauthorized)
Returned if the `Authorization: Bearer <token>` header is missing, invalid, or expired.
```json
{
    "message": "Unauthenticated."
}
```

---

## 3. Forgot Password
Request a password reset link. This will trigger Laravel to send a reset notification email containing the token link to the user.

* **Endpoint**: `POST /api/auth/forgot-password`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Body (JSON)
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `email` | string | Yes | The user's registered email address. |

### Response Example (200 Success)
```json
{
    "status": "success",
    "message": "We have emailed your password reset link!"
}
```

### Response Example (400 Bad Request)
Returned if the email address is invalid or not registered in the system.
```json
{
    "status": "error",
    "message": "We can't find a user with that email address."
}
```

---

## 4. Reset Password
Submit the reset token alongside the new password to update the user's password.

* **Endpoint**: `POST /api/auth/reset-password`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Body (JSON)
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `token` | string | Yes | The secure reset token sent in the email. |
| `email` | string | Yes | The user's registered email address. |
| `password` | string | Yes | The new password (minimum 8 characters). |
| `password_confirmation` | string | Yes | Confirms the new password (must match `password`). |

### Response Example (200 Success)
```json
{
    "status": "success",
    "message": "Your password has been reset!"
}
```

### Response Example (400 Bad Request)
Returned if the token is invalid, has expired, or email is incorrect.
```json
{
    "status": "error",
    "message": "This password reset token is invalid."
}
```
