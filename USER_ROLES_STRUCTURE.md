# 👥 هيكل أنواع المستخدمين في المشروع

## 🎯 نظرة عامة

المشروع يدعم **3 أنواع من المستخدمين**:

1. **عميل (Customer)** - خاص بـ API فقط
2. **أدمن (Admin)** - خاص بـ Web
3. **موظف (Staff)** - خاص بـ Web

---

## 📊 أنواع المستخدمين

### 🔵 عميل (Customer)
- **الاستخدام**: API فقط
- **الوصول**: Mobile Apps, Frontend Frameworks
- **المصادقة**: Laravel Sanctum (Tokens)
- **الصلاحيات**: 
  - عرض ملفه الشخصي
  - تحديث ملفه الشخصي
  - الوصول إلى Dashboard الخاص به

### 🟢 أدمن (Admin)
- **الاستخدام**: Web فقط
- **الوصول**: لوحة تحكم إدارية
- **المصادقة**: Session-based Authentication
- **الصلاحيات**:
  - إدارة جميع المستخدمين (Admin, Staff, Customer)
  - إنشاء مستخدمين جدد (Admin, Staff)
  - تعديل وحذف المستخدمين
  - الوصول إلى Dashboard الإداري

### 🟡 موظف (Staff)
- **الاستخدام**: Web فقط
- **الوصول**: لوحة تحكم الموظفين
- **المصادقة**: Session-based Authentication
- **الصلاحيات**:
  - عرض العملاء
  - عرض تفاصيل العملاء
  - الوصول إلى Dashboard الموظفين

---

## 📂 البنية

### Controllers

```
app/Http/Controllers/
├── Api/
│   ├── AuthController.php      # مصادقة العملاء (API)
│   └── CustomerController.php # إدارة العميل (API)
└── Web/
    ├── AuthController.php      # مصادقة الأدمن والموظفين (Web)
    ├── AdminController.php     # إدارة الأدمن (Web)
    └── StaffController.php     # إدارة الموظفين (Web)
```

### Models

```php
// app/Models/User.php
const ROLE_CUSTOMER = 'customer';
const ROLE_ADMIN = 'admin';
const ROLE_STAFF = 'staff';
```

### Middleware

```php
// app/Http/Middleware/CheckUserRole.php
// للتحقق من نوع المستخدم
```

---

## 🔐 المصادقة

### API (Customer)

#### التسجيل
```
POST /api/register
Body: {
  "name": "أحمد",
  "phone": "0501234567",
  "code": "123456"
}
```
→ ينشئ مستخدم بـ `role: "customer"`

#### تسجيل الدخول
```
POST /api/login
Body: {
  "phone": "0501234567",
  "code": "123456"
}
```
→ يعطي Token للمصادقة

#### استخدام Token
```
GET /api/user
Headers: Authorization: Bearer {token}
```

### Web (Admin/Staff)

#### تسجيل الدخول
```
POST /auth/login
Body: {
  "phone": "0501234567",
  "code": "123456"
}
```
→ ينشئ Session للمصادقة

---

## 🛣️ المسارات المتاحة

### API Routes (`/api/*`)

#### Public Routes
- `POST /api/send-verification-code` - إرسال كود التحقق
- `POST /api/register` - تسجيل عميل جديد
- `POST /api/login` - تسجيل دخول عميل

#### Protected Routes (Customer Only)
- `GET /api/user` - بيانات المستخدم
- `POST /api/logout` - تسجيل الخروج
- `GET /api/customer/profile` - ملف العميل الشخصي
- `PUT /api/customer/profile` - تحديث ملف العميل
- `GET /api/customer/dashboard` - Dashboard العميل

### Web Routes (`/*`)

#### Authentication Routes
- `GET /auth/register` - صفحة التسجيل
- `GET /auth/login` - صفحة تسجيل الدخول
- `POST /auth/send-verification-code` - إرسال كود التحقق
- `POST /auth/register` - معالجة التسجيل
- `POST /auth/login` - معالجة تسجيل الدخول
- `POST /auth/logout` - تسجيل الخروج

#### Admin Routes (`/admin/*`) - Requires Admin Role
- `GET /admin/dashboard` - Dashboard الأدمن
- `GET /admin/users` - قائمة المستخدمين
- `GET /admin/users/create` - إنشاء مستخدم جديد
- `POST /admin/users` - حفظ مستخدم جديد
- `GET /admin/users/{user}/edit` - تعديل مستخدم
- `PUT /admin/users/{user}` - تحديث مستخدم
- `DELETE /admin/users/{user}` - حذف مستخدم

#### Staff Routes (`/staff/*`) - Requires Staff or Admin Role
- `GET /staff/dashboard` - Dashboard الموظفين
- `GET /staff/customers` - قائمة العملاء
- `GET /staff/customers/{customer}` - تفاصيل عميل

---

## 🔒 Middleware

### CheckUserRole Middleware

```php
// في routes/web.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Routes للأدمن فقط
});

Route::middleware(['auth', 'role:staff,admin'])->group(function () {
    // Routes للموظفين والأدمن
});
```

### الاستخدام

```php
// للأدمن فقط
Route::middleware('role:admin')->group(function () {
    // ...
});

// للموظفين والأدمن
Route::middleware('role:staff,admin')->group(function () {
    // ...
});

// للعملاء فقط (في API)
// يتم التحقق في Controller مباشرة
```

---

## 💡 أمثلة الاستخدام

### مثال 1: تسجيل عميل جديد (API)

```javascript
// 1. إرسال كود التحقق
fetch('/api/send-verification-code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        phone: '0501234567',
        type: 'registration'
    })
});

// 2. التسجيل
fetch('/api/register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        name: 'أحمد',
        phone: '0501234567',
        code: '123456'
    })
})
.then(res => res.json())
.then(data => {
    // حفظ Token
    localStorage.setItem('token', data.token);
});
```

### مثال 2: تسجيل دخول أدمن (Web)

```html
<!-- Blade Template -->
<form action="{{ route('login.submit') }}" method="POST">
    @csrf
    <input type="text" name="phone" required>
    <input type="text" name="code" required>
    <button type="submit">تسجيل الدخول</button>
</form>
```

### مثال 3: الوصول إلى Dashboard الأدمن

```php
// في Controller
public function dashboard()
{
    // Middleware يتحقق تلقائياً من أن المستخدم admin
    return view('admin.dashboard');
}
```

---

## 🔧 Helper Methods في User Model

```php
$user = User::find(1);

// التحقق من نوع المستخدم
$user->isCustomer();      // true/false
$user->isAdmin();         // true/false
$user->isStaff();         // true/false
$user->isAdminOrStaff();  // true/false
```

---

## 📝 ملاحظات مهمة

1. **API Registration** → دائماً ينشئ `customer`
2. **Web Registration** → ينشئ `admin` (يمكن تغييره)
3. **Admin** يمكنه إدارة جميع المستخدمين
4. **Staff** يمكنه فقط عرض العملاء
5. **Customer** يمكنه فقط إدارة ملفه الشخصي

---

## ✅ الخلاصة

- ✅ **3 أنواع مستخدمين**: Customer, Admin, Staff
- ✅ **API للعملاء**: Controllers في `Api/`
- ✅ **Web للأدمن والموظفين**: Controllers في `Web/`
- ✅ **Middleware للتحقق**: `CheckUserRole`
- ✅ **Routes منفصلة**: لكل نوع مسارات خاصة

