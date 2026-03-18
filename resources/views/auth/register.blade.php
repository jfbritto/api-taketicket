@php
    $intended    = session('url.intended', '');
    $isBuyerFlow = str_contains($intended, 'checkout') || request()->query('para') === 'ingresso';
@endphp

@if($isBuyerFlow)
{{-- ───────────────────── BUYER FLOW ───────────────────── --}}
<x-layouts.auth title="Criar conta — TakeTicket">
<div style="display:flex;min-height:100vh;width:100%;">

    {{-- Left panel --}}
    <div class="hidden lg:flex" style="width:44%;background:linear-gradient(160deg,#0f172a 0%,#1e1b4b 50%,#312e81 100%);flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-60px;right:-60px;width:280px;height:280px;border-radius:50%;background:rgba(79,70,229,0.12);"></div>
        <div style="position:absolute;bottom:-40px;left:-40px;width:200px;height:200px;border-radius:50%;background:rgba(124,58,237,0.1);"></div>

        <div style="position:relative;z-index:10;">
            <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                <div style="width:38px;height:38px;background:linear-gradient(135deg,#818cf8,#a78bfa);border-radius:11px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <span style="font-size:20px;font-weight:800;color:white;letter-spacing:-0.4px;">TakeTicket</span>
            </a>
        </div>

        <div style="position:relative;z-index:10;">
            <p style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1px;margin:0 0 32px;">Etapas da compra</p>

            <div style="display:flex;flex-direction:column;gap:0;">
                <div style="display:flex;gap:16px;align-items:flex-start;">
                    <div style="display:flex;flex-direction:column;align-items:center;">
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
                <div style="display:flex;gap:16px;align-items:flex-start;">
                    <div style="display:flex;flex-direction:column;align-items:center;">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);border:2px solid rgba(165,180,252,0.5);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:13px;font-weight:700;color:white;">2</span>
                        </div>
                        <div style="width:1px;height:40px;background:rgba(255,255,255,0.08);margin:4px 0;"></div>
                    </div>
                    <div style="padding-top:6px;">
                        <p style="font-size:14px;font-weight:700;color:white;margin:0 0 3px;">Identifique-se</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.5);margin:0;">Crie sua conta gratuita</p>
                    </div>
                </div>
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
                        <svg width="13" height="13" fill="none" stroke="rgba(165,180,252,0.8)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <span style="font-size:13px;color:rgba(255,255,255,0.5);">QR Code para entrada no evento</span>
                </div>
            </div>
        </div>

        <div style="position:relative;z-index:10;">
            <p style="font-size:12px;color:rgba(255,255,255,0.2);margin:0;">&copy; {{ date('Y') }} TakeTicket · Um produto HelpFlux</p>
        </div>
    </div>

    {{-- Right panel --}}
    <div style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;background:#fafafa;padding:40px 24px;overflow-y:auto;">

        <div class="lg:hidden" style="margin-bottom:28px;text-align:center;">
            <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
                <div style="width:32px;height:32px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <span style="font-size:18px;font-weight:800;background:linear-gradient(135deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">TakeTicket</span>
            </a>
        </div>

        <div style="width:100%;max-width:400px;">

            <div style="margin-bottom:28px;">
                <div style="display:inline-flex;align-items:center;gap:6px;background:#ede9fe;border-radius:100px;padding:5px 12px;margin-bottom:14px;">
                    <div style="width:6px;height:6px;border-radius:50%;background:#7c3aed;"></div>
                    <span style="font-size:11.5px;font-weight:600;color:#6d28d9;">Etapa 2 de 3 — Crie sua conta</span>
                </div>
                <h1 style="font-size:26px;font-weight:800;color:#0f172a;margin:0 0 8px;letter-spacing:-0.5px;">Crie sua conta gratuita</h1>
                <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;">Leva menos de 1 minuto. Seu ingresso será enviado por e-mail após a compra.</p>
            </div>

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 14px;margin-bottom:22px;display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p style="font-size:13px;color:#dc2626;margin:0;font-weight:500;">{{ $errors->first() }}</p>
                </div>
            @endif

            @if(session('pending_collaborator_id'))
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 14px;margin-bottom:22px;">
                    <p style="font-size:13px;color:#1d4ed8;margin:0;font-weight:500;">Você foi convidado para fazer check-in em um evento. Complete seu cadastro para continuar.</p>
                </div>
            @endif

            <form method="POST" action="{{ url('/register') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Nome completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="João da Silva"
                           style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">E-mail</label>
                    @if(session('pending_collaborator_id'))
                        <input type="email" name="email" value="{{ session('pending_collaborator_email') }}" readonly
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#6b7280;background:#f9fafb;outline:none;font-family:inherit;">
                        <input type="hidden" name="email" value="{{ session('pending_collaborator_email') }}">
                    @else
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="seu@email.com"
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                    @endif
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Crie uma senha</label>
                    <input type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                           style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Confirme a senha</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repita a senha"
                           style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <button type="submit"
                        style="width:100%;padding:13px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 16px rgba(79,70,229,0.4);font-family:inherit;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Criar conta e continuar →
                </button>
            </form>

            <div style="display:flex;align-items:center;gap:12px;margin:24px 0;">
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                <span style="font-size:12px;color:#9ca3af;font-weight:500;">já tem conta?</span>
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
            </div>

            <a href="{{ url('/login') }}?para=ingresso"
               style="display:block;width:100%;box-sizing:border-box;padding:12px;background:white;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:700;color:#1e293b;text-align:center;text-decoration:none;"
               onmouseover="this.style.borderColor='#4f46e5';this.style.color='#4f46e5'" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#1e293b'">
                Entrar para finalizar →
            </a>
        </div>
    </div>
</div>
</x-layouts.auth>

@else
{{-- ───────────────────── ORGANIZER FLOW ───────────────────── --}}
<x-layouts.auth title="Criar Conta — TakeTicket">
    <div style="display:flex;min-height:100vh;width:100%;">

        {{-- Left panel --}}
        <div class="hidden lg:flex" style="width:55%;background:linear-gradient(135deg,#1e1b4b 0%,#3730a3 40%,#4c1d95 100%);flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-80px;right:-80px;width:320px;height:320px;border-radius:50%;background:rgba(139,92,246,0.15);"></div>
            <div style="position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;border-radius:50%;background:rgba(99,102,241,0.2);"></div>
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:500px;height:500px;border-radius:50%;background:rgba(139,92,246,0.05);"></div>

            <div style="position:relative;z-index:10;">
                <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#818cf8,#a78bfa);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(129,140,248,0.4);">
                        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <span style="font-size:22px;font-weight:800;color:white;letter-spacing:-0.5px;">TakeTicket</span>
                </a>
            </div>

            <div style="position:relative;z-index:10;">
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:100px;padding:6px 14px;margin-bottom:28px;">
                    <div style="width:6px;height:6px;border-radius:50%;background:#34d399;"></div>
                    <span style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.8);letter-spacing:0.5px;text-transform:uppercase;">Cadastro Gratuito</span>
                </div>
                <h1 style="font-size:42px;font-weight:800;color:white;line-height:1.15;margin:0 0 20px 0;letter-spacing:-1px;">
                    Comece a vender<br>
                    <span style="background:linear-gradient(90deg,#a5b4fc,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">ingressos hoje</span><br>
                    mesmo.
                </h1>
                <p style="font-size:16px;color:rgba(255,255,255,0.6);line-height:1.7;margin:0 0 40px 0;max-width:380px;">Crie sua conta grátis e comece a organizar eventos profissionais em minutos.</p>
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="rgba(165,180,252,1)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span style="font-size:14px;color:rgba(255,255,255,0.75);font-weight:500;">Cadastro 100% gratuito</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="rgba(165,180,252,1)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span style="font-size:14px;color:rgba(255,255,255,0.75);font-weight:500;">Configure seu evento em minutos</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="rgba(165,180,252,1)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <span style="font-size:14px;color:rgba(255,255,255,0.75);font-weight:500;">Pagamentos seguros e automáticos</span>
                    </div>
                </div>
            </div>

            <div style="position:relative;z-index:10;border-top:1px solid rgba(255,255,255,0.1);padding-top:24px;">
                <p style="font-size:13px;color:rgba(255,255,255,0.4);margin:0;">&copy; {{ date('Y') }} TakeTicket. Todos os direitos reservados.</p>
            </div>
        </div>

        {{-- Right panel --}}
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;background:#fafafa;padding:40px 24px;overflow-y:auto;">
            <div class="lg:hidden" style="margin-bottom:32px;text-align:center;">
                <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <span style="font-size:20px;font-weight:800;background:linear-gradient(90deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">TakeTicket</span>
                </a>
            </div>

            <div style="width:100%;max-width:400px;">
                <div style="margin-bottom:32px;">
                    <h2 style="font-size:28px;font-weight:800;color:#111827;margin:0 0 8px 0;letter-spacing:-0.5px;">Criar sua conta</h2>
                    <p style="font-size:15px;color:#6b7280;margin:0;">Gratuito para sempre. Sem cartão de crédito.</p>
                </div>

                @if($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;margin-bottom:24px;">
                        <p style="font-size:13px;color:#dc2626;margin:0;font-weight:500;">{{ $errors->first() }}</p>
                    </div>
                @endif

                @if(session('pending_collaborator_id'))
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:14px 16px;margin-bottom:24px;">
                        <p style="font-size:13px;color:#1d4ed8;margin:0;font-weight:500;">Você foi convidado para fazer check-in em um evento. Complete seu cadastro para continuar.</p>
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}" style="display:flex;flex-direction:column;gap:16px;">
                    @csrf
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Nome completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="João da Silva"
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">E-mail</label>
                        @if(session('pending_collaborator_id'))
                            <input type="email" name="email" value="{{ session('pending_collaborator_email') }}" readonly
                                   style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#6b7280;background:#f9fafb;outline:none;font-family:inherit;">
                            <input type="hidden" name="email" value="{{ session('pending_collaborator_email') }}">
                        @else
                            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="seu@email.com"
                                   style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                                   onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                        @endif
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Senha</label>
                        <input type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Confirmar senha</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repita a senha"
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                    <button type="submit"
                            style="width:100%;padding:13px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;letter-spacing:-0.2px;box-shadow:0 4px 14px rgba(79,70,229,0.35);font-family:inherit;margin-top:4px;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Criar conta grátis
                    </button>
                </form>

                <div style="display:flex;align-items:center;gap:12px;margin:28px 0;">
                    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                    <span style="font-size:12px;color:#9ca3af;font-weight:500;">ou</span>
                    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                </div>

                <p style="text-align:center;font-size:14px;color:#6b7280;margin:0;">
                    Já tem uma conta?
                    <a href="{{ url('/login') }}" style="color:#4f46e5;font-weight:700;text-decoration:none;margin-left:4px;">Entrar →</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
@endif
