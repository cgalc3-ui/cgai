# 📚 دليل اختبار API في Postman

## 🚀 خطوات الاستخدام

### 1. استيراد Collection في Postman

1. افتح Postman
2. اضغط على **Import** في الأعلى
3. اختر ملف `CGAI_API_Collection.json`
4. سيتم استيراد Collection كاملة

### 2. إعداد المتغيرات (Variables)

بعد الاستيراد، قم بتحديث المتغيرات التالية:

- **base_url**: `http://192.168.1.153:8000/api` (أو IP جهازك)
- **phone**: رقم الهاتف الذي تريد اختباره (مثال: `0501234567`)
- **token**: سيتم ملؤه تلقائياً بعد تسجيل الدخول
- **verification_code**: كود التحقق من SMS (يجب إدخاله يدوياً)

### 3. سيناريو الاختبار الكامل

#### 📝 **السيناريو 1: تسجيل عميل جديد**

1. **Send Verification Code (Registration)**
   - Method: `POST`
   - URL: `{{base_url}}/send-verification-code`
   - Body:
     ```json
     {
         "phone": "0501234567",
         "type": "registration"
     }
     ```
   - ✅ النتيجة: سيتم إرسال SMS بكود التحقق

2. **Register New Customer**
   - Method: `POST`
   - URL: `{{base_url}}/register`
   - Body:
     ```json
     {
         "name": "أحمد محمد",
         "phone": "0501234567",
         "code": "123456"
     }
     ```
   - ⚠️ **مهم**: استبدل `123456` بكود التحقق الفعلي من SMS
   - ✅ النتيجة: سيتم إنشاء المستخدم وإرجاع Token

#### 🔐 **السيناريو 2: تسجيل دخول عميل موجود**

1. **Send Verification Code (Login)**
   - Method: `POST`
   - URL: `{{base_url}}/send-verification-code`
   - Body:
     ```json
     {
         "phone": "0501234567",
         "type": "login"
     }
     ```
   - ✅ النتيجة: سيتم إرسال SMS بكود التحقق

2. **Login Customer**
   - Method: `POST`
   - URL: `{{base_url}}/login`
   - Body:
     ```json
     {
         "phone": "0501234567",
         "code": "123456"
     }
     ```
   - ⚠️ **مهم**: استبدل `123456` بكود التحقق الفعلي من SMS
   - ✅ النتيجة: سيتم إرجاع Token

#### 👤 **السيناريو 3: استخدام API المحمية (Protected Routes)**

بعد الحصول على Token من التسجيل أو تسجيل الدخول:

1. **Get Authenticated User**
   - Method: `GET`
   - URL: `{{base_url}}/user`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     ```
   - ✅ النتيجة: بيانات المستخدم

2. **Get Customer Profile**
   - Method: `GET`
   - URL: `{{base_url}}/customer/profile`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     ```
   - ✅ النتيجة: ملف العميل الشخصي

3. **Update Customer Profile**
   - Method: `PUT`
   - URL: `{{base_url}}/customer/profile`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     Content-Type: application/json
     ```
   - Body:
     ```json
     {
         "name": "أحمد محمد علي"
     }
     ```
   - ✅ النتيجة: تم تحديث الملف الشخصي

4. **Get Customer Dashboard**
   - Method: `GET`
   - URL: `{{base_url}}/customer/dashboard`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     ```
   - ✅ النتيجة: بيانات Dashboard العميل

5. **Logout**
   - Method: `POST`
   - URL: `{{base_url}}/logout`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     ```
   - ✅ النتيجة: تم تسجيل الخروج وحذف Token

#### 📅 **السيناريو 4: عملية الحجز الكاملة**

1. **Get Available Dates with Time Slots** (جلب التواريخ المتاحة مع الأوقات)
   - Method: `GET`
   - URL: `{{base_url}}/customer/bookings/available-dates?service_id=1`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     ```
   - ✅ النتيجة: قائمة بالتواريخ المتاحة للـ 30 يوم القادمة مع الأوقات المتاحة كل ساعة (من 8 صباحاً إلى 8 مساءً) لكل تاريخ
   - 📝 **ملاحظة**: 
     - التخصص والمدة تُؤخذ تلقائياً من الخدمة (المدة ثابتة: ساعة واحدة)
     - يعرض جميع الموظفين المتاحين لكل وقت
     - كل تاريخ يحتوي على قائمة `time_slots` مع الأوقات المتاحة وغير المتاحة

2. **Create Booking** (إنشاء حجز جديد)
   - Method: `POST`
   - URL: `{{base_url}}/customer/bookings`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     Content-Type: application/json
     ```
   - Body:
     ```json
     {
         "service_id": 1,
         "booking_date": "2025-12-26",
         "start_time": "10:00",
         "notes": "ملاحظات إضافية (اختياري)"
     }
     ```
   - ✅ النتيجة: تم إنشاء الحجز بنجاح
   - 📝 **ملاحظات مهمة**:
     - التخصص والمدة تُؤخذ تلقائياً من الخدمة (المدة ثابتة: ساعة واحدة)
     - النظام يختار الموظف تلقائياً (أول موظف متاح)
     - الموظفون الآخرون المتاحون يبقون متاحين للعملاء الآخرين

3. **Get All Bookings** (جلب جميع الحجوزات)
   - Method: `GET`
   - URL: `{{base_url}}/customer/bookings?status=pending&payment_status=unpaid`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     ```
   - Query Parameters (اختياري):
     - `status`: pending, confirmed, cancelled, completed
     - `payment_status`: paid, unpaid
   - ✅ النتيجة: قائمة بجميع حجوزات العميل

4. **Get Booking by ID** (تفاصيل حجز معين)
   - Method: `GET`
   - URL: `{{base_url}}/customer/bookings/1`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     ```
   - ✅ النتيجة: تفاصيل الحجز الكاملة

5. **Update Booking** (تحديث حجز)
   - Method: `PUT`
   - URL: `{{base_url}}/customer/bookings/1`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     Content-Type: application/json
     ```
   - Body:
     ```json
     {
         "notes": "ملاحظات محدثة"
     }
     ```
   - ✅ النتيجة: تم تحديث الحجز

6. **Cancel Booking** (إلغاء حجز)
   - Method: `POST`
   - URL: `{{base_url}}/customer/bookings/1/cancel`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     Content-Type: application/json
     ```
   - Body:
     ```json
     {
         "reason": "سبب الإلغاء (اختياري)"
     }
     ```
   - ✅ النتيجة: تم إلغاء الحجز

7. **Process Payment** (دفع الحجز)
   - Method: `POST`
   - URL: `{{base_url}}/customer/bookings/1/payment`
   - Headers:
     ```
     Authorization: Bearer {{token}}
     Content-Type: application/json
     ```
   - Body:
     ```json
     {
         "payment_method": "cash",
         "transaction_id": "TXN123456 (اختياري)"
     }
     ```
   - ✅ النتيجة: تم الدفع بنجاح وتغيير حالة الحجز إلى `confirmed`

---

## 📋 جميع Endpoints المتاحة

### 🔓 Public Routes (لا تحتاج Token)

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/send-verification-code` | إرسال كود التحقق |
| POST | `/api/verify-registration-code` | التحقق من كود التسجيل (الخطوة 1) |
| POST | `/api/complete-registration` | إكمال التسجيل (الخطوة 2) |
| POST | `/api/login` | تسجيل دخول عميل |

#### Services (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/services/categories` | قائمة الفئات |
| GET | `/api/services/categories/{id}` | تفاصيل فئة معينة |
| GET | `/api/services/sub-categories` | قائمة الفئات الفرعية |
| GET | `/api/services/sub-categories/{id}` | تفاصيل فئة فرعية معينة |
| GET | `/api/services/services` | قائمة الخدمات |
| GET | `/api/services/services/{id}` | تفاصيل خدمة معينة |

#### Consultations (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/consultations` | قائمة الاستشارات |
| GET | `/api/consultations/{id}` | تفاصيل استشارة معينة |
| GET | `/api/consultations/category/{categoryId}` | الاستشارات حسب الفئة |

#### FAQ (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/faqs` | قائمة الأسئلة الشائعة |
| GET | `/api/faqs/category/{category}` | الأسئلة حسب الفئة |

#### Ratings (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/ratings` | قائمة التقييمات |
| GET | `/api/ratings/statistics` | إحصائيات التقييمات |

#### Payment Callback (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| ANY | `/api/payment/callback` | Callback من بوابة الدفع (PayMob) |

### 🔒 Protected Routes (تحتاج Token)

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user` | بيانات المستخدم |
| POST | `/api/logout` | تسجيل الخروج |

#### Customer Profile
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer/profile` | ملف العميل |
| PUT | `/api/customer/profile` | تحديث ملف العميل |
| POST | `/api/customer/profile/avatar` | تحديث صورة الملف الشخصي |
| GET | `/api/customer/dashboard` | Dashboard العميل |

#### Customer Reports & Activity
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer/reports` | تقارير العميل |
| GET | `/api/customer/activity-log` | سجل نشاط العميل |

#### Help Guides
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer/help-guide` | قائمة أدلة المساعدة |
| GET | `/api/customer/help-guide/{id}` | تفاصيل دليل مساعدة معين |

#### Service Bookings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer/bookings` | جميع حجوزات العميل |
| GET | `/api/customer/bookings/past` | الحجوزات السابقة |
| GET | `/api/customer/bookings/available-dates` | التواريخ المتاحة مع الأوقات كل ساعة |
| GET | `/api/customer/bookings/available-time-slots` | الأوقات المتاحة |
| POST | `/api/customer/bookings` | إنشاء حجز جديد |
| GET | `/api/customer/bookings/{id}` | تفاصيل حجز معين |
| PUT | `/api/customer/bookings/{id}` | تحديث حجز |
| POST | `/api/customer/bookings/{id}/cancel` | إلغاء حجز |
| POST | `/api/customer/bookings/payment` | دفع الحجز |
| POST | `/api/customer/bookings/initiate-online-payment` | بدء الدفع الإلكتروني |
| POST | `/api/customer/bookings/{bookingId}/pay-online` | دفع إلكتروني (Legacy - PayMob) |

#### Consultation Bookings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer/bookings/consultation/available-dates` | التواريخ المتاحة للاستشارات |
| GET | `/api/customer/bookings/consultation/available-time-slots` | الأوقات المتاحة للاستشارات |
| POST | `/api/customer/bookings/consultation` | إنشاء حجز استشارة |

#### Ratings
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/customer/ratings` | إضافة تقييم |
| GET | `/api/customer/ratings/my-ratings` | تقييماتي |

#### Invoices
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer/invoices` | قائمة الفواتير |
| GET | `/api/customer/invoices/{booking}` | تفاصيل فاتورة معينة |
| GET | `/api/customer/invoices/{booking}/download` | تحميل الفاتورة |

#### Points & Wallet
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer/points/wallet` | محفظة النقاط |
| POST | `/api/customer/points/purchase` | شراء نقاط |
| GET | `/api/customer/points/transactions` | معاملات النقاط |

#### Employee Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/employee/dashboard` | Dashboard الموظف |
| GET | `/api/employee/bookings` | حجوزات الموظف |

#### Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notifications` | قائمة الإشعارات |
| GET | `/api/notifications/unread-count` | عدد الإشعارات غير المقروءة |
| POST | `/api/notifications/{notification}/read` | تحديد إشعار كمقروء |
| POST | `/api/notifications/mark-all-read` | تحديد جميع الإشعارات كمقروءة |
| DELETE | `/api/notifications/{notification}` | حذف إشعار |

#### Tickets (Support)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tickets` | قائمة التذاكر |
| POST | `/api/tickets` | إنشاء تذكرة جديدة |
| GET | `/api/tickets/{ticket}` | تفاصيل تذكرة معينة |
| POST | `/api/tickets/{ticketId}/messages` | إضافة رسالة لتذكرة |
| PUT | `/api/tickets/{ticket}/status` | تحديث حالة التذكرة |

#### Subscriptions
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/subscriptions` | قائمة الاشتراكات |
| GET | `/api/subscriptions/active` | الاشتراكات النشطة |
| GET | `/api/subscriptions/requests` | طلبات الاشتراكات |
| GET | `/api/subscriptions/{subscription}` | تفاصيل اشتراك معين |
| POST | `/api/subscriptions` | إنشاء طلب اشتراك |

#### Consultations (Protected - Available Dates)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/consultations/available-dates` | التواريخ المتاحة للاستشارات |
| GET | `/api/consultations/available-time-slots` | الأوقات المتاحة للاستشارات |

---

## 🔑 استخدام Token

بعد الحصول على Token من التسجيل أو تسجيل الدخول:

1. سيتم حفظه تلقائياً في متغير `{{token}}`
2. أضف Header في جميع الطلبات المحمية:
   ```
   Authorization: Bearer {{token}}
   ```

---

## ⚠️ ملاحظات مهمة

1. **كود التحقق**: يجب إدخال كود التحقق الفعلي من SMS يدوياً
2. **Token**: يتم حفظه تلقائياً بعد التسجيل/تسجيل الدخول
3. **رقم الهاتف**: يجب أن يكون رقم صحيح وغير مسجل للتسجيل
4. **Base URL**: تأكد من تحديث `base_url` ليطابق IP جهازك

---

## 🧪 أمثلة Responses

### ✅ Success Response (Register/Login)
```json
{
    "success": true,
    "message": "تم التسجيل بنجاح",
    "user": {
        "id": 1,
        "name": "أحمد محمد",
        "phone": "0501234567",
        "role": "customer",
        "phone_verified_at": "2024-01-01T12:00:00.000000Z"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### ❌ Error Response
```json
{
    "success": false,
    "message": "كود التحقق غير صحيح أو منتهي الصلاحية"
}
```

---

---

## 📅 شرح عملية الحجز بالتفصيل

### 🔄 سير العمل الكامل:

```
1. العميل يختار الخدمة (service_id)
   ↓
2. النظام يأخذ specialization_id من الخدمة تلقائياً
   ↓
3. جلب التواريخ المتاحة مع الأوقات كل ساعة (30 يوم):
   GET /api/customer/bookings/available-dates?service_id=1
   Response: [
     {
       "date": "2025-12-26",
       "formatted_date": "2025-12-26",
       "day_name": "الجمعة",
       "time_slots": [
         {
           "start_time": "10:00",
           "end_time": "11:00",
           "is_available": true
         },
         ...
       ]
     },
     ...
   ]
   ↓
4. العميل يختار التاريخ والوقت المتاح
   ↓
5. إنشاء الحجز:
   POST /api/customer/bookings
   Body: {
     "service_id": 1,
     "booking_date": "2025-12-26",
     "start_time": "10:00",
     "notes": "optional"
   }
   ↓
6. النظام يختار موظف واحد تلقائياً (أول موظف متاح)
   ↓
7. إنشاء الحجز مع الموظف المختار
   ↓
8. الموظفون الآخرون المتاحون يبقون متاحين للعملاء الآخرين
   ↓
9. الحجز جاهز (status: pending, payment_status: unpaid)
```

### 📋 ملاحظات مهمة عن الحجز:

1. **التخصص**: يُؤخذ تلقائياً من `service.specialization_id`
2. **المدة**: ثابتة - ساعة واحدة لكل حجز
3. **السعر**: من `service.hourly_rate`
4. **الموظف**: يُختار تلقائياً (أول موظف متاح)
5. **الأوقات**: كل ساعة من 8 صباحاً إلى 8 مساءً
6. **التواريخ**: 30 يوم القادمة (تُرجع جميع التواريخ دائماً، حتى لو لم يكن هناك موظفين متاحين - في هذه الحالة جميع الأوقات تكون `is_available: false`)
7. **الموظفون المتاحون**: يبقون متاحين للعملاء الآخرين حتى يتم حجزهم

### 📊 مثال Response للتواريخ مع الأوقات:

```json
{
  "success": true,
  "data": [
    {
      "date": "2025-12-26",
      "formatted_date": "2025-12-26",
      "day_name": "الجمعة",
      "time_slots": [
        {
          "start_time": "10:00",
          "end_time": "11:00",
          "is_available": true
        },
        {
          "start_time": "11:00",
          "end_time": "12:00",
          "is_available": true
        },
        {
          "start_time": "12:00",
          "end_time": "13:00",
          "is_available": false
        }
      ]
    },
    {
      "date": "2025-12-27",
      "formatted_date": "2025-12-27",
      "day_name": "السبت",
      "time_slots": [
        {
          "start_time": "08:00",
          "end_time": "09:00",
          "is_available": false
        },
        {
          "start_time": "09:00",
          "end_time": "10:00",
          "is_available": false
        },
        ...
      ],
      "message": "لا يوجد مواعيد متاحة"
    }
  ]
}
```

### 📊 مثال Response لإنشاء الحجز:

```json
{
  "success": true,
  "message": "تم إنشاء الحجز بنجاح",
  "data": {
    "id": 1,
    "customer_id": 1,
    "employee_id": 1,
    "service_id": 1,
    "service_duration_id": 1,
    "time_slot_id": 1,
    "booking_date": "2025-12-26",
    "start_time": "10:00:00",
    "end_time": "11:00:00",
    "total_price": "500.00",
    "status": "pending",
    "payment_status": "unpaid",
    "notes": "ملاحظات إضافية",
    "service": {...},
    "employee": {
      "user": {
        "name": "أحمد محمد"
      }
    },
    "timeSlot": {...},
    "serviceDuration": {...}
  }
}
```

---

## 📖 شرح مفصل لجميع Endpoints

### 🔐 Authentication Endpoints

#### 1. Send Verification Code
- **Method**: `POST`
- **URL**: `{{base_url}}/send-verification-code`
- **Headers**: `Content-Type: application/json`
- **Body**:
  ```json
  {
    "phone": "0501234567",
    "type": "registration" // أو "login"
  }
  ```
- **Response**: رسالة نجاح مع إرسال SMS

#### 2. Verify Registration Code (الخطوة 1)
- **Method**: `POST`
- **URL**: `{{base_url}}/verify-registration-code`
- **Headers**: `Content-Type: application/json`
- **Body**:
  ```json
  {
    "phone": "0501234567",
    "code": "123456"
  }
  ```
- **Response**: رمز مؤقت للخطوة التالية

#### 3. Complete Registration (الخطوة 2)
- **Method**: `POST`
- **URL**: `{{base_url}}/complete-registration`
- **Headers**: `Content-Type: application/json`
- **Body**:
  ```json
  {
    "phone": "0501234567",
    "name": "أحمد محمد",
    "verification_token": "token_from_step_1"
  }
  ```
- **Response**: بيانات المستخدم + Token

#### 4. Login
- **Method**: `POST`
- **URL**: `{{base_url}}/login`
- **Headers**: `Content-Type: application/json`
- **Body**:
  ```json
  {
    "phone": "0501234567",
    "code": "123456"
  }
  ```
- **Response**: بيانات المستخدم + Token

---

### 👤 Customer Profile Endpoints

#### 1. Get Profile
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/profile`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: بيانات الملف الشخصي الكاملة

#### 2. Update Profile
- **Method**: `PUT`
- **URL**: `{{base_url}}/customer/profile`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "name": "أحمد محمد علي",
    "email": "ahmed@example.com" // اختياري
  }
  ```
- **Response**: بيانات الملف المحدثة

#### 3. Update Avatar
- **Method**: `POST`
- **URL**: `{{base_url}}/customer/profile/avatar`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: multipart/form-data`
- **Body** (form-data):
  - `avatar`: ملف الصورة
- **Response**: رابط الصورة المحدثة

---

### 📊 Reports & Activity Log

#### 1. Get Reports
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/reports`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters** (اختياري):
  - `start_date`: تاريخ البداية
  - `end_date`: تاريخ النهاية
- **Response**: تقارير العميل (حجوزات، مبيعات، إلخ)

#### 2. Get Activity Log
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/activity-log`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters** (اختياري):
  - `page`: رقم الصفحة
  - `per_page`: عدد العناصر في الصفحة
- **Response**: سجل نشاط العميل

---

### 📚 Help Guides

#### 1. Get Help Guides
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/help-guide`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: قائمة أدلة المساعدة

#### 2. Get Help Guide by ID
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/help-guide/{id}`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: تفاصيل دليل مساعدة معين

---

### 📅 Consultation Bookings

#### 1. Get Available Consultation Dates
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/bookings/consultation/available-dates?consultation_id=1`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters**:
  - `consultation_id`: معرف الاستشارة (مطلوب)
- **Response**: التواريخ المتاحة للاستشارات

#### 2. Get Available Consultation Time Slots
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/bookings/consultation/available-time-slots?consultation_id=1&date=2025-12-26`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters**:
  - `consultation_id`: معرف الاستشارة (مطلوب)
  - `date`: التاريخ (مطلوب)
- **Response**: الأوقات المتاحة للاستشارة في تاريخ معين

#### 3. Create Consultation Booking
- **Method**: `POST`
- **URL**: `{{base_url}}/customer/bookings/consultation`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "consultation_id": 1,
    "booking_date": "2025-12-26",
    "start_time": "10:00",
    "notes": "ملاحظات إضافية (اختياري)"
  }
  ```
- **Response**: بيانات الحجز المنشأ

---

### ⭐ Ratings

#### 1. Add Rating
- **Method**: `POST`
- **URL**: `{{base_url}}/customer/ratings`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "booking_id": 1,
    "rating": 5, // من 1 إلى 5
    "comment": "تعليق على الخدمة (اختياري)"
  }
  ```
- **Response**: بيانات التقييم المنشأ

#### 2. Get My Ratings
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/ratings/my-ratings`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: قائمة تقييماتي

---

### 🧾 Invoices

#### 1. Get All Invoices
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/invoices`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters** (اختياري):
  - `status`: حالة الفاتورة
  - `page`: رقم الصفحة
- **Response**: قائمة الفواتير

#### 2. Get Invoice by Booking
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/invoices/{booking_id}`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: تفاصيل الفاتورة

#### 3. Download Invoice
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/invoices/{booking_id}/download`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: ملف PDF للفاتورة

---

### 💰 Points & Wallet

#### 1. Get Wallet
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/points/wallet`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: رصيد النقاط الحالي

#### 2. Purchase Points
- **Method**: `POST`
- **URL**: `{{base_url}}/customer/points/purchase`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "points_amount": 1000,
    "payment_method": "cash" // أو "online"
  }
  ```
- **Response**: بيانات عملية الشراء

#### 3. Get Points Transactions
- **Method**: `GET`
- **URL**: `{{base_url}}/customer/points/transactions`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters** (اختياري):
  - `type`: نوع المعاملة
  - `page`: رقم الصفحة
- **Response**: قائمة معاملات النقاط

---

### 👨‍💼 Employee Routes

#### 1. Get Employee Dashboard
- **Method**: `GET`
- **URL**: `{{base_url}}/employee/dashboard`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: بيانات Dashboard الموظف

#### 2. Get Employee Bookings
- **Method**: `GET`
- **URL**: `{{base_url}}/employee/bookings`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters** (اختياري):
  - `status`: حالة الحجز
  - `date`: التاريخ
- **Response**: قائمة حجوزات الموظف

---

### 🔔 Notifications

#### 1. Get All Notifications
- **Method**: `GET`
- **URL**: `{{base_url}}/notifications`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters** (اختياري):
  - `read`: true/false (فلترة المقروءة/غير المقروءة)
  - `page`: رقم الصفحة
- **Response**: قائمة الإشعارات

#### 2. Get Unread Count
- **Method**: `GET`
- **URL**: `{{base_url}}/notifications/unread-count`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: عدد الإشعارات غير المقروءة

#### 3. Mark Notification as Read
- **Method**: `POST`
- **URL**: `{{base_url}}/notifications/{notification_id}/read`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: رسالة نجاح

#### 4. Mark All as Read
- **Method**: `POST`
- **URL**: `{{base_url}}/notifications/mark-all-read`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: رسالة نجاح

#### 5. Delete Notification
- **Method**: `DELETE`
- **URL**: `{{base_url}}/notifications/{notification_id}`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: رسالة نجاح

---

### 🎫 Tickets (Support)

#### 1. Get All Tickets
- **Method**: `GET`
- **URL**: `{{base_url}}/tickets`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Parameters** (اختياري):
  - `status`: حالة التذكرة (open, closed, pending)
  - `page`: رقم الصفحة
- **Response**: قائمة التذاكر

#### 2. Create Ticket
- **Method**: `POST`
- **URL**: `{{base_url}}/tickets`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "subject": "عنوان التذكرة",
    "message": "نص الرسالة",
    "category": "technical" // أو "billing", "general"
  }
  ```
- **Response**: بيانات التذكرة المنشأة

#### 3. Get Ticket by ID
- **Method**: `GET`
- **URL**: `{{base_url}}/tickets/{ticket_id}`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: تفاصيل التذكرة مع الرسائل

#### 4. Add Message to Ticket
- **Method**: `POST`
- **URL**: `{{base_url}}/tickets/{ticket_id}/messages`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "message": "نص الرسالة"
  }
  ```
- **Response**: بيانات الرسالة المضافة

#### 5. Update Ticket Status
- **Method**: `PUT`
- **URL**: `{{base_url}}/tickets/{ticket_id}/status`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "status": "closed" // أو "open", "pending"
  }
  ```
- **Response**: بيانات التذكرة المحدثة

---

### 📦 Subscriptions

#### 1. Get All Subscriptions
- **Method**: `GET`
- **URL**: `{{base_url}}/subscriptions`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: قائمة جميع الاشتراكات المتاحة

#### 2. Get Active Subscriptions
- **Method**: `GET`
- **URL**: `{{base_url}}/subscriptions/active`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: قائمة الاشتراكات النشطة للعميل

#### 3. Get Subscription Requests
- **Method**: `GET`
- **URL**: `{{base_url}}/subscriptions/requests`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: قائمة طلبات الاشتراكات (pending, approved, rejected)

#### 4. Get Subscription by ID
- **Method**: `GET`
- **URL**: `{{base_url}}/subscriptions/{subscription_id}`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: تفاصيل اشتراك معين

#### 5. Create Subscription Request
- **Method**: `POST`
- **URL**: `{{base_url}}/subscriptions`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "subscription_id": 1,
    "payment_method": "cash" // أو "points", "online"
  }
  ```
- **Response**: بيانات طلب الاشتراك المنشأ

---

### 🛍️ Public Services API

#### 1. Get Categories
- **Method**: `GET`
- **URL**: `{{base_url}}/services/categories`
- **Headers**: لا يحتاج Token
- **Response**: قائمة الفئات

#### 2. Get Category by ID
- **Method**: `GET`
- **URL**: `{{base_url}}/services/categories/{category_id}`
- **Headers**: لا يحتاج Token
- **Response**: تفاصيل فئة معينة

#### 3. Get Sub-Categories
- **Method**: `GET`
- **URL**: `{{base_url}}/services/sub-categories`
- **Headers**: لا يحتاج Token
- **Query Parameters** (اختياري):
  - `category_id`: فلترة حسب الفئة
- **Response**: قائمة الفئات الفرعية

#### 4. Get Sub-Category by ID
- **Method**: `GET`
- **URL**: `{{base_url}}/services/sub-categories/{sub_category_id}`
- **Headers**: لا يحتاج Token
- **Response**: تفاصيل فئة فرعية معينة

#### 5. Get Services
- **Method**: `GET`
- **URL**: `{{base_url}}/services/services`
- **Headers**: لا يحتاج Token
- **Query Parameters** (اختياري):
  - `category_id`: فلترة حسب الفئة
  - `sub_category_id`: فلترة حسب الفئة الفرعية
  - `search`: البحث في الخدمات
- **Response**: قائمة الخدمات

#### 6. Get Service by ID
- **Method**: `GET`
- **URL**: `{{base_url}}/services/services/{service_id}`
- **Headers**: لا يحتاج Token
- **Response**: تفاصيل خدمة معينة

---

### 💬 Public Consultations API

#### 1. Get All Consultations
- **Method**: `GET`
- **URL**: `{{base_url}}/consultations`
- **Headers**: لا يحتاج Token
- **Query Parameters** (اختياري):
  - `category_id`: فلترة حسب الفئة
  - `search`: البحث في الاستشارات
- **Response**: قائمة الاستشارات

#### 2. Get Consultation by ID
- **Method**: `GET`
- **URL**: `{{base_url}}/consultations/{consultation_id}`
- **Headers**: لا يحتاج Token
- **Response**: تفاصيل استشارة معينة

#### 3. Get Consultations by Category
- **Method**: `GET`
- **URL**: `{{base_url}}/consultations/category/{category_id}`
- **Headers**: لا يحتاج Token
- **Response**: قائمة الاستشارات في فئة معينة

#### 4. Get Available Consultation Dates (Protected)
- **Method**: `GET`
- **URL**: `{{base_url}}/consultations/available-dates?consultation_id=1`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: التواريخ المتاحة للاستشارة

#### 5. Get Available Consultation Time Slots (Protected)
- **Method**: `GET`
- **URL**: `{{base_url}}/consultations/available-time-slots?consultation_id=1&date=2025-12-26`
- **Headers**: `Authorization: Bearer {{token}}`
- **Response**: الأوقات المتاحة للاستشارة في تاريخ معين

---

### ❓ FAQ API

#### 1. Get All FAQs
- **Method**: `GET`
- **URL**: `{{base_url}}/faqs`
- **Headers**: لا يحتاج Token
- **Response**: قائمة الأسئلة الشائعة

#### 2. Get FAQs by Category
- **Method**: `GET`
- **URL**: `{{base_url}}/faqs/category/{category}`
- **Headers**: لا يحتاج Token
- **Response**: قائمة الأسئلة في فئة معينة

---

### ⭐ Public Ratings API

#### 1. Get All Ratings
- **Method**: `GET`
- **URL**: `{{base_url}}/ratings`
- **Headers**: لا يحتاج Token
- **Query Parameters** (اختياري):
  - `service_id`: فلترة حسب الخدمة
  - `employee_id`: فلترة حسب الموظف
  - `rating`: فلترة حسب التقييم (1-5)
- **Response**: قائمة التقييمات

#### 2. Get Ratings Statistics
- **Method**: `GET`
- **URL**: `{{base_url}}/ratings/statistics`
- **Headers**: لا يحتاج Token
- **Query Parameters** (اختياري):
  - `service_id`: إحصائيات خدمة معينة
  - `employee_id`: إحصائيات موظف معين
- **Response**: إحصائيات التقييمات (متوسط، عدد، توزيع)

---

### 💳 Payment Endpoints

#### 1. Process Booking Payment
- **Method**: `POST`
- **URL**: `{{base_url}}/customer/bookings/payment`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "booking_id": 1,
    "payment_method": "cash", // أو "points", "online"
    "transaction_id": "TXN123456" // اختياري
  }
  ```
- **Response**: بيانات الدفع

#### 2. Initiate Online Payment
- **Method**: `POST`
- **URL**: `{{base_url}}/customer/bookings/initiate-online-payment`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Body**:
  ```json
  {
    "booking_id": 1
  }
  ```
- **Response**: رابط الدفع الإلكتروني (PayMob)

#### 3. Pay Online (Legacy - PayMob)
- **Method**: `POST`
- **URL**: `{{base_url}}/customer/bookings/{booking_id}/pay-online`
- **Headers**: 
  - `Authorization: Bearer {{token}}`
  - `Content-Type: application/json`
- **Response**: رابط الدفع الإلكتروني

#### 4. Payment Callback (Public)
- **Method**: `ANY`
- **URL**: `{{base_url}}/payment/callback`
- **Headers**: لا يحتاج Token
- **Description**: يتم استدعاؤه تلقائياً من بوابة الدفع (PayMob) بعد إتمام الدفع
- **Note**: لا يجب استدعاؤه يدوياً

---

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تأكد من أن السيرفر يعمل على `http://192.168.1.153:8000`
2. تأكد من تحديث `base_url` في Postman
3. تأكد من إضافة Header `Accept: application/json`
4. تأكد من صحة Token في الطلبات المحمية
5. تأكد من أن الخدمة تحتوي على `specialization_id` و `hourly_rate`
6. للطلبات التي تحتاج ملفات (مثل Avatar)، استخدم `multipart/form-data`
7. تأكد من صحة معرفات الـ IDs في الـ URLs

