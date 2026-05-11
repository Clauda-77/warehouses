
<x-filament-panels::page.simple>
    <form wire:submit.prevent="authenticate" class="space-y-8">
        {{ $this->form }}

        <x-filament::button type="submit" form="authenticate" class="w-full">
            {{ __('تسجيل الدخول') }}
        </x-filament::button>
    </form>
</x-filament-panels::page.simple>  