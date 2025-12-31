# 📦 دليل شامل: نظام الاشتراكات للـ React Frontend

## 📋 نظرة عامة

هذا الدليل يشرح كيفية استخدام API نظام الاشتراكات في تطبيق React. النظام يسمح للمستخدمين بـ:
1. **عرض الباقات المتاحة** (بدون مصادقة)
2. **طلب الاشتراك** في باقة معينة مع رفع صورة إثبات الدفع (يتطلب مصادقة)
3. **متابعة حالة طلب الاشتراك** (pending, approved, rejected)
4. **عرض الاشتراك النشط الحالي**

---

## 🔐 المصادقة (Authentication)

### الحصول على Token
```javascript
// بعد تسجيل الدخول، احفظ الـ token
const token = response.data.token;
localStorage.setItem('token', token);
```

### استخدام Token في الطلبات
```javascript
const headers = {
  'Authorization': `Bearer ${localStorage.getItem('token')}`,
  'Content-Type': 'application/json'
};

// للطلبات التي تحتوي على ملفات (FormData)
const formDataHeaders = {
  'Authorization': `Bearer ${localStorage.getItem('token')}`
  // لا تضيف Content-Type - سيتم تعيينه تلقائياً
};
```

---

## 🌐 Base URL

```
http://your-domain.com/api
```

---

## 📡 API Endpoints

### 1. عرض الباقات المتاحة (Public - بدون مصادقة)

#### `GET /api/public/subscriptions`

**الوصف:** الحصول على جميع الباقات النشطة المتاحة للاشتراك

**المصادقة:** ❌ غير مطلوبة

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
    },
    {
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
  ]
}
```

**مثال React:**
```javascript
const fetchSubscriptions = async () => {
  try {
    const response = await fetch('http://your-domain.com/api/public/subscriptions');
    const data = await response.json();
    
    if (data.success) {
      return data.data;
    }
  } catch (error) {
    console.error('Error fetching subscriptions:', error);
  }
};
```

---

#### `GET /api/public/subscriptions/{id}`

**الوصف:** الحصول على تفاصيل باقة معينة

**المصادقة:** ❌ غير مطلوبة

**Parameters:**
- `id` (path parameter): معرف الباقة

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

**مثال React:**
```javascript
const fetchSubscriptionDetails = async (subscriptionId) => {
  try {
    const response = await fetch(
      `http://your-domain.com/api/public/subscriptions/${subscriptionId}`
    );
    const data = await response.json();
    
    if (data.success) {
      return data.data;
    }
  } catch (error) {
    console.error('Error fetching subscription details:', error);
  }
};
```

---

### 2. طلب الاشتراك (يتطلب مصادقة)

#### `POST /api/subscriptions`

**الوصف:** إنشاء طلب اشتراك جديد مع رفع صورة إثبات الدفع

**المصادقة:** ✅ مطلوبة (Bearer Token)

**Request Body (FormData):**
```
subscription_id: 1 (number)
payment_proof: File (image: jpeg, png, jpg, gif, max: 2MB)
```

**Validation Rules:**
- `subscription_id`: required|exists:subscriptions,id
- `payment_proof`: required|image|mimes:jpeg,png,jpg,gif|max:2048

**Response (Success - 201):**
```json
{
  "success": true,
  "message": "تم إرسال طلب الاشتراك بنجاح",
  "data": {
    "id": 1,
    "user_id": 5,
    "subscription_id": 1,
    "payment_proof": "payment_proofs/abc123.jpg",
    "status": "pending",
    "admin_notes": null,
    "approved_at": null,
    "rejected_at": null,
    "approved_by": null,
    "created_at": "2025-01-27T10:00:00.000000Z",
    "updated_at": "2025-01-27T10:00:00.000000Z",
    "subscription": {
      "id": 1,
      "name": "الخطة التجريبية",
      "description": "للاختبار",
      "features": [...],
      "price": "0.00",
      "duration_type": "monthly"
    }
  }
}
```

**Response (Error - 400):**
```json
{
  "success": false,
  "message": "الباقة غير متاحة حالياً"
}
```

أو

```json
{
  "success": false,
  "message": "لديك طلب اشتراك معلق بالفعل"
}
```

**مثال React:**
```javascript
const createSubscriptionRequest = async (subscriptionId, paymentProofFile) => {
  try {
    const formData = new FormData();
    formData.append('subscription_id', subscriptionId);
    formData.append('payment_proof', paymentProofFile);

    const token = localStorage.getItem('token');
    
    const response = await fetch('http://your-domain.com/api/subscriptions', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`
        // لا تضيف Content-Type - سيتم تعيينه تلقائياً لـ FormData
      },
      body: formData
    });

    const data = await response.json();
    
    if (data.success) {
      return data.data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Error creating subscription request:', error);
    throw error;
  }
};
```

---

### 3. عرض الباقات مع معلومات المستخدم (يتطلب مصادقة)

#### `GET /api/subscriptions`

**الوصف:** الحصول على جميع الباقات النشطة مع معلومات الاشتراك النشط وطلب الاشتراك المعلق للمستخدم الحالي

**المصادقة:** ✅ مطلوبة (Bearer Token)

**Response:**
```json
{
  "success": true,
  "data": {
    "subscriptions": [
      {
        "id": 1,
        "name": "الخطة التجريبية",
        "description": "للاختبار",
        "features": [...],
        "price": "0.00",
        "duration_type": "monthly",
        "is_active": true
      }
    ],
    "active_subscription": {
      "id": 10,
      "subscription": {
        "id": 2,
        "name": "الخطة القياسية",
        "price": "29.00",
        "duration_type": "monthly"
      },
      "status": "active",
      "started_at": "2025-01-20T10:00:00.000000Z",
      "expires_at": "2025-02-20T10:00:00.000000Z"
    },
    "pending_request": {
      "id": 5,
      "subscription": {
        "id": 1,
        "name": "الخطة التجريبية"
      },
      "status": "pending",
      "created_at": "2025-01-27T10:00:00.000000Z"
    }
  }
}
```

**ملاحظات:**
- `active_subscription`: `null` إذا لم يكن لدى المستخدم اشتراك نشط
- `pending_request`: `null` إذا لم يكن لدى المستخدم طلب معلق

**مثال React:**
```javascript
const fetchSubscriptionsWithUserInfo = async () => {
  try {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://your-domain.com/api/subscriptions', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    const data = await response.json();
    
    if (data.success) {
      return data.data;
    }
  } catch (error) {
    console.error('Error fetching subscriptions:', error);
  }
};
```

---

### 4. عرض الاشتراك النشط (يتطلب مصادقة)

#### `GET /api/subscriptions/active`

**الوصف:** الحصول على الاشتراك النشط الحالي للمستخدم

**المصادقة:** ✅ مطلوبة (Bearer Token)

**Response (Success - 200):**
```json
{
  "success": true,
  "data": {
    "id": 10,
    "subscription": {
      "id": 2,
      "name": "الخطة القياسية",
      "description": "مشروع متقدم",
      "features": [...],
      "price": "29.00",
      "duration_type": "monthly"
    },
    "status": "active",
    "started_at": "2025-01-20T10:00:00.000000Z",
    "expires_at": "2025-02-20T10:00:00.000000Z",
    "is_active": true
  }
}
```

**Response (Error - 404):**
```json
{
  "success": false,
  "message": "لا يوجد اشتراك نشط"
}
```

**مثال React:**
```javascript
const fetchActiveSubscription = async () => {
  try {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://your-domain.com/api/subscriptions/active', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    const data = await response.json();
    
    if (data.success) {
      return data.data;
    } else {
      return null; // لا يوجد اشتراك نشط
    }
  } catch (error) {
    console.error('Error fetching active subscription:', error);
    return null;
  }
};
```

---

### 5. عرض طلبات الاشتراك (يتطلب مصادقة)

#### `GET /api/subscriptions/requests`

**الوصف:** الحصول على جميع طلبات الاشتراك للمستخدم الحالي

**المصادقة:** ✅ مطلوبة (Bearer Token)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "user_id": 5,
      "subscription_id": 1,
      "payment_proof": "payment_proofs/abc123.jpg",
      "status": "pending",
      "admin_notes": null,
      "approved_at": null,
      "rejected_at": null,
      "approved_by": null,
      "created_at": "2025-01-27T10:00:00.000000Z",
      "updated_at": "2025-01-27T10:00:00.000000Z",
      "subscription": {
        "id": 1,
        "name": "الخطة التجريبية",
        "price": "0.00",
        "duration_type": "monthly"
      },
      "approver": null
    },
    {
      "id": 3,
      "user_id": 5,
      "subscription_id": 2,
      "payment_proof": "payment_proofs/xyz789.jpg",
      "status": "approved",
      "admin_notes": "تم التحقق من الدفع",
      "approved_at": "2025-01-25T10:00:00.000000Z",
      "rejected_at": null,
      "approved_by": 1,
      "created_at": "2025-01-24T10:00:00.000000Z",
      "updated_at": "2025-01-25T10:00:00.000000Z",
      "subscription": {
        "id": 2,
        "name": "الخطة القياسية",
        "price": "29.00",
        "duration_type": "monthly"
      },
      "approver": {
        "id": 1,
        "name": "أحمد محمد",
        "email": "admin@example.com"
      }
    },
    {
      "id": 1,
      "user_id": 5,
      "subscription_id": 1,
      "payment_proof": "payment_proofs/old123.jpg",
      "status": "rejected",
      "admin_notes": "صورة إثبات الدفع غير واضحة",
      "approved_at": null,
      "rejected_at": "2025-01-20T10:00:00.000000Z",
      "approved_by": 1,
      "created_at": "2025-01-19T10:00:00.000000Z",
      "updated_at": "2025-01-20T10:00:00.000000Z",
      "subscription": {
        "id": 1,
        "name": "الخطة التجريبية",
        "price": "0.00",
        "duration_type": "monthly"
      },
      "approver": {
        "id": 1,
        "name": "أحمد محمد",
        "email": "admin@example.com"
      }
    }
  ]
}
```

**حالات الطلب (Status):**
- `pending`: الطلب معلق في انتظار المراجعة
- `approved`: تم قبول الطلب وإنشاء اشتراك نشط
- `rejected`: تم رفض الطلب

**مثال React:**
```javascript
const fetchSubscriptionRequests = async () => {
  try {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://your-domain.com/api/subscriptions/requests', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    const data = await response.json();
    
    if (data.success) {
      return data.data;
    }
  } catch (error) {
    console.error('Error fetching subscription requests:', error);
  }
};
```

---

### 6. عرض تفاصيل باقة معينة (يتطلب مصادقة)

#### `GET /api/subscriptions/{id}`

**الوصف:** الحصول على تفاصيل باقة معينة (للمستخدم المصادق عليه)

**المصادقة:** ✅ مطلوبة (Bearer Token)

**Parameters:**
- `id` (path parameter): معرف الباقة

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "الخطة التجريبية",
    "description": "للاختبار",
    "features": [...],
    "price": "0.00",
    "duration_type": "monthly",
    "is_active": true
  }
}
```

---

## 📊 هيكل البيانات

### Subscription Object
```typescript
interface Subscription {
  id: number;
  name: string;
  description: string | null;
  features: Array<{
    name: string;
  }>;
  price: string; // Decimal as string
  duration_type: 'monthly' | '3months' | '6months' | 'yearly';
  is_active: boolean;
  created_at: string; // ISO 8601
  updated_at: string; // ISO 8601
}
```

### SubscriptionRequest Object
```typescript
interface SubscriptionRequest {
  id: number;
  user_id: number;
  subscription_id: number;
  payment_proof: string; // Path to image
  status: 'pending' | 'approved' | 'rejected';
  admin_notes: string | null;
  approved_at: string | null; // ISO 8601
  rejected_at: string | null; // ISO 8601
  approved_by: number | null;
  created_at: string; // ISO 8601
  updated_at: string; // ISO 8601
  subscription?: Subscription;
  approver?: User;
}
```

### UserSubscription Object
```typescript
interface UserSubscription {
  id: number;
  user_id: number;
  subscription_id: number;
  subscription_request_id: number | null;
  status: 'active' | 'expired' | 'cancelled';
  started_at: string; // ISO 8601
  expires_at: string | null; // ISO 8601 (null for lifetime subscriptions)
  created_at: string; // ISO 8601
  updated_at: string; // ISO 8601
  subscription?: Subscription;
}
```

---

## 🎯 حالات الاستخدام (Use Cases)

### 1. عرض صفحة الباقات

```javascript
import { useState, useEffect } from 'react';

function SubscriptionsPage() {
  const [subscriptions, setSubscriptions] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchSubscriptions = async () => {
      try {
        const response = await fetch('http://your-domain.com/api/public/subscriptions');
        const data = await response.json();
        
        if (data.success) {
          setSubscriptions(data.data);
        }
      } catch (error) {
        console.error('Error:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchSubscriptions();
  }, []);

  if (loading) return <div>جاري التحميل...</div>;

  return (
    <div className="subscriptions-container">
      {subscriptions.map(subscription => (
        <div key={subscription.id} className="subscription-card">
          <h3>{subscription.name}</h3>
          <p>{subscription.description}</p>
          <div className="price">{subscription.price} ر.س</div>
          <div className="duration">{getDurationText(subscription.duration_type)}</div>
          <ul className="features">
            {subscription.features.map((feature, index) => (
              <li key={index}>{feature.name}</li>
            ))}
          </ul>
          <button onClick={() => handleSubscribe(subscription.id)}>
            اشترك الآن
          </button>
        </div>
      ))}
    </div>
  );
}
```

---

### 2. طلب الاشتراك مع رفع صورة

```javascript
import { useState } from 'react';

function SubscribeForm({ subscriptionId }) {
  const [paymentProof, setPaymentProof] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      // Validate file type
      const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
      if (!validTypes.includes(file.type)) {
        setError('نوع الملف غير مدعوم. يرجى رفع صورة (JPEG, PNG, JPG, GIF)');
        return;
      }
      
      // Validate file size (2MB = 2 * 1024 * 1024 bytes)
      if (file.size > 2 * 1024 * 1024) {
        setError('حجم الملف كبير جداً. الحد الأقصى: 2MB');
        return;
      }
      
      setPaymentProof(file);
      setError(null);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!paymentProof) {
      setError('يرجى رفع صورة إثبات الدفع');
      return;
    }

    setLoading(true);
    setError(null);

    try {
      const formData = new FormData();
      formData.append('subscription_id', subscriptionId);
      formData.append('payment_proof', paymentProof);

      const token = localStorage.getItem('token');
      
      const response = await fetch('http://your-domain.com/api/subscriptions', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`
        },
        body: formData
      });

      const data = await response.json();
      
      if (data.success) {
        setSuccess(true);
        setPaymentProof(null);
        // Reset file input
        e.target.reset();
      } else {
        setError(data.message || 'حدث خطأ أثناء إرسال الطلب');
      }
    } catch (error) {
      setError('حدث خطأ أثناء الاتصال بالخادم');
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="subscribe-form">
      {success && (
        <div className="success-message">
          تم إرسال طلب الاشتراك بنجاح! سيتم مراجعته من قبل الإدارة.
        </div>
      )}
      
      {error && (
        <div className="error-message">{error}</div>
      )}

      <div className="form-group">
        <label htmlFor="payment_proof">صورة إثبات الدفع *</label>
        <input
          type="file"
          id="payment_proof"
          accept="image/jpeg,image/png,image/jpg,image/gif"
          onChange={handleFileChange}
          required
        />
        {paymentProof && (
          <div className="file-preview">
            <p>تم اختيار الملف: {paymentProof.name}</p>
            <img 
              src={URL.createObjectURL(paymentProof)} 
              alt="Preview" 
              style={{ maxWidth: '200px', maxHeight: '200px' }}
            />
          </div>
        )}
      </div>

      <button type="submit" disabled={loading || !paymentProof}>
        {loading ? 'جاري الإرسال...' : 'إرسال طلب الاشتراك'}
      </button>
    </form>
  );
}
```

---

### 3. عرض حالة طلب الاشتراك

```javascript
import { useState, useEffect } from 'react';

function SubscriptionRequestsPage() {
  const [requests, setRequests] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchRequests = async () => {
      try {
        const token = localStorage.getItem('token');
        
        const response = await fetch('http://your-domain.com/api/subscriptions/requests', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        const data = await response.json();
        
        if (data.success) {
          setRequests(data.data);
        }
      } catch (error) {
        console.error('Error:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchRequests();
  }, []);

  const getStatusBadge = (status) => {
    const statusMap = {
      pending: { text: 'قيد المراجعة', class: 'warning' },
      approved: { text: 'مقبول', class: 'success' },
      rejected: { text: 'مرفوض', class: 'danger' }
    };
    
    const statusInfo = statusMap[status] || { text: status, class: 'default' };
    
    return (
      <span className={`badge badge-${statusInfo.class}`}>
        {statusInfo.text}
      </span>
    );
  };

  if (loading) return <div>جاري التحميل...</div>;

  return (
    <div className="requests-container">
      <h2>طلبات الاشتراك</h2>
      
      {requests.length === 0 ? (
        <p>لا توجد طلبات اشتراك</p>
      ) : (
        <div className="requests-list">
          {requests.map(request => (
            <div key={request.id} className="request-card">
              <div className="request-header">
                <h3>{request.subscription.name}</h3>
                {getStatusBadge(request.status)}
              </div>
              
              <div className="request-details">
                <p><strong>السعر:</strong> {request.subscription.price} ر.س</p>
                <p><strong>الفترة:</strong> {getDurationText(request.subscription.duration_type)}</p>
                <p><strong>تاريخ الطلب:</strong> {formatDate(request.created_at)}</p>
                
                {request.status === 'approved' && request.approved_at && (
                  <p><strong>تاريخ الموافقة:</strong> {formatDate(request.approved_at)}</p>
                )}
                
                {request.status === 'rejected' && request.rejected_at && (
                  <p><strong>تاريخ الرفض:</strong> {formatDate(request.rejected_at)}</p>
                )}
                
                {request.admin_notes && (
                  <div className="admin-notes">
                    <strong>ملاحظات الإدارة:</strong>
                    <p>{request.admin_notes}</p>
                  </div>
                )}
                
                {request.payment_proof && (
                  <div className="payment-proof">
                    <strong>صورة إثبات الدفع:</strong>
                    <img 
                      src={`http://your-domain.com/storage/${request.payment_proof}`}
                      alt="Payment Proof"
                      style={{ maxWidth: '300px' }}
                    />
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
```

---

### 4. عرض الاشتراك النشط

```javascript
import { useState, useEffect } from 'react';

function ActiveSubscriptionCard() {
  const [activeSubscription, setActiveSubscription] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchActiveSubscription = async () => {
      try {
        const token = localStorage.getItem('token');
        
        const response = await fetch('http://your-domain.com/api/subscriptions/active', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        const data = await response.json();
        
        if (data.success) {
          setActiveSubscription(data.data);
        }
      } catch (error) {
        console.error('Error:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchActiveSubscription();
  }, []);

  if (loading) return <div>جاري التحميل...</div>;

  if (!activeSubscription) {
    return (
      <div className="no-subscription">
        <p>لا يوجد اشتراك نشط حالياً</p>
      </div>
    );
  }

  const subscription = activeSubscription.subscription;
  const expiresAt = activeSubscription.expires_at 
    ? new Date(activeSubscription.expires_at)
    : null;
  const daysRemaining = expiresAt 
    ? Math.ceil((expiresAt - new Date()) / (1000 * 60 * 60 * 24))
    : null;

  return (
    <div className="active-subscription-card">
      <h3>الاشتراك النشط</h3>
      <div className="subscription-info">
        <h4>{subscription.name}</h4>
        <p>{subscription.description}</p>
        
        <div className="features">
          <strong>المميزات:</strong>
          <ul>
            {subscription.features.map((feature, index) => (
              <li key={index}>{feature.name}</li>
            ))}
          </ul>
        </div>
        
        <div className="subscription-dates">
          <p><strong>تاريخ البدء:</strong> {formatDate(activeSubscription.started_at)}</p>
          {expiresAt ? (
            <p><strong>تاريخ الانتهاء:</strong> {formatDate(expiresAt)}</p>
          ) : (
            <p><strong>نوع الاشتراك:</strong> دائم</p>
          )}
          {daysRemaining !== null && (
            <p className={daysRemaining < 7 ? 'warning' : ''}>
              <strong>الأيام المتبقية:</strong> {daysRemaining} يوم
            </p>
          )}
        </div>
      </div>
    </div>
  );
}
```

---

## 🛠️ Helper Functions

```javascript
// تحويل duration_type إلى نص عربي
const getDurationText = (durationType) => {
  const durationMap = {
    monthly: 'شهري',
    '3months': '3 أشهر',
    '6months': '6 أشهر',
    yearly: 'سنوي'
  };
  
  return durationMap[durationType] || durationType;
};

// تنسيق التاريخ
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('ar-SA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// الحصول على رابط صورة إثبات الدفع
const getPaymentProofUrl = (paymentProofPath) => {
  if (!paymentProofPath) return null;
  return `http://your-domain.com/storage/${paymentProofPath}`;
};
```

---

## ⚠️ ملاحظات مهمة

### 1. رفع الملفات (File Upload)
- استخدم `FormData` عند رفع الصور
- لا تضيف `Content-Type` header عند استخدام `FormData` - سيتم تعيينه تلقائياً
- تحقق من نوع الملف وحجمه قبل الإرسال

### 2. معالجة الأخطاء
- تحقق من `success` في response قبل استخدام البيانات
- اعرض رسائل خطأ واضحة للمستخدم
- تعامل مع حالات 401 (غير مصرح) و 404 (غير موجود)

### 3. تحديث البيانات
- استخدم polling أو WebSockets لتحديث حالة الطلبات تلقائياً
- أو أضف زر "تحديث" لتحديث البيانات يدوياً

### 4. عرض الصور
- استخدم المسار الكامل للصورة: `http://your-domain.com/storage/{payment_proof_path}`
- تأكد من أن الـ storage link تم إنشاؤه في Laravel

---

## 📝 ملخص الـ Endpoints

| Method | Endpoint | المصادقة | الوصف |
|--------|----------|----------|-------|
| GET | `/api/public/subscriptions` | ❌ | عرض جميع الباقات النشطة |
| GET | `/api/public/subscriptions/{id}` | ❌ | عرض تفاصيل باقة |
| GET | `/api/subscriptions` | ✅ | عرض الباقات مع معلومات المستخدم |
| GET | `/api/subscriptions/active` | ✅ | عرض الاشتراك النشط |
| GET | `/api/subscriptions/requests` | ✅ | عرض طلبات الاشتراك |
| GET | `/api/subscriptions/{id}` | ✅ | عرض تفاصيل باقة (مصادق) |
| POST | `/api/subscriptions` | ✅ | إنشاء طلب اشتراك |

---

## 🔗 روابط مفيدة

- **Base URL:** `http://your-domain.com/api`
- **Storage URL:** `http://your-domain.com/storage`
- **Authentication:** Bearer Token (Laravel Sanctum)

---

**تم إنشاء هذا الدليل بتاريخ:** 2025-01-27  
**الإصدار:** 1.0

