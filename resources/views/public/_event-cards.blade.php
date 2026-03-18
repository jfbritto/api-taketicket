@foreach($events as $event)
    @php
        $grads = [
            'linear-gradient(135deg,#4f46e5,#7c3aed)',
            'linear-gradient(135deg,#db2777,#9333ea)',
            'linear-gradient(135deg,#d97706,#ea580c)',
            'linear-gradient(135deg,#059669,#0284c7)',
            'linear-gradient(135deg,#0284c7,#4f46e5)',
            'linear-gradient(135deg,#7c3aed,#db2777)',
        ];
        $grad = $grads[$event->id % 6];
        $minPrice = $event->ticketTypes->min('price');
    @endphp
    <a href="{{ route('event.show', $event->slug) }}"
       style="background:white;border-radius:18px;border:1px solid #f1f5f9;overflow:hidden;text-decoration:none;display:block;transition:transform 0.18s,box-shadow 0.18s;"
       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,0.1)'"
       onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">

        {{-- Image / placeholder --}}
        <div style="position:relative;height:200px;overflow:hidden;">
            @if($event->banner)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($event->banner) }}" alt="{{ $event->title }}"
                     style="width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.3s;"
                     onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            @else
                <div style="width:100%;height:100%;background:{{ $grad }};display:flex;align-items:center;justify-content:center;position:relative;">
                    <span style="font-size:64px;font-weight:900;color:rgba(255,255,255,0.15);line-height:1;user-select:none;">{{ strtoupper(substr($event->title,0,1)) }}</span>
                    <div style="position:absolute;bottom:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.07);"></div>
                    <div style="position:absolute;top:-15px;left:-15px;width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,0.06);"></div>
                </div>
            @endif

            {{-- Price badge --}}
            @if($minPrice !== null)
                <div style="position:absolute;bottom:12px;right:12px;">
                    <span style="display:inline-block;background:rgba(255,255,255,0.95);backdrop-filter:blur(8px);color:{{ $minPrice > 0 ? '#4f46e5' : '#16a34a' }};font-size:12px;font-weight:800;padding:4px 12px;border-radius:100px;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                        {{ $minPrice > 0 ? 'A partir de R$ ' . number_format($minPrice, 2, ',', '.') : 'Gratuito' }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Card body --}}
        <div style="padding:18px 20px 20px;">
            <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 12px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-family:'Instrument Sans',sans-serif;">
                {{ $event->title }}
            </h3>

            <div style="display:flex;flex-direction:column;gap:7px;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;">
                    <svg width="14" height="14" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $event->start_date->format('d \d\e M \d\e Y') }} às {{ $event->start_date->format('H:i') }}
                </div>
                @if($event->city || $event->location)
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;">
                        <svg width="14" height="14" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $event->location ? $event->location . ($event->city ? ' · ' : '') : '' }}{{ $event->city }}{{ $event->city && $event->state ? ', ' . $event->state : '' }}
                        </span>
                    </div>
                @endif
            </div>

            <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:12px;color:#94a3b8;font-weight:500;">
                    {{ $event->ticketTypes->count() }} tipo(s) de ingresso
                </span>
                <span style="font-size:13px;font-weight:700;color:#4f46e5;display:flex;align-items:center;gap:4px;">
                    Ver evento
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </div>
    </a>
@endforeach
