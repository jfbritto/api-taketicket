@props(['expiresAt'])

<div x-data="countdown('{{ $expiresAt }}')" x-init="start()" class="text-center">
    <template x-if="expired">
        <span class="text-red-600 font-semibold">Session expired</span>
    </template>
    <template x-if="!expired">
        <span class="text-gray-600">
            Time remaining: <span class="font-mono font-semibold" x-text="display"></span>
        </span>
    </template>
</div>

@push('scripts')
<script>
function countdown(expiresAt) {
    return {
        expired: false,
        display: '',
        start() {
            const target = new Date(expiresAt).getTime();
            const tick = () => {
                const diff = target - Date.now();
                if (diff <= 0) {
                    this.expired = true;
                    this.display = '00:00';
                    // Dispatch event so the page can handle expiry (e.g., redirect to event page)
                    window.dispatchEvent(new CustomEvent('order-expired'));
                    return;
                }
                const mins = Math.floor(diff / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                this.display = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                setTimeout(tick, 1000);
            };
            tick();
        }
    };
}
</script>
@endpush
