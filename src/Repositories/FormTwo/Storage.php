<?php

namespace Nabre\Repositories\FormTwo;


trait Storage
{
    private function storage()
    {
        $rules = $this->elements->pluck('set.request.' . $this->method, 'variable');

        $validator = \Validator::make($this->request->all(), $rules->toArray());

        if ($validator->fails()) {
            $this->request->session()->flash('errors', $validator->errors());
            return redirect()->back()->withErrors( $validator->errors());
        }

        $vars = $validator->validated();
        $this->data->recursiveSave($vars);

        return null;
    }
}
