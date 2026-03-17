# TAKETICKET Web Interface Design Spec

## Goal

Add a complete web interface to the existing TAKETICKET Laravel API using Blade templates, Alpine.js, and TailwindCSS. The web layer covers three areas: organizer dashboard, public event pages, and ticket checkout flow. The application must run on shared PHP hosting (HostGator) with no Node.js runtime — assets are pre-built via Vite and committed to the repository.

## Architecture

```
Browser (Blade)  ──→  Web Controllers  ──→  Services  ──→  Models/DB
Mobile (JSON)    ──→  API Controllers  ──→  Services  ──→  Models/DB
```

Both delivery layers share the same service core. Web controllers call `OrderService`, `EventService`, `TicketService`, `CheckinService`, etc. directly — no internal HTTP calls to `/api/v1/`.

### Authentication Split

| Layer | Mechanism | Middleware |
|-------|-----------|------------|
| Web interface | Laravel session auth (cookies + CSRF) | `auth` |
| API (mobile/external) | Sanctum token auth | `auth:sanctum` |

### Tech Stack

- **Blade** — server-rendered templates with layouts and components
- **Alpine.js** — reactive UI (quantity selectors, modals, PIX polling, tabs) via CDN
- **TailwindCSS 4** — already configured in the project via Vite
- **html5-qrcode** — QR scanner for check-in page (CDN)
- **qrcode.js** — QR code rendering for "My Tickets" (CDN)

### Asset Strategy for Shared Hosting

1. Run `npm run build` locally
2. Commit `public/build/` to the repository
3. Shared hosting serves pre-built static assets — no Node.js required on server

---

## 1. Web Authentication Pages

No Fortify or Breeze — minimal custom implementation.

### Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/login` | `AuthController@showLogin` | Login form |
| POST | `/login` | `AuthController@login` | Authenticate, redirect to intended URL |
| GET | `/register` | `AuthController@showRegister` | Registration form |
| POST | `/register` | `AuthController@register` | Create user, auto-login, redirect |
| POST | `/logout` | `AuthController@logout` | Destroy session, redirect to `/` |

### Behavior

- Login uses `Auth::attempt()` with email + password
- Registration creates user via the same flow as the API (name, email, password, password_confirmation)
- After login/register, redirect to `intended()` URL (supports checkout redirect-back)
- CSRF protection on all POST forms via `@csrf`
- Validation errors displayed inline using `@error` directive

---

## 2. Public Pages

### Landing Page (`/`)

- Hero section with tagline and "Browse Events" CTA
- Grid of upcoming published events (paginated, 12 per page)
- Each event card: banner thumbnail, title, date, location, price range ("A partir de R$ XX,XX")
- Search bar + filters: city, date range

### Public Event Page (`/event/{slug}`)

- Full-width event banner
- Event info: title, date/time, location/address, city/state
- Description (plain text with line breaks preserved)
- Ticket types section: cards showing name, price, sale status badge (on sale / sold out / upcoming / ended)
- Quantity selector per ticket type (dropdown 0 to `min(ticket.available, 10)`)
- "Buy Tickets" button (disabled if no tickets selected)
- If unauthenticated: clicking "Buy Tickets" redirects to `/login` with `intended` set to return to this event page

### Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/` | `HomeController@index` | Landing page with event grid |
| GET | `/event/{slug}` | `PublicEventController@show` | Public event detail page |

---

## 3. Checkout Flow

Login required. The flow creates the order first, then collects participant info, then processes payment.

### Flow

```
/event/{slug}                    → Select tickets, click "Buy Tickets"
POST /checkout/order             → Create order (DB transaction + row lock), redirect to:
/checkout/{order}                → Participant information forms
POST /checkout/{order}           → Save participants, redirect to payment
/checkout/{order}/payment        → PIX QR code or redirect to Asaas
GET /checkout/{order}/status     → JSON endpoint for PIX polling
/checkout/success?order={id}     → Order confirmation
/checkout/cancel?order={id}      → Payment cancelled
```

### Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| POST | `/checkout/order` | `CheckoutController@createOrder` | Create order with stock reservation |
| GET | `/checkout/{order}` | `CheckoutController@show` | Participant forms |
| POST | `/checkout/{order}` | `CheckoutController@saveParticipants` | Save participant data |
| GET | `/checkout/{order}/payment` | `CheckoutController@payment` | Payment page |
| POST | `/checkout/{order}/payment` | `CheckoutController@processPayment` | Create charge + redirect or show PIX |
| GET | `/checkout/{order}/status` | `CheckoutController@status` | JSON: order payment status (for PIX polling) |
| GET | `/checkout/success` | `CheckoutController@success` | Confirmation page |
| GET | `/checkout/cancel` | `CheckoutController@cancel` | Payment cancelled page |

### Order Creation (`POST /checkout/order`)

- Receives `event_id`, `items[]` (ticket_type_id, quantity) from the event page form
- Calls `OrderService::createOrder()` — same service used by the API
- Uses `DB::transaction` with `lockForUpdate()` for stock reservation (already implemented)
- Order created with `expires_at` = now + 15 minutes
- Redirects to `/checkout/{order}` for participant info

### Participant Forms (`/checkout/{order}`)

- One form section per ticket
- Each section: name, email, phone, document (CPF)
- Event custom fields rendered by type (text input, number input, select dropdown, checkbox)
- Custom fields ordered by `position`
- Order summary sidebar: event name, ticket types x quantities, total price
- Countdown timer showing order expiration (Alpine.js)
- **Expired order handling**: if the order has expired when loading any checkout page, redirect to the event page with a flash message ("Order expired. Please try again.")
- **Pricing display**: only show total ticket price to the customer — platform fee is not visible (deducted from organizer side)

### Payment Page (`/checkout/{order}/payment`)

- Payment method selection: PIX or Credit Card (submitted as `billing_type` field matching the `BillingType` enum)
- **PIX flow:**
  - Creates Asaas charge via `PaymentService::createPayment()`
  - Displays QR code image + copy-paste code from `PaymentService::getPixQrCode()`
  - Alpine.js polls `GET /checkout/{order}/status` every 5 seconds
  - Endpoint verifies order belongs to authenticated user
  - When status becomes `paid` → auto-redirect to success page
  - Manual "I already paid" button to force-check
- **Credit Card flow:**
  - Creates Asaas charge with `return_url` → `/checkout/success?order={id}` and `cancel_url` → `/checkout/cancel?order={id}`
  - Redirects user to Asaas hosted payment page
  - Asaas redirects back after payment completion
  - Webhook confirms payment and triggers ticket generation

### Success Page (`/checkout/success`)

- Order confirmation: order number, event name, total paid
- List of tickets (may show "Processing..." state if tickets are still being generated via queue)
- "View My Tickets" link → `/my-tickets`

### Cancel Page (`/checkout/cancel`)

- Message: "Payment was not completed"
- "Try Again" button → back to payment page (if order not expired)
- "Browse Events" button → home

---

## 4. My Tickets

Authenticated users can view their purchased tickets.

### Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/my-tickets` | `MyTicketsController@index` | List all user's tickets |
| GET | `/my-tickets/{ticket}` | `MyTicketsController@show` | Single ticket with QR code |

### Ticket List (`/my-tickets`)

- Tickets grouped by event
- Each ticket card: event name, date, ticket type, participant name, status badge (valid/used/cancelled)
- Click → ticket detail page

### Ticket Detail (`/my-tickets/{ticket}`)

- Full ticket info: event, date, location, ticket type, participant details
- QR code rendered client-side via `qrcode.js` using the `qr_code_payload` field
- Ticket code displayed as text
- Status badge
- Authorization: only the ticket's order owner can view

---

## 5. Organizer Dashboard

### Access Control

- All `/dashboard/*` routes require `auth` middleware
- `EnsureHasOrganizer` middleware on all dashboard routes except:
  - `GET /dashboard/onboarding`
  - `POST /dashboard/onboarding`
- If user has no organizer profile → redirect to `/dashboard/onboarding`

### Onboarding (`/dashboard/onboarding`)

- Form: name, document (CNPJ/CPF), phone
- Calls `OrganizerService::createOrganizer()`
- After creation → redirect to `/dashboard`

### Dashboard Home (`/dashboard`)

- Summary cards: Total events, Total sales (R$, paid orders only), Total participants, Check-in rate (%)
- Recent orders table (last 10): order ID, buyer name, event, amount, status, date
- "Create Event" button

### Events List (`/dashboard/events`)

- Table: Title, Date, Status badge (draft/published/cancelled/finished), Tickets sold (sold/capacity), Revenue, Actions
- Actions: Edit, Manage, Publish/Cancel
- "Create Event" button at top
- Filter by status (all/draft/published/cancelled/finished)

### Create Event (`/dashboard/events/create`)

Single-page form with sections:

**Basic info:**
- Title, description (textarea), location, address, city, state
- Start date, end date (datetime inputs)
- Banner (file upload)

**Ticket types (inline editable table):**
- Columns: name, price, quantity, sale_start, sale_end
- Add/remove rows dynamically (Alpine.js)
- Validation: sale_start < sale_end, quantity > 0
- Cannot delete ticket types with existing sales

**Custom fields (inline editable):**
- Label, type (text/number/select/checkbox), required (toggle), options (for select type)
- Position field for ordering (drag-and-drop or manual number)
- Add/remove dynamically (Alpine.js)

Save → creates as draft, redirects to events list.

### Edit Event (`/dashboard/events/{id}/edit`)

- Same form as create, pre-populated
- Calls `EventService::updateEvent()`
- Publish button (only if event has ticket types)
- Cancel button (only if no paid orders)

### Event Orders (`/dashboard/events/{id}/orders`)

- Paginated table: Order #, Buyer, Qty, Total, Status, Payment method (PIX/Card), Payment status, Date
- Click → dedicated order detail page

### Order Detail (`/dashboard/events/{id}/orders/{order}`)

- Full order info: buyer details, items list, participants per ticket
- Payment info: method, status, Asaas payment ID
- Timeline of order status changes

### Event Participants (`/dashboard/events/{id}/participants`)

- Paginated table: Name, Email, Phone, Document, Ticket type, Ticket status, Check-in status
- Search by name/email/document
- Export CSV: all participants or filtered results

### Event Tickets (`/dashboard/events/{id}/tickets`)

- Paginated table: Ticket code, Participant, Type, Status, Check-in time
- Search by ticket code

### Check-in Page (`/dashboard/checkin`)

- Event selector dropdown (only published events belonging to the organizer)
- Two modes:
  - **Manual:** text input for ticket code + submit button
  - **QR Scanner:** browser camera via `html5-qrcode` (CDN)
- Result display:
  - Green: valid check-in + participant info (name, ticket type)
  - Red: invalid ticket
  - Yellow: already used + timestamp of previous check-in
- Running stats: checked-in / total tickets for selected event
- **Undo check-in** button: reverts a ticket from USED back to VALID, deletes the checkin record (requires new `CheckinService::undoCheckin()` method)

### Check-in Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/dashboard/checkin` | `Dashboard\CheckinController@index` | Check-in page |
| POST | `/dashboard/checkin/validate` | `Dashboard\CheckinController@validate` | Process check-in |
| POST | `/dashboard/checkin/undo` | `Dashboard\CheckinController@undo` | Undo check-in |

### All Dashboard Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/dashboard` | `Dashboard\DashboardController@index` | Summary |
| GET | `/dashboard/onboarding` | `Dashboard\DashboardController@onboarding` | Organizer form |
| POST | `/dashboard/onboarding` | `Dashboard\DashboardController@storeOrganizer` | Create organizer |
| GET | `/dashboard/events` | `Dashboard\DashboardEventController@index` | Events list |
| GET | `/dashboard/events/create` | `Dashboard\DashboardEventController@create` | Create form |
| POST | `/dashboard/events` | `Dashboard\DashboardEventController@store` | Store event |
| GET | `/dashboard/events/{event}/edit` | `Dashboard\DashboardEventController@edit` | Edit form |
| PUT | `/dashboard/events/{event}` | `Dashboard\DashboardEventController@update` | Update event |
| PATCH | `/dashboard/events/{event}/publish` | `Dashboard\DashboardEventController@publish` | Publish |
| PATCH | `/dashboard/events/{event}/cancel` | `Dashboard\DashboardEventController@cancel` | Cancel |
| GET | `/dashboard/events/{event}/orders` | `Dashboard\OrderController@index` | Event orders |
| GET | `/dashboard/events/{event}/orders/{order}` | `Dashboard\OrderController@show` | Order detail |
| GET | `/dashboard/events/{event}/participants` | `Dashboard\ParticipantController@index` | Participants |
| GET | `/dashboard/events/{event}/participants/export` | `Dashboard\ParticipantController@export` | Export CSV |
| GET | `/dashboard/events/{event}/tickets` | `Dashboard\TicketController@index` | Tickets list |
| GET | `/dashboard/checkin` | `Dashboard\CheckinController@index` | Check-in page |
| POST | `/dashboard/checkin/validate` | `Dashboard\CheckinController@validate` | Validate ticket |
| POST | `/dashboard/checkin/undo` | `Dashboard\CheckinController@undo` | Undo check-in |

---

## 6. File Structure

### Blade Views

```
resources/views/
├── layouts/
│   ├── app.blade.php              ← public pages (nav + footer)
│   ├── dashboard.blade.php        ← sidebar + top bar
│   └── checkout.blade.php         ← simplified checkout layout
├── components/
│   ├── alert.blade.php
│   ├── badge.blade.php
│   ├── input.blade.php
│   ├── select.blade.php
│   ├── textarea.blade.php
│   ├── modal.blade.php
│   ├── card.blade.php
│   ├── pagination.blade.php
│   └── countdown.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── public/
│   ├── home.blade.php
│   └── event-show.blade.php
├── checkout/
│   ├── show.blade.php             ← participant forms
│   ├── payment.blade.php          ← PIX QR / CC redirect
│   ├── success.blade.php
│   └── cancel.blade.php
├── dashboard/
│   ├── index.blade.php            ← summary cards + recent orders
│   ├── onboarding.blade.php
│   ├── events/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── orders.blade.php
│   │   ├── order-show.blade.php
│   │   ├── participants.blade.php
│   │   └── tickets.blade.php
│   └── checkin.blade.php
├── my-tickets/
│   ├── index.blade.php
│   └── show.blade.php
└── emails/
    └── tickets/purchased.blade.php  ← already exists
```

### Web Controllers

```
app/Http/Controllers/Web/
├── AuthController.php
├── HomeController.php
├── PublicEventController.php
├── CheckoutController.php
├── MyTicketsController.php
└── Dashboard/
    ├── DashboardController.php
    ├── DashboardEventController.php
    ├── OrderController.php
    ├── ParticipantController.php
    ├── TicketController.php
    └── CheckinController.php
```

### Middleware

```
app/Http/Middleware/
└── EnsureHasOrganizer.php
```

### Form Requests

```
app/Http/Requests/Web/
├── LoginRequest.php
├── RegisterRequest.php
├── StoreEventRequest.php
├── UpdateEventRequest.php
├── StoreOrderRequest.php
├── SaveParticipantsRequest.php
└── StoreOrganizerRequest.php
```

---

## 7. Middleware Configuration

### EnsureHasOrganizer

Applied to all `/dashboard/*` routes except `/dashboard/onboarding` (GET and POST).

```php
// Pseudocode
if (!auth()->user()->organizer) {
    return redirect('/dashboard/onboarding');
}
return $next($request);
```

### Route Middleware Groups

```php
// routes/web.php structure
Route::middleware('auth')->group(function () {
    // Checkout routes (no organizer required)
    // My Tickets routes (no organizer required)

    Route::prefix('dashboard')->middleware(EnsureHasOrganizer::class)->group(function () {
        // All dashboard routes except onboarding
    });

    // Onboarding routes (auth but no EnsureHasOrganizer)
    Route::get('dashboard/onboarding', ...);
    Route::post('dashboard/onboarding', ...);
});
```

---

## 8. Postman Collection

Organized by folder, uses `{{base_url}}` and `{{token}}` variables.

### Folders

1. **Auth** — register, login, logout, me
2. **Organizers** — create, get profile, update
3. **Events (Public)** — list, show by slug
4. **Events (Organizer)** — list, create, show, update, publish, cancel
5. **Ticket Types** — create, update, delete
6. **Custom Fields** — list, create, update, delete
7. **Orders** — create, my orders, show
8. **Checkout** — create order (POST), check status (GET)
9. **Webhooks** — Asaas webhook
10. **Tickets** — my tickets, show
11. **Check-in** — validate, undo
12. **Dashboard** — summary, orders, participants, tickets

---

## 9. Security

- CSRF protection on all web POST/PUT/PATCH/DELETE forms via `@csrf`
- `@method('PUT')` / `@method('PATCH')` / `@method('DELETE')` for non-POST methods
- Laravel policies for organizer access control (reuses existing `EventPolicy`)
- Form validation via `FormRequest` classes
- Rate limiting: `throttle:auth` on login/register (already configured)
- Order ownership verification on all checkout endpoints
- Ticket ownership verification on My Tickets pages
- Check-in restricted to organizer's own events
