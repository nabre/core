<?php

namespace Nabre\Tables\Builder\Navigation;

use Nabre\Repositories\Table\Structure;
use Nabre\Models\Page as Model;
use Nabre\Repositories\Table\Columns;

class PageTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['tree', 'uri', 'icon', 'string', 'enabled'];
    }

    function colTree()
    {
        $td = '';
        collect(range(1, $this->item->lvl))->each(function ($l) use (&$td) {
            $icon = $l != $this->item->lvl ?
                ($l == $this->item->lvl - 1 ?
                    ((bool)$this->item->folder ?
                        '<i class="fa-solid fa-chevron-down"></i>'
                        :
                        '<i class="fa-solid fa-angle-right"></i>')
                    : null)
                : ((bool)$this->item->folder ?
                    '<i class="fa-solid fa-folder-open text-warning"></i>'
                    : ($this->item->def_page ?
                        '<i class="fa-regular fa-file"></i>'
                        :
                        '<i class="fa-solid fa-file text-info"></i>')

                );

            $td .= '<td width="18px" >' . $icon . '</td>';
        });

        return '<table><tr>' . $td . '</tr></table>';
    }

    function actions()
    {
        return [
            'destroy' => 'nabre.builder.navigation.pages.destroy',
            'edit' => 'nabre.builder.navigation.pages.edit'
        ];
    }

    function query()
    {
        return $this->model::get()->sortPages()->values();
    }
}
