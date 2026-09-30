<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Biodex</title>

    <style>
        /* === GLOBAL RESET === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* === CARD === */
        .login-container {
            background: #fff;
            padding: 3rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        /* === HEADER === */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: #2e7d32;
            font-size: 2rem;
            font-weight: 600;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }

        /* === FORM === */
        form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        label {
            font-weight: 500;
            color: #2e7d32;
        }

        .input-group {
            position: relative;
        }

        input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #c8e6c9;
            border-radius: 10px;
            transition: 0.3s;
            font-size: 0.95rem;
        }

        input:focus {
            border-color: #43a047;
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 160, 71, 0.2);
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: #2e7d32;
            transition: 0.3s;
        }

        .toggle-password:hover {
            color: #1b5e20;
        }

        button[type="submit"] {
            padding: 0.9rem;
            border: none;
            border-radius: 10px;
            background-color: #43a047;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #2e7d32;
            transform: scale(1.03);
        }

        /* === LINK === */
        .register-link {
            text-align: center;
            margin-top: 1rem;
        }

        .register-link a {
            text-decoration: none;
            color: #2e7d32;
            font-weight: 500;
            transition: 0.3s;
        }

        .register-link a:hover {
            text-decoration: underline;
            color: #1b5e20;
        }

        /* === GOOGLE BUTTON === */
        .google-login {
            margin-top: 1rem;
            text-align: center;
        }

        .google-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #db4437;
            border-radius: 10px;
            background-color: #fff;
            color: #db4437;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }

        .google-btn:hover {
            background-color: #db4437;
            color: white;
            transform: scale(1.03);
        }

        .google-icon {
            font-size: 1.2rem;
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .divider {
            margin: 1.5rem 0;
            text-align: center;
            position: relative;
            color: #666;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #c8e6c9;
        }

        .divider span {
            background-color: #fff;
            padding: 0 1rem;
        }

        /* === RESPONSIVE === */
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }

            .login-header h2 {
                font-size: 1.6rem;
            }
        }

        body {
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(420px, 0.96fr);
            align-items: stretch;
            justify-content: stretch;
            background: #f7f9f3;
            font-family: 'Instrument Sans', 'Avenir Next', sans-serif;
        }

        .login-story {
            position: relative;
            isolation: isolate;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 44px 52px 48px;
            color: #fff;
            background: #153d30;
        }

        .login-story::after {
            position: absolute;
            z-index: -1;
            inset: 0;
            background: linear-gradient(180deg, rgba(8, 33, 25, 0.3), rgba(8, 33, 25, 0.84));
            content: '';
        }

        .login-story-image {
            position: absolute;
            z-index: -2;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .login-brand {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-size: 19px;
            font-weight: 700;
            text-decoration: none;
        }

        .login-brand-mark {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            color: #17352a;
            background: #d6ec8d;
            font-family: Georgia, serif;
            font-size: 22px;
        }

        .login-story-copy {
            max-width: 600px;
            margin-top: auto;
            padding-top: 100px;
        }

        .login-story-eyebrow {
            margin: 0 0 20px;
            color: #d6ec8d;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .login-story h1 {
            max-width: 560px;
            margin: 0;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 64px;
            font-weight: 400;
            line-height: 0.98;
        }

        .login-story h1 em {
            color: #d6ec8d;
            font-style: italic;
        }

        .login-story-description {
            max-width: 430px;
            margin: 24px 0 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 16px;
            line-height: 1.7;
        }

        .login-story-note {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 42px;
            color: rgba(255, 255, 255, 0.84);
            font-size: 13px;
        }

        .login-story-note::before {
            width: 34px;
            height: 1px;
            background: #d6ec8d;
            content: '';
        }

        .login-container {
            align-self: center;
            justify-self: center;
            width: min(100% - 48px, 440px);
            max-width: 440px;
            margin: 32px auto;
            padding: 40px;
            border: 1px solid #e4eadd;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 64px rgba(27, 57, 39, 0.09);
            transition: none;
        }

        .login-container:hover {
            transform: none;
        }

        .login-header {
            margin-bottom: 25px;
            text-align: left;
        }

        .login-header h2 {
            color: #17352a;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 36px;
            font-weight: 400;
            line-height: 1.1;
        }

        .login-header p {
            margin-top: 10px;
            color: #64746b;
            font-size: 14px;
            line-height: 1.6;
        }

        form {
            gap: 17px;
        }

        label {
            margin-bottom: 7px;
            color: #17352a;
            font-size: 13px;
            font-weight: 600;
        }

        input {
            min-height: 49px;
            padding: 0.8rem 0.9rem;
            border: 1px solid #dce4d8;
            border-radius: 6px;
            color: #17352a;
            background: #fbfcf9;
        }

        input:focus {
            border-color: #226944;
            box-shadow: 0 0 0 3px rgba(34, 105, 68, 0.13);
        }

        .toggle-password {
            color: #226944;
            font-size: 0.75rem;
            font-weight: 700;
        }

        button[type="submit"] {
            min-height: 50px;
            margin-top: 4px;
            border-radius: 6px;
            background: #226944;
        }

        button[type="submit"]:hover {
            background: #194f35;
            transform: translateY(-1px);
        }

        .divider {
            margin: 20px 0;
            color: #64746b;
        }

        .divider::before {
            background: #e4eadd;
        }

        .google-btn {
            min-height: 48px;
            border: 1px solid #dce4d8;
            border-radius: 6px;
            color: #17352a;
            font-size: 13px;
            background: #fff;
        }

        .google-btn:hover {
            border-color: #226944;
            color: #17352a;
            background: #f4f8ef;
            transform: none;
        }

        .register-link {
            margin-top: 22px;
            color: #64746b;
            font-size: 13px;
        }

        .register-link a {
            color: #226944;
            font-weight: 700;
        }

        .status-message {
            margin-bottom: 18px;
            padding: 12px 14px;
            border: 1px solid #cfe1bc;
            border-radius: 6px;
            color: #1d5638;
            background: #f0f7e8;
            font-size: 13px;
            line-height: 1.5;
        }

        .form-error {
            margin-top: 5px;
            color: #b42318;
            font-size: 12px;
        }

        input.is-invalid {
            border-color: #b42318;
        }

        @media (max-width: 900px) {
            body {
                grid-template-columns: minmax(0, 0.9fr) minmax(400px, 1.1fr);
            }

            .login-story {
                padding: 36px;
            }

            .login-story h1 {
                font-size: 52px;
            }

            .login-container {
                padding: 32px;
            }
        }

        @media (max-width: 700px) {
            body {
                display: flex;
                flex-direction: column;
            }

            .login-story {
                min-height: 280px;
                padding: 24px 26px 28px;
            }

            .login-story-copy {
                padding-top: 42px;
            }

            .login-story h1 {
                font-size: 40px;
            }

            .login-story-description {
                margin-top: 13px;
                font-size: 14px;
            }

            .login-story-note {
                display: none;
            }

            .login-container {
                width: min(100% - 32px, 520px);
                margin: 22px auto 32px;
                padding: 28px 24px;
            }
        }

        @media (max-width: 380px) {
            .login-story h1 {
                font-size: 35px;
            }

            .login-container {
                padding: 24px 20px;
            }

            .login-header h2 {
                font-size: 31px;
            }
        }
    </style>
</head>

<body>
    <section class="login-story" aria-labelledby="login-story-title">
        <img class="login-story-image" src="{{ asset('images/EarthFront.png') }}" alt="" aria-hidden="true">
        <a class="login-brand" href="{{ route('home') }}">
            <span class="login-brand-mark" aria-hidden="true">B</span>
            <span>Biodex</span>
        </a>
        <div class="login-story-copy">
            <p class="login-story-eyebrow">Welcome back to Biodex</p>
            <h1>Keep good things <em>moving.</em></h1>
            <p class="login-story-description">Pick up where you left off and find your next local way to make a difference.</p>
            <p class="login-story-note">Reuse more. Waste less. Stay connected.</p>
        </div>
    </section>

    <div class="login-container">
        <div class="login-header">
            <h2>Sign in</h2>
            <p>Access your Biodex account.</p>
        </div>

        @if (session('status'))
            <div class="status-message" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="form-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div>
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" autocomplete="email" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password">Password</label>
                <div class="input-group">
                    <input id="password" type="password" name="password" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" aria-label="Show password" onclick="togglePassword('password', this)">Show</button>
                </div>
            </div>

            <button type="submit">Sign in</button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="google-login">
            <a href="{{ route('auth.google') }}" class="google-btn">
                <svg class="google-icon" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>
        </div>

        <div class="register-link">
            <p>Don’t have an account? <a href="{{ route('register.form') }}">Create one</a></p>
        </div>
    </div>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            btn.textContent = isPassword ? "Hide" : "Show";
            btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        }
    </script>
</body>
</html>
