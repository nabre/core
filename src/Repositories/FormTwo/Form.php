<?php

namespace Nabre\Repositories\FormTwo;

use Illuminate\Database\Eloquent\Model;

class Form
{
    use Render;
    use Storage;
    use Structure;

    private $model = null;
    private $data = null;
    private $collection;
    private $request;

    function __construct($data = null)
    {
        if (!is_null($data)) {
            if(is_string($data)){
                $this->model($data);
            }else{
                $this->data($data);
            }
        }
        return $this;
    }

    public function data(Model $data)
    {
        $this->data = $data;
        $this->check();
        return $this;
    }

    public function model(string $model)
    {
        $this->model = $model;
        $this->check();
        return $this;
    }

    public function generate(?string $submit=null,bool $view=false)
    {
        $this->valueAssign();

        if($view){
            $this->elements=$this->elements->map(function($i){
                data_set($i,'output_original',data_get($i,'output'),true);
                data_set($i,'output',Field::STATIC,true);
                return $i;
            });
        }

        return $this->render($submit);
    }

    public function save(?array $request=null)
    {
        $this->request = $request??request()->all();

        $this->elements=$this->elements->filter(function($i){
            $type=data_get($i,'type');
            return $type && $type!='fake';
        });

        return $this->storage();
    }

    private function check()
    {
        #Controllo model
        if (is_null($this->model)) {
            if ($this->data instanceof Model) {
                $this->model = get_class($this->data);
            } else {
                abort(403);
            }
        }

        #controllo collection
        if(is_null($this->collection)){
            if (class_exists($this->model) && (new $this->model instanceof Model)) {
                $this->collection = new $this->model;
            } else {
                abort(403);
            }
        }

        #controllo data
        if (is_null($this->data)) {
            if ($this->collection instanceof Model) {
                $this->data = new $this->collection;
            } else {
                abort(403);
            }
        }

        #genera struttura
        $this->structure();

        return $this;
    }

    private function valueAssign(){

    }
}
