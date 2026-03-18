@php
    $intended    = session('url.intended', '');
    $isBuyerFlow = str_contains($intended, 'checkout') || request()->query('para') === 'ingresso';
@endphp

@if($isBuyerFlow)
{{-- ───────────────────── BUYER FLOW ───────────────────── --}}
<x-layouts.auth title="Entre para finalizar sua compra — TakeTicket">
<div style="display:flex;min-height:100vh;width:100%;">

    {{-- Left panel: context --}}
    <div class="hidden lg:flex" style="width:44%;background:linear-gradient(160deg,#0f172a 0%,#1e1b4b 50%,#312e81 100%);flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;">

        {{-- Decorative background --}}
        <div style="position:absolute;top:-60px;right:-60px;width:280px;height:280px;border-radius:50%;background:rgba(79,70,229,0.12);"></div>
        <div style="position:absolute;bottom:-40px;left:-40px;width:200px;height:200px;border-radius:50%;background:rgba(124,58,237,0.1);"></div>

        {{-- Logo --}}
        <div style="position:relative;z-index:10;">
            <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                <div style="width:38px;height:38px;background:linear-gradient(135deg,#818cf8,#a78bfa);border-radius:11px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <span style="font-size:20px;font-weight:800;color:white;letter-spacing:-0.4px;">TakeTicket</span>
            </a>
        </div>

        {{-- Steps progress --}}
        <div style="position:relative;z-index:10;">
            <p style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1px;margin:0 0 32px;">Etapas da compra</p>

            <div style="display:flex;flex-direction:column;gap:0;">
                {{-- Step 1 --}}
                <div style="display:flex;gap:16px;align-items:flex-start;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:0;">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div style="width:1px;height:40px;background:rgba(79,70,229,0.4);margin:4px 0;"></div>
                    </div>
                    <div style="padding-top:6px;">
                        <p style="font-size:14px;font-weight:700;color:rgba(255,255,255,0.9);margin:0 0 3px;">Escolha o ingresso</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.4);margin:0;">Tipo e quantidade selecionados</p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div style="display:flex;gap:16px;align-items:flex-start;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:0;">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);border:2px solid rgba(165,180,252,0.5);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:13px;font-weight:700;color:white;">2</span>
                        </div>
                        <div style="width:1px;height:40px;background:rgba(255,255,255,0.08);margin:4px 0;"></div>
                    </div>
                    <div style="padding-top:6px;">
                        <p style="font-size:14px;font-weight:700;color:white;margin:0 0 3px;">Identifique-se</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.5);margin:0;">Entre ou crie sua conta gratuita</p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div style="display:flex;gap:16px;align-items:flex-start;">
                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.3);">3</span>
                    </div>
                    <div style="padding-top:6px;">
                        <p style="font-size:14px;font-weight:600;color:rgba(255,255,255,0.35);margin:0 0 3px;">Pagamento</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.2);margin:0;">Pague com segurança</p>
                    </div>
                </div>
            </div>

            {{-- Trust badges --}}
            <div style="margin-top:48px;display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="13" height="13" fill="none" stroke="rgba(165,180,252,0.8)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <span style="font-size:13px;color:rgba(255,255,255,0.5);">Pagamento 100% seguro e criptografado</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="13" height="13" fill="none" stroke="rgba(165,180,252,0.8)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span style="font-size:13px;color:rgba(255,255,255,0.5);">Ingresso entregue por e-mail na hora</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="13" height="13" fill="none" stroke="rgba(165,180,252,0.8)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3m2 4H1m18-4h-2M7 20H3"/></svg>
                    </div>
                    <span style="font-size:13px;color:rgba(255,255,255,0.5);">QR Code para entrada no evento</span>
                </div>
            </div>
        </div>

        <div style="position:relative;z-index:10;">
            <p style="font-size:12px;color:rgba(255,255,255,0.2);margin:0;">&copy; {{ date('Y') }} TakeTicket · Um produto HelpFlux</p>
        </div>
    </div>

    {{-- Right panel: form --}}
    <div style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;background:#fafafa;padding:40px 24px;overflow-y:auto;">

        {{-- Mobile logo --}}
        <div class="lg:hidden" style="margin-bottom:28px;text-align:center;">
            <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
                <div style="width:32px;height:32px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <span style="font-size:18px;font-weight:800;background:linear-gradient(135deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">TakeTicket</span>
            </a>
        </div>

        <div style="width:100%;max-width:400px;">

            <div style="margin-bottom:32px;">
                <div style="display:inline-flex;align-items:center;gap:6px;background:#ede9fe;border-radius:100px;padding:5px 12px;margin-bottom:14px;">
                    <div style="width:6px;height:6px;border-radius:50%;background:#7c3aed;"></div>
                    <span style="font-size:11.5px;font-weight:600;color:#6d28d9;">Etapa 2 de 3 — Identificação</span>
                </div>
                <h1 style="font-size:26px;font-weight:800;color:#0f172a;margin:0 0 8px;letter-spacing:-0.5px;">Entre na sua conta</h1>
                <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;">Para receber seu ingresso por e-mail, entre ou crie uma conta gratuita.</p>
            </div>

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 14px;margin-bottom:22px;display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p style="font-size:13px;color:#dc2626;margin:0;font-weight:500;">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="seu@email.com"
                           style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Senha</label>
                    <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                           style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <button type="submit"
                        style="width:100%;padding:13px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 16px rgba(79,70,229,0.4);font-family:inherit;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Entrar e continuar →
                </button>
            </form>

            <div style="display:flex;align-items:center;gap:12px;margin:24px 0;">
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                <span style="font-size:12px;color:#9ca3af;font-weight:500;">ou não tem conta?</span>
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
            </div>

            <a href="{{ url('/register') }}?para=ingresso"
               style="display:block;width:100%;box-sizing:border-box;padding:12px;background:white;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:700;color:#1e293b;text-align:center;text-decoration:none;"
               onmouseover="this.style.borderColor='#4f46e5';this.style.color='#4f46e5'" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#1e293b'">
                Criar conta gratuita em 1 minuto →
            </a>
        </div>
    </div>
</div>
</x-layouts.auth>

@else
{{-- ───────────────────── ORGANIZER FLOW ───────────────────── --}}
<x-layouts.auth title="Entrar — TakeTicket">
<style>
    @keyframes float1 { 0%,100%{transform:translateY(0) rotate(-8deg)} 50%{transform:translateY(-18px) rotate(-8deg)} }
    @keyframes float2 { 0%,100%{transform:translateY(0) rotate(6deg)} 50%{transform:translateY(-12px) rotate(6deg)} }
    @keyframes float3 { 0%,100%{transform:translateY(0) rotate(-3deg)} 50%{transform:translateY(-22px) rotate(-3deg)} }
    @keyframes pulse-glow { 0%,100%{opacity:0.5;transform:scale(1)} 50%{opacity:0.8;transform:scale(1.05)} }
    .auth-bg {
        min-height: 100vh; width: 100%;
        background: #08071a;
        background-image:
            radial-gradient(ellipse 60% 50% at 15% 60%, rgba(79,70,229,0.25) 0%, transparent 70%),
            radial-gradient(ellipse 50% 40% at 85% 30%, rgba(124,58,237,0.2) 0%, transparent 70%),
            radial-gradient(ellipse 40% 60% at 50% 100%, rgba(99,102,241,0.1) 0%, transparent 60%);
        display: flex; align-items: center; justify-content: center;
        padding: 40px 20px; position: relative; overflow: hidden;
    }
    .glow-orb-1 { position:absolute;top:-120px;left:-120px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(79,70,229,0.3) 0%,transparent 70%);animation:pulse-glow 6s ease-in-out infinite;pointer-events:none; }
    .glow-orb-2 { position:absolute;bottom:-100px;right:-100px;width:350px;height:350px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,0.25) 0%,transparent 70%);animation:pulse-glow 8s ease-in-out infinite 2s;pointer-events:none; }
    .glow-orb-3 { position:absolute;top:40%;right:20%;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(99,102,241,0.15) 0%,transparent 70%);animation:pulse-glow 5s ease-in-out infinite 1s;pointer-events:none; }
    .ticket-deco { position:absolute;border-radius:14px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);backdrop-filter:blur(4px);pointer-events:none; }
    .ticket-1 { top:12%;left:6%;width:180px;padding:14px 16px;animation:float1 7s ease-in-out infinite; }
    .ticket-2 { bottom:18%;right:7%;width:200px;padding:14px 16px;animation:float2 9s ease-in-out infinite; }
    .ticket-3 { top:55%;left:3%;width:160px;padding:12px 14px;animation:float3 6s ease-in-out infinite 1s; }
    .auth-card { position:relative;z-index:10;background:rgba(255,255,255,0.97);border-radius:24px;padding:44px 40px;width:100%;max-width:420px;box-shadow:0 0 0 1px rgba(255,255,255,0.08),0 24px 80px rgba(0,0,0,0.5),0 0 60px rgba(79,70,229,0.15); }
    .auth-input { width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;font-family:inherit;transition:border-color 0.15s,background 0.15s; }
    .auth-input:focus { border-color:#4f46e5;background:white; }
    .auth-input::placeholder { color:#9ca3af; }
    .auth-btn { width:100%;padding:13px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;letter-spacing:-0.2px;box-shadow:0 4px 20px rgba(79,70,229,0.45);transition:transform 0.15s,box-shadow 0.15s;font-family:inherit; }
    .auth-btn:hover { transform:translateY(-1px);box-shadow:0 6px 28px rgba(79,70,229,0.55); }
    .auth-btn:active { transform:translateY(0); }
    .grid-lines { position:absolute;inset:0;z-index:0;background-image:linear-gradient(rgba(255,255,255,0.02) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.02) 1px,transparent 1px);background-size:60px 60px;pointer-events:none; }
</style>

<div class="auth-bg">
    <div class="grid-lines"></div>
    <div class="glow-orb-1"></div>
    <div class="glow-orb-2"></div>
    <div class="glow-orb-3"></div>

    <div class="ticket-deco ticket-1 hidden lg:block">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;">
                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <span style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.85);">Rock in Rio</span>
        </div>
        <div style="font-size:10px;color:rgba(255,255,255,0.4);margin-bottom:6px;">📍 Rio de Janeiro</div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:11px;font-weight:700;color:#a5b4fc;">R$ 180,00</span>
            <span style="font-size:10px;background:rgba(52,211,153,0.15);color:#34d399;border-radius:6px;padding:2px 7px;font-weight:600;">Ativo</span>
        </div>
    </div>
    <div class="ticket-deco ticket-2 hidden lg:block">
        <div style="font-size:10px;color:rgba(255,255,255,0.4);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">Receita do mês</div>
        <div style="font-size:22px;font-weight:800;color:white;letter-spacing:-0.5px;margin-bottom:4px;">R$ 24.800</div>
        <div style="display:flex;align-items:center;gap:5px;">
            <span style="font-size:10px;color:#34d399;font-weight:600;">↑ 34%</span>
            <span style="font-size:10px;color:rgba(255,255,255,0.35);">vs. mês anterior</span>
        </div>
    </div>
    <div class="ticket-deco ticket-3 hidden lg:block">
        <div style="font-size:10px;color:rgba(255,255,255,0.4);margin-bottom:8px;">Ingressos vendidos hoje</div>
        <div style="display:flex;gap:3px;">
            @for($i = 0; $i < 7; $i++)
                <div style="flex:1;height:24px;border-radius:3px;background:{{ $i < 5 ? 'rgba(79,70,229,0.8)' : 'rgba(255,255,255,0.08)' }};"></div>
            @endfor
        </div>
        <div style="font-size:11px;color:rgba(255,255,255,0.6);margin-top:6px;font-weight:600;">147 / 200</div>
    </div>

    <div class="auth-card">
        <div style="text-align:center;margin-bottom:32px;">
            <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                <div style="width:42px;height:42px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:13px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(79,70,229,0.4);">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <span style="font-size:22px;font-weight:800;background:linear-gradient(135deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-0.5px;">TakeTicket</span>
            </a>
        </div>

        <div style="margin-bottom:28px;">
            <h1 style="font-size:24px;font-weight:800;color:#111827;margin:0 0 6px 0;letter-spacing:-0.5px;">Bem-vindo de volta</h1>
            <p style="font-size:14px;color:#6b7280;margin:0;">Entre na sua conta para continuar gerenciando seus eventos.</p>
        </div>

        @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 14px;margin-bottom:22px;display:flex;align-items:center;gap:8px;">
                <svg width="15" height="15" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p style="font-size:13px;color:#dc2626;margin:0;font-weight:500;">{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" style="display:flex;flex-direction:column;gap:16px;">
            @csrf
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">E-mail</label>
                <input class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="seu@email.com">
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Senha</label>
                <input class="auth-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            <div style="display:flex;align-items:center;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:15px;height:15px;accent-color:#4f46e5;cursor:pointer;border-radius:4px;">
                    <span style="font-size:13px;color:#6b7280;font-weight:500;">Lembrar de mim</span>
                </label>
            </div>
            <button type="submit" class="auth-btn">Entrar</button>
        </form>

        <div style="display:flex;align-items:center;gap:12px;margin:24px 0;">
            <div style="flex:1;height:1px;background:#f3f4f6;"></div>
            <span style="font-size:12px;color:#d1d5db;font-weight:500;">ou</span>
            <div style="flex:1;height:1px;background:#f3f4f6;"></div>
        </div>

        <p style="text-align:center;font-size:14px;color:#6b7280;margin:0;">
            Não tem uma conta?
            <a href="{{ url('/register') }}" style="color:#4f46e5;font-weight:700;text-decoration:none;margin-left:3px;">Cadastre-se grátis →</a>
        </p>
    </div>
</div>
</x-layouts.auth>
@endif
