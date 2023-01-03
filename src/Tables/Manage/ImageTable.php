<?php

namespace Nabre\Tables\Manage;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Repositories\Table\Structure;
use App\Models\Image as Model;
use Nabre\Services\Html\UserStatus;

class ImageTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['name', 'preview'];
    }

    function colPreview()
    {
        $style = 'max-width:300px;max-height:300px';
        return Html::image(
            'data:' . data_get($this->row, 'type') . ';base64,' . base64_encode(data_get($this->row, 'code')),
            null,
            get_defined_vars()
        );
    }

    function actions()
    {
        return [
            'create' => 'nabre.manage.images.create',
            'edit' => 'nabre.manage.images.edit',
            'destroy' => 'nabre.manage.images.destroy',
        ];
    }

    function query()
    {
        return  $this->model::get()->sortBy('name')->values();
    }
}
