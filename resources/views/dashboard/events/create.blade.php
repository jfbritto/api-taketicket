<x-layouts.dashboard header="Criar Evento">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('dashboard.events') }}" class="text-indigo-600 hover:underline text-sm">&larr; Voltar para Eventos</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-2">Criar Evento</h2>
        </div>

        <x-card>
            <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Título do Evento *
                        <x-tooltip text="Nome do evento que será exibido publicamente. Use um título claro e atrativo." />
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Ex: Festival de Música Independente 2026"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Descrição
                        <x-tooltip text="Descreva o evento: programação, atrações, público-alvo, etc. Esta informação será exibida na página do evento." />
                    </label>
                    <x-textarea name="description" rows="4" placeholder="Descreva o evento, programação, atrações e informações importantes para os participantes..." />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Local / Venue
                        <x-tooltip text="Nome do local onde o evento acontecerá. Ex: Teatro Municipal, Centro de Convenções XYZ" />
                    </label>
                    <input type="text" name="location" value="{{ old('location') }}"
                           placeholder="Ex: Teatro Municipal de São Paulo"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Endereço
                        <x-tooltip text="Rua e número do local do evento." />
                    </label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           placeholder="Ex: Av. Paulista, 1234"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border @error('address') border-red-500 @enderror">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               placeholder="Ex: São Paulo"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            UF
                            <x-tooltip text="Sigla do estado, ex: SP, RJ, MG" />
                        </label>
                        <input type="text" name="state" value="{{ old('state') }}" maxlength="2"
                               placeholder="SP"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border @error('state') border-red-500 @enderror">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            Data e Hora de Início *
                            <x-tooltip text="Quando o evento começa" />
                        </label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            Data e Hora de Término
                            <x-tooltip text="Quando o evento termina. Opcional." />
                        </label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Imagem de Capa
                        <x-tooltip text="Imagem de destaque exibida na listagem e página do evento. Formatos: JPG, PNG, WEBP. Tamanho recomendado: 1200×630px." />
                    </label>
                    <input type="file" name="banner" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('banner')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('dashboard.events') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Criar Evento
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.dashboard>
