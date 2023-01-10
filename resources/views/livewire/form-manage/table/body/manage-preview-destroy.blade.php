<b>Anteprima:</b>

    <table class="table table-sm">
        @foreach (collect($cols) as $_i)
            @php
                data_set($_i, 'output', \Nabre\Repositories\FormTwo\Field::STATIC);
                data_set($_i, 'value', data_get($_row, data_get($_i, 'variable')));
            @endphp
            <tr>
                <td>{{ data_get($_i, 'label') }}:</td>
                <td> @include('Nabre::livewire.form-manage.item')</td>
            </tr>
        @endforeach
    </table>

