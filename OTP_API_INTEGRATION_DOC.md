# OTP Cloud API Integration Documentation

This document describes the API endpoints provided by the OTP Cloud service for generating and validating One-Time Passwords (OTPs), handling optional payments, and managing applications, gateways, logs, and responses.

---

## 1. Authentication & Base URL

The API routes are divided into two main categories:
1. **OTP Master APIs** (Requires Application Authentication Headers)
2. **App Management, Payment, Gateway, & Log APIs** (Public - No Authentication Headers Required)

### Base URL
```
http://<your-otp-cloud-domain>/api
```

### Required Request Headers (For OTP Master APIs Only)
Include these headers in every OTP Generator and Validator request:

| Header Name | Type | Description |
| :--- | :--- | :--- |
| `ID` | `string` | The **Application ID** (e.g. `101`) created in the admin panel. |
| `Username` | `string` | The **API Username** created in the admin panel. |
| `Password` | `string` | The **API Password** created in the admin panel. |
| `Accept` | `string` | Must be `application/json`. |
| `Content-Type`| `string` | Must be `application/json`. |

> [!IMPORTANT]
> Ensure the Application status is set to **Active** in the admin panel; otherwise, authentication will fail with a `401 Unauthorized` status.

---

## 2. OTP Endpoints (OTP Master APIs)

### 2.1. Generate OTP
Generates a random OTP code based on the application's configuration (OTP type and length), sends it via email using the customized template, and returns a success response.

- **Endpoint**: `POST /master/generator`
- **Authentication**: Required (Headers)

#### Request Body (JSON)
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `appID` | `string` | Yes | The **Application ID** matching the header ID value (e.g., `101`). |
| `username` | `string` | Yes | The target recipient's email address (must be a valid email format). |
| `name` | `string` | Yes | The target recipient's name (used for email body personalization). |

##### Example Request Body
```json
{
  "appID": "101",
  "username": "recipient@example.com",
  "name": "John Doe"
}
```

#### Responses

##### Success (200 OK)
```json
{
  "success": true,
  "status": "generated",
  "message": "OTP generated and sent to email successfully."
}
```

##### Validation Error / Missing Fields (200 OK or 400 Bad Request)
```json
{
  "status": "failed",
  "message": "Please validate your form.",
  "errors": {
    "username": [
      "The username must be a valid email address."
    ]
  }
}
```

##### Inactive Application / OTP Disabled (200 OK)
```json
{
  "status": "not_generated",
  "message": "This app does not require OTP."
}
```

---

### 2.2. Validate/Verify OTP
Validates the OTP code inputted by the user. If correct, the OTP is marked as `validated` and any remaining pending OTPs for the user are marked as `expired`.

- **Endpoint**: `POST /master/validator`
- **Authentication**: Required (Headers)

#### Request Body (JSON)
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `appID` | `string` | Yes | The **Application ID** matching the header ID value (e.g., `101`). |
| `username` | `string` | Yes | The recipient's email address (same as used in the generator). |
| `otp` | `string` | Yes | The OTP code entered by the user to verify. |

##### Example Request Body
```json
{
  "appID": "101",
  "username": "recipient@example.com",
  "otp": "4892"
}
```

#### Responses

##### Success (200 OK)
```json
{
  "success": true,
  "response_code": "validated",
  "response_message": "OTP validated successfully."
}
```

##### Invalid/Incorrect OTP (200 OK)
```json
{
  "success": false,
  "response_code": "Invalid OTP",
  "response_message": "Invalid OTP. Please try again."
}
```

##### Expired OTP (200 OK)
```json
{
  "success": false,
  "response_code": "not_validated",
  "response_message": "Expired OTP. Please try again."
}
```

---

## 3. Payment Gateway Integration (Optional)

If you enabled payment gateways in your application's setup, you can initiate and verify payments. 

> [!NOTE]
> Payment API routes **do not** require authentication headers. They are identified using the `appID` payload parameter.

### 3.1. Initiate Payment
Initializes a new transaction and returns a `checkout_url` where you should redirect the user to select their gateway and complete payment.

- **Endpoint**: `POST /payment/initiate`
- **Authentication**: None

#### Request Body (JSON)
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `appID` | `string` | Yes | Your unique Application ID (must exist in database). |
| `amount` | `numeric` | Yes | The amount to charge (min: `1`). |
| `currency` | `string` | No | Currency code (default: `NGN`). |
| `email` | `string` | Yes | Customer's email address. |
| `callback_url`| `string` | No | URL to redirect the user to after payment has been completed. |
| `metadata` | `object` | No | Optional key-value pairs (e.g., custom order details). |

##### Example Request Body
```json
{
  "appID": "101",
  "amount": 2500,
  "email": "customer@example.com",
  "callback_url": "https://yourapp.com/payment/callback",
  "metadata": {
    "order_id": "ORD-552",
    "product": "Premium Subscription"
  }
}
```

##### Example Response (200 OK)
```json
{
  "status": "success",
  "message": "Transaction initialized. Redirect user to checkout_url.",
  "data": {
    "checkout_url": "http://<your-otp-cloud-domain>/checkout/PAY-ABC123XYZ",
    "reference": "PAY-ABC123XYZ"
  }
}
```

### 3.2. Verify Payment
Queries the status of a payment transaction using its reference. If the payment is still pending, the server contacts the gateway to fetch the latest state and update the database.

- **Endpoint**: `GET /payment/verify/{reference}`
- **Authentication**: None

##### Example Response (200 OK)
```json
{
  "status": "success",
  "message": "Transaction verified.",
  "data": {
    "id": 14,
    "app_config_id": 2,
    "payment_gateway_id": 1,
    "reference": "PAY-ABC123XYZ",
    "amount": "2500.00",
    "currency": "NGN",
    "status": "successful",
    "customer_email": "customer@example.com"
  }
}
```

---

## 4. Application Configuration Management (Admin APIs)

These endpoints allow you to build custom dashboard interfaces for listing, viewing, creating, updating, deleting, and testing your application connections directly from your frontend.

### 4.1. Get Active Payment Gateways
Fetch all payment gateways registered in the system that are currently active. Use this list to display payment configuration options (e.g. checkbox options) in your frontend creation/edit screens.

- **Endpoint**: `GET /apps/gateways`

### 4.2. List App Configurations
Retrieves a list of all client application setups, including details about their associated payment gateways.

- **Endpoint**: `GET /apps`

### 4.3. View Single App Configuration
Retrieves detailed setup for a specific application configuration.

- **Endpoint**: `GET /apps/{id}`

### 4.4. Create App Configuration
Creates a new client application. If payment gateways are selected, they will be enabled for the application. If `admin_email` is provided, the API automatically sends connection credentials via email.

- **Endpoint**: `POST /apps`

### 4.5. Update App Configuration
Modifies an existing application setup.

- **Endpoint**: `PUT /apps/{id}`

### 4.6. Delete App Configuration
Removes an application from the hub and detaches any payment gateway associations.

- **Endpoint**: `DELETE /apps/{id}`

### 4.7. Test Connection
Allows your frontend to simulate sending a test OTP using the configurations of a specific app.

- **Endpoint**: `POST /apps/{id}/test`

---

## 5. Payment Gateway Configuration (Admin APIs)

These endpoints allow administrators to configure the global payment gateways and specify the detailed credential parameters for individual apps.

### 5.1. List Gateways & Transactions
Fetch all payment gateways in the database and a paginated list of transaction logs.

- **Endpoint**: `GET /payment-gateways`

### 5.2. List Transactions Only
Retrieve a paginated history of transaction logs.

- **Endpoint**: `GET /payment-gateways/transactions`

### 5.3. Show Payment Gateway Details
Retrieve settings for a single payment gateway.

- **Endpoint**: `GET /payment-gateways/{gatewayId}`

### 5.4. Update Payment Gateway Settings
Modify name, description, active state, and API credentials (`config` object) for a payment gateway.

- **Endpoint**: `PUT /payment-gateways/{gatewayId}`

### 5.5. Configure App Payment Credentials
Retrieves the payment gateway options for a specific application along with any credentials currently set for them.

- **Endpoint**: `GET /payment-gateways/app-config/{appId}`

### 5.6. Save App Payment Credentials
Save gateway configuration settings for a specific app and specify which gateway should be set as **active**.

- **Endpoint**: `POST /payment-gateways/app-config/{appId}`

---

## 6. Logs & Response Templates Configuration (Admin APIs)

These endpoints allow you to query archived OTP histories, compile unified logs (both active pending codes and historical archives), and read response mappings.

### 6.1. Get OTP History Logs
Fetches a paginated list of logs from the `otp_history` table.

- **Endpoint**: `GET /logs/history`
- **Authentication**: None
- **Query Parameters**:
  - `search` (string, optional): Search by `username`, `appID`, `IP`, or `OTP`.
  - `appID` (string, optional): Filter logs by specific App ID.
  - `status` (string, optional): Filter by status (e.g. `validated`, `expired`).
  - `per_page` (integer, optional): Items per page (default: `10`).

##### Example Response (200 OK)
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 45,
        "appID": "101",
        "username": "customer@example.com",
        "OTP": "6732",
        "OTP_Start": "2026-07-16 14:00:00",
        "OTP_End": "2026-07-16 14:05:00",
        "IP": "127.0.0.1",
        "status": "validated",
        "created_at": "2026-07-16T13:00:00.000000Z",
        "updated_at": "2026-07-16T13:02:00.000000Z"
      }
    ],
    "total": 12
  }
}
```

### 6.2. Get Unified Logs (Active & History)
Merges pending active logs from `otp_master` and archived logs from `otp_history` into a single date-sorted, paginated stream.

- **Endpoint**: `GET /logs/unified`
- **Query Parameters**: Same as History Logs (`search`, `appID`, `per_page`, `page`).

##### Example Response (200 OK)
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 8,
        "appID": "101",
        "username": "active@example.com",
        "OTP": "1994",
        "IP": "127.0.0.1",
        "status": "pending",
        "log_type": "master",
        "created_at": "2026-07-16T14:50:00.000000Z"
      },
      {
        "id": 45,
        "appID": "101",
        "username": "customer@example.com",
        "OTP": "6732",
        "IP": "127.0.0.1",
        "status": "validated",
        "log_type": "history",
        "created_at": "2026-07-16T13:00:00.000000Z"
      }
    ],
    "total": 2
  }
}
```

### 6.3. Get System Response Templates
Fetches response templates mapping statuses to codes and description strings.

- **Endpoint**: `GET /responses`

##### Example Response (200 OK)
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "code": "200",
      "message": "validated",
      "description": "OTP validated successfully.",
      "created_at": "2026-07-16T12:00:00.000000Z",
      "updated_at": "2026-07-16T12:00:00.000000Z"
    },
    {
      "id": 2,
      "code": "400",
      "message": "not_validated",
      "description": "Invalid OTP code entered.",
      "created_at": "2026-07-16T12:00:00.000000Z",
      "updated_at": "2026-07-16T12:00:00.000000Z"
    }
  ]
}
```

---

## 7. Frontend Integration Snippets (Axios & React / Vue)

Below is an example frontend service implementation using Axios for managing payment gateway credentials, app configurations, and querying history/responses logs:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://<your-otp-cloud-domain>/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Admin Log and Response Service
export const AdminLogService = {
  // Fetch paginated archived OTP history logs
  getHistoryLogs: async (params = {}) => {
    const { data } = await api.get('/logs/history', { params });
    return data.data;
  },

  // Fetch unified logs (both active pending and history)
  getUnifiedLogs: async (params = {}) => {
    const { data } = await api.get('/logs/unified', { params });
    return data.data;
  },

  // Fetch all response templates
  getResponses: async () => {
    const { data } = await api.get('/responses');
    return data.data;
  }
};

// Payment Gateway Management Service
export const PaymentGatewayService = {
  getOverview: async () => {
    const { data } = await api.get('/payment-gateways');
    return data.data;
  },
  getTransactions: async (page = 1) => {
    const { data } = await api.get(`/payment-gateways/transactions?page=${page}`);
    return data.data;
  },
  getGateway: async (id) => {
    const { data } = await api.get(`/payment-gateways/${id}`);
    return data.data;
  },
  updateGateway: async (id, gatewayData) => {
    const { data } = await api.put(`/payment-gateways/${id}`, gatewayData);
    return data;
  },
  getAppGatewayConfig: async (appId) => {
    const { data } = await api.get(`/payment-gateways/app-config/${appId}`);
    return data.data;
  },
  saveAppGatewayConfig: async (appId, configPayload) => {
    const { data } = await api.post(`/payment-gateways/app-config/${appId}`, configPayload);
    return data;
  }
};
```
