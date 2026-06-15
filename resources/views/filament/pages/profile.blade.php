<x-filament-panels::page>
    <div class="max-w-2xl space-y-6">

        {{-- CARD PROFILE --}}
        <x-filament::card>
            <div class="flex items-center space-x-4">

                {{-- Avatar --}}
                <div
                    style="
                        width:160px;
                        height:160px;
                        border-radius:9999px;
                        overflow:hidden;
                        margin-bottom: 30px;
                    "
                >
                    <img
                        src="{{ asset('storage/' . auth()->user()->avatar) }}"
                        style="
                            width:100%;
                            height:100%;
                            object-fit:cover;
                        "
                    />
                </div>

                <div>
                    <h2 class="text-lg font-semibold">
                        {{ auth()->user()->name }}
                    </h2>

                    <p class="text-sm text-gray-500">
                        {{ auth()->user()->email }}
                    </p>

                    <div class="mt-2 flex gap-2">
                        @foreach (auth()->user()->getRoleNames() as $role)
                            <x-filament::badge>
                                {{ $role }}
                            </x-filament::badge>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-filament::card>

    </div>
</x-filament-panels::page>
