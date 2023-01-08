<?php

namespace Nabre\Repositories\FormTwo;

use Illuminate\Database\Eloquent\Model;
use Nabre\Repositories\FormTwo\FormTrait\Output;
use Nabre\Repositories\FormTwo\FormTrait\Render;
use Nabre\Repositories\FormTwo\FormTrait\Storage;
use Nabre\Repositories\FormTwo\FormTrait\Structure;
use Nabre\Repositories\FormTwo\FormTrait\StructureErrors;
use Nabre\Repositories\FormTwo\FormTrait\StructureRequest;

class Form
{
    use Render;
    use Storage;
    use Structure;
    use StructureErrors;
    use StructureRequest;
    use Output;

    private $model = null;
    private $data = null;
    private $collection;
    private $request;
    private $view = false;
    private $redirect = null;

    private $wire = null;

    static function public($data, $back = null)
    {
        $model = get_class($data);
        $idData = data_get($data, $data->getKeyName());
        $formClass = get_called_class();
        unset($data);
        return livewire('form', get_defined_vars());
    }

    function __construct($data = null)
    {
        $this->input($data);
        return $this;
    }

    function input($data)
    {
        if (!is_null($data)) {
            if (is_string($data)) {
                $this->model = $data;
            } else {
                $this->data = $data;
            }
            $this->check();
        }
        return $this;
    }

    public function redirect(array $array)
    {
        $this->redirect = $array;

        return $this;
    }

    public function embedMode($wire = null)
    {
        $this->elements = null;
        $this->check();
        $this->add($this->collection->getKeyName())->insert();
        $this->checkErrors();

        $this->wire = $wire;

        $this->back = false;
        $this->submit = false;
        $this->form = false;

        return $this;
    }

    public function viewMode()
    {
        $this->view = true;
        return $this;
    }

    public function generate(?string $submit = null)
    {
        $this->check();
        $this->values();

        if ($this->view) {
            $this->elements = $this->elements->map(function ($i) {
                data_set($i, 'output_original', data_get($i, 'output'), true);
                data_set($i, 'output', Field::STATIC, true);
                return $i;
            });
        }

        return $this->render($submit);
    }

    public function values()
    {
        $this->check();
        $this->valueAssign();

        return $this->elements->pluck('value', 'variable')->toArray();
    }

    public function rules()
    {
        $RULES_PATH = 'set.request.' . $this->method;
        $this->check();

        $rules = collect([]);
        $elements = (new QueryElements($this->elements))->rulesAviable();

        $rules = $rules->merge($elements->rulesExcludeEmbeds()->results()->pluck($RULES_PATH, 'variable'));



        $elements->rulesOnlyEmbeds()->results()->each(function($i)use(&$rules){
            $embedForm = data_get($i, 'embed.wire.form');
            $model= data_get($i, 'set.rel.model');
            $embedRules=$this->embedObject($embedForm,$model)->rules();

            
        });

        return $rules->unique()->sort()->values()->toArray();
        //return $this->elements->pluck('value', 'variable')->toArray();
    }

    public function save(?array $request = null)
    {
        $this->check();
        $this->request = $request ?? request();

        $this->elements = $this->elements->filter(function ($i) {
            $type = data_get($i, 'type');
            return $type && $type != 'fake';
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
        if (is_null($this->collection)) {
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

    private function embedObject($embedForm,$data){
        return (new $embedForm($data))->embedMode();
    }

    private function valueAssign()
    {
        $this->elements = $this->elements->map(function ($i) {
            $type = data_get($i, 'type');
            if ($type && $type != 'fake') {
                $name = data_get($i, 'variable');
                $value = $this->data->readValue($name);

                if ($type == 'relation') {
                    switch (data_get($i, 'set.rel.type')) {
                        case "EmbedsMany":
                            $embedForm = data_get($i, 'embed.wire.form');
                            $value = [];
                            $this->data->$name->each(function ($item) use (&$value, $embedForm) {
                                $value[] = $this->embedObject($embedForm,$item)->values();
                            });
                            break;
                        case "EmbedsOne":
                            $embedForm = data_get($i, 'embed.wire.form');
                            $item=$this->data->$name ?? data_get($i, 'set.rel.model');
                            $value = $this->embedObject($embedForm,$item)->values();
                            break;
                    }
                }
                $overwrite = !is_null($value);
                $this->setData($i, 'value', $value, $overwrite);
            }

            return $i;
        });
    }
}