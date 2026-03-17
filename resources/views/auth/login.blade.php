<x-layouts.app title="Login">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Login">
            <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                @csrf
                <x-input label="Email" name="email" type="email" required />
                <x-input label="Password" name="password" type="password" required />
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 mr-2">
                        Remember me
                    </label>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium">
                    Login
                </button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Don't have an account? <a href="{{ url('/register') }}" class="text-indigo-600 hover:underline">Register</a>
            </p>
        </x-card>
    </div>
</x-layouts.app>
