<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;
use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\Livewire\Embed;
use Nabre\Repositories\FormTwo\Livewire\Render;
use Nabre\Repositories\FormTwo\Livewire\Submit;

class FormManage extends Component
{
    use Embed;
    use Render;
    use Submit;

    var $idData;
    var $model;
    var $formClass;
    var $back;

    var $print = [];
    var $wireValues = [];
    private $validateRules = [];

    private $form;

    function mount()
    {
        $this->form();

        $this->wireValues = $this->form->values();

        $this->print[] = $this->form->buttonBack();
        $this->form->elements->each(function ($item) {
            switch (data_get($item, 'output')) {
                case Field::EMBEDS_MANY:
                    data_set($item, 'html', $this->htmlEmbedItem($item));
                    $this->print[] = $item;
                    break;
                case Field::EMBEDS_ONE:
                    $this->print[] = $this->htmlEmbedItem($item, $this->embedRenderItem($item));
                    break;
                default:
                    $this->print[] = $this->htmlDefaultItem($item);
                    break;
            }
        });
        $this->print[] = $this->form->buttonSubmit();
    }

    private function form()
    {
        $data = $this->model::find($this->idData) ?? $this->model::make();
        $this->form = (new $this->formClass)->redirect(['index' => $this->back])->input($data);
    }

    public function render()
    {
        $out = (string) Html::tag('form', $this->generate(), ['wire:submit.prevent' => 'submit', 'class' => 'container']);
        return view('Nabre::livewire.form-manage',get_defined_vars());
    }
}
