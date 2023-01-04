<?php

namespace Nabre\Repositories\FormTwo;

trait Storage
{
    private function storage(){
        $this->validate=$this->elements->pluck('set.request.'.$this->method,'variable');
        $enabledVars=$this->validate->keys()->toArray();
        $vars=collect($this->request)->filter(fn($v,$k)=>in_array($k,$enabledVars))->toArray();
        $this->data->recursiveSave($vars);
        return $this;
    }
}
