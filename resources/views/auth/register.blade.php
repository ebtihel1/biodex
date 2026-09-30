<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Biodex</title>

    <style>
        /* === RESET & GLOBAL === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f1f8e9, #e8f5e9);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* === CONTAINER === */
        .register-container {
            background: #fff;
            padding: 3rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 430px;
            transition: transform 0.3s ease;
        }

        .register-container:hover {
            transform: translateY(-5px);
        }

        /* === HEADER === */
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h2 {
            color: #2e7d32;
            font-size: 2rem;
            font-weight: 600;
        }

        .register-header p {
            color: #666;
            font-size: 0.9rem;
        }

        /* === FORM === */
        form {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        label {
            font-weight: 500;
            color: #2e7d32;
        }

        .input-group {
            position: relative;
        }

        input {
            padding: 0.9rem 1rem;
            border: 2px solid #c8e6c9;
            border-radius: 10px;
            transition: 0.3s;
            width: 100%;
        }

        input:focus {
            border-color: #43a047;
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 160, 71, 0.2);
        }

        /* === EYE BUTTON === */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            color: #43a047;
            transition: 0.3s;
        }

        .toggle-password:hover {
            color: #2e7d32;
        }

        /* === BUTTON === */
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
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            text-decoration: none;
            color: #2e7d32;
            font-weight: 500;
            transition: 0.3s;
        }

        .login-link a:hover {
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
            .register-container {
                padding: 2rem 1.5rem;
            }

            .register-header h2 {
                font-size: 1.6rem;
            }
        }

        body {
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(460px, 0.96fr);
            align-items: stretch;
            justify-content: stretch;
            background: #f7f9f3;
            font-family: 'Instrument Sans', 'Avenir Next', sans-serif;
        }

        .register-story {
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

        .register-story::after {
            position: absolute;
            z-index: -1;
            inset: 0;
            background: linear-gradient(180deg, rgba(8, 33, 25, 0.3), rgba(8, 33, 25, 0.84));
            content: '';
        }

        .register-story-image {
            position: absolute;
            z-index: -2;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .story-brand {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-size: 19px;
            font-weight: 700;
            text-decoration: none;
        }

        .story-brand-mark {
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

        .story-copy {
            max-width: 600px;
            margin-top: auto;
            padding-top: 100px;
        }

        .story-eyebrow {
            margin: 0 0 20px;
            color: #d6ec8d;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .story-copy h1 {
            max-width: 580px;
            margin: 0;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 66px;
            font-weight: 400;
            line-height: 0.98;
        }

        .story-copy h1 em {
            color: #d6ec8d;
            font-style: italic;
        }

        .story-description {
            max-width: 430px;
            margin: 24px 0 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 16px;
            line-height: 1.7;
        }

        .story-note {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 42px;
            color: rgba(255, 255, 255, 0.84);
            font-size: 13px;
        }

        .story-note::before {
            width: 34px;
            height: 1px;
            background: #d6ec8d;
            content: '';
        }

        .register-container {
            align-self: center;
            justify-self: center;
            width: min(100% - 48px, 470px);
            max-width: 470px;
            margin: 32px auto;
            padding: 40px;
            border: 1px solid #e4eadd;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 64px rgba(27, 57, 39, 0.09);
            transition: none;
        }

        .register-container:hover {
            transform: none;
        }

        .register-header {
            margin-bottom: 25px;
            text-align: left;
        }

        .register-header h2 {
            color: #17352a;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 36px;
            font-weight: 400;
            line-height: 1.1;
        }

        .register-header p {
            margin-top: 10px;
            color: #64746b;
            font-size: 14px;
            line-height: 1.6;
        }

        form {
            gap: 16px;
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

        .login-link {
            margin-top: 22px;
            color: #64746b;
            font-size: 13px;
        }

        .login-link a {
            color: #226944;
            font-weight: 700;
        }

        .field-error {
            margin-top: 5px;
            color: #b42318;
            font-size: 12px;
        }

        input.is-invalid {
            border-color: #b42318;
        }

        @media (max-width: 900px) {
            body {
                grid-template-columns: minmax(0, 0.9fr) minmax(420px, 1.1fr);
            }

            .register-story {
                padding: 36px;
            }

            .story-copy h1 {
                font-size: 52px;
            }

            .register-container {
                padding: 32px;
            }
        }

        @media (min-width: 701px) and (max-height: 800px) {
            .register-container {
                margin: 12px auto;
                padding: 24px 36px;
            }

            .register-header {
                margin-bottom: 16px;
            }

            .register-header h2 {
                font-size: 32px;
            }

            .register-header p {
                margin-top: 6px;
            }

            form {
                gap: 11px;
            }

            label {
                margin-bottom: 5px;
            }

            input,
            .google-btn {
                min-height: 44px;
            }

            button[type="submit"] {
                min-height: 46px;
            }

            .divider {
                margin: 12px 0;
            }

            .login-link {
                margin-top: 14px;
            }
        }

        @media (max-width: 700px) {
            body {
                display: flex;
                flex-direction: column;
            }

            .register-story {
                min-height: 290px;
                padding: 24px 26px 28px;
            }

            .story-copy {
                padding-top: 48px;
            }

            .story-copy h1 {
                font-size: 42px;
            }

            .story-description {
                margin-top: 13px;
                font-size: 14px;
            }

            .story-note {
                display: none;
            }

            .register-container {
                width: min(100% - 32px, 520px);
                margin: 22px auto 32px;
                padding: 28px 24px;
            }
        }

        @media (max-width: 380px) {
            .story-copy h1 {
                font-size: 36px;
            }

            .register-container {
                padding: 24px 20px;
            }

            .register-header h2 {
                font-size: 31px;
            }
        }
    </style>
</head>

<body>
    <section class="register-story" aria-labelledby="register-story-title">
        <img class="register-story-image" src="{{ asset('images/EarthFront.png') }}" alt="" aria-hidden="true">
        <a class="story-brand" href="{{ route('home') }}">
            <span class="story-brand-mark" aria-hidden="true">B</span>
            <span>Biodex</span>
        </a>
        <div class="story-copy">
            <p class="story-eyebrow">Small actions. Circular impact.</p>
            <h1 id="register-story-title">Give good things a <em>second life.</em></h1>
            <p class="story-description">Find local ways to reuse, recycle and pass useful things on.</p>
            <p class="story-note">A more thoughtful way to deal with waste starts here.</p>
        </div>
    </section>

    <div class="register-container">
        <div class="register-header">
            <h2>Create your account</h2>
            <p>One account to discover collection points, reuse and local initiatives.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" placeholder="John Doe" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password">Password</label>
                <div class="input-group">
                    <input id="password" type="password" name="password" placeholder="••••••••" class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                    <button type="button" class="toggle-password" aria-label="Show password" onclick="togglePassword('password', this)">Show</button>
                </div>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation">Confirm password</label>
                <div class="input-group">
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" aria-label="Show password" onclick="togglePassword('password_confirmation', this)">Show</button>
                </div>
            </div>

            <button type="submit">Create account</button>
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

        <div class="login-link">
            <p>Already have an account? <a href="{{ route('login.form') }}">Sign in</a></p>
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
