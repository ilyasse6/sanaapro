@extends('user.master')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-bell-fill me-2"></i>Toutes mes notifications</h5>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Retour</a>
        </div>
        <div class="card-body p-0">
            @if($notifications->isEmpty())
                <div class="p-4 text-center text-muted">Aucune notification</div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        @php
                            $type = $notification->type;
                            $data = (array) $notification->data;
                            $created = $notification->created_at?->diffForHumans();
                            $isUnread = is_null($notification->read_at);
                            $iconClass = 'bi bi-info-circle text-info';
                            $title = $data['title'] ?? 'Notification';
                            $message = $data['message'] ?? '';
                            $url = $data['url'] ?? '#';
                        @endphp
                        <li class="list-group-item d-flex align-items-start {{ $isUnread ? 'bg-light' : '' }}">
                            <i class="{{ $iconClass }} me-3 fs-5"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $title }}</strong>
                                    <small class="text-muted">{{ $created }}</small>
                                </div>
                                @if($message)
                                    <div class="text-muted">{{ $message }}</div>
                                @endif
                                @if($url && $url !== '#')
                                    <a href="#" class="notification-link" data-id="{{ $notification->id }}" data-url="{{ $url }}">
                                        Ouvrir
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="p-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e) {
    const link = e.target.closest('.notification-link');
    if (!link) return;
    e.preventDefault();
    const id = link.getAttribute('data-id');
    const redirectUrl = link.getAttribute('data-url');
    const urlTemplate = "{{ route('notifications.read', ['notificationId' => 'PLACEHOLDER']) }}";
    const finalUrl = urlTemplate.replace('PLACEHOLDER', id);
    fetch(finalUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .finally(() => { window.location.href = redirectUrl; });
});
</script>
@endsection


