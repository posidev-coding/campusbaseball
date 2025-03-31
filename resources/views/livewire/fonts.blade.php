{{-- <x-slot:font>{{ $this->font }}</x-slot:font> --}}

<flux:menu.group heading="Font">
    <flux:menu.separator />

    @foreach ($this->fonts as $class => $name)
        <flux:menu.item @class([
            'cursor-pointer',
            'bg-gray-100' => $this->font == $class
        ]) wire:click="setFont('{{ $class }}')">
            <span class="{{ $class }}">
                {{ $name }}
            </span>
        </flux:menu.item>
    @endforeach

</flux:menu.group>
 