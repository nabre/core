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
        $this->data->recursiveSave($this->request);
        return $this->data;
    }
}
