<?php

namespace Nabre\Providers;

use App\Models\User;
use App\Models\UserContact;
use Nabre\Models\Page;
use Illuminate\Support\ServiceProvider;
use Nabre\Models\Collection;
use Nabre\Models\Setting;
use Nabre\Observers\CollectionObserver;
use Nabre\Observers\PageObserver;
use Nabre\Observers\SettingObserver;
use Nabre\Observers\UserContactObserver;
use Nabre\Observers\UserObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Collection::observe(CollectionObserver::class);
        Page::observe(PageObserver::class);
        Setting::observe(SettingObserver::class);
        User::observe(UserObserver::class);
        UserContact::observe(UserContactObserver::class);
    }
}
