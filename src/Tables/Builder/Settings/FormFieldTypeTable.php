<?php

namespace Nabre\Tables\Builder\Settings;

use Nabre\Repositories\Table\Structure;
use Nabre\Models\FormFieldType as Model;

class FormFieldTypeTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return [config('setting.database.key'),'string'];
    }

    function actions()
    {
        return [
            'edit' => 'nabre.builder.settings.form-field-type.edit'
        ];
    }

    function query()
    {
        return $this->model::get()->sortBy([config('setting.database.key')])->values();
    }
}
