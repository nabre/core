<?php

namespace Nabre\Listeners;

use App\Models\User;
use App\Models\UserContact;
use Nabre\Events\Setting\PageEvent as Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PageDisabled
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Nabre\Events\Settings  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $disabled=(bool)$event->page->disabled??false;
        switch($event->page->name){
            case "nabre.user.contact.index":
                if($disabled){
                    UserContact::truncate();
                }else{
                    User::get()->each(function($user){
                        $data=$user->contact;
                        if(is_null($data)){
                            $data= new UserContact;                            
                        }
                        $data->recursiveSave(['user'=>$user->id]);
                    });
                }
            break;
        }
    }
}
