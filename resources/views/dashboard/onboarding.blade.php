<x-layouts.app title="Configurar Conta — TakeTicket">

<style>
    @keyframes fade-up {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-fade { animation: fade-up 0.45s ease both; }
    .anim-1 { animation-delay: 0.05s; }
    .anim-2 { animation-delay: 0.15s; }
    .anim-3 { animation-delay: 0.25s; }

    .onb-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        color: #0f172a;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }
    .onb-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
    }
    .onb-input::placeholder { color: #94a3b8; }
    .onb-input.error { border-color: #ef4444; }
</style>

<div style="min-height:calc(100vh - 60px);background:linear-gradient(135deg,#f8faff 0%,#f0f4ff 100%);padding:48px 20px 80px;">
    <div style="max-width:960px;margin:0 auto;">

        {{-- Header --}}
        <div class="anim-fade anim-1" style="text-align:center;margin-bottom:40px;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:100px;padding:6px 16px;margin-bottom:20px;">
                <svg width="14" height="14" fill="none" stroke="#3b82f6" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <span style="font-size:13px;font-weight:600;color:#1d4ed8;">Você já pode comprar ingressos — agora configure seu perfil de organizador</span>
            </div>
            <h1 style="font-size:30px;font-weight:900;color:#0f172a;margin:0 0 10px;letter-spacing:-0.6px;">Olá, {{ auth()->user()->name }}!</h1>
            <p style="font-size:16px;color:#64748b;margin:0;max-width:520px;margin:0 auto;line-height:1.6;">Quer também <strong style="color:#4f46e5;">vender ingressos</strong> e criar seus próprios eventos? Configure seu perfil de organizador em poucos passos.</p>
        </div>

        {{-- Main grid --}}
        <div class="anim-fade anim-2" style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

            {{-- Left: Benefits panel --}}
            <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:20px;padding:32px;color:white;">
                <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>

                <h2 style="font-size:20px;font-weight:800;margin:0 0 8px;letter-spacing:-0.3px;">Torne-se um organizador</h2>
                <p style="font-size:14px;color:rgba(255,255,255,0.75);margin:0 0 28px;line-height:1.6;">Com o perfil de organizador você pode criar e gerenciar eventos, vender ingressos e acompanhar resultados em tempo real.</p>

                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;margin:0 0 2px;">Venda ingressos online</p>
                            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;line-height:1.5;">Crie eventos pagos ou gratuitos e receba pagamentos com segurança.</p>
                        </div>
                    </div>

                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6.364 1.636l-.707.707M20 12h-1M17.657 17.657l-.707-.707M12 19v1M6.343 17.657l-.707.707M4 12H3M6.343 6.343l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;margin:0 0 2px;">Check-in por QR Code</p>
                            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;line-height:1.5;">Valide entradas na portaria com o leitor integrado, rápido e sem papel.</p>
                        </div>
                    </div>

                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;margin:0 0 2px;">Relatórios em tempo real</p>
                            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;line-height:1.5;">Acompanhe vendas, participantes e receita com dashboards detalhados.</p>
                        </div>
                    </div>

                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;margin:0 0 2px;">Convide colaboradores</p>
                            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;line-height:1.5;">Adicione membros da equipe com permissões específicas para cada evento.</p>
                        </div>
                    </div>
                </div>

                <div style="margin-top:28px;padding-top:24px;border-top:1px solid rgba(255,255,255,0.15);">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span style="font-size:12px;color:rgba(255,255,255,0.55);">Seus dados de comprador continuam intactos</span>
                    </div>
                </div>
            </div>

            {{-- Right: Form --}}
            <div>
                <div style="background:white;border-radius:20px;border:1px solid #f1f5f9;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,0.05);">
                    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 6px;letter-spacing:-0.3px;">Perfil de Organizador</h2>
                    <p style="font-size:14px;color:#64748b;margin:0 0 28px;line-height:1.5;">Estas informações serão exibidas para os compradores de ingresso dos seus eventos.</p>

                    <form method="POST" action="{{ route('dashboard.storeOrganizer') }}">
                        @csrf

                        {{-- Organization name --}}
                        <div style="margin-bottom:20px;">
                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;">
                                <svg width="14" height="14" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Nome da Organização <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Ex: Eventos Culturais SP, Academia de Dança ABC..."
                                   class="onb-input{{ $errors->has('name') ? ' error' : '' }}">
                            @error('name')
                                <p style="margin:6px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>
                            @enderror
                            <p style="margin:6px 0 0;font-size:12px;color:#94a3b8;">Use o nome pelo qual você é conhecido no mercado.</p>
                        </div>

                        {{-- Document --}}
                        <div style="margin-bottom:20px;">
                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;">
                                <svg width="14" height="14" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                CPF ou CNPJ
                            </label>
                            <input type="text" name="document" value="{{ old('document') }}"
                                   placeholder="000.000.000-00 ou 00.000.000/0001-00"
                                   id="onboarding-document"
                                   class="onb-input{{ $errors->has('document') ? ' error' : '' }}"
                                   maxlength="18">
                            @error('document')
                                <p style="margin:6px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>
                            @enderror
                            <p style="margin:6px 0 0;font-size:12px;color:#94a3b8;">Pode ser solicitado para verificação e emissão de nota fiscal.</p>
                        </div>

                        {{-- Phone --}}
                        <div style="margin-bottom:28px;">
                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;">
                                <svg width="14" height="14" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Telefone / WhatsApp
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   placeholder="(11) 99999-9999"
                                   id="onboarding-phone"
                                   class="onb-input{{ $errors->has('phone') ? ' error' : '' }}"
                                   maxlength="15">
                            @error('phone')
                                <p style="margin:6px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 24px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 20px rgba(79,70,229,0.35);letter-spacing:-0.2px;transition:opacity 0.15s;"
                                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            Criar perfil e meu primeiro evento
                            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>

                        <p style="text-align:center;font-size:12px;color:#94a3b8;margin:14px 0 0;">Você poderá editar estas informações depois nas configurações.</p>
                    </form>
                </div>

                {{-- Skip link --}}
                <div style="text-align:center;margin-top:16px;">
                    <a href="{{ route('dashboard') }}"
                       style="font-size:13px;color:#94a3b8;text-decoration:none;font-weight:500;"
                       onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#94a3b8'">
                        Agora não — ir para minha área de participante
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Phone mask: (XX) XXXX-XXXX or (XX) XXXXX-XXXX
    var phoneEl = document.getElementById('onboarding-phone');
    if (phoneEl) {
        phoneEl.addEventListener('input', function () {
            var digits = this.value.replace(/\D/g, '').substring(0, 11);
            var formatted = '';
            if (digits.length === 0) {
                formatted = '';
            } else if (digits.length <= 2) {
                formatted = '(' + digits;
            } else if (digits.length <= 6) {
                formatted = '(' + digits.substring(0, 2) + ') ' + digits.substring(2);
            } else if (digits.length <= 10) {
                formatted = '(' + digits.substring(0, 2) + ') ' + digits.substring(2, 6) + '-' + digits.substring(6);
            } else {
                formatted = '(' + digits.substring(0, 2) + ') ' + digits.substring(2, 7) + '-' + digits.substring(7);
            }
            this.value = formatted;
        });
    }

    // Document mask: CPF (000.000.000-00) or CNPJ (00.000.000/0001-00)
    var docEl = document.getElementById('onboarding-document');
    if (docEl) {
        docEl.addEventListener('input', function () {
            var digits = this.value.replace(/\D/g, '');
            var formatted = '';
            if (digits.length <= 11) {
                // CPF
                digits = digits.substring(0, 11);
                if (digits.length <= 3) {
                    formatted = digits;
                } else if (digits.length <= 6) {
                    formatted = digits.substring(0, 3) + '.' + digits.substring(3);
                } else if (digits.length <= 9) {
                    formatted = digits.substring(0, 3) + '.' + digits.substring(3, 6) + '.' + digits.substring(6);
                } else {
                    formatted = digits.substring(0, 3) + '.' + digits.substring(3, 6) + '.' + digits.substring(6, 9) + '-' + digits.substring(9);
                }
            } else {
                // CNPJ
                digits = digits.substring(0, 14);
                if (digits.length <= 2) {
                    formatted = digits;
                } else if (digits.length <= 5) {
                    formatted = digits.substring(0, 2) + '.' + digits.substring(2);
                } else if (digits.length <= 8) {
                    formatted = digits.substring(0, 2) + '.' + digits.substring(2, 5) + '.' + digits.substring(5);
                } else if (digits.length <= 12) {
                    formatted = digits.substring(0, 2) + '.' + digits.substring(2, 5) + '.' + digits.substring(5, 8) + '/' + digits.substring(8);
                } else {
                    formatted = digits.substring(0, 2) + '.' + digits.substring(2, 5) + '.' + digits.substring(5, 8) + '/' + digits.substring(8, 12) + '-' + digits.substring(12);
                }
            }
            this.value = formatted;
        });
    }
});
</script>
@endpush

</x-layouts.app>
