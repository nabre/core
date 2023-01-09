<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;
use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\Livewire\Embed;
use Nabre\Repositories\FormTwo\Livewire\Submit;
use PhpParser\Node\Stmt\Break_;

class FormManage extends Component
{
    use Embed;
    use Submit;

    var $idData;
    var $model;
    var $formClass;
    var $back;

    var $print = [];
    var $wireValues = [];
    private $validateRules = [];

    private $form;
    private $embedForm;
    function mount()
    {
        $this->wireValues = $this->form()->values();
        $this->print();
    }

    function info($i)
    {
        if ($this->haveError($i)) {
            return;
        }
        return collect(data_get($i, 'set.info', []))->map(fn ($i) => (string) Html::div(data_get($i, 'text'), ['class' => 'badge text-bg-' . data_get($i, 'theme')]))->implode('<br>');
    }


    function rules()
    {
        return collect($this->form()->rules())->mapWithKeys(fn ($r, $k) => ['wireValues.' . $k => $r])->toArray();
    }

    function submit()
    {
        $validatedData = $this->validate();
        $this->form()->save(data_get($validatedData,'wireValues'));
    }

    private function print()
    {
        $this->print = collect([]);
        $this->print = $this->print->merge($this->form()->buttonBack());
        $this->print = $this->print->merge($this->recursivePrint());
        $this->print = $this->print->merge($this->form()->buttonSubmit());
    }

    function htmlItem($item, $field = null)
    {
        return (string) $this->form()->itemHtml($item, $field);
    }

    public function render()
    {
        return view('Nabre::livewire.form-manage.index');
    }

    private function haveError($i)
    {
        return (bool) (!data_get($i, 'type') || data_get($i, 'errors', collect([]))->count());
    }

    private function recursivePrint($elements = null)
    {
        $elements = $elements ?? $this->form()->elements;

        $elements = $elements->map(function ($i) {
            $this->embedForm = null;
            switch (data_get($i, 'output')) {
                case Field::EMBEDS_MANY:
                    $wire = '.*';
                case Field::EMBEDS_ONE:
                    $wire = data_get($i, 'embed.parent.variable') . ($wire ?? null);
                    $form = data_get($i, 'embed.wire.form');
                    $model = data_get($i, 'embed.wire.model');
                    $add = $this->generateEmbedItem($form, $model, $wire)->elements;
                    data_set($i, 'embed.wire.elements', $add);
                    break;
            }
            return $i;
        });

        return $elements;
    }

    private function form()
    {
        $this->form = $this->form ?? (new $this->formClass)->redirect(['index' => $this->back])->input($this->model::find($this->idData) ?? $this->model::make());
        return $this->form;
    }

    private function generateEmbedItem($form, $model, $wire)
    {
        $this->embedForm = $this->embedForm ?? (new $form($model))->embedMode($wire);
        return $this->embedForm;
    }
}
