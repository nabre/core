<?php

namespace Nabre\Http\Livewire;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use App\Models\Data\Istituto;
use Livewire\Component;
use Nabre\Models\Collection;

class NavigationConsole extends Component
{
    var $domain = Istituto::class;
    var $collection;
    var $navigation;
    var $value_nav;

    function mount()
    {
        $this->collection = Collection::with(['filter', 'topFilter', 'childs', 'parents', 'system'])->get();

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
        }
        $this->getCheckNavigation();
    }

    protected function getCheckNavigation($position = 0, $value = null)
    {
        $take = $this->checkNodeNavigation($position, $value) + 1;
        $this->navigation = $this->navigation->take($take);
    }

    protected function checkNodeNavigation($position = 0, $value = null)
    {
        if (!is_null($value)) {
            $edit = $this->navigation->where('position', $position)->first();
            data_set($edit, 'value', $value);
            $this->navigation->put($position, $edit);
        }

        $item = $this->navigation->where('position', $position)->first();
        $items = optional($this->collection->where('id', data_get($item, 'value'))->first())->childs;
        if (!is_null($items)) {
            $position++;
            $value = optional($this->navigation->where('position', $position)->first())->value ?? 0;
            $add = $this->nodeNavigation($items, $value);
            $this->navigation = $this->navigation->put($position, $add);

            if (data_get($add, 'value')) {
                return $this->checkNodeNavigation($position);
            }
        }
        return $position;
    }

    public function change_nav($position)
    {
        $this->getCheckNavigation($position, $this->value_nav);
    }

    protected function nodeNavigation($items, $value = 0)
    {
        $items = $items->pluck('string', 'id');
        $position = $this->navigation->count();
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
            $select = Form::select('', data_get($i, 'items'), data_get($i, 'value'), ['class' => "form-select", 'wire:model' => 'value_nav', 'wire:change' => "change_nav(" . data_get($i, 'position') . ")"]);
            $items .= Html::div($select, ['class' => 'col-auto']);
        });
        return Html::div($items, ['class' => 'row']);
    }

    protected function filter()
    {
    }

    protected function values()
    {
    }
}
