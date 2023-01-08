<?php

namespace Nabre\Repositories\FormTwo\FormTrait;


use Illuminate\Http\Exceptions\HttpResponseException;

trait Storage
{
    private function formUrl()
    {
        if ($this->method == self::$create) {
            $find = 'create';
        } else {
            $find = 'edit';
        }

        return  $this->redirect[$find] ?? null;
    }

    private function storage()
    {
        dd($this->request->all());
        $rules = $this->elements->pluck('set.request.' . $this->method, 'variable')->toArray();
        $validator = \Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            $destination = $this->formUrl();
            $response = redirect($destination);

            if (is_null($destination)) {
                $response = $response->back();
            }

            $errors=$validator->errors();
            session()->put('errors',$errors);
            throw new HttpResponseException($response);
        }

        $vars = $validator->validated();
        $this->data->recursiveSave($vars);

        return $this->data;
    }
}
