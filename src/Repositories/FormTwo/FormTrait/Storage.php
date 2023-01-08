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
        $rules = $this->rules();
        $validator = \Validator::make($this->request, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            session()->put('errors', $errors);
            return false;

            /*
            $destination = $this->formUrl();
            $response = redirect($destination);

            if (is_null($destination)) {
                $response = $response->back();
            }
            throw new HttpResponseException($response);*/
        } else {
            $vars = $validator->validated();
            $this->data->recursiveSave($vars);
        }

        return $this->data;
    }
}
