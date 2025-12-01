# Firebase Cloud Messaging API Endpoints

Base URL: `http://127.0.0.1:8000/api`

## 1. Send Notification to Single Device

**Endpoint:** `POST /notifications/send`

**Request Body:**
```json
{
  "fcm_token": "device_fcm_token_here",
  "title": "Payment Success",
  "body": "Your payment of Rp 50.000 has been processed successfully",
  "data": {
    "type": "payment",
    "amount": "50000",
    "transaction_id": "TRX123456"
  }
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Notification sent successfully",
  "data": {
    "name": "projects/{project_id}/messages/{message_id}"
  }
}
```

---

## 2. Send Notification to Multiple Devices

**Endpoint:** `POST /notifications/send-multiple`

**Request Body:**
```json
{
  "fcm_tokens": [
    "token1_here",
    "token2_here",
    "token3_here"
  ],
  "title": "New Promo Available!",
  "body": "Get 20% discount for all transactions today",
  "data": {
    "promo_code": "DISC20",
    "valid_until": "2025-12-31"
  }
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Notifications sent",
  "summary": {
    "total": 3,
    "success": 2,
    "failed": 1
  },
  "details": [
    {
      "token": "token1_truncated...",
      "success": true,
      "error": null
    },
    {
      "token": "token2_truncated...",
      "success": true,
      "error": null
    },
    {
      "token": "token3_truncated...",
      "success": false,
      "error": "Invalid FCM token"
    }
  ]
}
```

---

## 3. Send Notification to Topic

**Endpoint:** `POST /notifications/send-topic`

**Request Body:**
```json
{
  "topic": "all_users",
  "title": "System Maintenance",
  "body": "Our system will be under maintenance from 10 PM to 12 AM",
  "data": {
    "maintenance_start": "2025-12-01 22:00:00",
    "maintenance_end": "2025-12-02 00:00:00"
  }
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Notification sent to topic successfully",
  "data": {
    "name": "projects/{project_id}/messages/{message_id}"
  }
}
```

---

## 4. Send Notification to User by User ID

**Endpoint:** `POST /notifications/send-to-user`

**Request Body:**
```json
{
  "user_id": "OX1xmLm1ZY3Zhf19jAp0pHN",
  "title": "Payment Reminder",
  "body": "You have a pending payment of Rp 100.000",
  "data": {
    "bill_id": "BILL123",
    "amount": "100000",
    "due_date": "2025-12-10"
  }
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Notification sent to user successfully",
  "user_id": "OX1xmLm1ZY3Zhf19jAp0pHN",
  "data": {
    "name": "projects/{project_id}/messages/{message_id}"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "User not found"
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "User does not have FCM token"
}
```

---

## Error Responses

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "fcm_token": ["The fcm token field is required."],
    "title": ["The title field is required."]
  }
}
```

**Server Error (500):**
```json
{
  "success": false,
  "message": "Error sending notification",
  "error": "Detailed error message here"
}
```

---

## Testing with cURL

### Send to Single Device
```bash
curl -X POST http://127.0.0.1:8000/api/notifications/send \
  -H "Content-Type: application/json" \
  -d '{
    "fcm_token": "your_fcm_token_here",
    "title": "Test Notification",
    "body": "This is a test message",
    "data": {"test": "value"}
  }'
```

### Send to User by ID
```bash
curl -X POST http://127.0.0.1:8000/api/notifications/send-to-user \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "OX1xmLm1ZY3Zhf19jAp0pHN",
    "title": "Hello User",
    "body": "This is your personalized notification"
  }'
```

## Notes

- **FCM Token**: Pastikan FCM token valid dan aktif
- **Data Field**: Optional, untuk mengirim data tambahan ke aplikasi
- **Topic**: User harus subscribe ke topic terlebih dahulu agar bisa menerima notifikasi topic
- **User FCM Token**: Disimpan di Firestore collection `users` dengan field name `fcmToken`
