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

    function excludeWithErrors()
    {
        $this->elements = $this->elements->reject(function ($i) {
            return data_get($i, 'errors', collect([]))->count();
        })->values();

        return $this;
    }

    function rulesAviable()
    {
        return $this->removeInexistents()->excludeWithErrors();
    }

    function rulesOnlyEmbeds(){
        $this->elements = $this->elements->filter(function ($i) {
            return in_array(data_get($i,'output'),[Field::EMBEDS_MANY,Field::EMBEDS_ONE]);
        })->values();
        return $this;
    }

    function rulesExcludeEmbeds(){
        $this->elements = $this->elements->reject(function ($i) {
            return in_array(data_get($i,'output'),[Field::EMBEDS_MANY,Field::EMBEDS_ONE]);
        })->values();
        return $this;
    }
}
