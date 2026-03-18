<x-layouts.auth title="Criar Conta — TakeTicket">
    <div style="display:flex;min-height:100vh;width:100%;">

        {{-- Left panel: brand --}}
        <div class="hidden lg:flex" style="width:55%;background:linear-gradient(135deg,#1e1b4b 0%,#3730a3 40%,#4c1d95 100%);flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;">

            {{-- Decorative orbs --}}
            <div style="position:absolute;top:-80px;right:-80px;width:320px;height:320px;border-radius:50%;background:rgba(139,92,246,0.15);"></div>
            <div style="position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;border-radius:50%;background:rgba(99,102,241,0.2);"></div>
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:500px;height:500px;border-radius:50%;background:rgba(139,92,246,0.05);"></div>

            {{-- Logo --}}
            <div style="position:relative;z-index:10;">
                <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#818cf8,#a78bfa);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(129,140,248,0.4);">
                        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <span style="font-size:22px;font-weight:800;color:white;letter-spacing:-0.5px;">TakeTicket</span>
                </a>
            </div>

            {{-- Main content --}}
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

                <p style="font-size:16px;color:rgba(255,255,255,0.6);line-height:1.7;margin:0 0 40px 0;max-width:380px;">
                    Crie sua conta grátis e comece a organizar eventos profissionais em minutos.
                </p>

                {{-- Feature highlights --}}
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="rgba(165,180,252,1)" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span style="font-size:14px;color:rgba(255,255,255,0.75);font-weight:500;">Cadastro 100% gratuito</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="rgba(165,180,252,1)" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span style="font-size:14px;color:rgba(255,255,255,0.75);font-weight:500;">Configure seu evento em minutos</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="rgba(165,180,252,1)" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <span style="font-size:14px;color:rgba(255,255,255,0.75);font-weight:500;">Pagamentos seguros e automáticos</span>
                    </div>
                </div>
            </div>

            {{-- Bottom --}}
            <div style="position:relative;z-index:10;border-top:1px solid rgba(255,255,255,0.1);padding-top:24px;">
                <p style="font-size:13px;color:rgba(255,255,255,0.4);margin:0;">&copy; {{ date('Y') }} TakeTicket. Todos os direitos reservados.</p>
            </div>
        </div>

        {{-- Right panel: form --}}
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;background:#fafafa;padding:40px 24px;overflow-y:auto;">

            {{-- Mobile logo --}}
            <div class="lg:hidden" style="margin-bottom:32px;text-align:center;">
                <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <span style="font-size:20px;font-weight:800;background:linear-gradient(90deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">TakeTicket</span>
                </a>
            </div>

            <div style="width:100%;max-width:400px;">

                {{-- Header --}}
                <div style="margin-bottom:32px;">
                    <h2 style="font-size:28px;font-weight:800;color:#111827;margin:0 0 8px 0;letter-spacing:-0.5px;">Criar sua conta</h2>
                    <p style="font-size:15px;color:#6b7280;margin:0;">Gratuito para sempre. Sem cartão de crédito.</p>
                </div>

                {{-- Errors --}}
                @if($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;margin-bottom:24px;">
                        <p style="font-size:13px;color:#dc2626;margin:0;font-weight:500;">
                            {{ $errors->first() }}
                        </p>
                    </div>
                @endif

                {{-- Pending collaborator banner --}}
                @if(session('pending_collaborator_id'))
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:14px 16px;margin-bottom:24px;">
                        <p style="font-size:13px;color:#1d4ed8;margin:0;font-weight:500;">
                            Você foi convidado para fazer check-in em um evento. Complete seu cadastro para continuar.
                        </p>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ url('/register') }}" style="display:flex;flex-direction:column;gap:16px;">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Nome completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                               placeholder="João da Silva"
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid {{ $errors->has('name') ? '#fca5a5' : '#e5e7eb' }};border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='{{ $errors->has('name') ? '#fca5a5' : '#e5e7eb' }}'">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">E-mail</label>
                        @if(session('pending_collaborator_id'))
                            <input type="email" name="email"
                                   value="{{ session('pending_collaborator_email') }}"
                                   readonly
                                   style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#6b7280;background:#f9fafb;outline:none;font-family:inherit;">
                            <input type="hidden" name="email" value="{{ session('pending_collaborator_email') }}">
                        @else
                            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                                   placeholder="seu@email.com"
                                   style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid {{ $errors->has('email') ? '#fca5a5' : '#e5e7eb' }};border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                                   onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='{{ $errors->has('email') ? '#fca5a5' : '#e5e7eb' }}'">
                        @endif
                    </div>

                    {{-- Password --}}
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Senha</label>
                        <input type="password" name="password" required autocomplete="new-password"
                               placeholder="Mínimo 8 caracteres"
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid {{ $errors->has('password') ? '#fca5a5' : '#e5e7eb' }};border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='{{ $errors->has('password') ? '#fca5a5' : '#e5e7eb' }}'">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Confirmar senha</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                               placeholder="Repita a senha"
                               style="width:100%;box-sizing:border-box;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            style="width:100%;padding:13px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;letter-spacing:-0.2px;box-shadow:0 4px 14px rgba(79,70,229,0.35);transition:opacity 0.15s;font-family:inherit;margin-top:4px;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Criar conta grátis
                    </button>
                </form>

                {{-- Divider --}}
                <div style="display:flex;align-items:center;gap:12px;margin:28px 0;">
                    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                    <span style="font-size:12px;color:#9ca3af;font-weight:500;">ou</span>
                    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                </div>

                {{-- Login link --}}
                <p style="text-align:center;font-size:14px;color:#6b7280;margin:0;">
                    Já tem uma conta?
                    <a href="{{ url('/login') }}" style="color:#4f46e5;font-weight:700;text-decoration:none;margin-left:4px;">
                        Entrar →
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
