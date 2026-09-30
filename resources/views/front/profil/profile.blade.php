@extends('front.global')

@section('title', 'Mon Profil')

@section('content')
<style>
    body:has(.biodex-profile) .hero-header {
        display: none;
    }

    body:has(.biodex-profile) .navbar {
        background-color: #17634d !important;
        box-shadow: 0 2px 12px rgba(18, 54, 43, 0.16);
    }

    .biodex-profile {
        --profile-forest: #17634d;
        --profile-ink: #17352d;
        --profile-muted: #70817b;
        --profile-line: #e5ece8;
        max-width: 1080px;
        margin: 0 auto;
        padding: 38px 24px 56px;
        color: var(--profile-ink);
    }

    .profile-heading {
        margin-bottom: 26px;
    }

    .profile-eyebrow {
        margin: 0 0 9px;
        color: #a35d2a;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .profile-heading h1 {
        margin: 0;
        color: var(--profile-ink);
        font-size: 2rem;
        font-weight: 700;
    }

    .profile-heading > p:last-child {
        margin: 7px 0 0;
        color: var(--profile-muted);
    }

    .profile-panel {
        display: grid;
        grid-template-columns: minmax(250px, 0.78fr) minmax(0, 1.5fr);
        overflow: hidden;
        border: 1px solid var(--profile-line);
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 16px 42px rgba(23, 53, 45, 0.08);
    }

    .profile-identity {
        display: flex;
        min-height: 420px;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        padding: 42px 36px;
        background-color: var(--profile-forest);
        background-image: repeating-linear-gradient(135deg, transparent 0, transparent 24px, rgba(255, 255, 255, 0.035) 24px, rgba(255, 255, 255, 0.035) 25px);
        color: #fff;
    }

    .profile-avatar {
        display: grid;
        width: 92px;
        height: 92px;
        place-items: center;
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.62);
        border-radius: 50%;
        background: #f2d5a0;
        color: #674329;
        font-size: 2rem;
        font-weight: 700;
    }

    .profile-role {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        color: #d8eee4;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .profile-role::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #e9b464;
        content: '';
    }

    .profile-identity h2 {
        margin: 0;
        color: #fff;
        font-size: 1.65rem;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .profile-identity p {
        margin: 8px 0 0;
        color: #d6e7df;
        overflow-wrap: anywhere;
    }

    .profile-details {
        padding: 42px 46px;
    }

    .profile-details-heading {
        padding-bottom: 20px;
        border-bottom: 1px solid var(--profile-line);
    }

    .profile-details-heading h3 {
        margin: 0;
        color: var(--profile-ink);
        font-size: 1.08rem;
        font-weight: 700;
    }

    .profile-details-heading p {
        margin: 5px 0 0;
        color: var(--profile-muted);
        font-size: 0.9rem;
    }

    .profile-detail {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr);
        align-items: center;
        gap: 14px;
        padding: 20px 0;
        border-bottom: 1px solid var(--profile-line);
    }

    .profile-detail-icon {
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;
        border-radius: 8px;
        background: #edf5f0;
        color: var(--profile-forest);
        font-size: 1.05rem;
    }

    .profile-detail dt {
        margin-bottom: 3px;
        color: var(--profile-muted);
        font-size: 0.77rem;
        font-weight: 600;
    }

    .profile-detail dd {
        margin: 0;
        color: var(--profile-ink);
        font-weight: 500;
        overflow-wrap: anywhere;
    }

    .profile-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding-top: 24px;
    }

    .profile-settings-link,
    .profile-logout-button {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 9px 15px;
        border: 1px solid var(--profile-line);
        border-radius: 6px;
        background: #fff;
        color: var(--profile-forest);
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 160ms ease, border-color 160ms ease;
    }

    .profile-settings-link:hover {
        border-color: #b9d1c4;
        background: #f3f8f5;
        color: #104b39;
    }

    .profile-logout-button {
        border-color: #f0d9d4;
        color: #a33c32;
        cursor: pointer;
    }

    .profile-logout-button:hover {
        border-color: #e9bdb5;
        background: #fff7f5;
        color: #862d25;
    }

    @media (max-width: 700px) {
        .biodex-profile {
            padding: 30px 16px 40px;
        }

        .profile-heading h1 {
            font-size: 1.7rem;
        }

        .profile-panel {
            grid-template-columns: 1fr;
        }

        .profile-identity {
            min-height: auto;
            padding: 32px 26px;
        }

        .profile-avatar {
            width: 76px;
            height: 76px;
            margin-bottom: 18px;
        }

        .profile-details {
            padding: 28px 24px;
        }

        .profile-actions {
            align-items: stretch;
        }

        .profile-actions form,
        .profile-logout-button {
            width: 100%;
        }
    }
</style>

<div class="biodex-profile">
    <header class="profile-heading">
        <p class="profile-eyebrow">Biodex / Espace personnel</p>
        <h1>Mon profil</h1>
        <p>Retrouvez les informations liées à votre compte.</p>
    </header>

    <section class="profile-panel" aria-label="Informations du profil">
        <div class="profile-identity">
            <div class="profile-avatar" aria-hidden="true">{{ mb_substr($user->name, 0, 1) }}</div>
            <span class="profile-role">{{ $user->role ? ucfirst($user->role->name) : 'Citoyen' }}</span>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
        </div>

        <div class="profile-details">
            <div class="profile-details-heading">
                <h3>Informations du compte</h3>
                <p>Vos coordonnées et la sécurité de votre compte.</p>
            </div>

            <dl class="profile-detail-list">
                <div class="profile-detail">
                    <span class="profile-detail-icon" aria-hidden="true"><i class="bi bi-envelope"></i></span>
                    <div>
                        <dt>Adresse e-mail</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                </div>
                <div class="profile-detail">
                    <span class="profile-detail-icon" aria-hidden="true"><i class="bi bi-geo-alt"></i></span>
                    <div>
                        <dt>Adresse</dt>
                        <dd>{{ $user->address ?: 'Aucune adresse enregistrée' }}</dd>
                    </div>
                </div>
                <div class="profile-detail">
                    <span class="profile-detail-icon" aria-hidden="true"><i class="bi bi-calendar-check"></i></span>
                    <div>
                        <dt>Membre depuis</dt>
                        <dd>{{ $user->created_at->format('d/m/Y') }}</dd>
                    </div>
                </div>
            </dl>

            <div class="profile-actions">
                <a href="{{ route('settings.password') }}" class="profile-settings-link">
                    <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    Modifier le mot de passe
                </a>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
                    <button type="submit" class="profile-logout-button">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        Déconnexion
            </button>
        </form>
            </div>
        </div>
    </section>
    </div>
</div>
@endsection