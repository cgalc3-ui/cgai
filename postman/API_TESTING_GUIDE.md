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

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/send-verification-code` | إرسال كود التحقق |
| POST | `/api/register` | تسجيل عميل جديد |
| POST | `/api/login` | تسجيل دخول عميل |

### 🔒 Protected Routes (تحتاج Token)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user` | بيانات المستخدم |
| POST | `/api/logout` | تسجيل الخروج |
| GET | `/api/customer/profile` | ملف العميل |
| PUT | `/api/customer/profile` | تحديث ملف العميل |
| GET | `/api/customer/dashboard` | Dashboard العميل |
| GET | `/api/customer/bookings/available-dates` | التواريخ المتاحة مع الأوقات كل ساعة |
| GET | `/api/customer/bookings` | جميع حجوزات العميل |
| POST | `/api/customer/bookings` | إنشاء حجز جديد |
| GET | `/api/customer/bookings/{id}` | تفاصيل حجز معين |
| PUT | `/api/customer/bookings/{id}` | تحديث حجز |
| POST | `/api/customer/bookings/{id}/cancel` | إلغاء حجز |
| POST | `/api/customer/bookings/{id}/payment` | دفع الحجز |

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

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تأكد من أن السيرفر يعمل على `http://192.168.1.153:8000`
2. تأكد من تحديث `base_url` في Postman
3. تأكد من إضافة Header `Accept: application/json`
4. تأكد من صحة Token في الطلبات المحمية
5. تأكد من أن الخدمة تحتوي على `specialization_id` و `hourly_rate`

