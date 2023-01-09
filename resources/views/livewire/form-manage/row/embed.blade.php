<div class="row">
    <div class="col-md-1 pt-1">
        {{ data_get($_i, 'label') }}:
    </div>
    <div class="col">
        @switch(data_get($_i,'output'))
            @case(\Nabre\Repositories\FormTwo\Field::EMBEDS_MANY)
                @include('Nabre::livewire.form-manage.embed.many')
            @break

            @case(\Nabre\Repositories\FormTwo\Field::EMBEDS_ONE)
                @include('Nabre::livewire.form-manage.embed.one')
            @break
        @endswitch
    </div>
</div>
