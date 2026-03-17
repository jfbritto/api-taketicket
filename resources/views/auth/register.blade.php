<x-layouts.app title="Criar Conta — TakeTicket">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Criar sua conta">
            <p class="text-sm text-gray-500 mb-4">Cadastre-se gratuitamente e comece a comprar ou vender ingressos.</p>
            <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Nome completo
                        <x-tooltip text="Informe seu nome e sobrenome conforme seu documento de identidade." />
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                           placeholder="João da Silva"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        E-mail
                        <x-tooltip text="Você usará esse e-mail para entrar na sua conta. Enviaremos seus ingressos para este endereço." />
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                           placeholder="seu@email.com"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Senha
                        <x-tooltip text="Crie uma senha com pelo menos 8 caracteres. Use letras e números para maior segurança." />
                    </label>
                    <input type="password" name="password" required autocomplete="new-password"
                           placeholder="Mínimo 8 caracteres"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Confirmar Senha
                        <x-tooltip text="Digite a mesma senha novamente para confirmação." />
                    </label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           placeholder="Repita a senha"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 font-medium transition">
                    Criar Conta
                </button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Já tem uma conta? <a href="{{ url('/login') }}" class="text-indigo-600 hover:underline font-medium">Entrar</a>
            </p>
        </x-card>
    </div>
</x-layouts.app>
