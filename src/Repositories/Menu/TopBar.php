<?php

namespace Nabre\Repositories\Menu;

use Collective\Html\HtmlFacade as Html;

class TopBar
{
    use BuilderTrait;

    function item( $uri)
    {
        return Html::tag('li',Html::a($this->label, ['href' => url($uri), 'class' => 'nav-link','title'=>$this->title]), ['class' => "nav-item"]);
    }

    function subItem( $uri)
    {
        return Html::tag('li', Html::a($this->label, ['href' => url($uri), 'class' => 'dropdown-item']));
    }

    function header($title)
    {
        return  Html::div($title, ['class' => 'card-header']);
    }
/*
    function container($content)
    {
        return Html::tag('ul', $content, ['class' => 'navbar-nav']);
    }*/
}
