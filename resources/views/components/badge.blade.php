@props(['type' => 'default'])

@php
$styles = [
    'draft'            => 'background:#f1f5f9;color:#64748b;',
    'published'        => 'background:#dcfce7;color:#16a34a;',
    'cancelled'        => 'background:#fee2e2;color:#dc2626;',
    'finished'         => 'background:#dbeafe;color:#2563eb;',
    'valid'            => 'background:#dcfce7;color:#16a34a;',
    'used'             => 'background:#e0e7ff;color:#4338ca;',
    'paid'             => 'background:#dcfce7;color:#16a34a;',
    'pending'          => 'background:#fef9c3;color:#ca8a04;',
    'awaiting_payment' => 'background:#fef9c3;color:#ca8a04;',
    'expired'          => 'background:#f1f5f9;color:#94a3b8;',
    'refunded'         => 'background:#dbeafe;color:#2563eb;',
    'active'           => 'background:#dcfce7;color:#16a34a;',
    'revoked'          => 'background:#f1f5f9;color:#94a3b8;',
    'default'          => 'background:#f1f5f9;color:#64748b;',
];
$s = $styles[$type] ?? $styles['default'];
@endphp

<span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:100px;font-size:12px;font-weight:600;{{ $s }}">
    {{ $slot }}
</span>
