<x-layouts.dashboard header="Criar Evento">

<style>
    .ev-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        color: #0f172a;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
        font-family: 'Instrument Sans', sans-serif;
    }
    .ev-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }
    .ev-input::placeholder { color: #94a3b8; }
    .ev-input.err { border-color: #ef4444; }
    textarea.ev-input { resize: vertical; min-height: 110px; }
    select.ev-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 36px; }
    .tt-input {
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 9px 12px;
        font-size: 13px;
        color: #0f172a;
        background: #fff;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        font-family: 'Instrument Sans', sans-serif;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .tt-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
    .tt-input::placeholder { color: #94a3b8; }
    select.tt-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; padding-right: 32px; }
</style>

<div style="max-width:1060px;margin:0 auto;">

    {{-- Page header --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px;">
        <a href="{{ route('dashboard.events') }}"
           style="display:flex;align-items:center;justify-content:center;width:34px;height:34px;background:white;border:1.5px solid #e2e8f0;border-radius:9px;text-decoration:none;color:#64748b;flex-shrink:0;transition:border-color 0.15s;"
           onmouseover="this.style.borderColor='#4f46e5';this.style.color='#4f46e5'"
           onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;letter-spacing:-0.4px;">Criar Novo Evento</h2>
            <p style="font-size:13px;color:#94a3b8;margin:2px 0 0;">Configure todas as informações do evento em um só lugar.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data" id="create-event-form">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

            {{-- ── Left column ─────────────────────────────────────────────── --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Section: Basic info --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Informações Básicas</p>
                            <p style="font-size:12px;color:#94a3b8;margin:0;">Dados principais do evento</p>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">Título do Evento <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   placeholder="Ex: Festival de Música Independente 2026"
                                   class="ev-input{{ $errors->has('title') ? ' err' : '' }}">
                            @error('title')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">Descrição</label>
                            <textarea name="description" rows="4"
                                      placeholder="Descreva o evento: programação, atrações, público-alvo e informações importantes para os participantes..."
                                      class="ev-input{{ $errors->has('description') ? ' err' : '' }}">{{ old('description') }}</textarea>
                            @error('description')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Location --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#059669,#0d9488);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Local do Evento</p>
                            <p style="font-size:12px;color:#94a3b8;margin:0;">Onde o evento acontecerá</p>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">Local / Venue</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                   placeholder="Ex: Teatro Municipal de São Paulo"
                                   class="ev-input{{ $errors->has('location') ? ' err' : '' }}">
                            @error('location')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">Endereço</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                   placeholder="Ex: Av. Paulista, 1234"
                                   class="ev-input{{ $errors->has('address') ? ' err' : '' }}">
                            @error('address')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 100px;gap:12px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">Cidade</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                       placeholder="Ex: São Paulo"
                                       class="ev-input{{ $errors->has('city') ? ' err' : '' }}">
                                @error('city')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">UF</label>
                                <input type="text" name="state" value="{{ old('state') }}" maxlength="2"
                                       placeholder="SP" style="text-transform:uppercase;"
                                       class="ev-input{{ $errors->has('state') ? ' err' : '' }}">
                                @error('state')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section: Dates --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#ea580c,#db2777);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Data e Horário</p>
                            <p style="font-size:12px;color:#94a3b8;margin:0;">Quando o evento acontece</p>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">Início <span style="color:#ef4444;">*</span></label>
                            <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" required
                                   class="ev-input{{ $errors->has('start_date') ? ' err' : '' }}">
                            @error('start_date')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px;">Término <span style="font-size:11px;font-weight:400;color:#94a3b8;">(opcional)</span></label>
                            <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                                   class="ev-input{{ $errors->has('end_date') ? ' err' : '' }}">
                            @error('end_date')<p style="margin:5px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Ticket Types --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.04);"
                     x-data="{
                         tickets: [{ name: '', price: 0, quantity: 1, sale_start: '', sale_end: '' }],
                         addTicket() { this.tickets.push({ name: '', price: 0, quantity: 1, sale_start: '', sale_end: '' }); },
                         removeTicket(i) { this.tickets.splice(i, 1); }
                     }">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;background:linear-gradient(135deg,#0284c7,#059669);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            </div>
                            <div>
                                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Tipos de Ingresso</p>
                                <p style="font-size:12px;color:#94a3b8;margin:0;">Configure lotes com preço e quantidade</p>
                            </div>
                        </div>
                        <button type="button" @click="addTicket()"
                                style="display:flex;align-items:center;gap:6px;padding:7px 14px;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:8px;font-size:13px;font-weight:600;color:#16a34a;cursor:pointer;font-family:'Instrument Sans',sans-serif;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Adicionar lote
                        </button>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <template x-for="(ticket, index) in tickets" :key="index">
                            <div style="border:1.5px solid #f1f5f9;border-radius:12px;padding:18px;background:#f8fafc;position:relative;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="width:24px;height:24px;background:linear-gradient(135deg,#0284c7,#059669);border-radius:7px;display:flex;align-items:center;justify-content:center;">
                                            <svg width="11" height="11" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        </div>
                                        <span style="font-size:13px;font-weight:700;color:#374151;" x-text="'Lote ' + (index + 1)"></span>
                                    </div>
                                    <button type="button" @click="removeTicket(index)" x-show="tickets.length > 1"
                                            style="display:flex;align-items:center;gap:4px;padding:4px 10px;background:#fef2f2;border:1px solid #fecaca;border-radius:7px;font-size:12px;font-weight:600;color:#ef4444;cursor:pointer;font-family:'Instrument Sans',sans-serif;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Remover
                                    </button>
                                </div>

                                <div style="display:grid;grid-template-columns:1fr 130px 110px;gap:10px;margin-bottom:10px;">
                                    <div>
                                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Nome do lote *</label>
                                        <input type="text" :name="'ticket_types[' + index + '][name]'" x-model="ticket.name"
                                               placeholder="Ex: Meia-entrada, VIP..." required class="tt-input">
                                    </div>
                                    <div>
                                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Valor (R$) *</label>
                                        <input type="number" :name="'ticket_types[' + index + '][price]'" x-model="ticket.price"
                                               step="0.01" min="0" required placeholder="0,00" class="tt-input">
                                    </div>
                                    <div>
                                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Qtd. disponível *</label>
                                        <input type="number" :name="'ticket_types[' + index + '][quantity]'" x-model="ticket.quantity"
                                               min="1" required class="tt-input">
                                    </div>
                                </div>

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                    <div>
                                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Início das vendas *</label>
                                        <input type="datetime-local" :name="'ticket_types[' + index + '][sale_start]'" x-model="ticket.sale_start"
                                               required class="tt-input">
                                    </div>
                                    <div>
                                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Fim das vendas *</label>
                                        <input type="datetime-local" :name="'ticket_types[' + index + '][sale_end]'" x-model="ticket.sale_end"
                                               required class="tt-input">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    @error('ticket_types')
                        <p style="margin:8px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Section: Custom Fields (collapsible) --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.04);"
                     x-data="{
                         open: false,
                         fields: [],
                         addField() { this.fields.push({ label: '', type: 'text', required: false, options: '' }); },
                         removeField(i) { this.fields.splice(i, 1); }
                     }">
                    <button type="button" @click="open = !open"
                            style="display:flex;align-items:center;justify-content:space-between;width:100%;background:none;border:none;cursor:pointer;padding:0;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;background:linear-gradient(135deg,#7c3aed,#4f46e5);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div style="text-align:left;">
                                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Campos Personalizados</p>
                                <p style="font-size:12px;color:#94a3b8;margin:0;">Colete informações extras dos participantes (opcional)</p>
                            </div>
                        </div>
                        <svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"
                             :style="open ? 'transform:rotate(180deg);transition:transform 0.2s' : 'transition:transform 0.2s'">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" style="margin-top:20px;">
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <template x-for="(field, index) in fields" :key="index">
                                <div style="border:1.5px solid #f1f5f9;border-radius:12px;padding:16px;background:#f8fafc;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                        <span style="font-size:13px;font-weight:700;color:#374151;" x-text="'Campo ' + (index + 1)"></span>
                                        <button type="button" @click="removeField(index)"
                                                style="display:flex;align-items:center;gap:4px;padding:4px 10px;background:#fef2f2;border:1px solid #fecaca;border-radius:7px;font-size:12px;font-weight:600;color:#ef4444;cursor:pointer;font-family:'Instrument Sans',sans-serif;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Remover
                                        </button>
                                    </div>

                                    <div style="display:grid;grid-template-columns:1fr 160px;gap:10px;margin-bottom:10px;">
                                        <div>
                                            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Rótulo do campo *</label>
                                            <input type="text" :name="'custom_fields[' + index + '][label]'" x-model="field.label"
                                                   placeholder="Ex: Tamanho da camiseta" required class="tt-input">
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Tipo *</label>
                                            <select :name="'custom_fields[' + index + '][type]'" x-model="field.type" class="tt-input" style="height:38px;">
                                                <option value="text">Texto</option>
                                                <option value="number">Número</option>
                                                <option value="select">Seleção</option>
                                                <option value="checkbox">Checkbox</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div x-show="field.type === 'select'" style="margin-bottom:10px;">
                                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Opções (separadas por vírgula)</label>
                                        <input type="text" :name="'custom_fields[' + index + '][options]'" x-model="field.options"
                                               placeholder="P, M, G, GG" class="tt-input">
                                    </div>

                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="checkbox" :name="'custom_fields[' + index + '][required]'" x-model="field.required" value="1"
                                               style="width:15px;height:15px;accent-color:#4f46e5;">
                                        <span style="font-size:13px;color:#64748b;font-weight:500;">Campo obrigatório</span>
                                    </label>
                                </div>
                            </template>

                            <button type="button" @click="addField()"
                                    style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;border:2px dashed #e2e8f0;border-radius:12px;font-size:13px;color:#94a3b8;font-weight:600;background:transparent;cursor:pointer;font-family:'Instrument Sans',sans-serif;transition:border-color 0.15s,color 0.15s;"
                                    onmouseover="this.style.borderColor='#7c3aed';this.style.color='#7c3aed'"
                                    onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#94a3b8'">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Adicionar campo personalizado
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Section: Banner --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#0284c7,#4f46e5);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Imagem de Capa</p>
                            <p style="font-size:12px;color:#94a3b8;margin:0;">Destaque visual do seu evento</p>
                        </div>
                    </div>

                    <label for="banner-upload"
                           style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;border:2px dashed #e2e8f0;border-radius:12px;padding:32px 20px;cursor:pointer;background:#f8fafc;transition:border-color 0.15s,background 0.15s;"
                           onmouseover="this.style.borderColor='#4f46e5';this.style.background='#f5f3ff'"
                           onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'"
                           id="banner-label">
                        <div style="width:44px;height:44px;background:white;border:1.5px solid #e2e8f0;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                            <svg width="20" height="20" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:14px;font-weight:600;color:#374151;margin:0 0 3px;">Clique para enviar ou arraste o arquivo</p>
                            <p style="font-size:12px;color:#94a3b8;margin:0;">JPG, PNG ou WEBP · Recomendado: 1200 × 630px</p>
                        </div>
                        <input type="file" id="banner-upload" name="banner" accept="image/*"
                               style="display:none;" onchange="previewBanner(this)">
                    </label>

                    <div id="banner-preview" style="display:none;margin-top:12px;border-radius:10px;overflow:hidden;border:1px solid #f1f5f9;">
                        <img id="banner-preview-img" src="" alt="Preview" style="width:100%;max-height:200px;object-fit:cover;display:block;">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f8fafc;">
                            <span id="banner-name" style="font-size:12px;color:#64748b;"></span>
                            <button type="button" onclick="removeBanner()"
                                    style="font-size:12px;color:#ef4444;background:none;border:none;cursor:pointer;font-weight:600;padding:0;">Remover</button>
                        </div>
                    </div>

                    @error('banner')<p style="margin:8px 0 0;font-size:12px;color:#ef4444;">{{ $message }}</p>@enderror
                </div>

            </div>

            {{-- ── Right column ─────────────────────────────────────────────── --}}
            <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:24px;">

                {{-- Actions card --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 6px;">Publicar Evento</p>
                    <p style="font-size:12px;color:#64748b;margin:0 0 16px;line-height:1.6;">O evento será salvo como rascunho. Você pode publicá-lo quando estiver pronto.</p>

                    <button type="submit"
                            style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 16px rgba(79,70,229,0.3);font-family:'Instrument Sans',sans-serif;letter-spacing:-0.2px;margin-bottom:10px;transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <svg width="15" height="15" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Criar Evento
                    </button>

                    <a href="{{ route('dashboard.events') }}"
                       style="display:flex;align-items:center;justify-content:center;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;color:#374151;text-decoration:none;transition:border-color 0.15s;"
                       onmouseover="this.style.borderColor='#4f46e5';this.style.color='#4f46e5'"
                       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                        Cancelar
                    </a>
                </div>

                {{-- Checklist card --}}
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">O que você pode configurar</p>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:20px;height:20px;background:#f0fdf4;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="11" height="11" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p style="font-size:13px;color:#374151;margin:0;">Título e descrição do evento</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:20px;height:20px;background:#f0fdf4;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="11" height="11" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p style="font-size:13px;color:#374151;margin:0;">Local, cidade e endereço</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:20px;height:20px;background:#f0fdf4;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="11" height="11" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p style="font-size:13px;color:#374151;margin:0;">Data e horário de início/fim</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:20px;height:20px;background:#f0fdf4;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="11" height="11" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p style="font-size:13px;color:#374151;margin:0;">Lotes de ingresso com preços</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:20px;height:20px;background:#f5f3ff;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="11" height="11" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <p style="font-size:13px;color:#374151;margin:0;">Campos extras para participantes</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:20px;height:20px;background:#f0fdf4;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="11" height="11" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p style="font-size:13px;color:#374151;margin:0;">Imagem de capa do evento</p>
                        </div>
                    </div>
                </div>

                {{-- Required fields notice --}}
                <div style="display:flex;align-items:center;gap:6px;padding:10px 14px;background:#fef9c3;border:1px solid #fde68a;border-radius:10px;">
                    <svg width="14" height="14" fill="none" stroke="#92400e" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p style="font-size:12px;color:#92400e;margin:0;">Campos com <strong>*</strong> são obrigatórios.</p>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function previewBanner(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('banner-preview-img').src = e.target.result;
        document.getElementById('banner-name').textContent = file.name;
        document.getElementById('banner-preview').style.display = 'block';
        document.getElementById('banner-label').style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function removeBanner() {
    document.getElementById('banner-upload').value = '';
    document.getElementById('banner-preview').style.display = 'none';
    document.getElementById('banner-label').style.display = 'flex';
}
</script>
@endpush

</x-layouts.dashboard>
