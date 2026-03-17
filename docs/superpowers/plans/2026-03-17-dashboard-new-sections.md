# Dashboard New Sections Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add three new dashboard sections — Configurações da Conta, Financeiro, and Participantes Global — with sidebar navigation.

**Architecture:** Each section gets its own controller in `app/Http/Controllers/Web/Dashboard/`, a Blade view in `resources/views/dashboard/`, and routes registered inside the existing `dashboard` prefix + `EnsureHasOrganizer` middleware group. The sidebar in `components/layouts/dashboard.blade.php` is updated last to link all new sections. No new migrations needed — all data is available in existing models.

**Tech Stack:** Laravel 12, Blade, TailwindCSS, Alpine.js. PHP binary: `/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php`. Tests run with `php artisan test`.

---

## File Structure

**New files:**
- `app/Http/Controllers/Web/Dashboard/SettingsController.php` — show/update organizer settings + update user password
- `app/Http/Controllers/Web/Dashboard/FinancialController.php` — financial overview (read-only)
- `app/Http/Controllers/Web/Dashboard/GlobalParticipantController.php` — global participant list across all events
- `resources/views/dashboard/settings.blade.php` — settings form view
- `resources/views/dashboard/financeiro.blade.php` — financial overview view
- `resources/views/dashboard/participantes.blade.php` — global participants view
- `tests/Feature/Web/SettingsTest.php` — settings tests
- `tests/Feature/Web/FinancialTest.php` — financial tests
- `tests/Feature/Web/GlobalParticipantsTest.php` — global participants tests

**Modified files:**
- `routes/web.php` — add 4 new routes
- `resources/views/components/layouts/dashboard.blade.php` — add 3 new sidebar items

---

### Task 1: Configurações da Conta — Controller + Routes + Tests

**Files:**
- Create: `app/Http/Controllers/Web/Dashboard/SettingsController.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Web/SettingsTest.php`

**Context:**
- Organizer model fillable: `name`, `slug`, `description`, `logo`, `document`, `phone`, `address`, `city`, `state`, `postal_code`, `asaas_account_id`
- User model fillable: `name`, `email`, `password`, `phone`, `document`
- Logo stored in `storage/logos` disk `public`, similar to banner in events
- Existing pattern: `$request->user()->organizer` to get organizer
- `HasSlug` trait auto-regenerates slug when name changes — do NOT update slug manually
- Password update: validate `current_password` + `new_password` + `new_password_confirmation`

- [ ] **Step 1: Write failing tests**

```php
<?php

namespace Tests\Feature\Web;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithOrganizer(): User
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        Organizer::factory()->create(['user_id' => $user->id]);
        return $user;
    }

    public function test_settings_page_renders(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->get('/dashboard/settings');
        $response->assertStatus(200);
        $response->assertSee('Configurações');
    }

    public function test_unauthenticated_redirected(): void
    {
        $this->get('/dashboard/settings')->assertRedirect('/login');
    }

    public function test_organizer_profile_can_be_updated(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->put('/dashboard/settings/organizer', [
            'name' => 'Novo Nome',
            'description' => 'Descrição atualizada',
            'phone' => '11999999999',
            'document' => '12345678000199',
            'address' => 'Rua Nova, 100',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01310100',
        ]);
        $response->assertRedirect('/dashboard/settings');
        $this->assertDatabaseHas('organizers', ['name' => 'Novo Nome', 'city' => 'São Paulo']);
    }

    public function test_password_can_be_changed(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->put('/dashboard/settings/password', [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect('/dashboard/settings');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_wrong_current_password_rejected(): void
    {
        $user = $this->userWithOrganizer();
        $response = $this->actingAs($user)->put('/dashboard/settings/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);
        $response->assertSessionHasErrors('current_password');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
cd /Users/joaofilipibritto/Projetos/projeto-taketicket/api-taketicket
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/SettingsTest.php
```
Expected: FAIL — "Target class [App\Http\Controllers\Web\Dashboard\SettingsController] does not exist"

- [ ] **Step 3: Create SettingsController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        return view('dashboard.settings', compact('organizer'));
    }

    public function updateOrganizer(Request $request): RedirectResponse
    {
        $organizer = $request->user()->organizer;

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'document'    => 'nullable|string|max:18',
            'address'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:2',
            'postal_code' => 'nullable|string|max:9',
            'logo'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($validated['logo']);
        }

        $organizer->update($validated);

        return redirect()->route('dashboard.settings')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'      => ['required', 'current_password'],
            'new_password'          => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('dashboard.settings')->with('success', 'Senha alterada com sucesso.');
    }
}
```

- [ ] **Step 4: Add routes to routes/web.php**

Inside the `Route::prefix('dashboard')->middleware(EnsureHasOrganizer::class)->group(...)` block, add:

```php
// Settings
Route::get('settings', [SettingsController::class, 'index'])->name('dashboard.settings');
Route::put('settings/organizer', [SettingsController::class, 'updateOrganizer'])->name('dashboard.settings.organizer');
Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('dashboard.settings.password');
```

Also add the import at the top:
```php
use App\Http\Controllers\Web\Dashboard\SettingsController;
```

- [ ] **Step 5: Run tests — should pass**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/SettingsTest.php
```
Expected: 5 tests passing (the view test will fail until we create the view — that's OK, it's next)

- [ ] **Step 6: Create settings view**

Create `resources/views/dashboard/settings.blade.php`:

```blade
<x-layouts.dashboard header="Configurações">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Configurações</h2>
            <p class="text-gray-500 mt-1">Gerencie os dados do seu perfil e da sua conta.</p>
        </div>

        {{-- Organizer Profile --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900">Perfil do Organizador</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('dashboard.settings.organizer') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        {{-- Logo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                            @if($organizer->logo)
                                <img src="{{ asset('storage/' . $organizer->logo) }}" alt="Logo"
                                     class="mb-3 h-16 w-16 rounded-xl object-cover border border-gray-100 shadow-sm">
                            @endif
                            <input type="file" name="logo" accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                            @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <x-input label="Nome do Organizador" name="name" :value="$organizer->name" required />
                        <x-textarea label="Descrição" name="description" rows="3" :value="$organizer->description" />

                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="Telefone" name="phone" :value="$organizer->phone" class="mask-phone" />
                            <x-input label="CNPJ / CPF" name="document" :value="$organizer->document" class="mask-cnpj" />
                        </div>

                        <x-input label="Endereço" name="address" :value="$organizer->address" />

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <x-input label="Cidade" name="city" :value="$organizer->city" />
                            </div>
                            <x-input label="UF" name="state" :value="$organizer->state" maxlength="2" />
                        </div>

                        <x-input label="CEP" name="postal_code" :value="$organizer->postal_code" class="mask-cep" />

                        {{-- Asaas status --}}
                        <div class="rounded-xl border p-4 {{ $organizer->asaas_account_id ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200' }}">
                            <div class="flex items-center gap-2">
                                @if($organizer->asaas_account_id)
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-green-700">Conta de pagamentos conectada</span>
                                @else
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-amber-700">Conta de pagamentos pendente</span>
                                @endif
                            </div>
                            <p class="text-xs mt-1 {{ $organizer->asaas_account_id ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $organizer->asaas_account_id ? 'Pagamentos e repasses estão habilitados.' : 'A conta será criada automaticamente ao publicar seu primeiro evento.' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Salvar Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900">Alterar Senha</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('dashboard.settings.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <x-input label="Senha Atual" name="current_password" type="password" />
                        <x-input label="Nova Senha" name="new_password" type="password" />
                        <x-input label="Confirmar Nova Senha" name="new_password_confirmation" type="password" />
                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Alterar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.mask-phone').forEach(el => {
            IMask(el, { mask: '(00) 00000-0000' });
        });
        document.querySelectorAll('.mask-cnpj').forEach(el => {
            IMask(el, {
                mask: [{ mask: '000.000.000-00' }, { mask: '00.000.000/0000-00' }],
                dispatch: (appended, dynamicMasked) => {
                    const val = (dynamicMasked.value + appended).replace(/\D/g, '');
                    return dynamicMasked.compiledMasks[val.length > 11 ? 1 : 0];
                }
            });
        });
        document.querySelectorAll('.mask-cep').forEach(el => {
            IMask(el, { mask: '00000-000' });
        });
    </script>
    @endpush
</x-layouts.dashboard>
```

- [ ] **Step 7: Run all settings tests**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/SettingsTest.php
```
Expected: 5 tests, 5 passed

- [ ] **Step 8: Commit**

```bash
cd /Users/joaofilipibritto/Projetos/projeto-taketicket/api-taketicket
git add app/Http/Controllers/Web/Dashboard/SettingsController.php routes/web.php resources/views/dashboard/settings.blade.php tests/Feature/Web/SettingsTest.php
git commit -m "feat: adicionar tela de configurações da conta"
```

---

### Task 2: Financeiro — Controller + View + Tests

**Files:**
- Create: `app/Http/Controllers/Web/Dashboard/FinancialController.php`
- Modify: `routes/web.php`
- Create: `resources/views/dashboard/financeiro.blade.php`
- Create: `tests/Feature/Web/FinancialTest.php`

**Context:**
- `Order` model has: `organizer_amount`, `platform_fee`, `total_amount`, `status`
- `OrderStatus::PAID` is the only status that counts as revenue
- Access pattern: `$organizer->events()->...` then join to orders
- Data needed:
  - Total arrecadado = sum of `organizer_amount` from paid orders
  - Total de taxas = sum of `platform_fee` from paid orders
  - Total bruto = sum of `total_amount` from paid orders
  - Pedidos pagos count
  - Recent paid orders (15) with event title, buyer name, amount, date

- [ ] **Step 1: Write failing tests**

```php
<?php

namespace Tests\Feature\Web;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialTest extends TestCase
{
    use RefreshDatabase;

    private function userWithOrganizer(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        return [$user, $organizer];
    }

    public function test_financial_page_renders(): void
    {
        [$user] = $this->userWithOrganizer();
        $response = $this->actingAs($user)->get('/dashboard/financeiro');
        $response->assertStatus(200);
        $response->assertSee('Financeiro');
    }

    public function test_unauthenticated_redirected(): void
    {
        $this->get('/dashboard/financeiro')->assertRedirect('/login');
    }

    public function test_financial_shows_correct_totals(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $buyer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);

        Order::factory()->create([
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 100.00,
            'platform_fee' => 10.00,
            'organizer_amount' => 90.00,
        ]);
        Order::factory()->create([
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 200.00,
            'platform_fee' => 20.00,
            'organizer_amount' => 180.00,
        ]);
        // Pending order — should NOT count
        Order::factory()->create([
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'status' => OrderStatus::PENDING,
            'total_amount' => 50.00,
            'platform_fee' => 5.00,
            'organizer_amount' => 45.00,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/financeiro');
        $response->assertStatus(200);
        $response->assertSee('270'); // organizer_amount total (90+180)
        $response->assertSee('30');  // platform_fee total (10+20)
    }

    public function test_only_own_organizer_orders_shown(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $otherUser = User::factory()->create();
        $otherOrganizer = Organizer::factory()->create(['user_id' => $otherUser->id]);
        $otherEvent = Event::factory()->create(['organizer_id' => $otherOrganizer->id, 'status' => 'published']);

        Order::factory()->create([
            'event_id' => $otherEvent->id,
            'user_id' => $otherUser->id,
            'status' => OrderStatus::PAID,
            'total_amount' => 999.00,
            'platform_fee' => 99.00,
            'organizer_amount' => 900.00,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/financeiro');
        $response->assertStatus(200);
        $response->assertDontSee('900');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/FinancialTest.php
```
Expected: FAIL

- [ ] **Step 3: Create FinancialController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $eventIds = $organizer->events()->pluck('id');

        $paidOrders = Order::whereIn('event_id', $eventIds)
            ->where('status', OrderStatus::PAID);

        $totalGross     = (clone $paidOrders)->sum('total_amount');
        $totalFee       = (clone $paidOrders)->sum('platform_fee');
        $totalNet       = (clone $paidOrders)->sum('organizer_amount');
        $totalPaidCount = (clone $paidOrders)->count();

        $recentOrders = (clone $paidOrders)
            ->with(['user', 'event'])
            ->latest('updated_at')
            ->limit(15)
            ->get();

        return view('dashboard.financeiro', compact(
            'totalGross', 'totalFee', 'totalNet', 'totalPaidCount', 'recentOrders'
        ));
    }
}
```

- [ ] **Step 4: Add route to routes/web.php**

Inside the dashboard middleware group, add:
```php
// Financial
Route::get('financeiro', [FinancialController::class, 'index'])->name('dashboard.financeiro');
```

And import at top:
```php
use App\Http\Controllers\Web\Dashboard\FinancialController;
```

- [ ] **Step 5: Run tests — should pass (except view)**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/FinancialTest.php
```

- [ ] **Step 6: Create financial view**

Create `resources/views/dashboard/financeiro.blade.php`:

```blade
<x-layouts.dashboard header="Financeiro">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Financeiro</h2>
        <p class="text-gray-500 mt-1">Acompanhe seus recebimentos e o histórico de pedidos pagos.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Valor Líquido</p>
            <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalNet, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Após taxas da plataforma</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Bruto</p>
            <p class="text-3xl font-bold text-gray-900">R$ {{ number_format($totalGross, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Valor total arrecadado</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Taxa da Plataforma</p>
            <p class="text-3xl font-bold text-red-500">R$ {{ number_format($totalFee, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Total retido pela TakeTicket</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Pedidos Pagos</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalPaidCount }}</p>
            <p class="text-xs text-gray-400 mt-1">Total de transações confirmadas</p>
        </div>
    </div>

    {{-- Recent Paid Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Histórico de Recebimentos</h3>
            <p class="text-sm text-gray-500 mt-0.5">Últimos 15 pedidos pagos</p>
        </div>

        @if($recentOrders->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-700 font-semibold">Nenhum pagamento recebido ainda</p>
                <p class="text-gray-400 text-sm mt-1">Os recebimentos aparecerão aqui quando houver vendas confirmadas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 font-medium">Pedido</th>
                            <th class="px-6 py-3 font-medium">Comprador</th>
                            <th class="px-6 py-3 font-medium">Evento</th>
                            <th class="px-6 py-3 font-medium">Bruto</th>
                            <th class="px-6 py-3 font-medium">Taxa</th>
                            <th class="px-6 py-3 font-medium">Líquido</th>
                            <th class="px-6 py-3 font-medium">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $order->user->name }}</td>
                                <td class="px-6 py-4 text-gray-600 max-w-40 truncate">{{ $order->event->title }}</td>
                                <td class="px-6 py-4 text-gray-700">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-red-500">- R$ {{ number_format($order->platform_fee, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 font-semibold text-green-600">R$ {{ number_format($order->organizer_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layouts.dashboard>
```

- [ ] **Step 7: Run all financial tests**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/FinancialTest.php
```
Expected: 4 tests, 4 passed

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Web/Dashboard/FinancialController.php routes/web.php resources/views/dashboard/financeiro.blade.php tests/Feature/Web/FinancialTest.php
git commit -m "feat: adicionar tela financeira com histórico de recebimentos"
```

---

### Task 3: Participantes Global — Controller + View + Tests

**Files:**
- Create: `app/Http/Controllers/Web/Dashboard/GlobalParticipantController.php`
- Modify: `routes/web.php`
- Create: `resources/views/dashboard/participantes.blade.php`
- Create: `tests/Feature/Web/GlobalParticipantsTest.php`

**Context:**
- `Participant` model: `ticket_id`, `name`, `email`, `phone`, `document`
- `Ticket` model: `event_id`, `ticket_type_id`, `status`, `checked_in_at`
- Access pattern: participants → tickets → events → organizer
- Search: name, email, document
- Filter: by event_id
- Export: CSV (reuse pattern from existing ParticipantController@export)

- [ ] **Step 1: Write failing tests**

```php
<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalParticipantsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithOrganizer(): array
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create(['user_id' => $user->id]);
        return [$user, $organizer];
    }

    public function test_participants_page_renders(): void
    {
        [$user] = $this->userWithOrganizer();
        $response = $this->actingAs($user)->get('/dashboard/participantes');
        $response->assertStatus(200);
        $response->assertSee('Participantes');
    }

    public function test_unauthenticated_redirected(): void
    {
        $this->get('/dashboard/participantes')->assertRedirect('/login');
    }

    public function test_shows_participants_from_own_events(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        $participant = Participant::factory()->create(['ticket_id' => $ticket->id, 'name' => 'João Silva']);

        $response = $this->actingAs($user)->get('/dashboard/participantes');
        $response->assertSee('João Silva');
    }

    public function test_does_not_show_other_organizer_participants(): void
    {
        [$user] = $this->userWithOrganizer();
        $otherUser = User::factory()->create();
        $otherOrganizer = Organizer::factory()->create(['user_id' => $otherUser->id]);
        $otherEvent = Event::factory()->create(['organizer_id' => $otherOrganizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $otherEvent->id]);
        $ticket = Ticket::factory()->create(['event_id' => $otherEvent->id, 'ticket_type_id' => $ticketType->id]);
        Participant::factory()->create(['ticket_id' => $ticket->id, 'name' => 'Outro Participante']);

        $response = $this->actingAs($user)->get('/dashboard/participantes');
        $response->assertDontSee('Outro Participante');
    }

    public function test_search_filters_by_name(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $ticket1 = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        $ticket2 = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        Participant::factory()->create(['ticket_id' => $ticket1->id, 'name' => 'Maria Souza']);
        Participant::factory()->create(['ticket_id' => $ticket2->id, 'name' => 'Carlos Lima']);

        $response = $this->actingAs($user)->get('/dashboard/participantes?search=Maria');
        $response->assertSee('Maria Souza');
        $response->assertDontSee('Carlos Lima');
    }

    public function test_csv_export_works(): void
    {
        [$user, $organizer] = $this->userWithOrganizer();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'published']);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'ticket_type_id' => $ticketType->id]);
        Participant::factory()->create(['ticket_id' => $ticket->id, 'name' => 'Ana Teste']);

        $response = $this->actingAs($user)->get('/dashboard/participantes/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Ana Teste', $response->streamedContent());
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/GlobalParticipantsTest.php
```
Expected: FAIL

- [ ] **Step 3: Create GlobalParticipantController**

```php
<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GlobalParticipantController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $eventIds = $organizer->events()->pluck('id');
        $events = $organizer->events()->orderBy('start_date', 'desc')->get();

        $participants = Participant::whereHas('ticket', fn ($q) => $q->whereIn('event_id', $eventIds))
            ->with(['ticket.event', 'ticket.ticketType'])
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('document', 'like', "%{$s}%")
                )
            )
            ->when($request->event_id, fn ($q, $id) =>
                $q->whereHas('ticket', fn ($q) => $q->where('event_id', $id))
            )
            ->orderBy('name')
            ->paginate(20);

        return view('dashboard.participantes', compact('participants', 'events'));
    }

    public function export(Request $request): Response
    {
        $organizer = $request->user()->organizer;
        $eventIds = $organizer->events()->pluck('id');

        $participants = Participant::whereHas('ticket', fn ($q) => $q->whereIn('event_id', $eventIds))
            ->with(['ticket.event', 'ticket.ticketType'])
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('document', 'like', "%{$s}%")
                )
            )
            ->when($request->event_id, fn ($q, $id) =>
                $q->whereHas('ticket', fn ($q) => $q->where('event_id', $id))
            )
            ->orderBy('name')
            ->get();

        $csv = "Nome,E-mail,Telefone,Documento,Evento,Tipo de Ingresso,Check-in\n";
        foreach ($participants as $p) {
            $checkin = $p->ticket?->checked_in_at?->format('d/m/Y H:i') ?? '';
            $csv .= implode(',', [
                "\"{$p->name}\"",
                "\"{$p->email}\"",
                "\"{$p->phone}\"",
                "\"{$p->document}\"",
                "\"{$p->ticket?->event?->title}\"",
                "\"{$p->ticket?->ticketType?->name}\"",
                "\"{$checkin}\"",
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="participantes.csv"',
        ]);
    }
}
```

- [ ] **Step 4: Add routes to routes/web.php**

```php
// Global Participants
Route::get('participantes', [GlobalParticipantController::class, 'index'])->name('dashboard.participantes');
Route::get('participantes/export', [GlobalParticipantController::class, 'export'])->name('dashboard.participantes.export');
```

And import:
```php
use App\Http\Controllers\Web\Dashboard\GlobalParticipantController;
```

- [ ] **Step 5: Run tests**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/GlobalParticipantsTest.php
```

- [ ] **Step 6: Create participants view**

Create `resources/views/dashboard/participantes.blade.php`:

```blade
<x-layouts.dashboard header="Participantes">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Participantes</h2>
            <p class="text-gray-500 mt-1">Todos os participantes de todos os seus eventos.</p>
        </div>
        <a href="{{ route('dashboard.participantes.export', array_merge(['event_id' => request('event_id')], request()->only('search'))) }}"
           class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2.5 rounded-xl hover:bg-green-700 text-sm font-semibold transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar CSV
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('dashboard.participantes') }}" class="mb-6 flex items-center gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Buscar por nome, e-mail ou CPF..."
               class="rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border text-sm w-72"/>
        <select name="event_id" class="rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border text-sm bg-white">
            <option value="">Todos os eventos</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                    {{ $event->title }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 text-sm font-semibold transition">
            Buscar
        </button>
        @if(request('search') || request('event_id'))
            <a href="{{ route('dashboard.participantes') }}" class="text-sm text-gray-500 hover:underline">Limpar</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($participants->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-gray-700 font-semibold">Nenhum participante encontrado</p>
                <p class="text-gray-400 text-sm mt-1">Os participantes aparecerão aqui após a compra de ingressos.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 font-medium">Nome</th>
                            <th class="px-6 py-3 font-medium">E-mail</th>
                            <th class="px-6 py-3 font-medium">Documento</th>
                            <th class="px-6 py-3 font-medium">Evento</th>
                            <th class="px-6 py-3 font-medium">Tipo de Ingresso</th>
                            <th class="px-6 py-3 font-medium">Check-in</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $ticketStatusLabels = ['valid' => 'Válido', 'used' => 'Utilizado', 'cancelled' => 'Cancelado']; @endphp
                        @foreach($participants as $participant)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $participant->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $participant->email }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $participant->document ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600 max-w-36 truncate">{{ $participant->ticket?->event?->title ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $participant->ticket?->ticketType?->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($participant->ticket?->checked_in_at)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $participant->ticket->checked_in_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $participants->withQueryString()->links() }}
            </div>
        @endif
    </div>

</x-layouts.dashboard>
```

- [ ] **Step 7: Run all global participants tests**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Web/GlobalParticipantsTest.php
```
Expected: 5 tests, 5 passed

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Web/Dashboard/GlobalParticipantController.php routes/web.php resources/views/dashboard/participantes.blade.php tests/Feature/Web/GlobalParticipantsTest.php
git commit -m "feat: adicionar tela global de participantes com busca e export CSV"
```

---

### Task 4: Atualizar Sidebar com Novos Menus

**Files:**
- Modify: `resources/views/components/layouts/dashboard.blade.php`

**Context:**
- Current sidebar items: Dashboard (`/dashboard`), Eventos (`/dashboard/events`), Check-in (`/dashboard/checkin`)
- Add: Participantes, Financeiro, Configurações
- Sidebar uses `bg-indigo-800`, active state uses `bg-indigo-700`
- Active detection: `request()->is('dashboard/...')` pattern

- [ ] **Step 1: Update the sidebar**

In `resources/views/components/layouts/dashboard.blade.php`, replace the `<nav>` block with:

```blade
<nav class="mt-6 px-4 space-y-1">
    <a href="{{ url('/dashboard') }}"
       class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard') && !request()->is('dashboard/*') ? 'bg-indigo-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
        Dashboard
    </a>
    <a href="{{ url('/dashboard/events') }}"
       class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard/events*') ? 'bg-indigo-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Eventos
    </a>
    <a href="{{ url('/dashboard/participantes') }}"
       class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard/participantes*') ? 'bg-indigo-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Participantes
    </a>
    <a href="{{ url('/dashboard/checkin') }}"
       class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard/checkin*') ? 'bg-indigo-700' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Check-in
    </a>

    <div class="pt-2 mt-2 border-t border-indigo-700">
        <a href="{{ url('/dashboard/financeiro') }}"
           class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard/financeiro*') ? 'bg-indigo-700' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Financeiro
        </a>
        <a href="{{ url('/dashboard/settings') }}"
           class="flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:bg-indigo-700 {{ request()->is('dashboard/settings*') ? 'bg-indigo-700' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Configurações
        </a>
    </div>
</nav>
```

- [ ] **Step 2: Run full test suite to confirm nothing broke**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test
```
Expected: All tests passing

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/layouts/dashboard.blade.php
git commit -m "feat: adicionar Participantes, Financeiro e Configurações no sidebar"
```

---

### Task 5: Final Verification

- [ ] **Run complete test suite**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test
```
Expected: All tests passing (previously 117 + new tests)

- [ ] **Build assets**

```bash
cd /Users/joaofilipibritto/Projetos/projeto-taketicket/api-taketicket && npm run build
```

- [ ] **Push to remote**

```bash
git push origin main
```
