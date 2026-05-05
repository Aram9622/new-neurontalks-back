<x-filament-panels::page>
    <form wire:submit="runCommand">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit">
                Run Command
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
