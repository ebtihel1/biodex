@extends('back.layout')

@section('content')
<style>
    .back-password {
        max-width: 620px;
        margin: 0 auto;
    }

    .back-password .card {
        border: none;
        border-radius: 22px;
        box-shadow: 0 12px 32px rgba(15, 76, 66, 0.08);
        overflow: hidden;
    }

    .back-password .card-header {
        background: linear-gradient(135deg, #0f4f42, #0b2f2a);
        color: #fff;
        padding: 26px 30px;
        border: none;
    }

    .back-password .card-body {
        padding: 30px;
    }

    .back-password .form-label {
        font-weight: 600;
        color: #183b36;
    }

    .back-password .form-control {
        border: 1px solid #dbe5e0;
        border-radius: 10px;
        padding: 11px 14px;
    }

    .back-password .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
    }

    .back-password .form-text {
        color: #6c7d78;
    }
</style>

<div class="back-password">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-1">Change Password</h3>
            <p class="mb-0 text-white-50">Secure your back-office account with a new password.</p>
        </div>

        <div class="card-body">
            <div class="alert d-none" id="backPasswordFeedback" role="alert"></div>

            <form id="backPasswordForm" action="{{ route('back.profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current password</label>
                    <input type="password" class="form-control" name="current_password" id="current_password"
                           required autocomplete="current-password">
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New password</label>
                    <input type="password" class="form-control" name="new_password" id="new_password"
                           required minlength="8" autocomplete="new-password">
                    <div class="form-text">Use at least 8 characters with uppercase, lowercase, number and special character.</div>
                </div>

                <div class="mb-4">
                    <label for="new_password_confirmation" class="form-label">Confirm new password</label>
                    <input type="password" class="form-control" name="new_password_confirmation"
                           id="new_password_confirmation" required autocomplete="new-password">
                </div>

                <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-success" id="backPasswordSubmit">
                        <i class="bi bi-check-lg me-1"></i> Save new password
                    </button>
                    <a href="{{ route('back.profile') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('backPasswordForm');
        const feedback = document.getElementById('backPasswordFeedback');
        const submitButton = document.getElementById('backPasswordSubmit');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('new_password_confirmation');

        function showFeedback(type, message) {
            feedback.className = 'alert alert-' + type;
            feedback.textContent = message;
            feedback.classList.remove('d-none');
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (newPassword.value !== confirmPassword.value) {
                showFeedback('danger', 'The password confirmation does not match.');
                return;
            }

            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                });

                const result = await response.json();

                if (response.ok) {
                    showFeedback('success', result.message || 'Password updated successfully.');
                    form.reset();
                } else {
                    showFeedback('danger', result.message || 'Unable to update the password.');
                }
            } catch (error) {
                showFeedback('danger', 'A technical error occurred. Please try again.');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    });
</script>
@endsection
