<x-layouts.app title="Entrar — TakeTicket">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Entrar na sua conta">
            <p class="text-sm text-gray-500 mb-4">Acesse sua conta para gerenciar seus ingressos e eventos.</p>
            <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        E-mail
                        <x-tooltip text="Digite o e-mail que você usou para se cadastrar." />
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
                        <x-tooltip text="Mínimo de 8 caracteres." />
                    </label>
                    <input type="password" name="password" required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 mr-2">
                        Lembrar de mim
                    </label>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 font-medium transition">
                    Entrar
                </button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Não tem uma conta? <a href="{{ url('/register') }}" class="text-indigo-600 hover:underline font-medium">Cadastre-se</a>
            </p>
        </x-card>
    </div>
</x-layouts.app>
