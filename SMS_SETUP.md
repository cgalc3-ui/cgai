# دليل إعداد نظام المصادقة عبر SMS

## 📋 نظرة عامة

تم إعداد نظام مصادقة كامل يعمل عبر رقم الهاتف ورسائل SMS باستخدام Laravel Sanctum وشركة فورجوالي (4jawaly).

## 🔧 الإعدادات المطلوبة

### 1. إضافة متغيرات البيئة في ملف `.env`

```env
# فورجوالي SMS Settings
FORJAWALY_API_KEY=your_api_key_here
FORJAWALY_API_SECRET=your_api_secret_here
FORJAWALY_SENDER=TechPack
FORJAWALY_URL=https://api-sms.4jawaly.com/api/v1/

# SMS General Settings
SMS_NOTIFICATIONS_ENABLED=true
SMS_PROVIDER=fourjawaly
FOURJAWALY_ENABLED=true

# Rate Limiting
SMS_RATE_LIMITING_ENABLED=true
SMS_MAX_PER_MINUTE=5
SMS_MAX_PER_HOUR=20
SMS_MAX_PER_DAY=100

# Retry Settings
SMS_MAX_ATTEMPTS=3
SMS_BACKOFF_SECONDS=60

# Logging Settings
SMS_LOGGING_ENABLED=true
SMS_LOG_RETENTION_DAYS=90
SMS_MASK_PHONE=true

# Verification Code Settings
VERIFICATION_CODE_LENGTH=6
VERIFICATION_EXPIRES_IN=10
VERIFICATION_MAX_ATTEMPTS=5
```

## 🚀 مسارات API

### 1. إرسال كود التحقق
```
POST /api/send-verification-code
```

**Body:**
```json
{
  "phone": "0501234567",
  "type": "registration" // أو "login" أو "password_reset"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إرسال كود التحقق بنجاح",
  "expires_in": 10
}
```

### 2. التسجيل (Register)
```
POST /api/register
```

**Body:**
```json
{
  "name": "أحمد محمد",
  "phone": "0501234567",
  "code": "123456"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم التسجيل بنجاح",
  "user": {
    "id": 1,
    "name": "أحمد محمد",
    "phone": "0501234567",
    "phone_verified_at": "2025-12-23T12:00:00.000000Z"
  },
  "token": "1|xxxxxxxxxxxxx"
}
```

### 3. تسجيل الدخول (Login)
```
POST /api/login
```

**Body:**
```json
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
    "phone": "0501234567"
  },
  "token": "1|xxxxxxxxxxxxx"
}
```

### 4. الحصول على بيانات المستخدم
```
GET /api/user
Headers: Authorization: Bearer {token}
```

### 5. تسجيل الخروج
```
POST /api/logout
Headers: Authorization: Bearer {token}
```

## 🧪 الاختبار

### اختبار إرسال SMS مباشرة:
```bash
php artisan sms:test 0501234567 "رسالة اختبار"
```

## 📊 المكونات المنشأة

1. **Models:**
   - `SmsLog` - تسجيل جميع الرسائل المرسلة
   - `VerificationCode` - إدارة أكواد التحقق

2. **Services:**
   - `FourJawalySmsService` - خدمة إرسال SMS

3. **Channels:**
   - `FourJawalySmsChannel` - قناة إشعارات SMS

4. **Controllers:**
   - `AuthController` - معالجة المصادقة والتسجيل

5. **Database Tables:**
   - `sms_logs` - سجل الرسائل
   - `verification_codes` - أكواد التحقق
   - `users` - تم إضافة حقل `phone` و `phone_verified_at`

## 🔒 الأمان

- Rate Limiting لمنع إساءة الاستخدام
- تسجيل جميع الرسائل المرسلة
- أكواد التحقق تنتهي صلاحيتها بعد 10 دقائق
- حد أقصى 5 محاولات للتحقق من الكود

## 📝 ملاحظات

- رقم الهاتف يجب أن يكون بصيغة سعودية (05XXXXXXXX أو 5XXXXXXXX)
- كود التحقق مكون من 6 أرقام
- الكود صالح لمدة 10 دقائق
- يمكن إعادة إرسال الكود بعد انتهاء الصلاحية

