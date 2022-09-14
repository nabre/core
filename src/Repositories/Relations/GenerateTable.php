<?php

namespace Nabre\Repositories\Relations;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Models\Collection;
use Nabre\Repositories\Table\Structure;

class GenerateTable extends Structure
{
    function table()
    {
        $this->colName['eti']='#';
    }

    function colEti(){
        return Html::tag('i',$this->col);
    }

    function belongsToFilter(){
        return data_get($this->col,'eti');
    }

    protected function customCol($col)
    {
        if(in_array($col,$this->filter)){
            return 'belongsToFilter';
        }
        return parent::customCol($col);
    }
}
