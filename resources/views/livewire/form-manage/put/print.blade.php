@php
    $_hrBool = $_hrBool ?? false;
@endphp
@foreach ($printForm as $_i)
    @if (is_string($_i))
        {!! $_i !!}
    @else
        @if ($_hrBool && data_get($_i, 'output') != \Nabre\Repositories\FormTwo\Field::HIDDEN)
            <hr>
        @else
            @php
                $_hrBool = true;
            @endphp
        @endif

        @php
            if (!is_null($_num ?? null)) {
                //preg_replace('/\*/', $_num, $_i['set']['options']['wire:model.defer'], 1);
                data_set($_i, \Nabre\Repositories\FormTwo\FormConst::OPTIONS_WIREMODEL, str_replace('*', $_num, data_get($_i, \Nabre\Repositories\FormTwo\FormConst::OPTIONS_WIREMODEL)));
            }
        @endphp

        @switch(data_get($_i,\Nabre\Repositories\FormTwo\FormConst::OUTPUT))
            @case(\Nabre\Repositories\FormTwo\Field::EMBEDS_MANY)
            @case(\Nabre\Repositories\FormTwo\Field::EMBEDS_ONE)
                @include('Nabre::livewire.form-manage.put.row.embed', [
                    'printForm' => data_get($_i, \Nabre\Repositories\FormTwo\FormConst::EMBED_ELEMENTS),
                ])
            @break

            @case(\Nabre\Repositories\FormTwo\Field::MSG)
            @case(\Nabre\Repositories\FormTwo\Field::HTML)
                @php
                    $_hrBool = false;
                @endphp
            @case(\Nabre\Repositories\FormTwo\Field::HIDDEN)
                @include('Nabre::livewire.form-manage.put.row.other')
            @break

            @default
                @include('Nabre::livewire.form-manage.put.row.default')
        @endswitch
    @endif
@endforeach
