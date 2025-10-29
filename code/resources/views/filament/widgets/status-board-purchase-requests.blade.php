<div>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
        <x-filament::section>
            <x-slot name="heading">Nuevas</x-slot>
            <div class="space-y-3">
                @foreach ($nuevas as $pr)
                    <div class="rounded-lg border border-gray-700 p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold">{{ $pr->request_number }}</span>
                            <span class="px-2 py-0.5 text-xs rounded bg-blue-600/20 text-blue-300">Normal</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-300">{{ data_get($pr, 'user.name') }} · {{ data_get($pr, 'department.name') }}</div>
                        <div class="mt-1 text-xs text-gray-400">{{ optional($pr->request_date)->format('d/m/Y') }}</div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">En Proceso</x-slot>
            <div class="space-y-3">
                @foreach ($enProceso as $pr)
                    <div class="rounded-lg border border-gray-700 p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold">{{ $pr->request_number }}</span>
                            <span class="px-2 py-0.5 text-xs rounded bg-red-600/20 text-red-300">Urgente</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-300">{{ data_get($pr, 'user.name') }} · {{ data_get($pr, 'department.name') }}</div>
                        <div class="mt-1 text-xs text-gray-400">{{ optional($pr->required_date)->format('d/m/Y') }}</div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Pendiente de Entrega</x-slot>
            <div class="space-y-3">
                @foreach ($pendEntrega as $pr)
                    <div class="rounded-lg border border-gray-700 p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold">{{ $pr->request_number }}</span>
                            <span class="px-2 py-0.5 text-xs rounded bg-amber-600/20 text-amber-300">Alta</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-300">{{ data_get($pr, 'user.name') }} · {{ data_get($pr, 'department.name') }}</div>
                        <div class="mt-1 text-xs text-gray-400">{{ optional($pr->required_date)->format('d/m/Y') }}</div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Cerradas</x-slot>
            <div class="space-y-3">
                @foreach ($cerradas as $pr)
                    <div class="rounded-lg border border-gray-700 p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold">{{ $pr->request_number }}</span>
                            <span class="px-2 py-0.5 text-xs rounded bg-gray-600/20 text-gray-300">Normal</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-300">{{ data_get($pr, 'user.name') }} · {{ data_get($pr, 'department.name') }}</div>
                        <div class="mt-1 text-xs text-gray-400">{{ optional($pr->updated_at)->format('d/m/Y') }}</div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</div>
