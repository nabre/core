<?php
namespace Nabre\Http\Requests\Structure;

use Nabre\Forms\Structure\LoaderForm;
use Nabre\Http\FormRequest;


class LoaderRequest extends FormRequest{

    protected $form=LoaderForm::class;

    function authorize(){
        return true;
    }

    function rulesAll(){
        return [];
    }

}
