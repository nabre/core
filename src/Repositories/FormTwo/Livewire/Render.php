<?php

namespace Nabre\Repositories\FormTwo\Livewire;

use Nabre\Repositories\FormTwo\Field;

trait Render
{
    function generate()
    {
        return collect($this->print)->map(function ($item) {
            if (is_string($item)) {
                return $item;
            } else {
                $embed=$this->embedRenderItem($item);
                $html = data_get($item,'html');
                return str_replace(self::$findEmbed,$embed,$html);
            }
        })->implode('');
    }

    private function htmlDefaultItem($item)
    {
        return (string) $this->form->fieldItem($item);
    }
}
