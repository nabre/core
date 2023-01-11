<div class="row">
    @include('Nabre::livewire.form-manage.title')
</div>
@if (!count($itemsTable))
    <div class="alert alert-info">
        <p>La tabella risulta vuota.</p>
        <hr>
        @include('Nabre::livewire.form-manage.table.manage')
    </div>
@else
    <div class="table-responsive">
        <table class="table table-sm w-auto">
            <thead>
                <tr>
                    @foreach (collect($cols)->pluck('label') as $_h)
                        <th>{{ $_h }}</th>
                    @endforeach
                    <th>
                        @include('Nabre::livewire.form-manage.table.manage')
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($itemsTable as $_row)
                    <tr>
                        @foreach (collect($cols) as $_i)
                            @php
                                data_set($_i, 'output', \Nabre\Repositories\FormTwo\Field::STATIC);
                                data_set($_i, 'value', data_get($_row, data_get($_i, 'variable')));
                            @endphp
                            <td>
                                @include('Nabre::livewire.form-manage.item')
                            </td>
                        @endforeach
                        <td>
                            @include('Nabre::livewire.form-manage.table.body.manage')
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
@endif
