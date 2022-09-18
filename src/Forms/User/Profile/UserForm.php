<?php

namespace Nabre\Forms\User\Profile;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class UserForm extends Structure
{
    function build()
    {
        $field=null;
        if(!is_null($this->data->contact)){
            $field=Field::STATIC;
            $this->addMsg('I campi del nome utente e la e-mail sono editabili tramite la pagina dei contatti.');
        }
        $this->add('name',$field)->requestRequired();
        $this->add('email',$field)->requestRequired();

        $this->addMsg('Per modificare la password utilizzare la pagina "password dimenticata" durante la fase di login.','warning');
    }
}
