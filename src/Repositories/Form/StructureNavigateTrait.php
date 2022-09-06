<?php

namespace Nabre\Repositories\Form;

trait StructureNavigateTrait{
    var $structure;

    function structure($structure)
    {
        if(!($structure instanceof Structure)){
            $structure=new $structure;
        }
        $this->structure = $structure;
        return $this;
    }

    private function collection()
    {
        return $this->structure->collection;
    }

    private function elements()
    {
        return $this->structure->elements;
    }
}
