<x-layouts.auth title="Faça Logout Primeiro — TakeTicket">
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f9fafb;">
    <div style="text-align:center;padding:48px;max-width:420px;">
        <div style="font-size:48px;margin-bottom:16px;">🔒</div>
        <h1 style="font-size:22px;font-weight:800;color:#111827;margin:0 0 12px 0;">Você está logado em outra conta</h1>
        <p style="color:#6b7280;margin:0 0 24px 0;">Saia e acesse o link novamente para criar uma nova conta com o e-mail convidado.</p>
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit" style="background:#4f46e5;color:white;border:none;padding:12px 24px;border-radius:8px;font-weight:600;cursor:pointer;">Sair da conta atual</button>
        </form>
    </div>
</div>
</x-layouts.auth>
