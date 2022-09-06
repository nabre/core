<?php

namespace Nabre\Repositories\Menu;

use Collective\Html\HtmlFacade as Html;

class SideBar
{
    use BuilderTrait;

    function item($uri)
    {
        return Html::div(Html::a($this->label, ['href' => url($uri) ]), ['class' => "list-group-item"]);
    }

    function header($title){
        return  Html::div($title, ['class' => 'card-header']) ;
    }

    function list($list){
        return Html::div($list, ['class' => 'list-group list-group-flush']);
    }

    function container($content){
        return Html::div($content, ['class' => 'card m-1']);
    }
}
