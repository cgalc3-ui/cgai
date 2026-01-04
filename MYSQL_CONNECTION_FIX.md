# 🔧 حل مشكلة الاتصال بـ MySQL

## المشكلة
```
SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it
```

هذا الخطأ يعني أن **MySQL Server غير قيد التشغيل** أو لا يستمع على المنفذ المحدد.

---

## ✅ الحلول

### 1. بدء خدمة MySQL (Windows)

#### الطريقة الأولى: من Services
1. اضغط `Win + R`
2. اكتب `services.msc` واضغط Enter
3. ابحث عن `MySQL` أو `MySQL80` أو `MariaDB`
4. انقر بزر الماوس الأيمن واختر **Start**

#### الطريقة الثانية: من Command Prompt (كمسؤول)
```cmd
net start MySQL80
```
أو
```cmd
net start MySQL
```
أو
```cmd
net start MariaDB
```

#### الطريقة الثالثة: من PowerShell (كمسؤول)
```powershell
Start-Service MySQL80
```

---

### 2. التحقق من حالة MySQL

```cmd
sc query MySQL80
```

أو

```powershell
Get-Service MySQL80
```

---

### 3. إذا كان MySQL مثبت عبر XAMPP/WAMP

#### XAMPP:
1. افتح XAMPP Control Panel
2. اضغط **Start** بجانب MySQL

#### WAMP:
1. افتح WAMP Server
2. انقر على أيقونة WAMP في شريط المهام
3. اختر **MySQL** → **Service** → **Start/Resume Service**

---

### 4. إذا كان MySQL مثبت عبر Laragon

1. افتح Laragon
2. اضغط **Start All** أو **Start MySQL** فقط

---

### 5. التحقق من إعدادات الاتصال

تأكد من أن ملف `.env` يحتوي على الإعدادات الصحيحة:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cgai
DB_USERNAME=root
DB_PASSWORD=
```

**ملاحظة:** إذا كان MySQL يستخدم منفذ مختلف (مثل 3307)، قم بتحديث `DB_PORT`.

---

### 6. التحقق من أن قاعدة البيانات موجودة

بعد بدء MySQL، تأكد من أن قاعدة البيانات `cgai` موجودة:

```cmd
mysql -u root -e "SHOW DATABASES;"
```

إذا لم تكن موجودة، قم بإنشائها:

```cmd
mysql -u root -e "CREATE DATABASE IF NOT EXISTS cgai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

### 7. تشغيل Migrations

بعد التأكد من أن MySQL يعمل وقاعدة البيانات موجودة:

```cmd
php artisan migrate
```

---

### 8. اختبار الاتصال

```cmd
php artisan tinker
```

ثم في Tinker:
```php
DB::connection()->getPdo();
```

إذا نجح، سترى رسالة نجاح. إذا فشل، سترى رسالة الخطأ.

---

## 🔍 التحقق من المنفذ

إذا كان MySQL يعمل على منفذ مختلف:

```cmd
netstat -an | findstr :3306
```

إذا لم يظهر أي شيء، جرب:
```cmd
netstat -an | findstr :3307
```

ثم قم بتحديث `DB_PORT` في ملف `.env`.

---

## ⚠️ مشاكل شائعة أخرى

### المشكلة: "Access denied for user"
**الحل:** تحقق من اسم المستخدم وكلمة المرور في `.env`

### المشكلة: "Unknown database 'cgai'"
**الحل:** قم بإنشاء قاعدة البيانات:
```cmd
mysql -u root -e "CREATE DATABASE cgai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### المشكلة: MySQL يعمل لكن Laravel لا يتصل
**الحل:**
1. امسح cache الإعدادات:
   ```cmd
   php artisan config:clear
   php artisan cache:clear
   ```
2. أعد تحميل الإعدادات:
   ```cmd
   php artisan config:cache
   ```

---

## 📝 خطوات سريعة

1. ✅ بدء MySQL Service
2. ✅ التحقق من قاعدة البيانات `cgai`
3. ✅ التحقق من إعدادات `.env`
4. ✅ مسح cache: `php artisan config:clear`
5. ✅ اختبار الاتصال: `php artisan migrate:status`

---

**بعد حل المشكلة، يجب أن يعمل التطبيق بشكل طبيعي!**

