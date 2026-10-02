<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create your account - Biodex</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --forest: #0f2f24;
            --leaf: #1f6b44;
            --leaf-dark: #17533a;
            --lime: #d6ec8d;
            --ink: #14291f;
            --muted: #5d6f64;
            --line: #d9e2d4;
            --paper: #f6f8f2;
            --field: #ffffff;
            --error: #b42318;
            --serif: 'Fraunces', Georgia, 'Times New Roman', serif;
            --sans: 'Instrument Sans', 'Avenir Next', 'Segoe UI', sans-serif;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(440px, 0.95fr);
            min-height: 100vh;
            background: var(--paper);
            color: var(--ink);
            font-family: var(--sans);
            -webkit-font-smoothing: antialiased;
        }

        :focus-visible { outline: 3px solid rgba(31, 107, 68, 0.45); outline-offset: 2px; }

        /* ===== STORY PANEL ===== */
        .register-story {
            position: relative;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
            padding: 44px 56px 52px;
            overflow: hidden;
            color: #fff;
            background: linear-gradient(160deg, #1a5a3c, var(--forest));
        }

        .register-story::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background:
                linear-gradient(180deg, rgba(10, 36, 27, 0.35) 0%, rgba(10, 36, 27, 0.55) 40%, rgba(10, 36, 27, 0.92) 100%);
        }

        .register-story-image {
            position: absolute;
            inset: 0;
            z-index: -2;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }



        .story-brand {
            display: inline-flex;
            width: fit-content;
            padding: 12px 18px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
        }

        .story-brand img { display: block; width: 168px; height: auto; }

        .story-copy { max-width: 560px; margin-top: auto; padding-top: 80px; }

        .story-copy h1 {
            font-family: var(--serif);
            font-size: clamp(42px, 4.6vw, 68px);
            font-weight: 400;
            line-height: 1.02;
            letter-spacing: -0.02em;
        }

        .story-description {
            max-width: 440px;
            margin-top: 22px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 16px;
            line-height: 1.7;
        }

        .story-points {
            display: grid;
            gap: 18px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.22);
            list-style: none;
        }

        .story-points li { display: flex; align-items: flex-start; gap: 14px; }

        .story-points svg { flex-shrink: 0; width: 22px; height: 22px; margin-top: 2px; color: var(--lime); }

        .story-points strong { display: block; font-size: 15px; font-weight: 600; }

        .story-points span { display: block; margin-top: 2px; color: rgba(255, 255, 255, 0.72); font-size: 13.5px; line-height: 1.5; }

        /* ===== FORM PANEL ===== */
        .register-container {
            align-self: center;
            justify-self: center;
            width: min(100% - 48px, 440px);
            margin: 40px 0;
        }

        .register-header h2 {
            font-family: var(--serif);
            font-size: 38px;
            font-weight: 500;
            line-height: 1.1;
            letter-spacing: -0.015em;
        }

        .register-header p { margin-top: 12px; color: var(--muted); font-size: 15px; line-height: 1.6; }

        form { display: flex; flex-direction: column; gap: 18px; margin-top: 30px; }

        label { display: block; margin-bottom: 7px; font-size: 13.5px; font-weight: 600; }

        .input-group { position: relative; }

        input {
            width: 100%;
            min-height: 50px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--field);
            color: var(--ink);
            font: inherit;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input::placeholder { color: #9aa99f; }

        input:hover { border-color: #bccab6; }

        input:focus {
            outline: none;
            border-color: var(--leaf);
            box-shadow: 0 0 0 4px rgba(31, 107, 68, 0.14);
        }

        .input-group input { padding-right: 64px; }

        input.is-invalid { border-color: var(--error); }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            padding: 8px 10px;
            border: none;
            border-radius: 6px;
            background: none;
            color: var(--leaf);
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .toggle-password:hover { background: #eef4ea; }

        .field-error { margin-top: 6px; color: var(--error); font-size: 12.5px; line-height: 1.4; }

        /* password strength + match hints */
        .strength { display: grid; grid-template-columns: repeat(4, 1fr); gap: 4px; margin-top: 10px; }

        .strength i { height: 4px; border-radius: 4px; background: var(--line); transition: background 0.25s; }

        .hint { min-height: 18px; margin-top: 6px; color: var(--muted); font-size: 12.5px; }

        .hint.ok { color: var(--leaf); }

        .hint.bad { color: var(--error); }

        /* ===== BUTTONS ===== */
        button[type="submit"] {
            min-height: 52px;
            margin-top: 6px;
            border: none;
            border-radius: 10px;
            background: var(--leaf);
            color: #fff;
            font: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        button[type="submit"]:hover {
            background: var(--leaf-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(23, 83, 58, 0.25);
        }

        button[type="submit"]:disabled { opacity: 0.7; cursor: wait; transform: none; box-shadow: none; }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 24px 0;
            color: var(--muted);
            font-size: 13px;
        }

        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--line); }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 50px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--ink);
            font-size: 14.5px;
            font-weight: 600;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .google-btn:hover { border-color: var(--leaf); background: #f4f8ef; }

        .google-icon { width: 18px; height: 18px; flex-shrink: 0; }

        .login-link { margin-top: 26px; color: var(--muted); font-size: 14px; text-align: center; }

        .login-link a { color: var(--leaf); font-weight: 600; text-decoration: none; }

        .login-link a:hover { text-decoration: underline; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 960px) {
            body { grid-template-columns: minmax(0, 0.85fr) minmax(400px, 1.15fr); }
            .register-story { padding: 36px; }
        }

        @media (max-width: 760px) {
            body { display: flex; flex-direction: column; }
            .register-story { min-height: 300px; padding: 24px 24px 30px; }
            .story-copy { padding-top: 40px; }
            .story-description { font-size: 14.5px; }
            .story-points { display: none; }
            .register-container { width: min(100% - 40px, 480px); margin: 32px auto 44px; }
            .register-header h2 { font-size: 32px; }
        }

        @media (min-width: 761px) and (max-height: 820px) {
            .register-container { margin: 20px 0; }
            form { gap: 13px; margin-top: 20px; }
            .register-header h2 { font-size: 32px; }
            .register-header p { margin-top: 8px; }
            input, .google-btn { min-height: 46px; }
            button[type="submit"] { min-height: 48px; }
            .divider { margin: 16px 0; }
            .login-link { margin-top: 18px; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; }
        }
    </style>
</head>

<body>
    <section class="register-story" aria-labelledby="register-story-title">
        <img class="register-story-image" src="{{ asset('images/EarthFront.png') }}" alt="" aria-hidden="true">

        <a class="story-brand" href="{{ route('home') }}" aria-label="Biodex home">
            <img src="{{ asset('/images/biodex-logo.png') }}" alt="Biodex">
        </a>

        <div class="story-copy">
            <h1 id="register-story-title">Give good things a second life.</h1>
            <p class="story-description">Find local ways to reuse, recycle and pass useful things on.</p>

            <ul class="story-points">
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div><strong>Find collection points</strong><span>See where to drop off glass, paper, electronics and more near you.</span></div>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 0 1 15.5-6.2L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/></svg>
                    <div><strong>Reuse before you throw away</strong><span>Give items to neighbours who can still use them.</span></div>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/></svg>
                    <div><strong>Join local initiatives</strong><span>Take part in clean-ups, workshops and community projects.</span></div>
                </li>
            </ul>
        </div>
    </section>

    <main class="register-container">
        <div class="register-header">
            <h2>Create your account</h2>
            <p>One account for collection points, reuse and local initiatives.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
            @csrf

            <div>
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" placeholder="John Doe" autocomplete="name"
                       value="{{ old('name') }}" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" placeholder="you@example.com" autocomplete="email"
                       value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password">Password</label>
                <div class="input-group">
                    <input id="password" type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                    <button type="button" class="toggle-password" aria-label="Show password" onclick="togglePassword('password', this)">Show</button>
                </div>
                <div class="strength" id="strength" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
                <p class="hint" id="strength-hint" aria-live="polite"></p>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation">Confirm password</label>
                <div class="input-group">
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat your password" autocomplete="new-password" required>
                    <button type="button" class="toggle-password" aria-label="Show password" onclick="togglePassword('password_confirmation', this)">Show</button>
                </div>
                <p class="hint" id="match-hint" aria-live="polite"></p>
            </div>

            <button type="submit" id="submit-btn">Create account</button>
        </form>

        <div class="divider"><span>or</span></div>

        <a href="{{ route('auth.google') }}" class="google-btn">
            <svg class="google-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
        </a>

        <p class="login-link">Already have an account? <a href="{{ route('login.form') }}">Sign in</a></p>
    </main>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Hide' : 'Show';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        }

        const pwd = document.getElementById('password');
        const confirmPwd = document.getElementById('password_confirmation');
        const bars = document.querySelectorAll('#strength i');
        const strengthHint = document.getElementById('strength-hint');
        const matchHint = document.getElementById('match-hint');
        const levels = [
            { label: 'Too short', color: '#b42318' },
            { label: 'Weak: add numbers or capitals', color: '#d9822b' },
            { label: 'Good', color: '#8fb339' },
            { label: 'Strong', color: '#1f6b44' }
        ];

        function scorePassword(v) {
            if (v.length < 8) return 0;
            let s = 0;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
            if (/\d/.test(v)) s++;
            if (/[^A-Za-z0-9]/.test(v) || v.length >= 12) s++;
            return s;
        }

        function updateStrength() {
            const v = pwd.value;
            if (!v) {
                bars.forEach(b => b.style.background = '');
                strengthHint.textContent = '';
                strengthHint.className = 'hint';
                return;
            }
            const s = scorePassword(v);
            const level = levels[s];
            bars.forEach((b, i) => b.style.background = i <= s ? level.color : '');
            strengthHint.textContent = level.label;
            strengthHint.className = 'hint ' + (s >= 2 ? 'ok' : 'bad');
        }

        function updateMatch() {
            if (!confirmPwd.value) { matchHint.textContent = ''; matchHint.className = 'hint'; return; }
            const same = pwd.value === confirmPwd.value;
            matchHint.textContent = same ? 'Passwords match' : 'Passwords do not match yet';
            matchHint.className = 'hint ' + (same ? 'ok' : 'bad');
        }

        pwd.addEventListener('input', () => { updateStrength(); updateMatch(); });
        confirmPwd.addEventListener('input', updateMatch);

        document.getElementById('register-form').addEventListener('submit', function (e) {
            if (pwd.value !== confirmPwd.value) {
                e.preventDefault();
                updateMatch();
                confirmPwd.focus();
                return;
            }
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Creating your account...';
        });
    </script>
</body>
</html>