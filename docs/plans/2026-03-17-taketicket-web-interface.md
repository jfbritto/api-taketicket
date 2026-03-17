# TAKETICKET Web Interface Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a complete web interface (organizer dashboard, public event pages, checkout flow) to the existing TAKETICKET Laravel API using Blade + Alpine.js + TailwindCSS.

**Architecture:** Web controllers call the same Service layer used by API controllers. Web uses session auth (`auth` middleware); API uses Sanctum tokens (`auth:sanctum`). Assets pre-built via Vite, committed to repo for shared hosting.

**Tech Stack:** Laravel Blade, Alpine.js (CDN), TailwindCSS 4, html5-qrcode (CDN), qrcode.js (CDN)

**Spec:** `docs/specs/2026-03-17-taketicket-web-interface-design.md`

**Working directory:** `/Users/joaofilipibritto/Projetos/projeto-taketicket/api-taketicket`

**PHP binary:** `/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php`

**Run tests:** `/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test`

---

## File Structure

### Layouts (3 files)
- `resources/views/layouts/app.blade.php` — public pages (nav, footer)
- `resources/views/layouts/dashboard.blade.php` — sidebar + top bar
- `resources/views/layouts/checkout.blade.php` — simplified checkout layout

### Blade Components (9 files)
- `resources/views/components/alert.blade.php`
- `resources/views/components/badge.blade.php`
- `resources/views/components/input.blade.php`
- `resources/views/components/select.blade.php`
- `resources/views/components/textarea.blade.php`
- `resources/views/components/modal.blade.php`
- `resources/views/components/modal.blade.php`
- `resources/views/components/card.blade.php`
- `resources/views/components/countdown.blade.php`

### Auth Views (2 files)
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`

### Public Views (2 files)
- `resources/views/public/home.blade.php`
- `resources/views/public/event-show.blade.php`

### Checkout Views (4 files)
- `resources/views/checkout/show.blade.php`
- `resources/views/checkout/payment.blade.php`
- `resources/views/checkout/success.blade.php`
- `resources/views/checkout/cancel.blade.php`

### Dashboard Views (9 files)
- `resources/views/dashboard/index.blade.php`
- `resources/views/dashboard/onboarding.blade.php`
- `resources/views/dashboard/events/index.blade.php`
- `resources/views/dashboard/events/create.blade.php`
- `resources/views/dashboard/events/edit.blade.php`
- `resources/views/dashboard/events/orders.blade.php`
- `resources/views/dashboard/events/order-show.blade.php`
- `resources/views/dashboard/events/participants.blade.php`
- `resources/views/dashboard/events/tickets.blade.php`
- `resources/views/dashboard/checkin.blade.php`

### My Tickets Views (2 files)
- `resources/views/my-tickets/index.blade.php`
- `resources/views/my-tickets/show.blade.php`

### Web Controllers (11 files)
- `app/Http/Controllers/Web/AuthController.php`
- `app/Http/Controllers/Web/HomeController.php`
- `app/Http/Controllers/Web/PublicEventController.php`
- `app/Http/Controllers/Web/CheckoutController.php`
- `app/Http/Controllers/Web/MyTicketsController.php`
- `app/Http/Controllers/Web/Dashboard/DashboardController.php`
- `app/Http/Controllers/Web/Dashboard/DashboardEventController.php`
- `app/Http/Controllers/Web/Dashboard/OrderController.php`
- `app/Http/Controllers/Web/Dashboard/ParticipantController.php`
- `app/Http/Controllers/Web/Dashboard/TicketController.php`
- `app/Http/Controllers/Web/Dashboard/CheckinController.php`

### Middleware (1 file)
- `app/Http/Middleware/EnsureHasOrganizer.php`

### Service modification (1 file)
- `app/Services/CheckinService.php` — add `undoCheckin()` method

### Routes (1 file)
- `routes/web.php` — all web routes

### Tests (5 files)
- `tests/Feature/Web/AuthWebTest.php`
- `tests/Feature/Web/PublicPagesTest.php`
- `tests/Feature/Web/CheckoutTest.php`
- `tests/Feature/Web/DashboardTest.php`
- `tests/Feature/Web/CheckinWebTest.php`

### Assets (1 file)
- `resources/js/app.js` — add Alpine.js import

### Postman (1 file)
- `docs/taketicket-api.postman_collection.json`

---

## Dependency Graph

```
Task 1 (Layouts + Components + Alpine.js)
  └── Task 2 (Auth pages)
       └── Task 3 (Middleware + Routes skeleton)
            ├── Task 4 (Public pages: home + event)
            ├── Task 5 (Dashboard: onboarding + summary)
            │    └── Task 6 (Dashboard: event CRUD)
            │         └── Task 7 (Dashboard: orders + participants + tickets)
            ├── Task 8 (Checkout flow)
            ├── Task 9 (My Tickets)
            └── Task 10 (Dashboard: check-in + undo service)
  Task 11 (Postman collection) — independent
  Task 12 (Build assets + final cleanup) — last
```

**Parallelizable groups:**
- After Task 3: Tasks 4, 5, 8, 9, 10, 11 can start in parallel
- After Task 5: Task 6 can start
- After Task 6: Task 7 can start
- Task 12 must be last

---

## Design Notes

1. **Form Requests vs inline validation:** The spec lists Form Request classes, but this plan uses inline `$request->validate()` for simplicity. Both are equivalent for validation; Form Requests can be extracted later if needed.

2. **Placeholder participants in checkout:** `OrderService::createOrder()` expects participant data. The checkout flow creates orders with placeholder participant data (`name: 'Pending'`, `email: 'pending@pending.com'`) which gets overwritten in the `saveParticipants` step. The `ExpireOrdersJob` cleans up abandoned orders.

3. **Routes added incrementally:** Routes are added in each task alongside their controllers (not all at once) to avoid `ReflectionException` errors from referencing non-existent classes.

4. **Laravel default pagination:** Uses Laravel's built-in pagination views rather than a custom pagination component.

---

## Task 1: Layouts, Blade Components, and Alpine.js Setup

**Files:**
- Create: `resources/views/layouts/app.blade.php`
- Create: `resources/views/layouts/dashboard.blade.php`
- Create: `resources/views/layouts/checkout.blade.php`
- Create: `resources/views/components/alert.blade.php`
- Create: `resources/views/components/badge.blade.php`
- Create: `resources/views/components/input.blade.php`
- Create: `resources/views/components/select.blade.php`
- Create: `resources/views/components/textarea.blade.php`
- Create: `resources/views/components/card.blade.php`
- Create: `resources/views/components/countdown.blade.php`
- Modify: `resources/js/app.js`

- [ ] **Step 1: Add Alpine.js to app.js**

```javascript
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
```

Also add Alpine.js to package.json:

```bash
npm install alpinejs --save-dev
```

- [ ] **Step 2: Create the public layout (`layouts/app.blade.php`)**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TakeTicket' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Navigation --}}
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">TakeTicket</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/my-tickets') }}" class="text-gray-600 hover:text-gray-900">My Tickets</a>
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        <form method="POST" action="{{ url('/logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                        <a href="{{ url('/register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} TakeTicket. All rights reserved.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
```

- [ ] **Step 3: Create the dashboard layout (`layouts/dashboard.blade.php`)**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - TakeTicket</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <div class="fixed inset-y-0 left-0 z-30 w-64 bg-indigo-800 transform transition-transform duration-200 lg:translate-x-0 lg:static lg:inset-0"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex items-center justify-center h-16 bg-indigo-900">
                <a href="{{ url('/dashboard') }}" class="text-white text-xl font-bold">TakeTicket</a>
            </div>
            <nav class="mt-6 px-4 space-y-1">
                <a href="{{ url('/dashboard') }}"
                   class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard') && !request()->is('dashboard/*') ? 'bg-indigo-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                    Dashboard
                </a>
                <a href="{{ url('/dashboard/events') }}"
                   class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard/events*') ? 'bg-indigo-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Events
                </a>
                <a href="{{ url('/dashboard/checkin') }}"
                   class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard/checkin*') ? 'bg-indigo-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Check-in
                </a>
            </nav>
            <div class="absolute bottom-0 w-full px-4 pb-4">
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"></div>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b h-16 flex items-center px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800 ml-4 lg:ml-0">{{ $header ?? 'Dashboard' }}</h1>
                <div class="ml-auto text-sm text-gray-500">{{ auth()->user()->name }}</div>
            </header>

            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if(session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
```

- [ ] **Step 4: Create the checkout layout (`layouts/checkout.blade.php`)**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Checkout' }} - TakeTicket</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">TakeTicket</a>
            <span class="text-sm text-gray-500">Secure Checkout</span>
        </div>
    </nav>

    @if(session('error'))
        <div class="max-w-4xl mx-auto px-4 mt-4">
            <x-alert type="error" :message="session('error')" />
        </div>
    @endif

    <main class="max-w-4xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
```

- [ ] **Step 5: Create Blade components**

**`components/alert.blade.php`:**
```blade
@props(['type' => 'info', 'message'])

@php
$colors = [
    'success' => 'bg-green-100 text-green-800 border-green-300',
    'error' => 'bg-red-100 text-red-800 border-red-300',
    'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
    'info' => 'bg-blue-100 text-blue-800 border-blue-300',
];
@endphp

<div class="border rounded-lg px-4 py-3 mx-4 mt-4 {{ $colors[$type] ?? $colors['info'] }}" x-data="{ show: true }" x-show="show">
    <div class="flex justify-between items-center">
        <span>{{ $message }}</span>
        <button @click="show = false" class="ml-4">&times;</button>
    </div>
</div>
```

**`components/badge.blade.php`:**
```blade
@props(['type' => 'default'])

@php
$colors = [
    'draft' => 'bg-gray-100 text-gray-700',
    'published' => 'bg-green-100 text-green-700',
    'cancelled' => 'bg-red-100 text-red-700',
    'finished' => 'bg-blue-100 text-blue-700',
    'valid' => 'bg-green-100 text-green-700',
    'used' => 'bg-blue-100 text-blue-700',
    'paid' => 'bg-green-100 text-green-700',
    'pending' => 'bg-yellow-100 text-yellow-700',
    'awaiting_payment' => 'bg-yellow-100 text-yellow-700',
    'expired' => 'bg-gray-100 text-gray-700',
    'default' => 'bg-gray-100 text-gray-700',
];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$type] ?? $colors['default'] }}">
    {{ $slot }}
</span>
```

**`components/input.blade.php`:**
```blade
@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'required' => false])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
           value="{{ old($name, $value) }}"
           {{ $required ? 'required' : '' }}
           {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border']) }}>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
```

**`components/select.blade.php`:**
```blade
@props(['label' => null, 'name', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => 'Select...'])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <select name="{{ $name }}" id="{{ $name }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border']) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $key => $optionLabel)
            <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
```

**`components/textarea.blade.php`:**
```blade
@props(['label' => null, 'name', 'value' => null, 'required' => false, 'rows' => 4])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}"
              {{ $required ? 'required' : '' }}
              {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
```

**`components/modal.blade.php`:**
```blade
@props(['name', 'title' => null, 'maxWidth' => 'md'])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
][$maxWidth] ?? 'max-w-md';
@endphp

<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>
    <div x-show="open" x-transition class="relative bg-white rounded-lg shadow-xl {{ $maxWidthClass }} w-full mx-4 p-6">
        @if($title)
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
        @endif
        {{ $slot }}
    </div>
</div>
```

**`components/card.blade.php`:**
```blade
@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border p-6']) }}>
    @if($title)
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>
```

**`components/countdown.blade.php`:**
```blade
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
                    window.location.href = window.location.href; // refresh to trigger expired redirect
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
```

- [ ] **Step 6: Run existing tests to verify nothing broke**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test
```

Expected: All 78 existing tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: add Blade layouts, components, and Alpine.js setup"
```

---

## Task 2: Web Authentication (Login, Register, Logout)

**Files:**
- Create: `app/Http/Controllers/Web/AuthController.php`
- Create: `resources/views/auth/login.blade.php`
- Create: `resources/views/auth/register.blade.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Web/AuthWebTest.php`

- [ ] **Step 1: Write AuthWebTest**

```php
<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('Login');
    }

    public function test_register_page_renders(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
        $response->assertSee('Register');
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_login_redirects_to_intended_url(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        // Try to access protected page first
        $this->get('/my-tickets');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/my-tickets');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

- [ ] **Step 3: Create Web AuthController**

```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

- [ ] **Step 4: Create login view (`auth/login.blade.php`)**

```blade
<x-layouts.app title="Login">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Login">
            <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                @csrf
                <x-input label="Email" name="email" type="email" required />
                <x-input label="Password" name="password" type="password" required />
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 mr-2">
                        Remember me
                    </label>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium">
                    Login
                </button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Don't have an account? <a href="{{ url('/register') }}" class="text-indigo-600 hover:underline">Register</a>
            </p>
        </x-card>
    </div>
</x-layouts.app>
```

- [ ] **Step 5: Create register view (`auth/register.blade.php`)**

```blade
<x-layouts.app title="Register">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Create Account">
            <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                @csrf
                <x-input label="Name" name="name" required />
                <x-input label="Email" name="email" type="email" required />
                <x-input label="Password" name="password" type="password" required />
                <x-input label="Confirm Password" name="password_confirmation" type="password" required />
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium">
                    Register
                </button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Already have an account? <a href="{{ url('/login') }}" class="text-indigo-600 hover:underline">Login</a>
            </p>
        </x-card>
    </div>
</x-layouts.app>
```

- [ ] **Step 6: Add auth routes to `routes/web.php`**

```php
<?php

use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
```

- [ ] **Step 7: Run tests, verify pass**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test
```

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat: implement web authentication (login, register, logout)"
```

---

## Task 3: EnsureHasOrganizer Middleware + Route Skeleton

**Files:**
- Create: `app/Http/Middleware/EnsureHasOrganizer.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create EnsureHasOrganizer middleware**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->organizer) {
            return redirect('/dashboard/onboarding');
        }

        return $next($request);
    }
}
```

- [ ] **Step 2: Add only middleware to `routes/web.php`**

In this task we only create the middleware file. Routes will be added incrementally in each subsequent task alongside their controllers. This avoids `ReflectionException` errors from referencing non-existent controller classes.

No route changes needed in this step — each subsequent task adds its own routes when its controllers are created.

**IMPORTANT for all subsequent tasks:** When a task creates a controller, it must also add the corresponding routes to `routes/web.php`. The full route structure is documented below for reference:

```php
use App\Http\Middleware\EnsureHasOrganizer;

// Public pages (added in Task 4)
// Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('event/{slug}', [PublicEventController::class, 'show'])->name('event.show');

// Auth-required routes (added incrementally in Tasks 5, 8, 9, 10)
// Route::middleware('auth')->group(function () {
//     Checkout routes (Task 8)
//     My Tickets routes (Task 9)
//     Dashboard onboarding (Task 5) — auth but NO organizer middleware
//     Dashboard routes (Tasks 5, 6, 7, 10) — auth + EnsureHasOrganizer middleware
// });
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "feat: add EnsureHasOrganizer middleware and web route skeleton"
```

---

## Task 4: Public Pages (Home + Event)

**Files:**
- Create: `app/Http/Controllers/Web/HomeController.php`
- Create: `app/Http/Controllers/Web/PublicEventController.php`
- Create: `resources/views/public/home.blade.php`
- Create: `resources/views/public/event-show.blade.php`
- Create: `tests/Feature/Web/PublicPagesTest.php`

- [ ] **Step 1: Write PublicPagesTest**

```php
<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_published_events(): void
    {
        Event::factory()->create(['status' => EventStatus::PUBLISHED, 'title' => 'Public Event']);
        Event::factory()->create(['status' => EventStatus::DRAFT, 'title' => 'Draft Event']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Public Event');
        $response->assertDontSee('Draft Event');
    }

    public function test_event_page_shows_event_details(): void
    {
        $event = Event::factory()->create([
            'status' => EventStatus::PUBLISHED,
            'title' => 'Concert Night',
        ]);
        TicketType::factory()->create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 150,
            'available' => 50,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addMonth(),
        ]);

        $response = $this->get('/event/' . $event->slug);

        $response->assertOk();
        $response->assertSee('Concert Night');
        $response->assertSee('VIP');
        $response->assertSee('150');
    }

    public function test_draft_event_returns_404(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::DRAFT]);

        $response = $this->get('/event/' . $event->slug);

        $response->assertNotFound();
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

- [ ] **Step 3: Create HomeController**

```php
<?php

namespace App\Http\Controllers\Web;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::where('status', EventStatus::PUBLISHED)
            ->where('start_date', '>=', now())
            ->with('ticketTypes')
            ->orderBy('start_date');

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $events = $query->paginate(12);

        return view('public.home', compact('events'));
    }
}
```

- [ ] **Step 4: Create PublicEventController**

```php
<?php

namespace App\Http\Controllers\Web;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function show(string $slug): View
    {
        $event = Event::where('slug', $slug)
            ->where('status', EventStatus::PUBLISHED)
            ->with(['ticketTypes' => fn ($q) => $q->orderBy('price'), 'customFields'])
            ->firstOrFail();

        return view('public.event-show', compact('event'));
    }
}
```

- [ ] **Step 5: Create home view (`public/home.blade.php`)**

```blade
<x-layouts.app title="TakeTicket - Find Events">
    {{-- Hero --}}
    <div class="bg-indigo-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Find Your Next Event</h1>
            <p class="text-xl text-indigo-100 mb-8">Discover and buy tickets for the best events near you</p>
            <form method="GET" action="{{ url('/') }}" class="max-w-2xl mx-auto flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search events..."
                       class="flex-1 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                <input type="text" name="city" value="{{ request('city') }}"
                       placeholder="City"
                       class="w-40 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-36 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300"
                       placeholder="From">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-36 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300"
                       placeholder="To">
                <button type="submit" class="bg-indigo-800 px-6 py-3 rounded-lg hover:bg-indigo-900 font-medium">
                    Search
                </button>
            </form>
        </div>
    </div>

    {{-- Events Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($events->isEmpty())
            <p class="text-center text-gray-500 text-lg">No events found.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <a href="{{ route('event.show', $event->slug) }}" class="block bg-white rounded-lg shadow-sm border hover:shadow-md transition">
                        @if($event->banner)
                            <img src="{{ $event->banner }}" alt="{{ $event->title }}" class="w-full h-48 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-t-lg flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">{{ substr($event->title, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-900">{{ $event->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $event->start_date->format('d M Y, H:i') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $event->city }}{{ $event->state ? ', ' . $event->state : '' }}</p>
                            @php
                                $minPrice = $event->ticketTypes->min('price');
                            @endphp
                            @if($minPrice !== null)
                                <p class="mt-2 font-semibold text-indigo-600">
                                    {{ $minPrice > 0 ? 'A partir de R$ ' . number_format($minPrice, 2, ',', '.') : 'Free' }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $events->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
```

- [ ] **Step 6: Create event page view (`public/event-show.blade.php`)**

```blade
<x-layouts.app :title="$event->title">
    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Banner --}}
        @if($event->banner)
            <img src="{{ $event->banner }}" alt="{{ $event->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
        @else
            <div class="w-full h-64 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg mb-6 flex items-center justify-center">
                <span class="text-white text-6xl font-bold">{{ substr($event->title, 0, 1) }}</span>
            </div>
        @endif

        {{-- Event Info --}}
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
        <div class="flex flex-wrap gap-4 text-gray-600 mb-6">
            <span>{{ $event->start_date->format('d M Y, H:i') }}{{ $event->end_date ? ' - ' . $event->end_date->format('d M Y, H:i') : '' }}</span>
            <span>{{ $event->location }}{{ $event->address ? ', ' . $event->address : '' }}</span>
            <span>{{ $event->city }}{{ $event->state ? ', ' . $event->state : '' }}</span>
        </div>

        @if($event->description)
            <div class="prose max-w-none mb-8">
                {!! nl2br(e($event->description)) !!}
            </div>
        @endif

        {{-- Ticket Types --}}
        <x-card title="Tickets">
            <form method="POST" action="{{ route('checkout.order') }}" x-data="ticketSelector()" id="ticket-form">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="space-y-4">
                    @foreach($event->ticketTypes as $ticketType)
                        @php
                            $onSale = $ticketType->isOnSale();
                            $soldOut = $ticketType->available <= 0;
                            $upcoming = $ticketType->sale_start->isFuture();
                            $ended = $ticketType->sale_end->isPast();
                            $maxQty = min($ticketType->available, $ticketType->max_per_user ?? 10);
                        @endphp
                        <div class="flex items-center justify-between p-4 border rounded-lg {{ $onSale ? '' : 'opacity-60' }}">
                            <div>
                                <h4 class="font-semibold">{{ $ticketType->name }}</h4>
                                @if($ticketType->description)
                                    <p class="text-sm text-gray-500">{{ $ticketType->description }}</p>
                                @endif
                                <p class="text-lg font-bold text-indigo-600 mt-1">
                                    {{ $ticketType->price > 0 ? 'R$ ' . number_format($ticketType->price, 2, ',', '.') : 'Free' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($soldOut)
                                    <x-badge type="cancelled">Sold Out</x-badge>
                                @elseif($upcoming)
                                    <x-badge type="pending">Starts {{ $ticketType->sale_start->format('d/m') }}</x-badge>
                                @elseif($ended)
                                    <x-badge type="expired">Ended</x-badge>
                                @else
                                    <select name="items[{{ $ticketType->id }}][quantity]"
                                            @change="updateTotal()"
                                            data-price="{{ $ticketType->price }}"
                                            class="rounded-lg border-gray-300 w-20 text-center">
                                        @for($i = 0; $i <= $maxQty; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <input type="hidden" name="items[{{ $ticketType->id }}][ticket_type_id]" value="{{ $ticketType->id }}">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <div class="text-lg font-semibold" x-show="total > 0">
                        Total: R$ <span x-text="total.toFixed(2).replace('.', ',')"></span>
                    </div>
                    <button type="submit" :disabled="total === 0 && !hasFreeTickets"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Buy Tickets
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
    function ticketSelector() {
        return {
            total: 0,
            hasFreeTickets: false,
            updateTotal() {
                let sum = 0;
                let free = false;
                document.querySelectorAll('#ticket-form select[data-price]').forEach(sel => {
                    const qty = parseInt(sel.value) || 0;
                    const price = parseFloat(sel.dataset.price) || 0;
                    sum += qty * price;
                    if (qty > 0 && price === 0) free = true;
                });
                this.total = sum;
                this.hasFreeTickets = free;
            }
        };
    }
    </script>
    @endpush
</x-layouts.app>
```

- [ ] **Step 7: Add public routes to `routes/web.php`**

Replace the existing welcome route and add event route:
```php
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PublicEventController;

// Change this line:
Route::get('/', function () { return view('welcome'); });
// To:
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('event/{slug}', [PublicEventController::class, 'show'])->name('event.show');
```

- [ ] **Step 8: Run tests, verify pass**

- [ ] **Step 9: Commit**

```bash
git add -A
git commit -m "feat: implement public home page and event detail page"
```

---

## Task 5: Dashboard Onboarding + Summary

**Files:**
- Create: `app/Http/Controllers/Web/Dashboard/DashboardController.php`
- Create: `resources/views/dashboard/index.blade.php`
- Create: `resources/views/dashboard/onboarding.blade.php`
- Create: `tests/Feature/Web/DashboardTest.php`

- [ ] **Step 1: Write DashboardTest (partial — onboarding + summary)**

```php
<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_organizer_redirected_to_onboarding(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/dashboard/onboarding');
    }

    public function test_onboarding_page_renders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/onboarding');

        $response->assertOk();
        $response->assertSee('Create Organizer Profile');
    }

    public function test_can_create_organizer_via_onboarding(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard/onboarding', [
            'name' => 'My Events Co',
            'document' => '12345678000190',
            'phone' => '11999999999',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('organizers', ['user_id' => $user->id, 'name' => 'My Events Co']);
    }

    public function test_dashboard_shows_summary(): void
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);
        Order::factory()->count(3)->create([
            'event_id' => $event->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($organizer->user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('300'); // total revenue
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

- [ ] **Step 3: Create DashboardController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\DTO\CreateOrganizerDTO;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Services\OrganizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $events = $organizer->events;
        $eventIds = $events->pluck('id');

        $totalEvents = $events->count();
        $totalSales = \App\Models\Order::whereIn('event_id', $eventIds)
            ->where('status', OrderStatus::PAID)
            ->sum('total_amount');
        $totalParticipants = \App\Models\Participant::whereHas('ticket', fn ($q) => $q->whereIn('event_id', $eventIds))->count();
        $totalTickets = \App\Models\Ticket::whereIn('event_id', $eventIds)->count();
        $checkedIn = \App\Models\Ticket::whereIn('event_id', $eventIds)
            ->where('status', \App\Enums\TicketStatus::USED)->count();
        $checkinRate = $totalTickets > 0 ? round(($checkedIn / $totalTickets) * 100) : 0;

        $recentOrders = \App\Models\Order::whereIn('event_id', $eventIds)
            ->with('user', 'event')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalEvents', 'totalSales', 'totalParticipants', 'checkinRate', 'recentOrders'
        ));
    }

    public function onboarding(): View
    {
        return view('dashboard.onboarding');
    }

    public function storeOrganizer(Request $request, OrganizerService $organizerService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->user()->organizer) {
            return redirect('/dashboard');
        }

        $dto = CreateOrganizerDTO::fromRequest($validated);
        $organizerService->createOrganizer($request->user(), $dto);

        return redirect('/dashboard')->with('success', 'Organizer profile created!');
    }
}
```

- [ ] **Step 4: Create dashboard index view (`dashboard/index.blade.php`)**

```blade
<x-layouts.dashboard header="Dashboard">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <p class="text-sm text-gray-500">Total Events</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalEvents }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Total Sales</p>
            <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Total Participants</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalParticipants }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Check-in Rate</p>
            <p class="text-3xl font-bold text-indigo-600">{{ $checkinRate }}%</p>
        </x-card>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-8">
        <a href="{{ route('dashboard.events.create') }}" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Create Event
        </a>
    </div>

    {{-- Recent Orders --}}
    <x-card title="Recent Orders">
        @if($recentOrders->isEmpty())
            <p class="text-gray-500">No orders yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Order</th>
                            <th class="pb-3 font-medium">Buyer</th>
                            <th class="pb-3 font-medium">Event</th>
                            <th class="pb-3 font-medium">Amount</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="py-3">#{{ $order->id }}</td>
                                <td class="py-3">{{ $order->user->name }}</td>
                                <td class="py-3">{{ $order->event->title }}</td>
                                <td class="py-3">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                <td class="py-3"><x-badge :type="$order->status->value">{{ $order->status->value }}</x-badge></td>
                                <td class="py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layouts.dashboard>
```

- [ ] **Step 5: Create onboarding view (`dashboard/onboarding.blade.php`)**

```blade
<x-layouts.app title="Create Organizer Profile">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Create Organizer Profile">
            <p class="text-gray-600 mb-6">Set up your organizer profile to start creating events.</p>
            <form method="POST" action="{{ route('dashboard.storeOrganizer') }}" class="space-y-4">
                @csrf
                <x-input label="Organization Name" name="name" required />
                <x-input label="Document (CPF/CNPJ)" name="document" />
                <x-input label="Phone" name="phone" />
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium">
                    Create Profile
                </button>
            </form>
        </x-card>
    </div>
</x-layouts.app>
```

- [ ] **Step 6: Add dashboard routes to `routes/web.php`**

Add after the existing auth routes:

```php
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Middleware\EnsureHasOrganizer;

Route::middleware('auth')->group(function () {
    // Dashboard onboarding (auth but NO organizer middleware)
    Route::get('dashboard/onboarding', [DashboardController::class, 'onboarding'])->name('dashboard.onboarding');
    Route::post('dashboard/onboarding', [DashboardController::class, 'storeOrganizer'])->name('dashboard.storeOrganizer');

    // Dashboard (auth + organizer required)
    Route::prefix('dashboard')->middleware(EnsureHasOrganizer::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        // More routes added in Tasks 6, 7, 10
    });
});
```

- [ ] **Step 7: Run tests, verify pass**

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat: implement dashboard onboarding and summary page"
```

---

## Task 6: Dashboard Event CRUD

**Files:**
- Create: `app/Http/Controllers/Web/Dashboard/DashboardEventController.php`
- Create: `resources/views/dashboard/events/index.blade.php`
- Create: `resources/views/dashboard/events/create.blade.php`
- Create: `resources/views/dashboard/events/edit.blade.php`
- Add tests to: `tests/Feature/Web/DashboardTest.php`

- [ ] **Step 1: Add event CRUD tests to DashboardTest**

```php
public function test_events_list_shows_organizer_events(): void
{
    $organizer = Organizer::factory()->create();
    Event::factory()->create(['organizer_id' => $organizer->id, 'title' => 'My Event']);

    $response = $this->actingAs($organizer->user)->get('/dashboard/events');

    $response->assertOk();
    $response->assertSee('My Event');
}

public function test_create_event_page_renders(): void
{
    $organizer = Organizer::factory()->create();

    $response = $this->actingAs($organizer->user)->get('/dashboard/events/create');

    $response->assertOk();
    $response->assertSee('Create Event');
}

public function test_can_create_event(): void
{
    $organizer = Organizer::factory()->create();

    $response = $this->actingAs($organizer->user)->post('/dashboard/events', [
        'title' => 'New Event',
        'description' => 'A test event',
        'location' => 'Convention Center',
        'city' => 'Sao Paulo',
        'state' => 'SP',
        'start_date' => now()->addMonth()->format('Y-m-d\TH:i'),
    ]);

    $response->assertRedirect('/dashboard/events');
    $this->assertDatabaseHas('events', ['title' => 'New Event']);
}

public function test_can_update_event(): void
{
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);

    $response = $this->actingAs($organizer->user)->put('/dashboard/events/' . $event->id, [
        'title' => 'Updated Title',
        'start_date' => now()->addMonth()->format('Y-m-d\TH:i'),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated Title']);
}

public function test_can_publish_event(): void
{
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => EventStatus::DRAFT]);
    \App\Models\TicketType::factory()->create(['event_id' => $event->id]);
    \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['id' => 'acc_123'])]);

    $response = $this->actingAs($organizer->user)->patch('/dashboard/events/' . $event->id . '/publish');

    $response->assertRedirect();
    $this->assertEquals(EventStatus::PUBLISHED, $event->fresh()->status);
}

public function test_cannot_manage_other_organizer_event(): void
{
    $organizer1 = Organizer::factory()->create();
    $organizer2 = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer1->id]);

    $response = $this->actingAs($organizer2->user)->get('/dashboard/events/' . $event->id . '/edit');

    $response->assertForbidden();
}
```

- [ ] **Step 2: Create DashboardEventController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\DTO\CreateEventDTO;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\CustomField;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardEventController extends Controller
{
    public function __construct(private readonly EventService $eventService) {}

    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $query = $organizer->events()->withCount(['orders' => fn ($q) => $q->where('status', 'paid')]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->latest()->paginate(15);

        return view('dashboard.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('dashboard.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'banner' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $dto = CreateEventDTO::fromRequest($validated);
        $this->eventService->createEvent($request->user()->organizer, $dto);

        return redirect()->route('dashboard.events')->with('success', 'Event created as draft.');
    }

    public function edit(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $event->load('ticketTypes', 'customFields');

        return view('dashboard.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'banner' => 'nullable|image|max:2048',
            // Ticket types
            'ticket_types' => 'nullable|array',
            'ticket_types.*.id' => 'nullable|exists:ticket_types,id',
            'ticket_types.*.name' => 'required|string|max:255',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.quantity' => 'required|integer|min:1',
            'ticket_types.*.sale_start' => 'required|date',
            'ticket_types.*.sale_end' => 'required|date|after:ticket_types.*.sale_start',
            // Custom fields
            'custom_fields' => 'nullable|array',
            'custom_fields.*.id' => 'nullable|exists:custom_fields,id',
            'custom_fields.*.label' => 'required|string|max:255',
            'custom_fields.*.type' => 'required|in:text,number,select,checkbox',
            'custom_fields.*.required' => 'nullable|boolean',
            'custom_fields.*.options' => 'nullable|string',
            'custom_fields.*.position' => 'nullable|integer',
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $dto = CreateEventDTO::fromRequest($validated);
        $this->eventService->updateEvent($event, $dto);

        // Sync ticket types
        $existingIds = [];
        foreach ($validated['ticket_types'] ?? [] as $ttData) {
            if (!empty($ttData['id'])) {
                $tt = TicketType::findOrFail($ttData['id']);
                $tt->update([
                    'name' => $ttData['name'],
                    'price' => $ttData['price'],
                    'quantity' => $ttData['quantity'],
                    'available' => $ttData['quantity'] - ($tt->quantity - $tt->available),
                    'sale_start' => $ttData['sale_start'],
                    'sale_end' => $ttData['sale_end'],
                ]);
                $existingIds[] = $tt->id;
            } else {
                $tt = TicketType::create([
                    'event_id' => $event->id,
                    'name' => $ttData['name'],
                    'price' => $ttData['price'],
                    'quantity' => $ttData['quantity'],
                    'available' => $ttData['quantity'],
                    'sale_start' => $ttData['sale_start'],
                    'sale_end' => $ttData['sale_end'],
                ]);
                $existingIds[] = $tt->id;
            }
        }
        // Delete removed ticket types (only those without sales)
        $event->ticketTypes()->whereNotIn('id', $existingIds)
            ->whereDoesntHave('orderItems')
            ->delete();

        // Sync custom fields
        $existingFieldIds = [];
        foreach ($validated['custom_fields'] ?? [] as $index => $cfData) {
            if (!empty($cfData['id'])) {
                $cf = CustomField::findOrFail($cfData['id']);
                $cf->update([
                    'label' => $cfData['label'],
                    'type' => $cfData['type'],
                    'required' => $cfData['required'] ?? false,
                    'options' => $cfData['type'] === 'select' && !empty($cfData['options'])
                        ? array_map('trim', explode(',', $cfData['options']))
                        : null,
                    'position' => $cfData['position'] ?? $index,
                ]);
                $existingFieldIds[] = $cf->id;
            } else {
                $cf = CustomField::create([
                    'event_id' => $event->id,
                    'label' => $cfData['label'],
                    'type' => $cfData['type'],
                    'required' => $cfData['required'] ?? false,
                    'options' => $cfData['type'] === 'select' && !empty($cfData['options'])
                        ? array_map('trim', explode(',', $cfData['options']))
                        : null,
                    'position' => $cfData['position'] ?? $index,
                ]);
                $existingFieldIds[] = $cf->id;
            }
        }
        $event->customFields()->whereNotIn('id', $existingFieldIds)
            ->whereDoesntHave('values')
            ->delete();

        return redirect()->route('dashboard.events.edit', $event)->with('success', 'Event updated.');
    }

    public function publish(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        try {
            $this->eventService->publishEvent($event);
            return redirect()->back()->with('success', 'Event published!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        try {
            $this->eventService->cancelEvent($event);
            return redirect()->back()->with('success', 'Event cancelled.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
```

- [ ] **Step 3: Add event CRUD routes to `routes/web.php`**

Add inside the `Route::prefix('dashboard')->middleware(EnsureHasOrganizer::class)` group:

```php
use App\Http\Controllers\Web\Dashboard\DashboardEventController;

Route::get('events', [DashboardEventController::class, 'index'])->name('dashboard.events');
Route::get('events/create', [DashboardEventController::class, 'create'])->name('dashboard.events.create');
Route::post('events', [DashboardEventController::class, 'store'])->name('dashboard.events.store');
Route::get('events/{event}/edit', [DashboardEventController::class, 'edit'])->name('dashboard.events.edit');
Route::put('events/{event}', [DashboardEventController::class, 'update'])->name('dashboard.events.update');
Route::patch('events/{event}/publish', [DashboardEventController::class, 'publish'])->name('dashboard.events.publish');
Route::patch('events/{event}/cancel', [DashboardEventController::class, 'cancel'])->name('dashboard.events.cancel');
```

- [ ] **Step 4: Create events index view, create view, and edit view**

These are larger Blade files. The implementer should follow the patterns established in Tasks 4-5:
- `events/index.blade.php`: Table with title, date, status badge, sold/capacity, revenue, action buttons (edit, publish, cancel). Filter dropdown by status.
- `events/create.blade.php`: Form with basic info fields (title, description, location, address, city, state, start_date, end_date, banner upload). Uses `x-input`, `x-textarea`, `x-select` components.
- `events/edit.blade.php`: Same as create but pre-populated. Adds inline ticket types section (Alpine.js dynamic rows) and custom fields section (Alpine.js dynamic rows). Publish/cancel buttons shown based on event status.

The edit view should use Alpine.js `x-data` for dynamic ticket type and custom field management with add/remove row functionality.

- [ ] **Step 5: Run tests, verify pass**

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: implement dashboard event CRUD with ticket types and custom fields"
```

---

## Task 7: Dashboard Orders, Participants, and Tickets

**Files:**
- Create: `app/Http/Controllers/Web/Dashboard/OrderController.php`
- Create: `app/Http/Controllers/Web/Dashboard/ParticipantController.php`
- Create: `app/Http/Controllers/Web/Dashboard/TicketController.php`
- Create: `resources/views/dashboard/events/orders.blade.php`
- Create: `resources/views/dashboard/events/order-show.blade.php`
- Create: `resources/views/dashboard/events/participants.blade.php`
- Create: `resources/views/dashboard/events/tickets.blade.php`

- [ ] **Step 1: Create Dashboard OrderController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $orders = $event->orders()
            ->with('user', 'items.ticketType', 'payment')
            ->latest()
            ->paginate(15);

        return view('dashboard.events.orders', compact('event', 'orders'));
    }

    public function show(Request $request, Event $event, Order $order): View
    {
        $this->authorize('manage', $event);
        abort_if($order->event_id !== $event->id, 404);

        $order->load('user', 'items.ticketType', 'payment');
        $tickets = \App\Models\Ticket::whereIn('order_item_id', $order->items->pluck('id'))
            ->with('participant')
            ->get();

        return view('dashboard.events.order-show', compact('event', 'order', 'tickets'));
    }
}
```

- [ ] **Step 2: Create Dashboard ParticipantController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $query = Participant::whereHas('ticket', fn ($q) => $q->where('event_id', $event->id))
            ->with('ticket.ticketType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%");
            });
        }

        $participants = $query->paginate(15);

        return view('dashboard.events.participants', compact('event', 'participants'));
    }

    public function export(Request $request, Event $event): StreamedResponse
    {
        $this->authorize('manage', $event);

        $query = Participant::whereHas('ticket', fn ($q) => $q->where('event_id', $event->id))
            ->with('ticket.ticketType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%");
            });
        }

        $participants = $query->get();

        return response()->streamDownload(function () use ($participants) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Document', 'Ticket Type', 'Ticket Status', 'Check-in']);
            foreach ($participants as $p) {
                fputcsv($handle, [
                    $p->name,
                    $p->email,
                    $p->phone,
                    $p->document,
                    $p->ticket->ticketType->name ?? '',
                    $p->ticket->status->value ?? '',
                    $p->ticket->checked_in_at?->format('d/m/Y H:i') ?? '',
                ]);
            }
            fclose($handle);
        }, "participants-{$event->slug}.csv", ['Content-Type' => 'text/csv']);
    }
}
```

- [ ] **Step 3: Create Dashboard TicketController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $query = $event->tickets()->with('participant', 'ticketType');

        if ($request->filled('search')) {
            $query->where('ticket_code', 'like', '%' . $request->search . '%');
        }

        $tickets = $query->paginate(15);

        return view('dashboard.events.tickets', compact('event', 'tickets'));
    }
}
```

- [ ] **Step 4: Add orders/participants/tickets routes to `routes/web.php`**

Add inside the dashboard middleware group:

```php
use App\Http\Controllers\Web\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Web\Dashboard\ParticipantController;
use App\Http\Controllers\Web\Dashboard\TicketController as DashboardTicketController;

Route::get('events/{event}/orders', [DashboardOrderController::class, 'index'])->name('dashboard.orders');
Route::get('events/{event}/orders/{order}', [DashboardOrderController::class, 'show'])->name('dashboard.orders.show');
Route::get('events/{event}/participants', [ParticipantController::class, 'index'])->name('dashboard.participants');
Route::get('events/{event}/participants/export', [ParticipantController::class, 'export'])->name('dashboard.participants.export');
Route::get('events/{event}/tickets', [DashboardTicketController::class, 'index'])->name('dashboard.tickets');
```

- [ ] **Step 5: Add tests to DashboardTest for orders, participants, and tickets**

Add these tests to `tests/Feature/Web/DashboardTest.php`:

```php
public function test_orders_page_renders(): void
{
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);
    Order::factory()->create(['event_id' => $event->id]);

    $response = $this->actingAs($organizer->user)->get("/dashboard/events/{$event->id}/orders");

    $response->assertOk();
}

public function test_order_detail_page_renders(): void
{
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);
    $order = Order::factory()->create(['event_id' => $event->id]);

    $response = $this->actingAs($organizer->user)->get("/dashboard/events/{$event->id}/orders/{$order->id}");

    $response->assertOk();
}

public function test_cannot_view_other_organizer_orders(): void
{
    $organizer1 = Organizer::factory()->create();
    $organizer2 = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer1->id]);

    $response = $this->actingAs($organizer2->user)->get("/dashboard/events/{$event->id}/orders");

    $response->assertForbidden();
}

public function test_participants_page_renders_with_search(): void
{
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);

    $response = $this->actingAs($organizer->user)->get("/dashboard/events/{$event->id}/participants?search=john");

    $response->assertOk();
}

public function test_participants_csv_export(): void
{
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);

    $response = $this->actingAs($organizer->user)->get("/dashboard/events/{$event->id}/participants/export");

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
}

public function test_tickets_page_renders_with_search(): void
{
    $organizer = Organizer::factory()->create();
    $event = Event::factory()->create(['organizer_id' => $organizer->id]);

    $response = $this->actingAs($organizer->user)->get("/dashboard/events/{$event->id}/tickets?search=TKT");

    $response->assertOk();
}
```

- [ ] **Step 6: Create the 4 Blade views**

- `orders.blade.php`: Paginated table with Order #, Buyer, Qty (sum of items), Total, Status badge, Payment method (PIX/CREDIT_CARD), Payment status, Date. Link to order detail.
- `order-show.blade.php`: Full order info — buyer details, items table (ticket type, qty, price), participants per ticket, payment info.
- `participants.blade.php`: Search form, paginated table (Name, Email, Phone, Document, Ticket Type, Status badge, Check-in badge), Export CSV button (preserves search query).
- `tickets.blade.php`: Search by ticket code, paginated table (Ticket Code, Participant, Type, Status badge, Check-in time).

All views extend `x-layouts.dashboard` and use existing components.

- [ ] **Step 7: Run tests, verify pass**

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat: implement dashboard orders, participants, and tickets views"
```

---

## Task 8: Checkout Flow

**Files:**
- Create: `app/Http/Controllers/Web/CheckoutController.php`
- Create: `resources/views/checkout/show.blade.php`
- Create: `resources/views/checkout/payment.blade.php`
- Create: `resources/views/checkout/success.blade.php`
- Create: `resources/views/checkout/cancel.blade.php`
- Create: `tests/Feature/Web/CheckoutTest.php`

- [ ] **Step 1: Write CheckoutTest**

```php
<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function setupEvent(): array
    {
        $organizer = Organizer::factory()->create(['asaas_account_id' => 'acc_123']);
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 100,
            'quantity' => 10,
            'available' => 10,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addMonth(),
        ]);
        return [$event, $ticketType];
    }

    public function test_can_create_order_via_checkout(): void
    {
        Http::fake(['*' => Http::response(['id' => 'pay_123', 'status' => 'PENDING'])]);
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/checkout/order', [
            'event_id' => $event->id,
            'items' => [
                $ticketType->id => ['ticket_type_id' => $ticketType->id, 'quantity' => 2],
            ],
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $response->assertRedirect("/checkout/{$order->id}");
    }

    public function test_checkout_shows_participant_forms(): void
    {
        Http::fake(['*' => Http::response(['id' => 'pay_123', 'status' => 'PENDING'])]);
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();

        $this->actingAs($user)->post('/checkout/order', [
            'event_id' => $event->id,
            'items' => [$ticketType->id => ['ticket_type_id' => $ticketType->id, 'quantity' => 1]],
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $response = $this->actingAs($user)->get("/checkout/{$order->id}");

        $response->assertOk();
        $response->assertSee('Participant');
    }

    public function test_expired_order_redirects_to_event(): void
    {
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => OrderStatus::AWAITING_PAYMENT,
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($user)->get("/checkout/{$order->id}");

        $response->assertRedirect("/event/{$event->slug}");
    }

    public function test_cannot_access_other_users_order(): void
    {
        [$event, $ticketType] = $this->setupEvent();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user1->id, 'event_id' => $event->id]);

        $response = $this->actingAs($user2)->get("/checkout/{$order->id}");

        $response->assertForbidden();
    }

    public function test_checkout_status_returns_json(): void
    {
        [$event, $ticketType] = $this->setupEvent();
        $user = User::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $user->id, 'event_id' => $event->id]);

        $response = $this->actingAs($user)->getJson("/checkout/{$order->id}/status");

        $response->assertOk();
        $response->assertJson(['status' => 'paid']);
    }

    public function test_unauthenticated_checkout_redirects_to_login(): void
    {
        $response = $this->post('/checkout/order', ['event_id' => 1]);

        $response->assertRedirect('/login');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

- [ ] **Step 3: Create CheckoutController**

```php
<?php

namespace App\Http\Controllers\Web;

use App\DTO\CreateOrderDTO;
use App\DTO\CreateParticipantDTO;
use App\Enums\BillingType;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\OrderService;
use App\Services\ParticipantService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function createOrder(Request $request, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'items' => 'required|array|min:1',
            'items.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $event = Event::findOrFail($validated['event_id']);

        // Build items with empty participants (will be filled in checkout form)
        $items = [];
        foreach ($validated['items'] as $item) {
            if ($item['quantity'] < 1) continue;
            $participants = [];
            for ($i = 0; $i < $item['quantity']; $i++) {
                $participants[] = ['name' => 'Pending', 'email' => 'pending@pending.com'];
            }
            $items[] = [
                'ticket_type_id' => $item['ticket_type_id'],
                'quantity' => $item['quantity'],
                'participants' => $participants,
            ];
        }

        if (empty($items)) {
            return back()->with('error', 'Please select at least one ticket.');
        }

        try {
            $dto = new CreateOrderDTO(
                eventId: $event->id,
                billingType: BillingType::PIX, // placeholder, real billing type selected at payment
                items: $items,
            );
            $order = $orderService->createOrder($request->user(), $dto);
            return redirect()->route('checkout.show', $order);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Request $request, Order $order): View|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        $order->load('event.customFields', 'items.ticketType');
        $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))
            ->with('participant')
            ->get();

        return view('checkout.show', compact('order', 'tickets'));
    }

    public function saveParticipants(Request $request, Order $order, ParticipantService $participantService): RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        $validated = $request->validate([
            'participants' => 'required|array',
            'participants.*.ticket_id' => 'required|exists:tickets,id',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.email' => 'required|email',
            'participants.*.phone' => 'nullable|string',
            'participants.*.document' => 'nullable|string',
            'participants.*.custom_fields' => 'nullable|array',
        ]);

        foreach ($validated['participants'] as $data) {
            $ticket = Ticket::findOrFail($data['ticket_id']);
            // Update existing participant
            if ($ticket->participant) {
                $ticket->participant->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'document' => $data['document'] ?? null,
                ]);
            }
            // Handle custom fields
            if (!empty($data['custom_fields'])) {
                foreach ($data['custom_fields'] as $fieldId => $value) {
                    \App\Models\ParticipantFieldValue::updateOrCreate(
                        ['participant_id' => $ticket->participant->id, 'custom_field_id' => $fieldId],
                        ['value' => $value]
                    );
                }
            }
        }

        return redirect()->route('checkout.payment', $order);
    }

    public function payment(Request $request, Order $order): View|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        if ($order->status === OrderStatus::PAID) {
            return redirect()->route('checkout.success', ['order' => $order->id]);
        }

        $order->load('event', 'items.ticketType', 'payment');

        return view('checkout.payment', compact('order'));
    }

    public function processPayment(Request $request, Order $order, PaymentService $paymentService): View|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        $validated = $request->validate([
            'billing_type' => 'required|in:PIX,CREDIT_CARD',
        ]);

        $billingType = BillingType::from($validated['billing_type']);

        // If payment already exists, reuse it
        $payment = $order->payment;
        if (!$payment) {
            $payment = $paymentService->createPayment($order, $billingType);
            $order->update(['status' => OrderStatus::AWAITING_PAYMENT]);
        }

        if ($billingType === BillingType::PIX) {
            $pixData = $paymentService->getPixQrCode($payment);
            $order->load('event', 'items.ticketType');
            return view('checkout.payment', compact('order', 'pixData'));
        }

        // Credit Card: redirect to Asaas hosted payment page
        // The payment record stores the Asaas invoice URL for redirect
        if ($payment->invoice_url) {
            return redirect()->away($payment->invoice_url);
        }

        // Fallback: if no invoice URL available, redirect to success (webhook will confirm payment)
        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        return response()->json([
            'status' => $order->fresh()->status->value,
        ]);
    }

    public function success(Request $request): View
    {
        $order = Order::where('id', $request->query('order'))
            ->where('user_id', $request->user()->id)
            ->with('event', 'items.ticketType')
            ->firstOrFail();

        $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))
            ->with('participant')
            ->get();

        return view('checkout.success', compact('order', 'tickets'));
    }

    public function cancel(Request $request): View
    {
        $order = Order::where('id', $request->query('order'))
            ->where('user_id', $request->user()->id)
            ->with('event')
            ->first();

        return view('checkout.cancel', compact('order'));
    }
}
```

- [ ] **Step 4: Add checkout routes to `routes/web.php`**

Add inside the `Route::middleware('auth')` group:

```php
use App\Http\Controllers\Web\CheckoutController;

Route::post('checkout/order', [CheckoutController::class, 'createOrder'])->name('checkout.order');
Route::get('checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::get('checkout/{order}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('checkout/{order}', [CheckoutController::class, 'saveParticipants'])->name('checkout.participants');
Route::get('checkout/{order}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('checkout/{order}/payment', [CheckoutController::class, 'processPayment'])->name('checkout.processPayment');
Route::get('checkout/{order}/status', [CheckoutController::class, 'status'])->name('checkout.status');
```

- [ ] **Step 5: Create checkout Blade views**

- `checkout/show.blade.php`: Uses checkout layout. Shows order summary sidebar, countdown timer, participant forms (one per ticket with name, email, phone, document, custom fields). "Continue to Payment" button.
- `checkout/payment.blade.php`: Payment method selection (PIX / Credit Card). If PIX data available, shows QR code image and copy-paste code with Alpine.js polling. If Credit Card, submit button redirects to Asaas.
- `checkout/success.blade.php`: Order confirmation with order number, event name, total. Ticket list with status badges ("Processing..." for tickets without codes).
- `checkout/cancel.blade.php`: "Payment not completed" message. "Try Again" button (if order not expired). "Browse Events" button.

- [ ] **Step 6: Run tests, verify pass**

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: implement checkout flow with participant forms and payment"
```

---

## Task 9: My Tickets

**Files:**
- Create: `app/Http/Controllers/Web/MyTicketsController.php`
- Create: `resources/views/my-tickets/index.blade.php`
- Create: `resources/views/my-tickets/show.blade.php`

- [ ] **Step 1: Create MyTicketsController**

```php
<?php

namespace App\Http\Controllers\Web;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyTicketsController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::whereHas('orderItem.order', fn ($q) => $q->where('user_id', $request->user()->id))
            ->where('status', '!=', TicketStatus::CANCELLED)
            ->with(['participant', 'event', 'ticketType'])
            ->get()
            ->groupBy('event_id');

        return view('my-tickets.index', compact('tickets'));
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless($ticket->orderItem->order->user_id === $request->user()->id, 403);

        $ticket->load('participant', 'event', 'ticketType');

        return view('my-tickets.show', compact('ticket'));
    }
}
```

- [ ] **Step 2: Add My Tickets routes to `routes/web.php`**

Add inside the `Route::middleware('auth')` group:

```php
use App\Http\Controllers\Web\MyTicketsController;

Route::get('my-tickets', [MyTicketsController::class, 'index'])->name('my-tickets');
Route::get('my-tickets/{ticket}', [MyTicketsController::class, 'show'])->name('my-tickets.show');
```

- [ ] **Step 3: Create MyTicketsTest**

Create `tests/Feature/Web/MyTicketsTest.php`:

```php
<?php

namespace Tests\Feature\Web;

use App\Enums\TicketStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function test_my_tickets_page_renders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/my-tickets');

        $response->assertOk();
    }

    public function test_my_tickets_shows_user_tickets(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $user->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);
        $ticket = Ticket::factory()->create([
            'order_item_id' => $orderItem->id,
            'event_id' => $order->event_id,
            'status' => TicketStatus::VALID,
        ]);

        $response = $this->actingAs($user)->get('/my-tickets');

        $response->assertOk();
    }

    public function test_cannot_view_other_users_ticket(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $user1->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);
        $ticket = Ticket::factory()->create([
            'order_item_id' => $orderItem->id,
            'event_id' => $order->event_id,
        ]);

        $response = $this->actingAs($user2)->get("/my-tickets/{$ticket->id}");

        $response->assertForbidden();
    }
}
```

- [ ] **Step 4: Create my-tickets views**

- `my-tickets/index.blade.php`: Uses app layout. Tickets grouped by event (use `@foreach($tickets as $eventId => $eventTickets)`). Each ticket card: event name, date, ticket type, participant name, status badge.
- `my-tickets/show.blade.php`: Full ticket info. QR code rendered client-side using qrcode.js CDN (`<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>`). Ticket code displayed as text.

- [ ] **Step 5: Run tests, verify pass**

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: implement My Tickets page with QR code display"
```

---

## Task 10: Dashboard Check-in + Undo Service

**Files:**
- Modify: `app/Services/CheckinService.php` — add `undoCheckin()` method
- Create: `app/Http/Controllers/Web/Dashboard/CheckinController.php`
- Create: `resources/views/dashboard/checkin.blade.php`
- Create: `tests/Feature/Web/CheckinWebTest.php`

- [ ] **Step 1: Write CheckinWebTest**

```php
<?php

namespace Tests\Feature\Web;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinWebTest extends TestCase
{
    use RefreshDatabase;

    private function setupCheckinScenario(): array
    {
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $order = Order::factory()->paid()->create(['event_id' => $event->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id, 'ticket_type_id' => $ticketType->id]);
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'order_item_id' => $orderItem->id,
            'status' => TicketStatus::VALID,
        ]);
        Participant::factory()->create(['ticket_id' => $ticket->id]);

        return [$organizer, $event, $ticket];
    }

    public function test_checkin_page_renders(): void
    {
        $organizer = Organizer::factory()->create();
        Event::factory()->create([
            'organizer_id' => $organizer->id,
            'status' => EventStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($organizer->user)->get('/dashboard/checkin');

        $response->assertOk();
        $response->assertSee('Check-in');
    }

    public function test_can_validate_ticket(): void
    {
        [$organizer, $event, $ticket] = $this->setupCheckinScenario();

        $response = $this->actingAs($organizer->user)->post('/dashboard/checkin/validate', [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'valid']);
        $this->assertEquals(TicketStatus::USED, $ticket->fresh()->status);
    }

    public function test_can_undo_checkin(): void
    {
        [$organizer, $event, $ticket] = $this->setupCheckinScenario();

        // First check in
        $ticket->update(['status' => TicketStatus::USED, 'checked_in_at' => now()]);
        Checkin::create([
            'ticket_id' => $ticket->id,
            'checked_by' => $organizer->user->id,
            'checked_at' => now(),
        ]);

        // Then undo
        $response = $this->actingAs($organizer->user)->post('/dashboard/checkin/undo', [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'undone']);
        $this->assertEquals(TicketStatus::VALID, $ticket->fresh()->status);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

- [ ] **Step 3: Add `undoCheckin()` to CheckinService**

Add this method to `app/Services/CheckinService.php`:

```php
public function undoCheckin(string $ticketCode, User $user): array
{
    $ticket = $this->ticketService->validateTicket($ticketCode);

    if (! $ticket) {
        return ['status' => 'invalid'];
    }

    if ($ticket->status !== TicketStatus::USED) {
        return ['status' => 'not_checked_in'];
    }

    $ticket->update([
        'status' => TicketStatus::VALID,
        'checked_in_at' => null,
    ]);

    // Delete the most recent checkin record
    $ticket->checkins()->latest('checked_at')->first()?->delete();

    // Log the undo action for audit trail
    \Illuminate\Support\Facades\Log::info('Check-in undone', [
        'ticket_id' => $ticket->id,
        'ticket_code' => $ticket->ticket_code,
        'undone_by' => $user->id,
        'undone_at' => now()->toIso8601String(),
    ]);

    return ['status' => 'undone'];
}
```

- [ ] **Step 4: Create Dashboard CheckinController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\CheckinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinController extends Controller
{
    public function __construct(private readonly CheckinService $checkinService) {}

    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $events = $organizer->events()
            ->where('status', EventStatus::PUBLISHED)
            ->withCount(['tickets', 'tickets as checked_in_count' => fn ($q) => $q->where('status', TicketStatus::USED)])
            ->get();

        return view('dashboard.checkin', compact('events'));
    }

    public function validateTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_code' => 'required_without:qr_code_payload|string',
            'qr_code_payload' => 'required_without:ticket_code|string',
        ]);

        $isQr = isset($validated['qr_code_payload']);
        $input = $isQr ? $validated['qr_code_payload'] : $validated['ticket_code'];

        // Verify ticket belongs to organizer's events before check-in
        $organizer = $request->user()->organizer;
        $ticket = \App\Models\Ticket::where('ticket_code', $isQr ? null : $input)
            ->orWhere('qr_code_payload', $isQr ? $input : null)
            ->first();

        if ($ticket && !$organizer->events()->where('id', $ticket->event_id)->exists()) {
            return response()->json(['status' => 'unauthorized', 'message' => 'This ticket does not belong to your events.'], 403);
        }

        $result = $this->checkinService->performCheckin($input, $request->user(), $isQr);

        $statusCode = match ($result['status']) {
            'valid' => 200,
            'already_used' => 409,
            default => 404,
        };

        return response()->json($result, $statusCode);
    }

    public function undo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_code' => 'required|string',
        ]);

        // Verify ticket belongs to organizer's events before undo
        $organizer = $request->user()->organizer;
        $ticket = \App\Models\Ticket::where('ticket_code', $validated['ticket_code'])->first();

        if ($ticket && !$organizer->events()->where('id', $ticket->event_id)->exists()) {
            return response()->json(['status' => 'unauthorized', 'message' => 'This ticket does not belong to your events.'], 403);
        }

        $result = $this->checkinService->undoCheckin($validated['ticket_code'], $request->user());

        return response()->json($result, $result['status'] === 'undone' ? 200 : 404);
    }
}
```

- [ ] **Step 5: Add check-in routes to `routes/web.php`**

Add inside the dashboard middleware group:

```php
use App\Http\Controllers\Web\Dashboard\CheckinController;

Route::get('checkin', [CheckinController::class, 'index'])->name('dashboard.checkin');
Route::post('checkin/validate', [CheckinController::class, 'validateTicket'])->name('dashboard.checkin.validate');
Route::post('checkin/undo', [CheckinController::class, 'undo'])->name('dashboard.checkin.undo');
```

Note: The method is named `validateTicket` instead of `validate` to avoid conflict with the base Controller `validate` method.

- [ ] **Step 6: Create checkin view (`dashboard/checkin.blade.php`)**

The view should include:
- Event selector dropdown (Alpine.js)
- Manual check-in: text input + submit button
- QR Scanner: using html5-qrcode CDN (`<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>`)
- Result display area (green/red/yellow based on status)
- Stats display (checked-in / total for selected event)
- Undo button (shown after successful check-in)
- All check-in/undo actions use `fetch()` for AJAX calls, display results without page reload

- [ ] **Step 7: Run tests, verify pass**

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat: implement check-in page with QR scanner and undo functionality"
```

---

## Task 11: Postman Collection

**Files:**
- Create: `docs/taketicket-api.postman_collection.json`

- [ ] **Step 1: Generate Postman collection**

Create a comprehensive Postman collection JSON file covering all API endpoints. Use variables `{{base_url}}` (default `http://localhost:8080/api/v1`) and `{{token}}` for Bearer auth.

**Folders:**
1. Auth: register, login, logout, me
2. Organizers: create, get profile, update
3. Events (Public): list, show by slug
4. Events (Organizer): list, create, show, update, publish, cancel
5. Ticket Types: create, update, delete
6. Custom Fields: list, create, update, delete
7. Orders: create, my orders, show
8. Checkout: POST checkout/order, GET checkout/{order}/status
9. Webhooks: Asaas webhook
10. Tickets: my tickets, show
11. Check-in: validate, undo
12. Dashboard: summary, orders, participants, tickets

Each request should include example body, headers (`Accept: application/json`, `Authorization: Bearer {{token}}`), and the correct HTTP method.

- [ ] **Step 2: Commit**

```bash
git add docs/taketicket-api.postman_collection.json
git commit -m "docs: add Postman collection for all API endpoints"
```

---

## Task 12: Build Assets + Final Cleanup

**Files:**
- Modify: `.gitignore` — ensure `public/build` is NOT ignored
- Run: `npm install && npm run build`
- Run: Laravel Pint
- Run: full test suite

- [ ] **Step 1: Install npm dependencies and build**

```bash
npm install
npm run build
```

- [ ] **Step 2: Ensure `public/build` is committed**

Check `.gitignore` — if it contains `/public/build`, remove that line so built assets are committed to the repo for shared hosting.

- [ ] **Step 3: Run Laravel Pint**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php ./vendor/bin/pint
```

- [ ] **Step 4: Run full test suite**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test
```

All tests (existing 78 + new web tests) must pass.

- [ ] **Step 5: Final commit**

```bash
git add -A
git commit -m "chore: build assets, code style cleanup, and finalize web interface"
```

---

## Summary

| Task | Description | Dependencies | Parallelizable |
|------|-------------|--------------|----------------|
| 1 | Layouts, Components, Alpine.js | None | No |
| 2 | Auth (login, register, logout) | 1 | No |
| 3 | Middleware + Route skeleton | 2 | No |
| 4 | Public pages (home, event) | 3 | Yes |
| 5 | Dashboard onboarding + summary | 3 | Yes |
| 6 | Dashboard event CRUD | 5 | No |
| 7 | Dashboard orders/participants/tickets | 6 | No |
| 8 | Checkout flow | 3 | Yes |
| 9 | My Tickets | 3 | Yes |
| 10 | Check-in + undo service | 3 | Yes |
| 11 | Postman collection | None | Yes |
| 12 | Build assets + cleanup | All | Last |
