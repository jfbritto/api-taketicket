<x-layouts.app title="Register">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Create Account">
            <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                @csrf
                <x-input label="Name" name="name" required />
                <x-input label="Email" name="email" type="email" required />
                <x-input label="Password" name="password" type="password" required />
                <x-input label="Confirm Password" name="password_confirmation" type="password" required />
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium">
                    Register
                </button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Already have an account? <a href="{{ url('/login') }}" class="text-indigo-600 hover:underline">Login</a>
            </p>
        </x-card>
    </div>
</x-layouts.app>
