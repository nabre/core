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
    private $redirect=null;

    private $prefix = null;
    private $wire = false;

    function __construct($data = null)
    {
        if (!is_null($data)) {
            if (is_string($data)) {
                $this->model($data);
            } else {
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

    public function redirect(array $array){
        $this->redirect=$array;

        return $this;
    }

    public function embedMode($prefix = null, bool $wire = false)
    {
        $this->check();
        $this->prefix = $prefix;
        $this->wire = $wire;
        $this->back = false;
        $this->submit = false;

        $this->elements = $this->elements->map(function ($i) {
            if ($this->wire !== false) {
                $this->setData($i, 'set.options.wire:model', $this->wire, true);
            }

            if (!is_null($this->prefix)) {
                $variable = collect(explode('.', $this->prefix))->merge(collect(explode('.', data_get($i, 'variable'))))->implode('.');
                $this->setData($i, 'variable', $variable, true);
            }

            return $i;
        });

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
        $this->valueAssign();

        if ($this->view) {
            $this->elements = $this->elements->map(function ($i) {
                data_set($i, 'output_original', data_get($i, 'output'), true);
                data_set($i, 'output', Field::STATIC, true);
                return $i;
            });
        }

        return $this->render($submit);
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

    private function valueAssign()
    {
        $this->elements = $this->elements->map(function ($i) {
            $type = data_get($i, 'type');
            if ($type && $type != 'fake') {
                $value = $this->data->readValue(data_get($i, 'variable'));
                if ($type == 'relation') {
                    switch (data_get($i, 'set.rel.type')) {
                        case "EmbedsMany":
                        case "EmbedsOne":
                            $this->setData($i, 'set.embeds.modelKey', $this->data->{$this->data->getKeyName()} ?? null);
                            $this->setData($i, 'set.embeds.variable', data_get($i, 'variable'));
                            break;
                    }
                }
                $overwrite =!is_null($value);
                $this->setData($i, 'value', $value, $overwrite);
            }

            return $i;
        });
    }
}
