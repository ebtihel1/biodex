@extends('front.global')

@section('title', 'Modifier le mot de passe')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fb 0%, #e4edf5 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        padding: 20px;
    }
    
    .password-container {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 40px 30px;
        max-width: 520px;
        width: 100%;
        margin: 60px auto;
        transition: all 0.3s ease-in-out;
    }

    .password-container:hover {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
    }

    .password-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header-icon {
        background: #198754;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .header-icon i {
        font-size: 2rem;
        color: white;
    }

    .password-header h2 {
        color: #198754;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .password-header p {
        color: #666;
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.7rem;
        display: flex;
        align-items: center;
    }

    .form-label i {
        margin-right: 8px;
        color: #198754;
        width: 16px;
    }

    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-control {
        border: 2px solid #e1f3e4;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fdf9;
    }

    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
        background: white;
    }

    .form-control.is-valid {
        border-color: #198754;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        z-index: 5;
        transition: all 0.3s ease;
        padding: 8px;
        border-radius: 50%;
    }

    .password-toggle:hover {
        color: #198754;
        background: rgba(25, 135, 84, 0.05);
    }

    .password-strength {
        height: 6px;
        margin-top: 8px;
        border-radius: 3px;
        background-color: #e9ecef;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .password-requirements {
        margin-top: 1.5rem;
        padding: 1.5rem;
        background-color: #f8fdf9;
        border-radius: 10px;
        font-size: 0.9rem;
        border: 1px solid #e1f3e4;
    }

    .password-requirements h6 {
        color: #333;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .password-requirements h6 i {
        margin-right: 8px;
        color: #198754;
    }

    .requirement {
        display: flex;
        align-items: center;
        margin-bottom: 0.7rem;
        transition: all 0.3s ease;
    }

    .requirement i {
        margin-right: 0.7rem;
        width: 16px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .requirement.valid {
        color: #198754;
    }

    .requirement.invalid {
        color: #6c757d;
    }

    .btn-update {
        background: #198754;
        border: none;
        padding: 12px 2rem;
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s ease;
        font-size: 1rem;
        width: 100%;
        margin-top: 1.5rem;
        color: white;
    }

    .btn-update:hover {
        background: #157347;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
    }

    .password-feedback {
        display: none;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        align-items: center;
        animation: fadeIn 0.5s ease;
    }

    .password-feedback.success {
        background-color: rgba(25, 135, 84, 0.1);
        border: 1px solid #198754;
        color: #198754;
    }

    .password-feedback.error {
        background-color: rgba(220, 53, 69, 0.1);
        border: 1px solid #dc3545;
        color: #dc3545;
    }

    .match-indicator {
        margin-top: 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        font-weight: 500;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }

    .shake {
        animation: shake 0.5s ease;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .pulse {
        animation: pulse 0.5s ease;
    }

    @media (max-width: 576px) {
        .password-container {
            padding: 30px 20px;
            margin: 30px auto;
        }
        
        .password-header h2 {
            font-size: 1.5rem;
        }
        
        .header-icon {
            width: 70px;
            height: 70px;
        }
        
        .header-icon i {
            font-size: 1.7rem;
        }
    }
</style>

<div class="password-container">
    <div class="password-header">
        <div class="header-icon">
            <i class="bi bi-lock-fill"></i>
        </div>
        <h2>Modifier le mot de passe</h2>
        <p>Sécurisez votre compte avec un nouveau mot de passe</p>
    </div>
    
    <div class="password-body">
        {{-- Display session-based errors/success if any --}}
        @if (session('success'))
            <div class="password-feedback success" style="display: flex;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span class="feedback-message">{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="password-feedback error" style="display: flex;">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <span class="feedback-message">{{ $errors->first() }}</span>
            </div>
        @endif
        
        <div class="password-feedback success" id="successFeedback">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span class="feedback-message">Votre mot de passe a été modifié avec succès.</span>
        </div>
        
        <div class="password-feedback error" id="errorFeedback">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <span class="feedback-message">Une erreur s'est produite. Veuillez vérifier vos informations.</span>
        </div>
        
        <form id="passwordForm" action="{{ route('settings.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="current_password" class="form-label">
                    <i class="bi bi-key-fill"></i>
                    Mot de passe actuel
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" name="current_password" id="current_password" required placeholder="Saisissez votre mot de passe actuel">
                    <button type="button" class="password-toggle" data-target="current_password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label for="new_password" class="form-label">
                    <i class="bi bi-lock-fill"></i>
                    Nouveau mot de passe
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" name="new_password" id="new_password" required placeholder="Créez un nouveau mot de passe sécurisé">
                    <button type="button" class="password-toggle" data-target="new_password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <small class="text-muted mt-2 d-block" id="passwordStrengthText">Force du mot de passe</small>
            </div>

            <div class="mb-4">
                <label for="new_password_confirmation" class="form-label">
                    <i class="bi bi-lock-fill"></i>
                    Confirmer le nouveau mot de passe
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation" required placeholder="Confirmez votre nouveau mot de passe">
                    <button type="button" class="password-toggle" data-target="new_password_confirmation">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="match-indicator" id="passwordMatchIndicator"></div>
            </div>
            
            <div class="password-requirements">
                <h6><i class="bi bi-list-check"></i>Votre mot de passe doit contenir :</h6>
                <div class="requirement invalid" id="reqLength">
                    <i class="bi bi-circle"></i>
                    <span>Au moins 8 caractères</span>
                </div>
                <div class="requirement invalid" id="reqUppercase">
                    <i class="bi bi-circle"></i>
                    <span>Une lettre majuscule</span>
                </div>
                <div class="requirement invalid" id="reqLowercase">
                    <i class="bi bi-circle"></i>
                    <span>Une lettre minuscule</span>
                </div>
                <div class="requirement invalid" id="reqNumber">
                    <i class="bi bi-circle"></i>
                    <span>Un chiffre</span>
                </div>
                <div class="requirement invalid" id="reqSpecial">
                    <i class="bi bi-circle"></i>
                    <span>Un caractère spécial</span>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-update">
                    <i class="bi bi-save me-2"></i>Enregistrer le nouveau mot de passe
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.password-toggle');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('new_password_confirmation');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        const matchIndicator = document.getElementById('passwordMatchIndicator');
        const form = document.getElementById('passwordForm');
        const successFeedback = document.getElementById('successFeedback');
        const errorFeedback = document.getElementById('errorFeedback');

        // Hide feedback messages initially
        successFeedback.style.display = 'none';
        errorFeedback.style.display = 'none';

        // Password visibility toggle
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });

        // Real-time validation
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            checkPasswordStrength(password);
            checkPasswordMatch();
            checkPasswordRequirements(password);
        });

        confirmPasswordInput.addEventListener('input', checkPasswordMatch);

        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = '';
            let color = '';

            if (password.length >= 8) strength += 20;
            if (password.length >= 12) strength += 10;
            if (/[a-z]/.test(password)) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;

            if (strength === 0) {
                feedback = 'Très faible';
                color = '#dc3545';
            } else if (strength <= 40) {
                feedback = 'Faible';
                color = '#ef476f';
            } else if (strength <= 70) {
                feedback = 'Moyen';
                color = '#ffc107';
            } else if (strength <= 90) {
                feedback = 'Fort';
                color = '#198754';
            } else {
                feedback = 'Très fort';
                color = '#0d6e3f';
            }

            strengthBar.style.width = strength + '%';
            strengthBar.style.backgroundColor = color;
            strengthText.textContent = 'Force du mot de passe: ' + feedback;
            strengthText.style.color = color;
        }

        function checkPasswordMatch() {
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword === '') {
                matchIndicator.innerHTML = '';
                confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                return;
            }

            if (password === confirmPassword) {
                matchIndicator.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Les mots de passe correspondent';
                matchIndicator.className = 'match-indicator text-success';
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
            } else {
                matchIndicator.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Les mots de passe ne correspondent pas';
                matchIndicator.className = 'match-indicator text-danger';
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.add('is-invalid');
            }
        }

        function checkPasswordRequirements(password) {
            const requirements = [
                { id: 'reqLength', test: password.length >= 8 },
                { id: 'reqUppercase', test: /[A-Z]/.test(password) },
                { id: 'reqLowercase', test: /[a-z]/.test(password) },
                { id: 'reqNumber', test: /[0-9]/.test(password) },
                { id: 'reqSpecial', test: /[^A-Za-z0-9]/.test(password) }
            ];

            requirements.forEach(req => {
                const element = document.getElementById(req.id);
                if (req.test) {
                    element.classList.remove('invalid');
                    element.classList.add('valid');
                    element.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>' + element.textContent + '</span>';
                } else {
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                    element.innerHTML = '<i class="bi bi-circle"></i><span>' + element.textContent + '</span>';
                }
            });
        }

        // Form submission with AJAX
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Hide previous feedback
            successFeedback.style.display = 'none';
            errorFeedback.style.display = 'none';

            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // Client-side validation
            let hasErrors = false;
            let errorMessage = '';
            
            if (password !== confirmPassword) {
                errorMessage = 'Les mots de passe ne correspondent pas.';
                hasErrors = true;
            } else if (password.length < 8) {
                errorMessage = 'Le mot de passe doit contenir au moins 8 caractères.';
                hasErrors = true;
            }

            if (hasErrors) {
                errorFeedback.querySelector('.feedback-message').textContent = errorMessage;
                errorFeedback.style.display = 'flex';
                form.classList.add('shake');
                setTimeout(() => {
                    form.classList.remove('shake');
                }, 500);
                return;
            }

            const csrfToken = document.querySelector('input[name="_token"]').value;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Show loading state
            submitButton.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Modification en cours...';
            submitButton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                });

                const result = await response.json();

                if (response.ok) {
                    successFeedback.style.display = 'flex';
                    errorFeedback.style.display = 'none';
                    form.reset();
                    strengthBar.style.width = '0%';
                    strengthText.textContent = 'Force du mot de passe';
                    strengthText.style.color = '#6c757d';
                    matchIndicator.innerHTML = '';
                    checkPasswordRequirements('');
                    
                    // Add success animation
                    successFeedback.classList.add('pulse');
                    setTimeout(() => {
                        successFeedback.classList.remove('pulse');
                    }, 500);
                    
                    setTimeout(() => {
                        successFeedback.style.display = 'none';
                    }, 5000);
                } else {
                    errorFeedback.querySelector('.feedback-message').textContent = result.message || 'Une erreur s\'est produite.';
                    errorFeedback.style.display = 'flex';
                    successFeedback.style.display = 'none';
                }
            } catch (error) {
                errorFeedback.querySelector('.feedback-message').textContent = 'Une erreur technique s\'est produite. Veuillez réessayer.';
                errorFeedback.style.display = 'flex';
                successFeedback.style.display = 'none';
            } finally {
                // Restore button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    });
</script>
@endsection