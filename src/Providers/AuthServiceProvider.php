<?php

namespace Nabre\Providers;

use App\Models\Image;
use Nabre\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Nabre\Models\Collection;
use Nabre\Models\CollectionRelation;
use Nabre\Models\FormFieldType;
use Nabre\Models\Menu;
use Nabre\Models\Setting;
use Nabre\Policies\CollectionPolicy;
use Nabre\Policies\CollectionRelationPolicy;
use Nabre\Policies\FormFieldTypePolicy;
use Nabre\Policies\ImagePolicy;
use Nabre\Policies\MenuPolicy;
use Nabre\Policies\PagePolicy;
use Nabre\Policies\PermissionPolicy;
use Nabre\Policies\RolePolicy;
use Nabre\Policies\SettingPolicy;
use Nabre\Policies\UserContactPolicy;
use Nabre\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Permission::class => PermissionPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        Menu::class => MenuPolicy::class,
        Page::class => PagePolicy::class,
        Setting::class => SettingPolicy::class,
        FormFieldType::class => FormFieldTypePolicy::class,
        Collection::class => CollectionPolicy::class,
        CollectionRelation::class=>CollectionRelationPolicy::class,
        UserContact::class=>UserContactPolicy::class,
        Image::class=>ImagePolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
