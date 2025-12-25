# 🔄 تغييرات نظام المصادقة في الويب

## 📋 التغييرات

### ✅ ما تم تغييره:

1. **إزالة التسجيل (Register)** من الويب
   - لا يمكن لأي شخص التسجيل من الويب
   - الأدمن فقط هو من يضيف الموظفين والأدمن الجدد

2. **تغيير تسجيل الدخول**
   - من: الهاتف + كود SMS
   - إلى: البريد الإلكتروني + كلمة المرور

---

## 🔐 نظام المصادقة الجديد

### تسجيل الدخول (Web)

#### Route
```
POST /auth/login
```

#### Request Body
```json
{
  "email": "admin@example.com",
  "password": "password123",
  "remember": true  // optional
}
```

#### Response
- **نجاح**: إعادة توجيه إلى Dashboard حسب نوع المستخدم
  - Admin → `/admin/dashboard`
  - Staff → `/staff/dashboard`
- **فشل**: إرجاع أخطاء التحقق

#### التحقق
- ✅ المستخدم يجب أن يكون `admin` أو `staff` فقط
- ✅ كلمة المرور يجب أن تكون صحيحة
- ✅ يتم إنشاء Session للمصادقة

---

## 👥 إضافة المستخدمين

### الأدمن فقط يمكنه إضافة مستخدمين

#### Route
```
POST /admin/users
```

#### Request Body
```json
{
  "name": "أحمد محمد",
  "email": "ahmed@example.com",
  "phone": "0501234567",
  "role": "staff",  // أو "admin"
  "password": "password123"
}
```

#### التحقق
- ✅ `role` يجب أن يكون `admin` أو `staff` فقط
- ✅ لا يمكن إضافة `customer` من هنا
- ✅ `email` و `phone` يجب أن يكونا فريدين

---

## 🛣️ المسارات المتاحة

### Authentication Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/auth/login` | `Web\AuthController` | صفحة تسجيل الدخول |
| POST | `/auth/login` | `Web\AuthController` | معالجة تسجيل الدخول |
| POST | `/auth/logout` | `Web\AuthController` | تسجيل الخروج |

### Admin Routes (يحتاج role:admin)

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/admin/users` | `Web\AdminController` | قائمة المستخدمين |
| GET | `/admin/users/create` | `Web\AdminController` | إنشاء مستخدم جديد |
| POST | `/admin/users` | `Web\AdminController` | حفظ مستخدم جديد |

---

## 📝 مثال الاستخدام

### 1. تسجيل الدخول (Web)

```html
<!-- resources/views/auth/login.blade.php -->
<form action="{{ route('login.submit') }}" method="POST">
    @csrf
    
    <div>
        <label>البريد الإلكتروني</label>
        <input type="email" name="email" required>
    </div>
    
    <div>
        <label>كلمة المرور</label>
        <input type="password" name="password" required>
    </div>
    
    <div>
        <label>
            <input type="checkbox" name="remember" value="1">
            تذكرني
        </label>
    </div>
    
    <button type="submit">تسجيل الدخول</button>
</form>
```

### 2. إضافة موظف جديد (Admin)

```html
<!-- resources/views/admin/users/create.blade.php -->
<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    
    <div>
        <label>الاسم</label>
        <input type="text" name="name" required>
    </div>
    
    <div>
        <label>البريد الإلكتروني</label>
        <input type="email" name="email" required>
    </div>
    
    <div>
        <label>رقم الهاتف</label>
        <input type="text" name="phone" required>
    </div>
    
    <div>
        <label>النوع</label>
        <select name="role" required>
            <option value="admin">أدمن</option>
            <option value="staff">موظف</option>
        </select>
    </div>
    
    <div>
        <label>كلمة المرور</label>
        <input type="password" name="password" required minlength="8">
    </div>
    
    <button type="submit">إنشاء مستخدم</button>
</form>
```

---

## 🔒 الأمان

### التحقق من الصلاحيات

1. **تسجيل الدخول**
   - فقط `admin` و `staff` يمكنهم تسجيل الدخول من الويب
   - `customer` لا يمكنه تسجيل الدخول من الويب (يستخدم API فقط)

2. **إضافة المستخدمين**
   - فقط `admin` يمكنه إضافة مستخدمين
   - يمكن إضافة `admin` أو `staff` فقط
   - لا يمكن إضافة `customer` من الويب

3. **Session Management**
   - استخدام Laravel Session للمصادقة
   - دعم "تذكرني" (Remember Me)
   - إعادة توليد Session بعد تسجيل الدخول

---

## 📊 مقارنة بين API و Web

| الميزة | API (Customer) | Web (Admin/Staff) |
|--------|---------------|-------------------|
| **المصادقة** | الهاتف + كود SMS | البريد + كلمة المرور |
| **التسجيل** | متاح | غير متاح (الأدمن فقط يضيف) |
| **الـ Token** | Sanctum Token | Session |
| **الأنواع** | customer فقط | admin, staff |
| **الاستخدام** | Mobile Apps | لوحة التحكم |

---

## ✅ الخلاصة

- ✅ **لا يوجد Register في الويب** - الأدمن فقط يضيف المستخدمين
- ✅ **تسجيل الدخول بالبريد وكلمة المرور** - بدلاً من الهاتف وكود SMS
- ✅ **فقط Admin و Staff** يمكنهم تسجيل الدخول من الويب
- ✅ **الأدمن فقط** يمكنه إضافة موظفين وأدمن جدد
- ✅ **Customer** يستخدم API فقط (الهاتف + كود SMS)

