<x-layouts.app title="Create Organizer Profile">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Create Organizer Profile">
            <p class="text-gray-600 mb-6">Set up your organizer profile to start creating events.</p>
            <form method="POST" action="{{ route('dashboard.storeOrganizer') }}" class="space-y-4">
                @csrf
                <x-input label="Organization Name" name="name" required />
                <x-input label="Document (CPF/CNPJ)" name="document" />
                <x-input label="Phone" name="phone" />
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 font-medium">
                    Create Profile
                </button>
            </form>
        </x-card>
    </div>
</x-layouts.app>
