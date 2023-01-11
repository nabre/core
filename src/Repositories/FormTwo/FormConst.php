<?php

namespace Nabre\Repositories\FormTwo;

class FormConst
{
    #
    #stringVariables
    #
    const VARIABLE = ['variable'];
    const OUTPUT = ['output'];
    const TYPE = ['type'];
    const VALUE = ['value'];
    const VALUE_LABEL = ['value_LABEL'];
    const LABEL = ['label'];

    #options
    const OPTIONS = ['set', 'options'];
    const OPTIONS_WIREMODEL = ['set', 'options', 'wire:model.defer'];
    const OPTIONS_CLASS = ['set', 'options', 'class'];

    #list
    const LIST = ['set', 'list'];
    const LIST_ITEMS = ['set', 'list', 'items'];
    const LIST_EMPTY = ['set', 'list', 'empty'];
    const LIST_LABEL = ['set', 'list', 'label'];
    const LIST_DISABLEDL = ['set', 'list', 'disabled'];

    #embed
    const EMBED = ['embed'];
    const EMBED_VARIABLE = ['embed', 'parent', 'variable'];
    const EMBED_OUTPUT = ['embed', 'wire', 'output'];
    const EMBED_FORM = ['embed', 'wire', 'form'];

    #Errors
    const ERROR = ['error'];
    const ERROR_PRINT = ['error_print'];

    #relations
    const REL = ['set', 'rel'];
    const REL_MODEL = ['set', 'rel', 'model'];
    const REL_NAME = ['set', 'rel', 'name'];
    const REL_TYPE = ['set', 'rel', 'type'];
    const REL_FK = ['set', 'rel', 'foreignKey'];
    const REL_OK = ['set', 'rel', 'ownerKey'];

    #stringValues
    const EMPTY_KEY = '';

    static function request($method)
    {
        return ['set', 'request', $method];
    }
}
