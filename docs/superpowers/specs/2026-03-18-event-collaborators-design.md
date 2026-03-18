# Event Collaborators (Equipe de Check-in) — Design Spec

## Goal

Allow event organizers to invite staff members (collaborators) to perform check-in operations on the day of the event. Each collaborator has a scoped, time-limited account with access only to check-in, participants list, and event statistics for their specific event.

## Context

The existing system has a single user type: organizers who own events. Check-in is done via `/dashboard/checkin`. There is no concept of staff or sub-users. The `checkins` table already records `checked_by` (user_id), so audit trails are in place.

Relevant existing classes:
- `App\Http\Controllers\Web\AuthController` — handles `login()` and `register()` (not `AuthenticatedSessionController` or `RegisteredUserController`)
- `App\Services\CheckinService` — `performCheckin()` and `undoCheckin()`. Does **not** check event ownership internally; that check is done inline in `CheckinController` before calling the service.
- `App\Http\Middleware\EnsureHasOrganizer` — redirects to `/dashboard/onboarding` if `$request->user()->organizer` is null. Needs modification to not redirect collaborator-only users there.
- `EventPolicy::manage` — checks `$user->id === $event->organizer->user_id`. Reused for invite/revoke authorization.

---

## Data Model

### New table: `event_collaborators`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | — |
| `event_id` | FK → events, cascade delete | Scoped to one event |
| `inviter_user_id` | FK → users | The organizer who sent the invite |
| `invitee_email` | string | Email used for the invitation |
| `user_id` | FK → users, nullable | Filled when the person accepts |
| `status` | enum | `pending`, `active`, `revoked` |
| `expires_at` | datetime | Set at invite time (see rules below) |
| `accepted_at` | datetime, nullable | When the collaborator accepted |
| timestamps | — | — |

**`expires_at` rules (applied at invite time):**
- If `event.end_date` is not null → `expires_at = event.end_date`
- If `event.end_date` is null → `expires_at = event.start_date + 24 hours`
- **Rejection condition:** if computed `expires_at < now()` → validation error: "Não é possível convidar colaboradores para um evento que já encerrou." (A multi-day event still in progress has `end_date` in the future, so the invite is allowed.)

**Model: `App\Models\EventCollaborator`**
- Relationships: `event()`, `inviter()` (belongsTo User via `inviter_user_id`), `user()` (belongsTo User via `user_id`, nullable)
- Scope `active()`: `where('status', 'active')->where('expires_at', '>', now())` — used for middleware and login redirect only
- Method `isExpired()`: `$this->expires_at < now()` — used for display logic in the organizer list
- The organizer list query loads **all** collaborators for the event (unscoped), derives display state in the view

---

## CheckinService — No Modification Needed

`CheckinService::performCheckin()` and `undoCheckin()` do global ticket lookups and do not check event ownership internally. Event ownership is checked **inline in the controller** before calling the service (as `CheckinController` already does).

`StaffCheckinController` follows the same pattern:
1. Look up the ticket via `TicketService` (same as now)
2. Verify `$ticket->event_id === $event->id` — where `$event` comes from the route
3. If mismatch → return `['status' => 'invalid']` (403 not appropriate here; ticket just doesn't belong to this event)
4. Call `CheckinService::performCheckin()` passing the authenticated collaborator as `$user`

This keeps `CheckinService` unchanged and scopes ownership via the route-bound `$event`.

---

## `EnsureHasOrganizer` Middleware — Modification

Current behavior: redirects to `/dashboard/onboarding` when `$request->user()->organizer` is null.

**Modification:** before redirecting to onboarding, check if the user has any active `EventCollaborator` records. If yes → redirect to `/staff` instead of `/dashboard/onboarding`.

```php
if (! $request->user()->organizer) {
    $hasActiveCollaboration = EventCollaborator::where('user_id', $request->user()->id)
        ->where('status', 'active')
        ->where('expires_at', '>', now())
        ->exists();

    return $hasActiveCollaboration
        ? redirect('/staff')
        : redirect('/dashboard/onboarding');
}
```

This prevents collaborator-only users who navigate to `/dashboard` from landing on the onboarding wizard.

---

## Invitation Flow

### Signed URL mechanism

No token column stored. The signed URL:

```php
URL::temporarySignedRoute('invitation.accept', now()->addDays(7), ['collaborator' => $collaborator->id])
```

`InvitationController@show` also checks `expires_at` independently — a cryptographically valid but post-event URL is still rejected.

### Flow

```
Organizer submits email
        │
        ├── Validation fails → redirect back with errors bag (field: email)
        │
        ├── User EXISTS with that email
        │       → Create EventCollaborator (status: active, user_id: filled, accepted_at: now())
        │       → Queue CollaboratorAddedMail
        │       → Redirect back with success flash
        │
        └── User does NOT exist
                → Create EventCollaborator (status: pending, user_id: null)
                → Queue CollaboratorInvitedMail with signed URL
                → Redirect back with success flash
```

**`CollaboratorController@store` — execution order:**
1. `$this->authorize('manage', $event)` — called first; aborts 403 if the authenticated user is not the event's organizer
2. Validate the `email` field (constraints below)
3. Create the `EventCollaborator` record and dispatch the appropriate mailable

**Invite constraints** (field error on `email`):
- Computed `expires_at` must be > now()
- Email must not belong to the event's organizer user
- No existing non-revoked record for this event + email (`status IN (pending, active)`)
- Standard `email` format validation

### `InvitationController@show` — GET /invitation/{collaborator}

No auth required. `signed` middleware rejects tampered/expired URLs before controller runs.

Controller logic:
1. If `collaborator.expires_at < now()` → show error view: "Este convite expirou. Peça ao organizador um novo convite."
2. If `collaborator.status = revoked` → show error view: "Este convite foi cancelado."
3. If `collaborator.status = active`:
   - If `auth()->check()` → redirect to `route('staff.checkin', $collaborator->event)`
   - If not authenticated → store `route('staff.checkin', $collaborator->event)` in `session('url.intended')`, redirect to `/login`
   - If authenticated as a **different user** (email mismatch) → show error view: "Este convite foi enviado para outro endereço de e-mail."
4. If `collaborator.status = pending`:
   - If authenticated as any user → show error view: "Você está logado em outra conta. Saia e acesse o link novamente para criar uma nova conta com o e-mail convidado."
   - If not authenticated → store `$collaborator->id` in `session('pending_collaborator_id')` and `$collaborator->invitee_email` in `session('pending_collaborator_email')`, redirect to `/register`

### Registration page modification (`register` Blade view)

Two session keys are used throughout this flow:
- `pending_collaborator_id` — the `EventCollaborator` primary key (integer)
- `pending_collaborator_email` — the `invitee_email` string, used to pre-fill the email field

When `session('pending_collaborator_id')` is set:
- Show notice banner: "Você foi convidado para fazer check-in no evento [X]. Complete seu cadastro para continuar."
- Pre-fill the email field with `session('pending_collaborator_email')` using `value="{{ session('pending_collaborator_email', old('email')) }}"`
- Make the email field **read-only** (HTML `readonly` attribute + a hidden `<input name="email">` carrying the same value to guarantee it is submitted even with `readonly`). This prevents email mismatch.

The `/register` route is behind `guest` middleware. Since pending-collaborator users arrive unauthenticated (authenticated users are blocked in step 4 above), this is not an issue.

### `AuthController@register` modification

After creating the user, if `session('pending_collaborator_id')` is set:
- Load `EventCollaborator` by ID
- Verify `status = pending` (guard: organizer may have revoked during registration)
- Verify `strtolower($collaborator->invitee_email) === strtolower($user->email)` (defensive guard — should always match given the locked field)
- If both pass: set `user_id = $user->id`, `status = active`, `accepted_at = now()`, clear both session keys
- Redirect to `route('staff.checkin', $collaborator->event)`

If verification fails: clear session keys, redirect to `/` with flash: "Seu convite foi cancelado antes de ser aceito."

### Revocation

`CollaboratorController@destroy` sets `status = revoked`.

- **Active collaborators:** middleware blocks on next HTTP request. Active browser sessions are not immediately invalidated — accepted known limitation.
- **Pending collaborators:** `InvitationController@show` checks `status` before processing, so the signed URL becomes inert.

---

## Routing

### Staff routes

```php
Route::middleware('auth')->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('index');

    Route::middleware('ensure.collaborator')->group(function () {
        Route::get('events/{event}/checkin', [StaffCheckinController::class, 'index'])->name('checkin');
        Route::post('events/{event}/checkin/validate', [StaffCheckinController::class, 'validateTicket'])->name('checkin.validate');
        Route::post('events/{event}/checkin/undo', [StaffCheckinController::class, 'undo'])->name('checkin.undo');
        Route::get('events/{event}/participants', [StaffParticipantController::class, 'index'])->name('participants');
    });
});

Route::middleware('signed')->get('invitation/{collaborator}', [InvitationController::class, 'show'])->name('invitation.accept');
```

### Organizer routes (inside existing dashboard group)

```php
Route::post('events/{event}/collaborators', [CollaboratorController::class, 'store'])->name('dashboard.collaborators.store');
Route::delete('events/{event}/collaborators/{collaborator}', [CollaboratorController::class, 'destroy'])->name('dashboard.collaborators.destroy');
```

### Login redirect — `AuthController@login` modification

After `Auth::attempt()` succeeds, apply the following priority order (evaluate top to bottom, stop at first match):

```
1. session('url.intended') is set → redirect()->intended()   ← always takes priority
2. user->organizer exists         → redirect('/dashboard')
3. Else:
  $collaborations = EventCollaborator::where('user_id', $user->id)
      ->where('status', 'active')
      ->where('expires_at', '>', now())
      ->get();
  count == 1 → redirect(route('staff.checkin', $collaborations->first()->event))
  count > 1  → redirect(route('staff.index'))
  count == 0 → redirect('/') with error flash:
               "Sua conta não tem acesso ativo a nenhum evento."
               [do NOT logout]
```

---

## Middleware: `EnsureIsEventCollaborator` (alias `ensure.collaborator`)

`{event}` is resolved via route model binding (404 if not found). Retrieve the bound model using `$request->route('event')` inside `handle()` — do **not** declare it as a typed parameter on `handle()`, since Laravel does not inject route model bindings into middleware parameters automatically.

```php
public function handle(Request $request, Closure $next): Response
{
    $event = $request->route('event'); // already resolved to Event model

    $collaborator = EventCollaborator::where('event_id', $event->id)
        ->where('user_id', auth()->id())
        ->latest() // most recent record wins on revoke + re-invite
        ->first();

    // ... apply checks below
}
```

- No record, `status = revoked`, `status = pending` → abort(403, "Você não tem acesso a este evento.")
- `status = active`, `expires_at < now()` → abort(403, "Seu acesso a este evento expirou.")
- `status = active`, `expires_at > now()` → pass

### `StaffController@index` (GET /staff — no `ensure.collaborator`)

- If `auth()->user()->organizer` exists → redirect to `/dashboard`
- Else → load active collaborations, show event selector or empty state

---

## Staff Interface

### Layout: `x-layouts.staff`

Props: `$event` (nullable — null on the event selector page).

Fixed top header:
- TakeTicket logo → `/staff`
- If `$event` is set: event name + formatted date
- User's first name + logout button (POST `/logout`)

Two nav tabs (only when `$event` is set): **Check-in** | **Participantes**

### `/staff` — Event selector (`StaffController@index`)

Cards: event name, date, location, "Acessar Check-in →" link.
Empty state: "Você não tem acesso ativo a nenhum evento no momento."

### Check-in tab (`StaffCheckinController@index`)

Stat cards:
- Total: `$event->ticketTypes()->sum('quantity')`
- Check-ins realizados: `$event->tickets()->where('status', 'used')->count()`
- Faltam: total − checked-in

Form: manual ticket code input + "Validar" button + QR scanner button. Event fixed from route.

**Undo UI:** after a `valid` response, the success toast shows participant name and a "Desfazer" link (visible for 30 seconds via Alpine.js timer, or until next scan). Clicking sends `POST .../checkin/undo`. On success: "Check-in desfeito." toast, stats refresh. Only the immediately preceding scan can be undone.

### JSON response format (StaffCheckinController)

`StaffCheckinController` calls `CheckinService::performCheckin()` and transforms the result before returning JSON. The `valid` response includes `ticket_code` from the ticket model (not available directly from `Participant`):

```json
{ "status": "valid", "participant": { "name": "Ana Souza", "ticket_code": "TKT-ABCD1234" } }
{ "status": "already_used", "checked_in_at": "2026-03-25T20:14:00" }
{ "status": "invalid" }
```

The controller retrieves `ticket_code` by reloading the ticket from the service result: `$result['participant']->ticket->ticket_code` (the `Participant` model has a `ticket()` belongsTo relationship).

### Participants tab (`StaffParticipantController@index`)

Query: participants through tickets scoped to `$event->id`. Paginated (20/page). Search via `q` (LIKE on `participants.name` or `tickets.ticket_code`).

Columns: Nome, Tipo de ingresso, Código, Status (verde "Check-in feito" / cinza "Válido").
Read-only. No export.

---

## Organizer Interface — Team Management

New section at bottom of `dashboard/events/show.blade.php`.

`DashboardEventController@show` adds: `$collaborators = $event->collaborators()->with('user')->latest()->get()`

### List

Each row: name (if `user` loaded) or `invitee_email`, status badge, remove button.

Status badge (derived in view):
- `pending` → gray "Aguardando cadastro"
- `active` + `isExpired()` → gray "Expirado"
- `active` + not expired → green "Ativo"
- `revoked` → muted "Revogado", no button

Remove button: shown for `active` (not expired) and `pending` only.

### Invite form

Inline form. Error: `$errors->get('email')` shown below input, field stays populated (`old('email')`).

### Revoke (`CollaboratorController@destroy`)

- `$this->authorize('manage', $event)` (EventPolicy::manage)
- Verify `$collaborator->event_id === $event->id`
- Set `status = revoked`
- Redirect back with flash: "Acesso de [$name/$email] revogado."

---

## Mailables

Both `Mailable`, both `ShouldQueue`.

### `CollaboratorInvitedMail` (new user)
- Organizer name, event name/date/location
- CTA "Aceitar convite" → signed URL
- "Este convite expira em 7 dias."

### `CollaboratorAddedMail` (existing user)
- "Você foi adicionado à equipe de check-in do evento [X]."
- CTA "Acessar check-in" → `route('staff.checkin', $event)`

---

## Authorization Summary

| Action | Actor | Mechanism |
|---|---|---|
| Invite collaborator | Organizer | `EventPolicy::manage` in `CollaboratorController@store` |
| Revoke collaborator | Organizer | `EventPolicy::manage` + `collaborator->event_id` check |
| Access staff routes | Collaborator | `EnsureIsEventCollaborator` middleware |
| Validate ticket | Collaborator | Middleware + inline `$ticket->event_id === $event->id` check + `CheckinService` |
| Undo check-in | Collaborator | Middleware + inline event check + `CheckinService::undoCheckin()` |
| View participants | Collaborator | Middleware (read-only) |

---

## Known Limitations

- Active sessions not invalidated on revocation — middleware blocks on next request
- No re-send invitation UI — organizer revokes and re-invites
- No offline/PWA support

---

## Out of Scope

- Per-event permission levels
- Collaborator access to financial data
- In-app notifications
- Mobile PWA / offline mode
