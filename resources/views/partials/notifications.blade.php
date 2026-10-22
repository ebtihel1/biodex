@auth
    @php
        $notifications = Auth::user()->notifications()->latest()->take(10)->get();
        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
        $tag = $tag ?? 'div';
        $tagClass = $tagClass ?? 'dropdown';
        $context = $context ?? (request()->is('back*') ? 'back' : 'front');
    @endphp

    <{{ $tag }} class="{{ $tagClass }}">
        <a href="#" class="position-relative d-inline-flex align-items-center {{ $btnClass ?? 'header-profile' }}" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications" title="Notifications">
            <i class="bi bi-bell fs-5"></i>
            @if ($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadCount }}</span>
            @endif
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 330px; max-height: 420px; overflow-y: auto;">
            <li class="d-flex justify-content-between align-items-center px-3 py-2">
                <strong>Notifications</strong>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.readAll') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Mark all as read</button>
                    </form>
                @endif
            </li>
            <li><hr class="dropdown-divider"></li>

            @forelse ($notifications as $notification)
                <li>
                    <a href="{{ route('notifications.read', ['notification' => $notification, 'context' => $context]) }}" class="dropdown-item d-flex gap-2 py-2 {{ $notification->is_read ? '' : 'fw-semibold' }}">
                        <i class="bi {{ $notification->type === 'success' ? 'bi-check-circle-fill text-success' : ($notification->type === 'error' ? 'bi-exclamation-circle-fill text-danger' : 'bi-info-circle-fill text-primary') }}"></i>
                        <span class="d-block">
                            <span class="d-block">{{ $notification->title }}</span>
                            <small class="text-muted d-block" style="white-space: pre-line;">{{ $notification->message }}</small>
                            <small class="text-muted">{{ optional($notification->created_at)->diffForHumans() }}</small>
                        </span>
                    </a>
                </li>
            @empty
                <li><span class="dropdown-item-text text-muted">No notifications</span></li>
            @endforelse
        </ul>
    </{{ $tag }}>
@endauth
