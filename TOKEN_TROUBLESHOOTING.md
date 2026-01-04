# 🔧 حل مشكلة "Unauthenticated" في API

## المشكلة
عند محاولة الوصول إلى `/api/reports/statistics` تحصل على:
```json
{
    "message": "Unauthenticated."
}
```

## السبب
الـ Token الذي تستخدمه غير موجود في قاعدة البيانات أو منتهي الصلاحية.

---

## ✅ الحل

### 1. الحصول على Token جديد

#### الخطوة 1: إرسال كود التحقق
```http
POST http://127.0.0.1:8000/api/send-verification-code
Content-Type: application/json
Accept: application/json

{
    "phone": "0501234567"
}
```

**Response:**
```json
{
    "success": true,
    "message": "تم إرسال كود التحقق بنجاح"
}
```

#### الخطوة 2: تسجيل الدخول
```http
POST http://127.0.0.1:8000/api/login
Content-Type: application/json
Accept: application/json

{
    "phone": "0501234567",
    "code": "123456"
}
```

**Response:**
```json
{
    "success": true,
    "message": "تم تسجيل الدخول بنجاح",
    "user": {
        "id": 1,
        "name": "أحمد محمد",
        "email": "ahmed@example.com",
        "phone": "0501234567",
        "role": "customer"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

#### الخطوة 3: استخدام الـ Token الجديد
```http
GET http://127.0.0.1:8000/api/reports/statistics
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Accept: application/json
```

---

## 🔍 التحقق من الـ Token

### طريقة 1: استخدام `/api/user`
```http
GET http://127.0.0.1:8000/api/user
Authorization: Bearer YOUR_TOKEN_HERE
Accept: application/json
```

إذا كان الـ Token صحيح، ستحصل على:
```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "أحمد محمد",
        ...
    }
}
```

إذا كان الـ Token غير صحيح، ستحصل على:
```json
{
    "message": "Unauthenticated."
}
```

---

## 📝 ملاحظات مهمة

### 1. تنسيق الـ Token
- ✅ صحيح: `Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
- ❌ خطأ: `Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx ` (مسافة إضافية)
- ❌ خطأ: `1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` (بدون Bearer)

### 2. الـ Token ينتهي عند:
- تسجيل الخروج (`POST /api/logout`)
- حذف الـ Token يدوياً من قاعدة البيانات
- انتهاء صلاحية الـ Token (إذا كان محدود بوقت)

### 3. الـ Token صالح لـ:
- جميع الـ endpoints المحمية بـ `auth:sanctum`
- حتى يتم تسجيل الخروج أو حذف الـ Token

---

## 🧪 اختبار سريع

### باستخدام cURL:
```bash
# 1. تسجيل الدخول
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"phone":"0501234567","code":"123456"}'

# 2. استخدام الـ Token (استبدل YOUR_TOKEN بالـ token الذي حصلت عليه)
curl -X GET http://127.0.0.1:8000/api/reports/statistics \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### باستخدام Postman:
1. **Login Request:**
   - Method: `POST`
   - URL: `http://127.0.0.1:8000/api/login`
   - Headers:
     - `Content-Type: application/json`
     - `Accept: application/json`
   - Body (raw JSON):
     ```json
     {
         "phone": "0501234567",
         "code": "123456"
     }
     ```
   - احفظ الـ `token` من الـ Response

2. **Reports Request:**
   - Method: `GET`
   - URL: `http://127.0.0.1:8000/api/reports/statistics`
   - Headers:
     - `Authorization: Bearer YOUR_TOKEN_HERE`
     - `Accept: application/json`

---

## 🚨 أخطاء شائعة

### 1. "Unauthenticated"
**السبب:** الـ Token غير صحيح أو منتهي الصلاحية
**الحل:** احصل على token جديد من `/api/login`

### 2. "هذا الـ endpoint متاح للمستخدمين فقط"
**السبب:** المستخدم ليس `customer`
**الحل:** تأكد من أن `role` في قاعدة البيانات هو `customer`

### 3. Token موجود لكن لا يعمل
**السبب:** قد يكون هناك مشكلة في الـ middleware
**الحل:** تحقق من أن الـ route محمي بـ `auth:sanctum`

---

## 📞 إذا استمرت المشكلة

1. تحقق من أن Laravel Sanctum مثبت بشكل صحيح
2. تحقق من أن الـ token موجود في جدول `personal_access_tokens`
3. تحقق من أن المستخدم موجود في جدول `users`
4. تحقق من أن `role` المستخدم هو `customer`

---

**تم إنشاء هذا الدليل بتاريخ:** 2025-01-27

