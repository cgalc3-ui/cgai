<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدخال كود التحقق - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Toast CSS -->
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .verify-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .verify-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .verify-header p {
            color: #666;
            font-size: 14px;
        }

        .phone-display {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 25px;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .code-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 24px;
            font-family: 'Cairo', sans-serif;
            text-align: center;
            letter-spacing: 10px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .code-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group .remember {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group .remember input {
            width: auto;
        }

        .btn-verify {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-verify:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .alert li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-header">
            <h1>إدخال كود التحقق</h1>
            <p>تم إرسال كود التحقق إلى رقم الهاتف</p>
        </div>

        <div class="phone-display">
            <i class="fas fa-mobile-alt"></i> {{ $phone }}
        </div>


        <form action="{{ route('verify.code.submit') }}" method="POST" id="verifyForm">
            @csrf
            
            <div class="form-group">
                <label for="code">كود التحقق (6 أرقام)</label>
                <input type="text" 
                       id="code" 
                       name="code" 
                       class="code-input" 
                       maxlength="6" 
                       pattern="[0-9]{6}" 
                       required 
                       autofocus
                       placeholder="123456">
            </div>

            <div class="form-group">
                <label class="remember">
                    <input type="checkbox" name="remember" value="1">
                    <span>تذكرني</span>
                </label>
            </div>

            <button type="submit" class="btn-verify" id="submitBtn">
                <i class="fas fa-check"></i> تأكيد
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-right"></i> العودة لتسجيل الدخول
            </a>
        </div>
    </div>

    <script>
        // Auto format code input
        const codeInput = document.getElementById('code');
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6) {
                document.getElementById('verifyForm').submit();
            }
        });

        // Focus on input
        codeInput.focus();

        // Disable submit button on submit
        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحقق...';
        });
    </script>
    <script src="{{ asset('js/toast.js') }}"></script>
    <script>
        // Show toast notifications from session
        @if(session('success'))
            Toast.success('{{ session('success') }}');
        @endif

        @if(session('error'))
            Toast.error('{{ session('error') }}');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                Toast.error('{{ $error }}');
            @endforeach
        @endif
    </script>
</body>
</html>

