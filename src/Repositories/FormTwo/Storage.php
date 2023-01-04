<?php

namespace Nabre\Repositories\FormTwo;

use Illuminate\Contracts\Validation\Validator;

trait Storage
{
    private function storage()
    {
        $rules = $this->elements->pluck('set.request.' . $this->method, 'variable');
        //$enabledVars = $rules->keys()->toArray();

        $vars=$this->request->validate($rules->toArray());
        //$vars=$this->request->only($enabledVars);


        $this->data->recursiveSave($vars);
        return $this;
    }
}
