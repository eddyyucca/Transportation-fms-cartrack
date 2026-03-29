<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Fleet Monitoring</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #172554 50%, #1d4ed8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
        }
        .brand { text-align: center; margin-bottom: 2rem; }
        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #1746a2, #2563eb);
            border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: .75rem;
        }
        .brand-icon i { color: #fff; font-size: 28px; }
        .brand h1 { font-size: 1.4rem; font-weight: 800; color: #0f172a; }
        .brand p { font-size: .875rem; color: #64748b; margin-top: .25rem; }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: .875rem; font-weight: 600; color: #374151; margin-bottom: .4rem; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: .75rem 1rem .75rem 2.75rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: .95rem;
            outline: none;
            transition: border-color .2s;
        }
        input:focus { border-color: #2563eb; }
        .check-row { display: flex; align-items: center; gap: .5rem; margin-bottom: 1.5rem; font-size: .875rem; color: #64748b; }
        .btn-login {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #1746a2, #2563eb);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .3px;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; }
        .alert-error {
            background: #fee2e2; color: #b91c1c;
            border-radius: 10px; padding: .75rem 1rem;
            font-size: .875rem; margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-truck-moving"></i></div>
            <h1>Fleet Monitoring</h1>
            <p>PT Sulawesi Cahaya Mineral — SCM</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@scm.co.id" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>
            <div class="check-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin:0;font-weight:400">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>
    </div>
</body>
</html>
