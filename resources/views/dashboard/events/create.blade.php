<x-layouts.dashboard header="Create Event">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('dashboard.events') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Events</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-2">Create Event</h2>
        </div>

        <x-card>
            <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <x-input label="Title" name="title" required />

                <x-textarea label="Description" name="description" rows="4" />

                <x-input label="Location" name="location" />

                <x-input label="Address" name="address" />

                <div class="grid grid-cols-2 gap-4">
                    <x-input label="City" name="city" />
                    <x-input label="State (UF)" name="state" maxlength="2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Start Date" name="start_date" type="datetime-local" required />
                    <x-input label="End Date" name="end_date" type="datetime-local" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banner Image</label>
                    <input type="file" name="banner" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('banner')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('dashboard.events') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Create Event
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.dashboard>
