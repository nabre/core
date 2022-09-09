<?php

namespace Nabre\Http\Livewire;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use App\Models\Data\Istituto;
use Livewire\Component;
use Mpdf\Tag\Select;
use Nabre\Models\Collection;

class NavigationConsole extends Component
{
    var $domain = Istituto::class;
    var $collection;
    var $navigation;
    var $values_nav;
    protected $filter_defined;

    function mount()
    {
        $this->collection = Collection::with(['filter', 'topFilter', 'childs', 'parents', 'system'])->where('class', $this->domain)->orWhereHas('system', function ($q) {
            $q->where('class', $this->domain);
        })->get();

        if (is_null($this->navigation)) {
            $this->navigation = collect([]);
        }
        if (is_string($this->domain) && !class_exists($this->domain) && !in_array($this->domain, $this->collection->pluck('class')->toArray())) {
            abort(404);
        }

        $this->domain = $this->collection->where('class', $this->domain)->first();
        $this->structureNavigation();
    }

    protected function structureNavigation()
    {
        if (is_null($this->navigation)) {
            $this->navigation = collect([]);
        }

        if (!$this->navigation->count()) {
            $items = $this->domain->system;
            $add = $this->nodeNavigation($items);
            $this->navigation = $this->navigation->push($add);
            $this->values_nav[0] = data_get($add, 'value');
        }

        $this->getCheckNavigation();
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
            $this->navigation->put($position, $edit);
        }

        $item = $this->navigation->where('position', $position)->first();
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

    public function change_nav($position)
    {
        $this->getCheckNavigation($position, $this->values_nav[$position] ?? null);
    }

    protected function nodeNavigation($items, $position = 0, $value = 0)
    {
        $items = $items->pluck('string', 'id');
        if ($position) {
            $items = $items->prepend('-Seleziona-', 0);
        }
        $value = in_array($value, $items->keys()->toArray()) ? $value : $items->keys()->first();
        return get_defined_vars();
    }

    public function render()
    {
        return '<div>'
            . $this->navigation()
            . $this->filter()
            . $this->values()
            . '</div>';
    }

    protected function navigation()
    {
        $items = '';
        $this->navigation->each(function ($i) use (&$items) {
            if ($i) {
                $position = data_get($i, 'position');
                $select = Form::select('', data_get($i, 'items'), data_get($i, 'value'), ['class' => "form-select", 'wire:model' => 'values_nav.' . $position, 'wire:change' => "change_nav(" . $position . ")"]);
            } else {
                $select = "<< Fine";
            }
            $items .= Html::div($select, ['class' => 'col-auto']);
        });
        return Html::div($items, ['class' => 'row']) . '<hr>';
    }

    protected function filter()
    {
        $html = '';
        if (is_null($this->filter_defined)) {
            $this->filter_defined = collect([]);
        }

        collect($this->values_nav)->filter()->each(function ($classId) use (&$html) {
            $item = $this->collection->where('id', $classId)->first();
            $exclude = $this->filter_defined->toArray();
            $addFilter = $item->topFilter->whereNotIn('class', $exclude);
            $filter = $item->filter->whereNotIn('class', $exclude);
            $addFilter = $addFilter->merge($filter);

            $this->filter_defined = $this->filter_defined->merge($addFilter->pluck('class'));

            $addFilter->each(function ($i) use (&$html,$filter) {
                $id = 'input';
                $items = collect(['aaa' => 'valore aaa','bbb'=>'Secondo valore bbb']);
                $value = 'bbb';
                switch ($items->count()) {
                    case 0:
                        $content = Form::input('text', '', '-Nessun valore-', ['class' => 'form-control is-invalid', 'id' => $id, 'disabled']);
                        break;
                    case 1:
                        $value = $items->toArray()[$value] ?? null;
                        $class = ['form-control'];
                        if (is_null($value)) {
                            $value = '#errore';
                            $class[] = 'is-invalid';
                        }
                        $content = Form::input('text', '', $value, ['class' => $class, 'id' => $id, 'disabled']);
                        break;
                    default:
                        if(in_array($i->class,$filter->pluck('class')->toArray())){
                            $items=$items->prepend('-Seleziona-','');
                        }
                        $content = Form::select('', $items, $value, ['class' => 'form-select', 'id' => $id]);
                        break;
                }
                $node = Html::div($content . Html::tag('label', $i->string, ['for' => $id]), ['class' => 'form-floating']);
                $html .= Html::div($node, ['class' => 'col-3']);
            });
        });

        return Html::div($html, ['class' => 'row']) . ($this->filter_defined->count() ? '<hr>' : null);
    }

    protected function values()
    {
    }
}
