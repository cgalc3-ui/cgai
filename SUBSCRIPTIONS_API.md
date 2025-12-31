# 📦 API Documentation: نظام الاشتراكات

## نظرة عامة

نظام الاشتراكات يسمح للإدارة بإنشاء وإدارة باقات الاشتراك، وعرضها في الواجهة الأمامية (React).

---

## 🔐 المصادقة

### Admin Endpoints
تتطلب جميع endpoints الخاصة بالـ Admin:
- **Authentication**: `Bearer Token` (Laravel Sanctum)
- **Role**: `admin`

### Public Endpoints
لا تتطلب مصادقة - متاحة للجميع.

---

## 📋 API Endpoints

### 1. Public Endpoints (للـ Frontend React)

#### GET `/api/public/subscriptions`
الحصول على جميع الباقات النشطة

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "الخطة التجريبية",
      "description": "للاختبار",
      "features": [
        {
          "name": "صيانة دورية للموقع"
        },
        {
          "name": "احتضان الموقع"
        },
        {
          "name": "إدخال البيانات للموقع"
        }
      ],
      "price": "0.00",
      "duration_type": "monthly",
      "is_active": true,
      "created_at": "2025-01-27T10:00:00.000000Z",
      "updated_at": "2025-01-27T10:00:00.000000Z"
    }
  ]
}
```

#### GET `/api/public/subscriptions/{id}`
الحصول على تفاصيل باقة معينة

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "الخطة التجريبية",
    "description": "للاختبار",
      "features": [
        {
          "name": "صيانة دورية للموقع"
        },
        {
          "name": "احتضان الموقع"
        },
        {
          "name": "إدخال البيانات للموقع"
        }
      ],
      "price": "29.00",
      "duration_type": "monthly",
    "is_active": true,
    "created_at": "2025-01-27T10:00:00.000000Z",
    "updated_at": "2025-01-27T10:00:00.000000Z"
  }
}
```

---

### 2. Admin Endpoints

#### GET `/api/admin/subscriptions`
الحصول على جميع الباقات (نشطة وغير نشطة)

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "الخطة التجريبية",
      "description": "للاختبار",
      "features": [
        {
          "name": "صيانة دورية للموقع"
        },
        {
          "name": "احتضان الموقع"
        }
      ],
      "price": "0.00",
      "duration_type": "monthly",
      "is_active": true,
      "created_at": "2025-01-27T10:00:00.000000Z",
      "updated_at": "2025-01-27T10:00:00.000000Z"
    }
  ]
}
```

#### GET `/api/admin/subscriptions/{id}`
الحصول على تفاصيل باقة معينة

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "الخطة التجريبية",
    "description": "للاختبار",
    "features": [
      "صيانة دورية للموقع",
      "احتضان الموقع"
    ],
    "price": "0.00",
    "duration_type": "month",
    "is_active": true,
    "created_at": "2025-01-27T10:00:00.000000Z",
    "updated_at": "2025-01-27T10:00:00.000000Z"
  }
}
```

#### POST `/api/admin/subscriptions`
إنشاء باقة جديدة

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "الخطة القياسية",
  "description": "مشروع متقدم",
  "features": [
    {
      "name": "صيانة دورية للموقع"
    },
    {
      "name": "احتضان الموقع"
    },
    {
      "name": "إدخال البيانات للموقع"
    },
    {
      "name": "تدريب الذكاء الاصطناعي"
    }
  ],
  "price": 29.00,
  "duration_type": "monthly",
  "is_active": true
}
```

**Validation Rules:**
- `name`: required|string|max:255
- `description`: nullable|string
- `features`: required|array|min:1
- `features.*.name`: required|string|max:255
- `price`: required|numeric|min:0
- `duration_type`: nullable|in:monthly,3months,6months,yearly (default: monthly)
- `is_active`: nullable|boolean (default: true)

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء الباقة بنجاح",
  "data": {
    "id": 2,
    "name": "الخطة القياسية",
    "description": "مشروع متقدم",
    "features": [
      {
        "name": "صيانة دورية للموقع"
      },
      {
        "name": "احتضان الموقع"
      },
      {
        "name": "إدخال البيانات للموقع"
      },
      {
        "name": "تدريب الذكاء الاصطناعي"
      }
    ],
    "price": "29.00",
    "duration_type": "monthly",
    "is_active": true,
    "created_at": "2025-01-27T10:00:00.000000Z",
    "updated_at": "2025-01-27T10:00:00.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "البيانات المدخلة غير صحيحة",
  "errors": {
    "name": ["حقل الاسم مطلوب"],
    "features": ["حقل المميزات مطلوب"]
  }
}
```

#### PUT/PATCH `/api/admin/subscriptions/{id}`
تحديث باقة موجودة

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "الخطة القياسية - محدثة",
  "description": "مشروع متقدم - محدث",
  "features": [
    {
      "name": "صيانة دورية للموقع"
    },
    {
      "name": "احتضان الموقع"
    },
    {
      "name": "إدخال البيانات للموقع"
    },
    {
      "name": "تدريب الذكاء الاصطناعي"
    },
    {
      "name": "ميزة جديدة"
    }
  ],
  "price": 39.00,
  "is_active": true
}
```

**Validation Rules:** (نفس قواعد الإنشاء، لكن جميع الحقول optional)

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث الباقة بنجاح",
  "data": {
    "id": 2,
    "name": "الخطة القياسية - محدثة",
    "description": "مشروع متقدم - محدث",
    "features": [
      {
        "name": "صيانة دورية للموقع"
      },
      {
        "name": "احتضان الموقع"
      },
      {
        "name": "إدخال البيانات للموقع"
      },
      {
        "name": "تدريب الذكاء الاصطناعي"
      },
      {
        "name": "ميزة جديدة"
      }
    ],
    "price": "39.00",
    "duration_type": "monthly",
    "is_active": true,
    "created_at": "2025-01-27T10:00:00.000000Z",
    "updated_at": "2025-01-27T11:00:00.000000Z"
  }
}
```

#### DELETE `/api/admin/subscriptions/{id}`
حذف باقة

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "تم حذف الباقة بنجاح"
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "لا يمكن حذف الباقة لأنها مرتبطة بطلبات اشتراك"
}
```

أو

```json
{
  "success": false,
  "message": "لا يمكن حذف الباقة لأنها مرتبطة باشتراكات نشطة"
}
```

---

## 📝 أمثلة الاستخدام

### React Example - Fetch Public Subscriptions

```javascript
// Fetch all active subscriptions
const fetchSubscriptions = async () => {
  try {
    const response = await fetch('/api/public/subscriptions');
    const data = await response.json();
    
    if (data.success) {
      console.log('Subscriptions:', data.data);
      return data.data;
    }
  } catch (error) {
    console.error('Error fetching subscriptions:', error);
  }
};
```

### React Example - Create Subscription (Admin)

```javascript
// Create a new subscription
const createSubscription = async (subscriptionData) => {
  try {
    const token = localStorage.getItem('token'); // Admin token
    
    const response = await fetch('/api/admin/subscriptions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        name: 'الخطة القياسية',
        description: 'مشروع متقدم',
        features: [
          {
            name: 'صيانة دورية للموقع'
          },
          {
            name: 'احتضان الموقع'
          },
          {
            name: 'إدخال البيانات للموقع'
          }
        ],
        price: 29.00,
        duration_type: 'monthly',
        is_active: true
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      console.log('Subscription created:', data.data);
      return data.data;
    } else {
      console.error('Error:', data.message, data.errors);
    }
  } catch (error) {
    console.error('Error creating subscription:', error);
  }
};
```

### React Example - Update Subscription (Admin)

```javascript
// Update a subscription
const updateSubscription = async (subscriptionId, updates) => {
  try {
    const token = localStorage.getItem('token'); // Admin token
    
    const response = await fetch(`/api/admin/subscriptions/${subscriptionId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(updates)
    });
    
    const data = await response.json();
    
    if (data.success) {
      console.log('Subscription updated:', data.data);
      return data.data;
    } else {
      console.error('Error:', data.message, data.errors);
    }
  } catch (error) {
    console.error('Error updating subscription:', error);
  }
};
```

---

## 🔧 ملاحظات مهمة

1. **حقل `features`**: هو array من objects، كل object يحتوي على `name` يمثل اسم الميزة. يمكن إضافة عدد غير محدود من المميزات.

2. **`duration_type`**: 
   - `monthly`: شهري
   - `3months`: 3 أشهر
   - `6months`: 6 أشهر
   - `yearly`: سنوي

3. **`is_active`**: الباقات غير النشطة لا تظهر في Public endpoints.

4. **التحقق من الصلاحيات**: جميع Admin endpoints تتحقق من أن المستخدم:
   - مصادق عليه (authenticated)
   - لديه role = `admin`

5. **ترتيب الباقات**: في Public endpoints، يتم ترتيب الباقات حسب السعر (من الأقل للأعلى).

---

## 📌 Routes Summary

| Method | Endpoint | Description | Auth Required | Role Required |
|--------|----------|-------------|---------------|---------------|
| GET | `/api/public/subscriptions` | عرض جميع الباقات النشطة | ❌ | - |
| GET | `/api/public/subscriptions/{id}` | عرض تفاصيل باقة | ❌ | - |
| GET | `/api/admin/subscriptions` | عرض جميع الباقات | ✅ | admin |
| GET | `/api/admin/subscriptions/{id}` | عرض تفاصيل باقة | ✅ | admin |
| POST | `/api/admin/subscriptions` | إنشاء باقة جديدة | ✅ | admin |
| PUT/PATCH | `/api/admin/subscriptions/{id}` | تحديث باقة | ✅ | admin |
| DELETE | `/api/admin/subscriptions/{id}` | حذف باقة | ✅ | admin |

---

**تم إنشاء هذا التوثيق بتاريخ:** 2025-01-27

