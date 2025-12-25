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

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تأكد من أن السيرفر يعمل على `http://192.168.1.153:8000`
2. تأكد من تحديث `base_url` في Postman
3. تأكد من إضافة Header `Accept: application/json`
4. تأكد من صحة Token في الطلبات المحمية

