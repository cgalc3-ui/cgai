# 📁 هيكل Controllers في المشروع

## 🎯 نظرة عامة

تم تنظيم المشروع بحيث يكون هناك مجلدات منفصلة لـ **API Controllers** و **Web Controllers**.

---

## 📂 البنية الحالية

```
app/Http/Controllers/
├── Controller.php              # Base Controller الرئيسي
├── Api/                        # API Controllers
│   ├── Controller.php          # Base Controller للـ API
│   └── AuthController.php      # Authentication للـ API
└── Web/                        # Web Controllers
    ├── Controller.php          # Base Controller للـ Web
    └── AuthController.php      # Authentication للـ Web
```

---

## 🔵 API Controllers (`app/Http/Controllers/Api/`)

### الاستخدام
- **للاستخدام مع Mobile Apps** (iOS, Android)
- **للاستخدام مع Frontend Frameworks** (React, Vue, Angular)
- **للاستخدام مع Third-party Integrations**

### المميزات
- ✅ ترجع **JSON Responses** فقط
- ✅ تستخدم **Laravel Sanctum** للمصادقة
- ✅ لا تستخدم **Sessions**
- ✅ تعتمد على **Tokens** للمصادقة

### مثال: `Api/AuthController`

```php
namespace App\Http\Controllers\Api;

class AuthController extends Controller
{
    // ترجع JSON فقط
    public function register(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ]);
    }
}
```

### Routes: `routes/api.php`

```php
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
```

---

## 🟢 Web Controllers (`app/Http/Controllers/Web/`)

### الاستخدام
- **للاستخدام مع Blade Views** (Laravel Views)
- **للاستخدام مع Traditional Web Applications**
- **للاستخدام مع Server-side Rendering**

### المميزات
- ✅ ترجع **Views** أو **Redirects**
- ✅ تستخدم **Session-based Authentication**
- ✅ تستخدم **Laravel's Auth Facade**
- ✅ تعتمد على **Sessions** للمصادقة

### مثال: `Web/AuthController`

```php
namespace App\Http\Controllers\Web;

class AuthController extends Controller
{
    // ترجع View
    public function showRegisterForm()
    {
        return view('auth.register');
    }
    
    // ترجع Redirect
    public function register(Request $request)
    {
        auth()->login($user);
        return redirect('/')->with('success', 'تم التسجيل بنجاح');
    }
}
```

### Routes: `routes/web.php`

```php
use App\Http\Controllers\Web\AuthController;

Route::get('/auth/register', [AuthController::class, 'showRegisterForm']);
Route::post('/auth/register', [AuthController::class, 'register']);
```

---

## 📋 المسارات المتاحة

### API Routes (`/api/*`)

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| POST | `/api/send-verification-code` | `Api\AuthController` | إرسال كود التحقق |
| POST | `/api/register` | `Api\AuthController` | التسجيل |
| POST | `/api/login` | `Api\AuthController` | تسجيل الدخول |
| GET | `/api/user` | `Api\AuthController` | بيانات المستخدم (محمي) |
| POST | `/api/logout` | `Api\AuthController` | تسجيل الخروج (محمي) |

### Web Routes (`/*`)

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/auth/register` | `Web\AuthController` | صفحة التسجيل |
| GET | `/auth/login` | `Web\AuthController` | صفحة تسجيل الدخول |
| POST | `/auth/send-verification-code` | `Web\AuthController` | إرسال كود التحقق |
| POST | `/auth/register` | `Web\AuthController` | معالجة التسجيل |
| POST | `/auth/login` | `Web\AuthController` | معالجة تسجيل الدخول |
| POST | `/auth/logout` | `Web\AuthController` | تسجيل الخروج |

---

## 🔧 كيفية إضافة Controller جديد

### إضافة API Controller

```bash
php artisan make:controller Api/UserController
```

```php
<?php

namespace App\Http\Controllers\Api;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'users' => User::all()
        ]);
    }
}
```

### إضافة Web Controller

```bash
php artisan make:controller Web/DashboardController
```

```php
<?php

namespace App\Http\Controllers\Web;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
}
```

---

## 🔒 المصادقة

### API Authentication
- تستخدم **Laravel Sanctum Tokens**
- الـ Token يُرسل في Header: `Authorization: Bearer {token}`
- الـ Token يُحفظ في جدول `personal_access_tokens`

### Web Authentication
- تستخدم **Session-based Authentication**
- الـ Session يُحفظ تلقائياً في Cookies
- استخدام `auth()->login($user)` و `auth()->logout()`

---

## 📝 ملاحظات مهمة

1. **لا تخلط بين API و Web Controllers**
   - API Controllers ترجع JSON فقط
   - Web Controllers ترجع Views أو Redirects

2. **استخدم الـ Namespace الصحيح**
   - API: `App\Http\Controllers\Api\`
   - Web: `App\Http\Controllers\Web\`

3. **استخدم Base Controllers**
   - `Api\Controller` للـ API Controllers
   - `Web\Controller` للـ Web Controllers

4. **Routes منفصلة**
   - API Routes في `routes/api.php`
   - Web Routes في `routes/web.php`

---

## 🎯 أمثلة الاستخدام

### مثال API Request

```javascript
// JavaScript/Fetch
fetch('/api/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
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

### مثال Web Request

```html
<!-- Blade Template -->
<form action="{{ route('register.submit') }}" method="POST">
    @csrf
    <input type="text" name="name" required>
    <input type="text" name="phone" required>
    <input type="text" name="code" required>
    <button type="submit">تسجيل</button>
</form>
```

---

## ✅ الخلاصة

- ✅ **API Controllers** في `app/Http/Controllers/Api/`
- ✅ **Web Controllers** في `app/Http/Controllers/Web/`
- ✅ **API Routes** في `routes/api.php`
- ✅ **Web Routes** في `routes/web.php`
- ✅ **منفصلة تماماً** ولا تتداخل مع بعضها

