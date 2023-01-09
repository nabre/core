@php
    $_hrBool = $_hrBool ?? false;
@endphp

@foreach ($print as $_i)
    @if (is_string($_i))
        {!! $_i !!}
    @else
        @if ($_hrBool && data_get($_i,'output')!=\Nabre\Repositories\FormTwo\Field::HIDDEN)
            <hr>
        @else
            @php
                $_hrBool = true;
            @endphp
        @endif

        @php
            if(!is_null($_num??null)){
                //preg_replace('/\*/', $_num, $_i['set']['options']['wire:model.defer'], 1);
                $_i['set']['options']['wire:model.defer']=str_replace('*',$_num,$_i['set']['options']['wire:model.defer']);
            }
        @endphp

        @switch(data_get($_i,'output'))
            @case(\Nabre\Repositories\FormTwo\Field::EMBEDS_MANY)
            @case(\Nabre\Repositories\FormTwo\Field::EMBEDS_ONE)
                @include('Nabre::livewire.form-manage.row.embed', [
                    'print' => data_get($_i, 'embed.wire.elements'),
                ])
            @break

            @case(\Nabre\Repositories\FormTwo\Field::MSG)
            @case(\Nabre\Repositories\FormTwo\Field::HTML)
                @php
                    $_hrBool = false;
                @endphp
            @case(\Nabre\Repositories\FormTwo\Field::HIDDEN)
                @include('Nabre::livewire.form-manage.row.other')
            @break

            @default
                @include('Nabre::livewire.form-manage.row.default')
        @endswitch
    @endif
@endforeach
