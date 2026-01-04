# 📊 API Documentation: التقارير والإحصائيات

## نظرة عامة

Endpoint لعرض التقارير والإحصائيات للمستخدمين (Customer) في الـ Frontend.

---

## 🔐 المصادقة

**مطلوبة:** ✅ نعم (Bearer Token - Laravel Sanctum)

---

## 📡 Endpoint

### `GET /api/reports/statistics`

**الوصف:** الحصول على التقارير والإحصائيات الشاملة حسب نوع المستخدم

**المصادقة:** ✅ مطلوبة

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
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
    "bookings": {
      "total": 15,
      "pending": 2,
      "confirmed": 5,
      "in_progress": 1,
      "completed": 6,
      "cancelled": 1,
      "today": 1,
      "this_week": 3,
      "this_month": 8,
      "this_year": 15,
      "upcoming": 3
    },
    "payments": {
      "total_spent": 1450.50,
      "paid_bookings": 12,
      "unpaid_bookings": 3,
      "pending_payment": 300.00,
      "this_month_spent": 850.00,
      "this_year_spent": 1450.50
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
        }
      ],
      "monthly_spending": [
        {
          "month": "2024-02",
          "month_name": "فبراير 2024",
          "amount": 200.00
        },
        {
          "month": "2024-03",
          "month_name": "مارس 2024",
          "amount": 450.00
        }
      ],
      "bookings_by_status": {
        "pending": 2,
        "confirmed": 5,
        "in_progress": 1,
        "completed": 6,
        "cancelled": 1
      }
    },
    "most_used_services": [
      {
        "id": 5,
        "name": "استشارة قانونية",
        "bookings_count": 8
      },
      {
        "id": 3,
        "name": "استشارة مالية",
        "bookings_count": 5
      }
    ],
    "most_used_consultations": [
      {
        "id": 2,
        "name": "استشارة تقنية",
        "bookings_count": 3
      }
    ],
    "recent_bookings": [
      {
        "id": 25,
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
      }
    ],
    "upcoming_bookings": [
      {
        "id": 26,
        "service": {
          "id": 3,
          "name": "استشارة مالية"
        },
        "consultation": null,
        "employee": {
          "id": 2,
          "user": {
            "id": 8,
            "name": "سارة علي"
          }
        },
        "booking_type": "service",
        "booking_date": "2025-01-29",
        "start_time": "14:00:00",
        "end_time": "15:00:00",
        "status": "confirmed",
        "actual_status": "pending"
      }
    ]
  }
}
```

---

## 📊 تفاصيل البيانات

#### Bookings Statistics
```typescript
interface BookingsStats {
  total: number;
  pending: number;
  confirmed: number;
  in_progress: number;
  completed: number;
  cancelled: number;
  today: number;
  this_week: number;
  this_month: number;
  this_year: number;
  upcoming: number;
}
```

#### Payment Statistics
```typescript
interface PaymentStats {
  total_spent: number;
  paid_bookings: number;
  unpaid_bookings: number;
  pending_payment: number;
  this_month_spent: number;
  this_year_spent: number;
}
```

#### Charts Data
```typescript
interface ChartsData {
  monthly_bookings: Array<{
    month: string;
    month_name: string;
    count: number;
  }>;
  monthly_spending: Array<{
    month: string;
    month_name: string;
    amount: number;
  }>;
  bookings_by_status: {
    pending: number;
    confirmed: number;
    in_progress: number;
    completed: number;
    cancelled: number;
  };
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
      <h1>التقارير والإحصائيات</h1>

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
          <h3>الحجوزات المكتملة</h3>
          <p className="stat-value">{reportsData.bookings.completed}</p>
        </div>
        
        <div className="stat-card">
          <h3>الحجوزات القادمة</h3>
          <p className="stat-value">{reportsData.bookings.upcoming}</p>
        </div>
      </div>

      {/* Charts Section */}
      <div className="charts-section">
        <h2>الرسوم البيانية</h2>
        
        {/* Monthly Bookings Chart */}
        <div className="chart-container">
          <h3>الحجوزات الشهرية</h3>
          <Chart
            data={reportsData.charts.monthly_bookings}
            xKey="month_name"
            yKey="count"
            type="line"
          />
        </div>

        {/* Monthly Spending Chart */}
        <div className="chart-container">
          <h3>الإنفاق الشهري</h3>
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
      <div className="services-section">
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

## 📝 ملاحظات مهمة

### 1. الرسوم البيانية
- `monthly_bookings`: الحجوزات الشهرية (آخر 12 شهر)
- `monthly_spending`: الإنفاق الشهري (آخر 12 شهر) - للمستخدم فقط
- `monthly_revenue`: الإيرادات الشهرية (آخر 12 شهر) - للإدارة فقط
- `bookings_by_status`: توزيع الحجوزات حسب الحالة

### 2. الأداء
- البيانات يتم جلبها من قاعدة البيانات مباشرة
- لا يتم إنشاء تقارير مسبقة - البيانات ديناميكية
- للحصول على بيانات محدثة، قم بطلب الـ endpoint مرة أخرى

---

## 🔗 Endpoints ذات الصلة

- `GET /api/customer/dashboard` - Dashboard للمستخدم
- `GET /api/customer/bookings` - قائمة الحجوزات
- `GET /api/tickets` - قائمة التذاكر
- `GET /api/subscriptions/active` - الاشتراك النشط

---

## 📌 ملخص الـ Endpoint

| Method | Endpoint | المصادقة | الوصف |
|--------|----------|----------|-------|
| GET | `/api/reports/statistics` | ✅ | التقارير والإحصائيات الشاملة |

---

**تم إنشاء هذا الدليل بتاريخ:** 2025-01-27  
**الإصدار:** 1.0
