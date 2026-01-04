# 📊 API Documentation: التقارير والإحصائيات للمستخدمين

## نظرة عامة

Endpoint شامل للحصول على جميع التقارير والإحصائيات الشخصية للمستخدم (Customer) في الـ Frontend. يحتوي على إحصائيات الحجوزات، المدفوعات، التذاكر، الاشتراكات، والرسوم البيانية.

---

## 🔐 المصادقة

**مطلوبة:** ✅ نعم (Bearer Token - Laravel Sanctum)

**الصلاحيات:** ✅ Customer فقط

---

## 📡 Endpoint

### `GET /api/reports/statistics`

**الوصف:** الحصول على جميع الإحصائيات والتقارير الشخصية للمستخدم

**المصادقة:** ✅ مطلوبة (Customer فقط)

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters:** لا يوجد

**مثال:**
```
GET /api/reports/statistics
```

---

## 📤 Response Structure

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 5,
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "0501234567"
    },
    "bookings": {
      "total": 25,
      "pending": 2,
      "confirmed": 8,
      "in_progress": 1,
      "completed": 12,
      "cancelled": 2,
      "today": 1,
      "this_week": 3,
      "this_month": 8,
      "this_year": 25,
      "upcoming": 3
    },
    "payments": {
      "total_spent": 3450.75,
      "paid_bookings": 20,
      "unpaid_bookings": 5,
      "pending_payment": 850.50,
      "this_month_spent": 1200.00,
      "this_year_spent": 3450.75
    },
    "tickets": {
      "total": 8,
      "open": 2,
      "in_progress": 1,
      "resolved": 4,
      "closed": 1
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
          },
          {
            "name": "إدخال البيانات للموقع"
          }
        ]
      },
      "status": "active",
      "started_at": "2025-01-20T10:00:00.000000Z",
      "expires_at": "2025-02-20T10:00:00.000000Z",
      "is_active": true
    },
    "pending_subscription_request": null,
    "charts": {
      "monthly_bookings": [
        {
          "month": "2024-02",
          "month_name": "فبراير 2024",
          "count": 2
        },
        {
          "month": "2024-03",
          "month_name": "مارس 2024",
          "count": 3
        },
        {
          "month": "2024-04",
          "month_name": "أبريل 2024",
          "count": 5
        },
        {
          "month": "2024-05",
          "month_name": "مايو 2024",
          "count": 4
        },
        {
          "month": "2024-06",
          "month_name": "يونيو 2024",
          "count": 3
        },
        {
          "month": "2024-07",
          "month_name": "يوليو 2024",
          "count": 2
        },
        {
          "month": "2024-08",
          "month_name": "أغسطس 2024",
          "count": 1
        },
        {
          "month": "2024-09",
          "month_name": "سبتمبر 2024",
          "count": 2
        },
        {
          "month": "2024-10",
          "month_name": "أكتوبر 2024",
          "count": 1
        },
        {
          "month": "2024-11",
          "month_name": "نوفمبر 2024",
          "count": 0
        },
        {
          "month": "2024-12",
          "month_name": "ديسمبر 2024",
          "count": 1
        },
        {
          "month": "2025-01",
          "month_name": "يناير 2025",
          "count": 1
        }
      ],
      "monthly_spending": [
        {
          "month": "2024-02",
          "month_name": "فبراير 2024",
          "amount": 300.00
        },
        {
          "month": "2024-03",
          "month_name": "مارس 2024",
          "amount": 450.00
        },
        {
          "month": "2024-04",
          "month_name": "أبريل 2024",
          "amount": 750.00
        },
        {
          "month": "2024-05",
          "month_name": "مايو 2024",
          "amount": 600.00
        },
        {
          "month": "2024-06",
          "month_name": "يونيو 2024",
          "amount": 450.00
        },
        {
          "month": "2024-07",
          "month_name": "يوليو 2024",
          "amount": 300.00
        },
        {
          "month": "2024-08",
          "month_name": "أغسطس 2024",
          "amount": 150.00
        },
        {
          "month": "2024-09",
          "month_name": "سبتمبر 2024",
          "amount": 300.00
        },
        {
          "month": "2024-10",
          "month_name": "أكتوبر 2024",
          "amount": 150.00
        },
        {
          "month": "2024-11",
          "month_name": "نوفمبر 2024",
          "amount": 0.00
        },
        {
          "month": "2024-12",
          "month_name": "ديسمبر 2024",
          "amount": 150.00
        },
        {
          "month": "2025-01",
          "month_name": "يناير 2025",
          "amount": 150.00
        }
      ],
      "bookings_by_status": {
        "pending": 2,
        "confirmed": 8,
        "in_progress": 1,
        "completed": 12,
        "cancelled": 2
      }
    },
    "most_used_services": [
      {
        "id": 5,
        "name": "استشارة قانونية",
        "bookings_count": 8
      },
      {
        "id": 12,
        "name": "استشارة مالية",
        "bookings_count": 5
      },
      {
        "id": 3,
        "name": "استشارة تقنية",
        "bookings_count": 4
      },
      {
        "id": 7,
        "name": "استشارة طبية",
        "bookings_count": 3
      },
      {
        "id": 9,
        "name": "استشارة تعليمية",
        "bookings_count": 2
      }
    ],
    "most_used_consultations": [
      {
        "id": 2,
        "name": "استشارة عامة",
        "bookings_count": 3
      },
      {
        "id": 5,
        "name": "استشارة متخصصة",
        "bookings_count": 2
      }
    ],
    "recent_bookings": [
      {
        "id": 45,
        "service": {
          "id": 5,
          "name": "استشارة قانونية",
          "sub_category": {
            "id": 2,
            "name": "القانون المدني"
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
        "booking_type": "service",
        "booking_date": "2025-01-28",
        "start_time": "10:00:00",
        "end_time": "11:00:00",
        "total_price": "150.00",
        "status": "confirmed",
        "actual_status": "pending",
        "payment_status": "paid",
        "created_at": "2025-01-27T10:00:00.000000Z"
      },
      {
        "id": 44,
        "service": null,
        "consultation": {
          "id": 2,
          "name": "استشارة عامة"
        },
        "employee": {
          "id": 5,
          "user": {
            "id": 12,
            "name": "سارة علي"
          }
        },
        "booking_type": "consultation",
        "booking_date": "2025-01-25",
        "start_time": "14:00:00",
        "end_time": "15:00:00",
        "total_price": "200.00",
        "status": "completed",
        "actual_status": "completed",
        "payment_status": "paid",
        "created_at": "2025-01-24T09:00:00.000000Z"
      }
    ],
    "upcoming_bookings": [
      {
        "id": 45,
        "service": {
          "id": 5,
          "name": "استشارة قانونية"
        },
        "consultation": null,
        "employee": {
          "id": 3,
          "user": {
            "id": 10,
            "name": "محمد أحمد"
          }
        },
        "booking_type": "service",
        "booking_date": "2025-01-28",
        "start_time": "10:00:00",
        "end_time": "11:00:00",
        "status": "confirmed",
        "actual_status": "pending"
      },
      {
        "id": 46,
        "service": {
          "id": 12,
          "name": "استشارة مالية"
        },
        "consultation": null,
        "employee": {
          "id": 4,
          "user": {
            "id": 11,
            "name": "فاطمة حسن"
          }
        },
        "booking_type": "service",
        "booking_date": "2025-01-30",
        "start_time": "15:00:00",
        "end_time": "16:00:00",
        "status": "confirmed",
        "actual_status": "pending"
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

---

### 2. Bookings Statistics
```typescript
interface BookingsStats {
  total: number;           // إجمالي الحجوزات
  pending: number;         // الحجوزات قيد الانتظار
  confirmed: number;       // الحجوزات المؤكدة
  in_progress: number;     // الحجوزات قيد التنفيذ
  completed: number;       // الحجوزات المكتملة
  cancelled: number;       // الحجوزات الملغاة
  today: number;           // حجوزات اليوم
  this_week: number;       // حجوزات هذا الأسبوع
  this_month: number;      // حجوزات هذا الشهر
  this_year: number;       // حجوزات هذه السنة
  upcoming: number;        // الحجوزات القادمة (غير ملغاة)
}
```

---

### 3. Payment Statistics
```typescript
interface PaymentStats {
  total_spent: number;         // إجمالي المبلغ المنفق
  paid_bookings: number;       // عدد الحجوزات المدفوعة
  unpaid_bookings: number;     // عدد الحجوزات غير المدفوعة
  pending_payment: number;     // المبلغ المعلق (غير مدفوع)
  this_month_spent: number;   // المبلغ المنفق هذا الشهر
  this_year_spent: number;    // المبلغ المنفق هذه السنة
}
```

---

### 4. Tickets Statistics
```typescript
interface TicketsStats {
  total: number;           // إجمالي التذاكر
  open: number;            // التذاكر المفتوحة
  in_progress: number;     // التذاكر قيد المعالجة
  resolved: number;        // التذاكر المحلولة
  closed: number;          // التذاكر المغلقة
}
```

---

### 5. Subscription (إذا كان موجود)
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
  started_at: string;      // ISO 8601
  expires_at: string | null; // ISO 8601
  is_active: boolean;
}
```

**ملاحظة:** `subscription` يكون `null` إذا لم يكن لدى المستخدم اشتراك نشط.

---

### 6. Charts Data

#### Monthly Bookings (آخر 12 شهر)
```typescript
interface MonthlyBookings {
  month: string;        // YYYY-MM
  month_name: string;   // اسم الشهر بالعربية (مثال: "يناير 2025")
  count: number;        // عدد الحجوزات في هذا الشهر
}
```

#### Monthly Spending (آخر 12 شهر)
```typescript
interface MonthlySpending {
  month: string;        // YYYY-MM
  month_name: string;   // اسم الشهر بالعربية
  amount: number;       // المبلغ المنفق في هذا الشهر
}
```

#### Bookings by Status
```typescript
interface BookingsByStatus {
  pending: number;
  confirmed: number;
  in_progress: number;
  completed: number;
  cancelled: number;
}
```

---

### 7. Most Used Services (أكثر الخدمات استخداماً - Top 5)
```typescript
interface MostUsedService {
  id: number;
  name: string;
  bookings_count: number;
}
```

---

### 8. Most Used Consultations (أكثر الاستشارات استخداماً - Top 5)
```typescript
interface MostUsedConsultation {
  id: number;
  name: string;
  bookings_count: number;
}
```

---

### 9. Recent Bookings (آخر 10 حجوزات)
```typescript
interface RecentBooking {
  id: number;
  service: {
    id: number;
    name: string;
    sub_category: {
      id: number;
      name: string;
    } | null;
  } | null;
  consultation: {
    id: number;
    name: string;
  } | null;
  employee: {
    id: number;
    user: {
      id: number;
      name: string;
    } | null;
  } | null;
  booking_type: 'service' | 'consultation';
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

---

### 10. Upcoming Bookings (الحجوزات القادمة - خلال 7 أيام)
```typescript
interface UpcomingBooking {
  id: number;
  service: {
    id: number;
    name: string;
  } | null;
  consultation: {
    id: number;
    name: string;
  } | null;
  employee: {
    id: number;
    user: {
      id: number;
      name: string;
    } | null;
  } | null;
  booking_type: 'service' | 'consultation';
  booking_date: string;    // YYYY-MM-DD
  start_time: string;      // HH:mm:ss
  end_time: string;        // HH:mm:ss
  status: 'pending' | 'confirmed' | 'in_progress' | 'completed' | 'cancelled';
  actual_status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
}
```

---

## 🎯 مثال على الاستخدام في React

```javascript
import { useState, useEffect } from 'react';

function ReportsPage() {
  const [reportsData, setReportsData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchReports = async () => {
      try {
        const token = localStorage.getItem('token');
        
        const response = await fetch('http://your-domain.com/api/reports/statistics', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        const data = await response.json();
        
        if (data.success) {
          setReportsData(data.data);
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

    fetchReports();
  }, []);

  if (loading) return <div>جاري التحميل...</div>;
  if (error) return <div className="error">{error}</div>;
  if (!reportsData) return null;

  return (
    <div className="reports-page">
      {/* Statistics Cards */}
      <div className="stats-grid">
        <div className="stat-card">
          <h3>إجمالي الحجوزات</h3>
          <p className="stat-value">{reportsData.bookings.total}</p>
        </div>
        
        <div className="stat-card">
          <h3>إجمالي المنفق</h3>
          <p className="stat-value">{reportsData.payments.total_spent} ر.س</p>
        </div>
        
        <div className="stat-card">
          <h3>الحجوزات القادمة</h3>
          <p className="stat-value">{reportsData.bookings.upcoming}</p>
        </div>
        
        <div className="stat-card">
          <h3>التذاكر المفتوحة</h3>
          <p className="stat-value">{reportsData.tickets.open}</p>
        </div>
      </div>

      {/* Charts */}
      <div className="charts-section">
        <h2>الرسوم البيانية</h2>
        
        {/* Monthly Bookings Chart */}
        <div className="chart-container">
          <h3>الحجوزات الشهرية (آخر 12 شهر)</h3>
          <Chart
            data={reportsData.charts.monthly_bookings}
            xKey="month_name"
            yKey="count"
            type="line"
          />
        </div>
        
        {/* Monthly Spending Chart */}
        <div className="chart-container">
          <h3>الإنفاق الشهري (آخر 12 شهر)</h3>
          <Chart
            data={reportsData.charts.monthly_spending}
            xKey="month_name"
            yKey="amount"
            type="bar"
          />
        </div>
        
        {/* Bookings by Status Chart */}
        <div className="chart-container">
          <h3>الحجوزات حسب الحالة</h3>
          <Chart
            data={reportsData.charts.bookings_by_status}
            type="pie"
          />
        </div>
      </div>

      {/* Most Used Services */}
      <div className="most-used-section">
        <h2>أكثر الخدمات استخداماً</h2>
        <ul>
          {reportsData.most_used_services.map(service => (
            <li key={service.id}>
              {service.name} - {service.bookings_count} حجز
            </li>
          ))}
        </ul>
      </div>

      {/* Recent Bookings */}
      <div className="recent-bookings-section">
        <h2>آخر الحجوزات</h2>
        {reportsData.recent_bookings.map(booking => (
          <div key={booking.id} className="booking-card">
            <h4>{booking.service?.name || booking.consultation?.name}</h4>
            <p>التاريخ: {booking.booking_date}</p>
            <p>الحالة: {getStatusText(booking.actual_status)}</p>
            <p>السعر: {booking.total_price} ر.س</p>
          </div>
        ))}
      </div>

      {/* Upcoming Bookings */}
      <div className="upcoming-bookings-section">
        <h2>الحجوزات القادمة</h2>
        {reportsData.upcoming_bookings.map(booking => (
          <div key={booking.id} className="booking-card">
            <h4>{booking.service?.name || booking.consultation?.name}</h4>
            <p>التاريخ: {booking.booking_date}</p>
            <p>الوقت: {booking.start_time} - {booking.end_time}</p>
          </div>
        ))}
      </div>
    </div>
  );
}

// Helper Functions
const getStatusText = (status) => {
  const statusMap = {
    pending: 'قيد الانتظار',
    in_progress: 'قيد التنفيذ',
    completed: 'مكتمل',
    cancelled: 'ملغي'
  };
  return statusMap[status] || status;
};

export default ReportsPage;
```

---

## 📈 استخدام الرسوم البيانية

### مثال باستخدام Chart.js

```javascript
import { Line, Bar, Pie } from 'react-chartjs-2';

// Monthly Bookings Chart
const monthlyBookingsChart = {
  labels: reportsData.charts.monthly_bookings.map(item => item.month_name),
  datasets: [{
    label: 'عدد الحجوزات',
    data: reportsData.charts.monthly_bookings.map(item => item.count),
    borderColor: 'rgb(75, 192, 192)',
    backgroundColor: 'rgba(75, 192, 192, 0.2)',
  }]
};

// Monthly Spending Chart
const monthlySpendingChart = {
  labels: reportsData.charts.monthly_spending.map(item => item.month_name),
  datasets: [{
    label: 'المبلغ (ر.س)',
    data: reportsData.charts.monthly_spending.map(item => item.amount),
    backgroundColor: 'rgba(54, 162, 235, 0.5)',
  }]
};

// Bookings by Status Chart
const bookingsByStatusChart = {
  labels: ['قيد الانتظار', 'مؤكد', 'قيد التنفيذ', 'مكتمل', 'ملغي'],
  datasets: [{
    data: [
      reportsData.charts.bookings_by_status.pending,
      reportsData.charts.bookings_by_status.confirmed,
      reportsData.charts.bookings_by_status.in_progress,
      reportsData.charts.bookings_by_status.completed,
      reportsData.charts.bookings_by_status.cancelled,
    ],
    backgroundColor: [
      '#FFCE56',
      '#36A2EB',
      '#FF6384',
      '#4BC0C0',
      '#9966FF',
    ],
  }]
};
```

---

## ⚠️ ملاحظات مهمة

### 1. البيانات الشهرية
- `monthly_bookings` و `monthly_spending` تحتوي على آخر 12 شهر
- البيانات مرتبة من الأقدم للأحدث

### 2. الحالات الفعلية
- `actual_status` يتم حسابه تلقائياً بناءً على الوقت الحالي
- `status` هو الحالة المحفوظة في قاعدة البيانات

### 3. الحجوزات القادمة
- تشمل الحجوزات في الـ 7 أيام القادمة فقط
- لا تشمل الحجوزات الملغاة

### 4. أكثر الخدمات/الاستشارات استخداماً
- تعرض Top 5 فقط
- مرتبة حسب عدد الحجوزات (من الأكثر للأقل)

---

## 🔗 Endpoints ذات الصلة

- `GET /api/customer/dashboard` - Dashboard سريع
- `GET /api/customer/bookings` - قائمة الحجوزات الكاملة
- `GET /api/tickets` - قائمة التذاكر الكاملة
- `GET /api/subscriptions/active` - الاشتراك النشط

---

**تم إنشاء هذا الدليل بتاريخ:** 2025-01-27  
**الإصدار:** 1.0
