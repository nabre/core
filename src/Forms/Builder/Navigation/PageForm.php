<?php

namespace Nabre\Forms\Builder\Navigation;

use Nabre\Models\Page;
use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;
use Nabre\Repositories\Pages;

class PageForm extends Structure
{
    function build()
    {
        $this->add('name', Field::STATIC);
        $this->add('uri', Field::STATIC);
        if(!Pages::isDefinedConfig($this->data->name)){
            $this->add('icon');
            $this->add('title')->request('max:255');
        }elseif(!($this->data->folder??false)){
            $this->add('disabled');
        }

        if($this->data->folder && !Pages::isDefinedConfig($this->data->name, 'd') && $this->data->childs->count()!=1){
            $this->add('redirectdefpage')->listLabel('uri');
        }
    }

    function queryRedirectdefpage(){
        return $this->data->childs;
    }
}
