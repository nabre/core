<?php

namespace Nabre\Repositories\Menu;

use Collective\Html\HtmlFacade as Html;

trait BuilderTrait
{
    var $set;
    var $currents;

    function __construct($menu)
    {
        $text = $menu->text ?? false;
        $icon = $menu->icon ?? false;

        $this->currents = \Breadcrumbs::generate()->pluck('url')->toArray();
        unset($menu);
        $this->set = (object)get_defined_vars();
    }

    function build($array)
    {
        return $this->recursive($array);
    }

    function recursive($array)
    {
        $html = '';
        $array->each(function ($item) use (&$html) {
            $this->title = $this->title($item['i']);
            $this->label = $this->label($item['i']);
            if ($item['sub'] === false) {
                $uri = $item['i']['uri'];
                $html .= $this->item($uri);
            } else {
                $html .= $this->container($this->folder($this->label, $this->recursive($item['sub'])));
            }
        });

        return $html;
    }

    function folder($label, $list)
    {
        $content = ($label) ? $this->header($label) : null;
        $content .= $this->list($list);
        return $content;
    }

    function label($page)
    {
        if (is_null($page)) {
            return false;
        }
        return trim(($this->set->icon ? $page->icon : null) . " " . (($this->set->text || !$this->set->icon || is_null($page->icon)) ? $page->string : null));
    }

    function title($page)
    {
        return optional($page)->string;
    }

    function isCurrent($url)
    {
        if (in_array($url, $this->currents)) {
            return true;
        }
        return false;
    }

    function item($uri)
    {
        $url = url($uri);
        $active = '';
        if ($this->isCurrent($url)) {
            $active = 'active';
        }
        return Html::a($this->label, ['href' => $url, 'title' => $this->title, 'class' => $active]);
    }

    function subItem($uri)
    {
        return $this->item($uri);
    }

    function header($title)
    {
        return  $title;
    }

    function list($list)
    {
        return $list;
    }

    function container($content)
    {
        return $content;
    }
}
