<?php

namespace Nabre\Repositories\FormTwo\FormTrait;

use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\QueryElements;
use Nabre\Repositories\FormTwo\Rule;

trait StructureErrors
{
    private function errors()
    {
        $mode = strtolower(env('APP_ENV', 'production'));

        switch ($mode) {
            case "local":
                break;
            default:
                $this->elements = (new QueryElements($this->elements))->removeInexistents()->results();
                break;
        }

        $this->elements = $this->elements->map(function ($i) use ($mode) {
            $errors = collect([]);
            $type = data_get($i, 'type', false);

            if (!$type) {
                $errors = $errors->push('Variabile non esistente.');
            } else {

                $output = data_get($i, 'output', false);

                switch ($output) {
                    case Field::EMBEDS_MANY:
                    case Field::EMBEDS_ONE:
                        $string = data_get($i, 'embed', false);
                        if (!$string) {
                            $errors = $errors->push('Il form nidificato non è stato definito.');
                        }
                        break;
                    default:
                        $array = Field::fieldsListRequired();
                        if (count($array) && in_array($output, $array)) {
                            $list = data_get($i, 'set.list.items', false);
                            if (!$list) {
                                $errors = $errors->push('Lista items non definita.');
                            }

                            $label = data_get($i, 'set.list.label', false);
                            if (!$label) {
                                $errors = $errors->push('Campo etichetta lista non definito.');
                            }

                            $model = data_get($i, 'set.rel.model');
                            if (!($this->isAttribute($label, $model) || $this->isFillable($label, $model))) {
                                $errors = $errors->push('Campo etichetta non valido.');
                            }
                        }
                        break;
                }
            }

            if ($errors->count()) {
                switch ($mode) {
                    case "local":
                        break;
                    default:
                        $errors = collect([]);
                        $errors = $errors->push("Configurazione non corretta.");
                        break;
                }
                $this->setData($i, 'errors', $errors);
            }
            return $i;
        })->values();
    }
}
