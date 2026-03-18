# Event Collaborators (Equipe de Check-in) — Design Spec

## Goal

Allow event organizers to invite staff members (collaborators) to perform check-in operations on the day of the event. Each collaborator has a scoped, time-limited account with access only to check-in, participants list, and event statistics for their specific event.

## Context

The existing system has a single user type: organizers who own events. Check-in is currently done by the organizer themselves via `/dashboard/checkin`. There is no concept of staff or sub-users. The `checkins` table already records `checked_by` (user_id), so audit trails are already in place — we just need to create collaborator accounts that can trigger those records.

---

## Data Model

### New table: `event_collaborators`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | — |
| `event_id` | FK → events | Scoped to one event |
| `inviter_user_id` | FK → users | The organizer who sent the invite |
| `invitee_email` | string | Email used for the invitation |
| `user_id` | FK → users, nullable | Filled when the person accepts |
| `status` | enum | `pending`, `active`, `revoked` |
| `expires_at` | datetime | Copied from `event.end_date` at invite time |
| `accepted_at` | datetime, nullable | When the collaborator accepted |
| timestamps | — | — |

**Model: `App\Models\EventCollaborator`**
- Relationships: `event()`, `inviter()` (→ User), `user()` (→ User, nullable)
- Scopes: `active()` — status = active AND expires_at > now()
- `isExpired()`: `expires_at < now()`

---

## Invitation Flow

```
Organizer submits email
        │
        ├── User EXISTS with that email
        │       → Create EventCollaborator (status: active, user_id: filled)
        │       → Send notification email: "Você foi adicionado à equipe do evento X"
        │       → Collaborator can log in immediately
        │
        └── User does NOT exist
                → Create EventCollaborator (status: pending, user_id: null)
                → Send invitation email with signed URL → /register?invitation={token}
                → Person registers → system matches email → links user_id → status: active
```

**Signed URL:** uses Laravel's `URL::temporarySignedRoute()`, valid for 7 days.

**Registration with pending invite:** the `RegisteredUserController` checks for a pending `EventCollaborator` matching the registered email and activates it automatically.

**Constraints:**
- Cannot invite the event's own organizer user
- Cannot invite someone already in the team for this event
- Invitation token expires in 7 days; after that the organizer must re-invite

**Revocation:** organizer clicks "Remover" → status set to `revoked` → middleware blocks on next request. Available for `active` and `pending` collaborators only.

**Expiration:** `expires_at` is set to `event.end_date` at invite time. No cron needed — the middleware checks `expires_at > now()` on every request.

---

## Routing

### Staff routes (new group)

```
GET  /staff/events/{event}/checkin           → StaffCheckinController@index
POST /staff/events/{event}/checkin/validate  → StaffCheckinController@validateTicket
POST /staff/events/{event}/checkin/undo      → StaffCheckinController@undo
GET  /staff/events/{event}/participants      → StaffParticipantController@index
GET  /staff                                  → StaffController@index (event selector)
```

Middleware: `auth`, `EnsureIsEventCollaborator`

### Organizer routes (additions to existing group)

```
POST   /dashboard/events/{event}/collaborators          → CollaboratorController@store
DELETE /dashboard/events/{event}/collaborators/{collaborator} → CollaboratorController@destroy
```

### Login redirect (modification)

After login, if user has no organizer:
- 1 active collaboration → redirect to `/staff/events/{event}/checkin`
- N active collaborations → redirect to `/staff` (event selector page)
- 0 active collaborations → redirect back to `/login` with error "Sua conta não tem acesso ativo."

---

## Middleware: `EnsureIsEventCollaborator`

Applied to all `/staff/events/{event}/...` routes.

Checks:
1. User is authenticated
2. User has an `EventCollaborator` record for the `{event}` in the route
3. `status = active`
4. `expires_at > now()`

On failure:
- Not found / revoked → 403 "Você não tem acesso a este evento."
- Expired → 403 "Seu acesso a este evento expirou."

---

## Staff Interface

### Layout: `x-layouts.staff`

Minimal layout — no sidebar. Fixed top header with:
- TakeTicket logo (links to `/staff`)
- Event name + date
- Logged-in user's name + logout button

Two navigation tabs: **Check-in** | **Participantes**

### Check-in tab

Three stat cards at the top:
- Total de ingressos
- Check-ins realizados
- Faltam

Below: same check-in form as the organizer (manual ticket code + QR scanner). Event is pre-selected — no event selector shown.

Reuses `CheckinService` — no duplicate logic. Creates `Checkin` records with the collaborator's `user_id` as `checked_by`.

### Participants tab

Paginated, searchable list (by name or ticket code). Columns: name, ticket type, ticket code, status (válido / check-in feito). Read-only.

### Feedback

Same toast behavior as the existing check-in interface: green (valid), yellow (already used), red (invalid).

---

## Organizer Interface — Team Management

A new **"Equipe de Check-in"** section added at the bottom of the existing event show page (`dashboard/events/show.blade.php`).

### Collaborator list

Each row shows: name (if accepted) or email (if pending), status badge, remove button.

Status badges:
- `pending` → gray "Aguardando cadastro"
- `active` → green "Ativo"
- `revoked` → muted, no action button
- expired (active but `expires_at < now()`) → gray "Expirado"

### Invite form

Inline form below the list — email input + "Convidar" button. No modal, no separate page. On success, the list refreshes and shows the new collaborator.

### Revoke

"Remover" button (✕) on active and pending rows only. Instant — no confirmation modal (low-stakes action, easily re-invited).

---

## Email Notifications

### Mailable: `CollaboratorInvited`

Sent when inviting a non-existing user. Contains:
- Event name, date, location
- Organizer name
- CTA button → signed `/register?invitation={token}` URL
- Expiry note: "Este convite expira em 7 dias."

### Notification: `CollaboratorAdded` (existing user)

Sent when inviting an existing user. Simple notification:
- "Você foi adicionado à equipe de check-in do evento X."
- CTA button → `/staff/events/{event}/checkin`

---

## Authorization

- Only the event's organizer can invite or revoke collaborators (`EventPolicy::manage`)
- Collaborators cannot invite other collaborators
- Collaborators have read-only access to participants (no export, no edit)
- Each check-in action is attributed to the collaborator's own user_id

---

## Out of Scope (this iteration)

- Per-event permission levels (e.g., view-only vs. can-validate)
- Collaborator access to financial data
- Collaborator notifications when check-in stats change
- Re-sending invitations (organizer can revoke and re-invite)
- Mobile-specific PWA or offline mode
