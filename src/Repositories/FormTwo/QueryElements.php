<?php

namespace Nabre\Repositories\FormTwo;

class QueryElements
{
    private $elements;

    function __construct($elements)
    {
        $this->elements($elements);
        return $this;
    }

    function elements($elements)
    {
        $this->elements = $elements;
        return $this;
    }

    function results()
    {
        return $this->elements;
    }

    function removeInexistents()
    {
        $this->elements = $this->elements->filter(function ($i) {
            return data_get($i, 'type', false);
        })->values();

        return $this;
    }

    function withErrors()
    {
        $this->elements = $this->elements->filter(function ($i) {
            return data_get($i, 'errors', collect([]))->count();
        })->values();

        return $this;
    }
}
