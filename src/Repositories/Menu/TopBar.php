<?php

namespace Nabre\Repositories\Menu;

use Collective\Html\HtmlFacade as Html;

class TopBar
{
    use BuilderTrait;

    function item( $uri)
    {
        $url=url($uri);
        $active = '';
        if ($this->isCurrent($url)) {
            $active = 'active';
        }
        return Html::tag('li',Html::a($this->label, ['href' => $url, 'class' => ['nav-link',$active],'title'=>$this->title]), ['class' => "nav-item"]);
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
