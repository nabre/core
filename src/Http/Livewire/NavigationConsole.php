<?php

namespace Nabre\Http\Livewire;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use App\Models\Data\Istituto;
use App\Models\Data\Student;
use Livewire\Component;
use Mpdf\Tag\Select;
use Nabre\Models\Collection;
use Illuminate\Support\Arr;
use Nabre\Repositories\Relations\GenerateTable;

class NavigationConsole extends Component
{
    var $domain = Istituto::class;
    var $isAdmin = true;
    var $viewEdit = false;
    var $collection;
    var $navigation;
    var $values_nav;
    var $values;
    var $with;
    var $model;
    protected $filter_defined;
    protected $array;

    function mount()
    {
        $this->collection = Collection::with(['filter', 'topFilter', 'childs', 'parents', 'system'])->where('class', $this->domain)->orWhereHas('system2', function ($q) {
            $q->where('class', $this->domain);
        })->orderBy('position')->get();

        if (is_null($this->navigation)) {
            $this->navigation = collect([]);
        }

        if (is_string($this->domain) && !class_exists($this->domain) && !in_array($this->domain, $this->collection->pluck('class')->toArray())) {
            abort(404);
        }

        $this->domain = $this->collection->where('class', $this->domain)->first();
        $this->structureNavigation();
        $this->structureFilter();
    }

    public function change_nav($position)
    {
        $this->viewEdit = true;
        $this->getCheckNavigation($position, $this->values_nav[$position] ?? null);
        $this->structureFilter();
    }

    function values_edit()
    {
        $this->structureFilter();
    }

    protected function structureNavigation()
    {
        if (is_null($this->navigation)) {
            $this->navigation = collect([]);
        }
        if (!$this->navigation->count()) {
            $items = data_get($this->domain, ($this->isAdmin ? 'system' : 'childs'));
            $add = $this->nodeNavigation($items);
            $this->navigation = $this->navigation->push($add);
            $this->values_nav[0] = data_get($add, 'value');
        }
        $this->getCheckNavigation();
    }

    protected function structureFilter()
    {
        if (is_null($this->values)) {
            $this->values = collect([]);
        }
        if (is_null($this->filter_defined)) {
            $this->filter_defined = collect([]);
        }
        $position = 0;
        collect($this->values_nav)->filter()->each(function ($classId) use (&$position) {
            $item = $this->collection->where('id', $classId)->first();
            $exclude = $this->filter_defined->toArray();
            $addFilter = $item->topFilter->whereNotIn('class', $exclude)->whereIn('class', $this->collection->pluck('class')->toArray())->sortBy('position')->values();
            $table = $item->filter->sortBy('position')->values();
            $filter = $table->whereNotIn('class', $exclude)->whereIn('class', $this->collection->pluck('class')->toArray());
            $addFilter = $addFilter->merge($filter)->unique();
            $addFilter->each(function ($i) use (&$position, $filter, $table) {
                $class = data_get($i, 'class');

                $edit = (array)$this->values->where('class', $class)->first();

                data_set($edit, 'class', data_get($i, 'class'));
                data_set($edit, 'name', data_get($i, 'string'));
                data_set($edit, 'isFilter', in_array($class, $filter->pluck('class')->toArray()));
                data_set($edit, 'isTableField', in_array($class, $table->pluck('class')->toArray()));
                data_set($edit, 'items', $this->arrayFindOrCreate($class)->pluck('eti', 'id'));
                data_set($edit, 'show', true);

                $this->putValues($position, $edit);
                data_set($edit, 'value', $this->generateItems($class));
                $this->putValues($position, $edit);
                $position++;
            });
            $this->filter_defined = $this->filter_defined->merge($addFilter->pluck('class'));
        });

        $take = $position + ($position ? 1 : 0);
        $this->values = $this->values->take($take)->values();
        return $this;
    }

    protected function putValues($position, $edit)
    {
        $this->values = $this->values->put($position, $edit);
        return $this;
    }

    protected function generateItems($class)
    {
        $with = collect([]);
        collect(data_get(optional($this->with->where('from', $class)->first()), 'with'))->whereNotIn('to', $this->filter_defined)->pluck('with')->each(function ($w) use (&$with) {
            $with = $with->merge($w)->unique()->sort()->values();
        });
        $with = Arr::undot(collect(array_flip($with->toArray()))->map(fn ($fn) => (false)));
        return $this->recursiveWith($with, $class);
    }

    protected function recursiveWith($with, $class = null)
    {
        $this->arrayFindOrCreate($class);

        collect($with)->each(function ($with, $fn) use ($class) {
            $current = collect([]);
            $this->array[$class]->whereIn('id', (array)$this->getValue($class))->each(function ($i) use (&$current, $fn) {
                $add = $i->$fn;
                if (!($add instanceof \Illuminate\Database\Eloquent\Collection)) {
                    $current = $current->push($add)->unique();
                } else {
                    $current = $current->merge($add)->unique();
                }
            });

            $class = optional((new $class)->relationshipFind($fn))->model;
            if (!is_null($class)) {
                $this->array[$class] = $this->arrayFindOrCreate($class)->whereIn('id', $current->pluck('id')->toArray())->values();
                if ($with) {
                    $this->recursiveWith($with, $class);
                }
            }
        });

        return $this->getValue($class);
    }

    protected function getValue($class)
    {
        $items = $this->arrayFindOrCreate($class)->pluck('id');
        $value = data_get($this->values->where('class', $class)->first(), 'value');
        $list = $items->toArray();

        $node = $this->values->where('class', $class)->first();
        $bool = data_get($node, 'isFilter');
        $value = (!is_null($value) && $value == 0 || is_array($value)) ? $list : (!in_array($value, $list) ? ($bool ? $list : $items->first()) : $value);
        return $value;
    }

    protected function arrayFindOrCreate($class)
    {
        $this->array[$class] = $this->array[$class] ?? $class::all()->sortBy('eti');
        return $this->array[$class];
    }

    protected function getCheckNavigation($position = 0, $value = null)
    {
        $take = $this->checkNodeNavigation($position, $value) + 1;
        $this->navigation = $this->navigation->take($take);
        $this->values_nav = collect($this->values_nav)->take($take)->toArray();
    }

    protected function checkNodeNavigation($position = 0, $value = null)
    {
        if (!is_null($value)) {
            $edit = $this->navigation->where('position', $position)->first();
            data_set($edit, 'value', $value);
            $this->navigation = $this->navigation->put($position, $edit);
        }
        $item = $this->navigation->where('position', $position)->first();

        if ($v = data_get($item, 'value')) {
            $node = $this->collection->where('id', $v)->first();
            $this->with = collect(data_get($node, 'with'));
            $this->model = data_get($node, 'class');
        }

        $exclude = $this->navigation->where('position', '<', $position)->pluck('value')->toArray();
        $items = optional(optional($this->collection->where('id', data_get($item, 'value'))->first())->childs)->whereNotIn('id', $exclude);
        if (!is_null($items) && $items->count()) {
            $position++;
            $value = optional($this->navigation->where('position', $position)->first())->value ?? 0;
            $add = $this->nodeNavigation($items, $position, $value);
            $this->navigation = $this->navigation->put($position, $add);
            $this->values_nav[$position] = data_get($add, 'value');
            if (data_get($add, 'value')) {
                return $this->checkNodeNavigation($position);
            }
        } elseif (!is_null($items) && !optional($items)->count()) {
            $position++;
            $this->navigation = $this->navigation->push(false);
        }
        return $position;
    }

    protected function nodeNavigation($items, $position = 0, $value = 0)
    {
        $items = $items->sortBy(['position','string'])->pluck('string', 'id');
        if ($position) {
            $items = $items->prepend('-Seleziona-', 0);
        }
        $value = in_array($value, $items->keys()->toArray()) ? $value : $items->keys()->first();
        return get_defined_vars();
    }

    public function render()
    {
        $nav = $this->navigation();
        $content = $this->errors() ?? $this->table();

        $this->values = $this->values->map(function ($i) {
            if (is_array(data_get($i, 'value'))) {
                data_set($i, 'value', 0);
            }
            return $i;
        });
        $filter = $this->filter();
        $title = data_get($this->collection->where('class', $this->model)->first(), 'string');
        return '<div>
        <div class="toggle-content alert alert-dark p-1">
            <div class="row" style="' . (!$this->viewEdit ? null : 'display:none') . '">
                <div class="col-auto h1">' . $title . '</div>
                <div class="col handle"><i class="fa-regular fa-pen-to-square cursor-pointer" title="Modifica"></i></div>
            </div>
            <div style="' . ($this->viewEdit ? null : 'display:none') . '" class="row">
                <div class="col-auto">' . $nav . '</div>
                <div class="col handle"><i class="fa-solid fa-xmark cursor-pointer" title="Chiudi"></i></div>
            </div>
        </div>
         <hr>'
            . $filter
            . $content
            . '</div>';
    }

    protected function navigation()
    {
        $items = '';
        $this->navigation->each(function ($i) use (&$items) {
            if ($i) {
                $position = data_get($i, 'position');
                $this->values_nav[$position] = data_get($i, 'value');
                $select = Form::select('', data_get($i, 'items'), null, ['class' => "form-select", 'wire:model' => 'values_nav.' . $position, 'wire:change' => "change_nav(" . $position . ")"]);
            } else {
                $select = ""; #segnale finito
            }
            $items .= Html::div($select, ['class' => 'col-auto']);
        });
        return Html::div($items, ['class' => 'row']);
    }

    protected function filter()
    {
        $html = '';
        $print = $this->values->where('show', true);
        $print->each(function ($i, $pos) use (&$html) {
            $id = "selector-" . $pos;
            $items = data_get($i, 'items');

            if (!($items instanceof \Illuminate\Database\Eloquent\Collection)) {
                $items = collect($items);
            }
            $value = data_get($i, 'value');

            switch ($items->count()) {
                case 0:
                    $content = Form::input('text', '', '-Nessun valore-', ['class' => 'form-control is-invalid', 'id' => $id, 'disabled']);
                    break;
                case 1:
                    $class = ['form-control'];
                    $value = $items->first();
                    if (is_null($value)) {
                        $value = '#errore';
                        $class[] = 'is-invalid';
                    }
                    $content = Form::input('text', '', $value, ['class' => $class, 'id' => $id, 'disabled']);
                    break;
                default:
                    if (data_get($i, 'isFilter')) {
                        $items = $items->prepend('-Seleziona-', 0);
                    }
                    $content = Form::select('', $items, '', ['class' => 'form-select', 'id' => $id, 'wire:model' => 'values.' . $pos . '.value', 'wire:change' => 'values_edit']);
                    break;
            }

            $node = Html::div($content . Html::tag('label', data_get($i, 'name'), ['for' => $id]), ['class' => 'form-floating']);
            $html .= Html::div($node, ['class' => 'col-3 mb-1']);
        });
        return Html::div($html, ['class' => 'row']) . (($print->count()) ? '<hr>' : null);
    }

    protected function values()
    {
        $items = new $this->model;
        $rel = $items->definedRelations();
        $this->values->where('isFilter', true)->pluck('class')->each(function ($class) use (&$items, $rel) {
            //  $class=data_get($i,'class');
            $values = (array)$this->getValue($class);
            $fn = optional($rel->where('model', $class)->first())->name;
            if (!is_null($fn)) {
                $items = $items->wherehas($fn, function ($q) use ($values) {
                    $q->whereIn('_id', $values);
                });
            }
        });

        return $items->get()->sortBy('eti')->values();
    }

    protected function table()
    {
        $table = new GenerateTable;
        $table->model = $this->model;
        $table->data = $this->values();
        $rel = (new $table->model)->definedRelations();
        $table->filter = $this->values->where('isTableField', true)->pluck('class')->map(function ($class) use ($rel) {
            return optional($rel->where('model', $class)->first())->name;
        })->toArray();
        $table->columns = collect($table->filter)->prepend('eti');

        return $table->html();
    }

    protected function errors()
    {
        $errorValue = $this->values->filter(function ($item) {
            $value = data_get($item, 'value');
            return is_null($value) || is_array($value) && !count($value);
        })->where('isTableField', true);

        if ($errorValue->count()) {
            $error = $this->collection->whereIn('class', $errorValue->pluck('class')->toArray())
                ->map(function ($i) {
                    data_set($i, 'html', Html::tag('li', data_get($i, 'class'), ['class' => 'list-group-item list-group-item-warning']));
                    return $i;
                });
            $content = $error->implode('html');
            return Html::tag('ul', $content, ['class' => 'list-group']);
        }
        return null;
    }
}
