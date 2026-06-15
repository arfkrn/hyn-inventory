<x-filament-panels::page>
    <form wire:submit="cetak">
        {{ $this->form }}
        
        <div class="mt-4">
            @foreach($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament-panels::page>