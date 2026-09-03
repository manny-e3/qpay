# Payment Integration API Documentation

This API allows external applications to initiate and verify payments through the central hub.

## 1. Initiate Payment
**Endpoint**: `POST /api/payment/initiate`

### Request Body (JSON)
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `appID` | string | Yes | Your unique Application ID. |
| `amount` | numeric | Yes | The amount to charge (e.g., `5000`). |
| `currency` | string | No | Currency code (default: `NGN`). |
| `email` | string | Yes | Customer's email address. |
| `callback_url`| string | No | URL to redirect the user to after payment. |
| `metadata` | object | No | Optional key-value pairs to store with the transaction. |

### Example Request
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

### Example Response
```json
{
    "status": "success",
    "message": "Transaction initialized.",
    "data": {
        "checkout_url": "https://checkout.paystack.com/...",
        "reference": "PAY-ABC123XYZ"
    }
}
```

---

## 2. Verify Payment
**Endpoint**: `GET /api/payment/verify/{reference}`

### Example Response
```json
{
    "status": "success",
    "message": "Transaction verified.",
    "data": {
        "reference": "PAY-ABC123XYZ",
        "amount": "2500.00",
        "status": "successful",
        "customer_email": "customer@example.com",
        "gateway_response": { ... }
    }
}
```

---

## 3. Callback Handling
If a `callback_url` is provided during initialization, the user will be redirected back to it with the following query parameters:
- `status`: `successful`, `failed`, or `pending`.
- `reference`: The transaction reference.

**Example Redirect**: `https://yourapp.com/payment/callback?status=successful&reference=PAY-ABC123XYZ`
