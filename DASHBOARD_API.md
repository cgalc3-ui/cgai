# 📊 API Documentation: Dashboard Endpoint

## نظرة عامة

Endpoint شامل لعرض بيانات Dashboard للمستخدم في الـ Frontend. يحتوي على معلومات المستخدم، الاشتراك النشط، إحصائيات الحجوزات والتذاكر، وآخر الأنشطة.

---

## 🔐 المصادقة

**مطلوبة:** ✅ نعم (Bearer Token - Laravel Sanctum)

---

## 📡 Endpoint

### `GET /api/customer/dashboard`

**الوصف:** الحصول على جميع بيانات Dashboard للمستخدم الحالي

**المصادقة:** ✅ مطلوبة

**Headers:**
```
Authorization: Bearer {token}
```

---

## 📤 Response Structure

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "0501234567"
    },
    "subscription": {
      "id": 10,
      "subscription": {
        "id": 2,
        "name": "الخطة القياسية",
        "description": "مشروع متقدم",
        "price": "29.00",
        "duration_type": "monthly",
        "features": [
          {
            "name": "صيانة دورية للموقع"
          },
          {
            "name": "احتضان الموقع"
          }
        ]
      },
      "status": "active",
      "started_at": "2025-01-20T10:00:00.000000Z",
      "expires_at": "2025-02-20T10:00:00.000000Z",
      "is_active": true
    },
    "pending_subscription_request": {
      "id": 5,
      "subscription": {
        "id": 1,
        "name": "الخطة التجريبية"
      },
      "status": "pending",
      "created_at": "2025-01-27T10:00:00.000000Z"
    },
    "stats": {
      "bookings": {
        "total": 15,
        "pending": 2,
        "confirmed": 5,
        "in_progress": 1,
        "completed": 6,
        "cancelled": 1,
        "today": 1,
        "upcoming": 3
      },
      "payments": {
        "total_spent": 1450.50,
        "paid_bookings": 12,
        "unpaid_bookings": 3
      },
      "tickets": {
        "total": 8,
        "open": 2,
        "in_progress": 1,
        "resolved": 5
      },
      "notifications": {
        "unread_count": 3
      }
    },
    "recent_bookings": [
      {
        "id": 25,
        "booking_type": "service",
        "service": {
          "id": 5,
          "name": "استشارة قانونية",
          "sub_category": {
            "id": 2,
            "name": "القانون المدني",
            "category": {
              "id": 1,
              "name": "القانون"
            }
          }
        },
        "consultation": null,
        "employee": {
          "id": 3,
          "user": {
            "id": 10,
            "name": "محمد أحمد"
          }
        },
        "booking_date": "2025-01-28",
        "start_time": "10:00:00",
        "end_time": "11:00:00",
        "total_price": "150.00",
        "status": "confirmed",
        "actual_status": "pending",
        "payment_status": "paid",
        "created_at": "2025-01-27T10:00:00.000000Z"
      }
    ],
    "recent_tickets": [
      {
        "id": 12,
        "subject": "مشكلة في الحجز",
        "status": "open",
        "priority": "high",
        "assigned_to": {
          "id": 1,
          "name": "أحمد الإداري"
        },
        "latest_message": {
          "id": 45,
          "message": "شكراً لتواصلكم، سنقوم بمراجعة المشكلة",
          "created_at": "2025-01-27T09:00:00.000000Z"
        },
        "created_at": "2025-01-26T10:00:00.000000Z",
        "resolved_at": null
      }
    ]
  }
}
```

---

## 📊 تفاصيل البيانات

### 1. User Information
```typescript
interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
}
```

### 2. Subscription (إذا كان موجود)
```typescript
interface Subscription {
  id: number;
  subscription: {
    id: number;
    name: string;
    description: string | null;
    price: string;
    duration_type: 'monthly' | '3months' | '6months' | 'yearly';
    features: Array<{ name: string }>;
  };
  status: 'active' | 'expired' | 'cancelled';
  started_at: string; // ISO 8601
  expires_at: string | null; // ISO 8601
  is_active: boolean;
}
```

**ملاحظة:** `subscription` يكون `null` إذا لم يكن لدى المستخدم اشتراك نشط.

---

### 3. Pending Subscription Request (إذا كان موجود)
```typescript
interface PendingRequest {
  id: number;
  subscription: {
    id: number;
    name: string;
  };
  status: 'pending';
  created_at: string; // ISO 8601
}
```

**ملاحظة:** `pending_subscription_request` يكون `null` إذا لم يكن لدى المستخدم طلب معلق.

---

### 4. Statistics

#### Bookings Statistics
```typescript
interface BookingsStats {
  total: number;           // إجمالي الحجوزات
  pending: number;         // الحجوزات قيد الانتظار
  confirmed: number;       // الحجوزات المؤكدة
  in_progress: number;     // الحجوزات قيد التنفيذ
  completed: number;       // الحجوزات المكتملة
  cancelled: number;       // الحجوزات الملغاة
  today: number;           // حجوزات اليوم
  upcoming: number;        // الحجوزات القادمة (خلال 7 أيام)
}
```

#### Payments Statistics
```typescript
interface PaymentsStats {
  total_spent: number;     // إجمالي المبلغ المنفق
  paid_bookings: number;   // عدد الحجوزات المدفوعة
  unpaid_bookings: number; // عدد الحجوزات غير المدفوعة
}
```

#### Tickets Statistics
```typescript
interface TicketsStats {
  total: number;           // إجمالي التذاكر
  open: number;            // التذاكر المفتوحة
  in_progress: number;     // التذاكر قيد المعالجة
  resolved: number;        // التذاكر المحلولة
}
```

#### Notifications Statistics
```typescript
interface NotificationsStats {
  unread_count: number;    // عدد الإشعارات غير المقروءة
}
```

---

### 5. Recent Bookings (آخر 5 حجوزات)

```typescript
interface RecentBooking {
  id: number;
  booking_type: 'service' | 'consultation';
  service: {
    id: number;
    name: string;
    sub_category: {
      id: number;
      name: string;
      category: {
        id: number;
        name: string;
      } | null;
    } | null;
  } | null;
  consultation: {
    id: number;
    name: string;
    category: {
      id: number;
      name: string;
    } | null;
  } | null;
  employee: {
    id: number;
    user: {
      id: number;
      name: string;
    } | null;
  } | null;
  booking_date: string;    // YYYY-MM-DD
  start_time: string;      // HH:mm:ss
  end_time: string;        // HH:mm:ss
  total_price: string;     // Decimal as string
  status: 'pending' | 'confirmed' | 'in_progress' | 'completed' | 'cancelled';
  actual_status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  payment_status: 'paid' | 'unpaid';
  created_at: string;      // ISO 8601
}
```

**ملاحظات:**
- `actual_status`: الحالة الفعلية بناءً على الوقت الحالي (يتم حسابها تلقائياً)
- `service` أو `consultation` واحد فقط سيكون موجود (حسب `booking_type`)

---

### 6. Recent Tickets (آخر 5 تذاكر)

```typescript
interface RecentTicket {
  id: number;
  subject: string;
  status: 'open' | 'in_progress' | 'resolved' | 'closed';
  priority: 'low' | 'medium' | 'high' | 'urgent';
  assigned_to: {
    id: number;
    name: string;
  } | null;
  latest_message: {
    id: number;
    message: string;
    created_at: string;    // ISO 8601
  } | null;
  created_at: string;      // ISO 8601
  resolved_at: string | null; // ISO 8601
}
```

---

## 🎯 مثال على الاستخدام في React

```javascript
import { useState, useEffect } from 'react';

function Dashboard() {
  const [dashboardData, setDashboardData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchDashboard = async () => {
      try {
        const token = localStorage.getItem('token');
        
        const response = await fetch('http://your-domain.com/api/customer/dashboard', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        const data = await response.json();
        
        if (data.success) {
          setDashboardData(data.data);
        } else {
          setError(data.message || 'حدث خطأ أثناء تحميل البيانات');
        }
      } catch (err) {
        setError('حدث خطأ أثناء الاتصال بالخادم');
        console.error('Error:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboard();
  }, []);

  if (loading) return <div>جاري التحميل...</div>;
  if (error) return <div className="error">{error}</div>;
  if (!dashboardData) return null;

  return (
    <div className="dashboard">
      {/* User Info */}
      <div className="user-section">
        <h2>مرحباً، {dashboardData.user.name}</h2>
      </div>

      {/* Active Subscription */}
      {dashboardData.subscription && (
        <div className="subscription-card">
          <h3>الاشتراك النشط</h3>
          <p>{dashboardData.subscription.subscription.name}</p>
          {dashboardData.subscription.expires_at && (
            <p>ينتهي في: {formatDate(dashboardData.subscription.expires_at)}</p>
          )}
        </div>
      )}

      {/* Pending Request */}
      {dashboardData.pending_subscription_request && (
        <div className="pending-request-alert">
          <p>لديك طلب اشتراك قيد المراجعة</p>
        </div>
      )}

      {/* Statistics */}
      <div className="stats-grid">
        <div className="stat-card">
          <h3>إجمالي الحجوزات</h3>
          <p className="stat-value">{dashboardData.stats.bookings.total}</p>
        </div>
        
        <div className="stat-card">
          <h3>الحجوزات القادمة</h3>
          <p className="stat-value">{dashboardData.stats.bookings.upcoming}</p>
        </div>
        
        <div className="stat-card">
          <h3>إجمالي المنفق</h3>
          <p className="stat-value">{dashboardData.stats.payments.total_spent} ر.س</p>
        </div>
        
        <div className="stat-card">
          <h3>التذاكر المفتوحة</h3>
          <p className="stat-value">{dashboardData.stats.tickets.open}</p>
        </div>
        
        <div className="stat-card">
          <h3>الإشعارات غير المقروءة</h3>
          <p className="stat-value">{dashboardData.stats.notifications.unread_count}</p>
        </div>
      </div>

      {/* Recent Bookings */}
      <div className="recent-section">
        <h3>آخر الحجوزات</h3>
        {dashboardData.recent_bookings.length === 0 ? (
          <p>لا توجد حجوزات</p>
        ) : (
          <div className="bookings-list">
            {dashboardData.recent_bookings.map(booking => (
              <div key={booking.id} className="booking-card">
                <h4>
                  {booking.service?.name || booking.consultation?.name}
                </h4>
                <p>التاريخ: {booking.booking_date}</p>
                <p>الوقت: {booking.start_time} - {booking.end_time}</p>
                <p>الحالة: {getStatusText(booking.actual_status)}</p>
                <p>السعر: {booking.total_price} ر.س</p>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Recent Tickets */}
      <div className="recent-section">
        <h3>آخر التذاكر</h3>
        {dashboardData.recent_tickets.length === 0 ? (
          <p>لا توجد تذاكر</p>
        ) : (
          <div className="tickets-list">
            {dashboardData.recent_tickets.map(ticket => (
              <div key={ticket.id} className="ticket-card">
                <h4>{ticket.subject}</h4>
                <p>الحالة: {getTicketStatusText(ticket.status)}</p>
                {ticket.latest_message && (
                  <p>آخر رسالة: {ticket.latest_message.message}</p>
                )}
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

// Helper Functions
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('ar-SA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getStatusText = (status) => {
  const statusMap = {
    pending: 'قيد الانتظار',
    in_progress: 'قيد التنفيذ',
    completed: 'مكتمل',
    cancelled: 'ملغي'
  };
  return statusMap[status] || status;
};

const getTicketStatusText = (status) => {
  const statusMap = {
    open: 'مفتوح',
    in_progress: 'قيد المعالجة',
    resolved: 'محلول',
    closed: 'مغلق'
  };
  return statusMap[status] || status;
};

export default Dashboard;
```

---

## ⚠️ ملاحظات مهمة

### 1. الحالات الفعلية للحجوزات (Actual Status)
- `actual_status` يتم حسابه تلقائياً بناءً على الوقت الحالي:
  - `pending`: الحجز لم يبدأ بعد
  - `in_progress`: الحجز قيد التنفيذ الآن
  - `completed`: الحجز انتهى
  - `cancelled`: الحجز ملغي

### 2. الحجوزات القادمة (Upcoming)
- تشمل الحجوزات في الـ 7 أيام القادمة
- لا تشمل الحجوزات الملغاة

### 3. الإشعارات
- `unread_count` يعرض عدد الإشعارات غير المقروءة فقط
- للحصول على قائمة الإشعارات الكاملة، استخدم endpoint: `GET /api/notifications`

### 4. الأداء
- الـ endpoint يجلب آخر 5 حجوزات و 5 تذاكر فقط
- للحصول على قائمة كاملة، استخدم endpoints المخصصة:
  - `GET /api/customer/bookings` للحجوزات
  - `GET /api/tickets` للتذاكر

---

## 🔗 Endpoints ذات الصلة

- `GET /api/customer/profile` - معلومات المستخدم
- `GET /api/customer/bookings` - قائمة الحجوزات الكاملة
- `GET /api/tickets` - قائمة التذاكر الكاملة
- `GET /api/notifications` - قائمة الإشعارات
- `GET /api/subscriptions/active` - الاشتراك النشط
- `GET /api/subscriptions/requests` - طلبات الاشتراك

---

**تم إنشاء هذا الدليل بتاريخ:** 2025-01-27  
**الإصدار:** 1.0

