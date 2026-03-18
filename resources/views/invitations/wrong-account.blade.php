<x-layouts.auth title="Conta Incorreta — TakeTicket">
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f9fafb;">
    <div style="text-align:center;padding:48px;max-width:400px;">
        <div style="font-size:48px;margin-bottom:16px;">👤</div>
        <h1 style="font-size:22px;font-weight:800;color:#111827;margin:0 0 12px 0;">Conta incorreta</h1>
        <p style="color:#6b7280;margin:0 0 24px 0;">Este convite foi enviado para outro endereço de e-mail.</p>
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit" style="color:#4f46e5;background:none;border:none;font-weight:600;cursor:pointer;text-decoration:underline;">Sair e tentar com outra conta</button>
        </form>
    </div>
</div>
</x-layouts.auth>
