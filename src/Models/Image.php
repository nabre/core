<?php

namespace Nabre\Models;

use Nabre\Casts\ImageCodeCast;
use Nabre\Database\Eloquent\Model;

class Image extends Model
{
    protected $guard_name = 'web';
    protected $collection = 'Images'; //nome collection
    protected $fillable = ['code','type']; //campi editabili
    //  protected $attributes = ['¨slug'=>null,'title'=>null]; //Valori di default
    protected $casts = ['code'=> ImageCodeCast::class]; //tipo di dato
    protected $foreignCascade = false;

    function getImageAttribute(){
        $response = \Response::make($this->code, 200);
        $response->header("Content-Type", $this->type);
        return $response;
    }
}

