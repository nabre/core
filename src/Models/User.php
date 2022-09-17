<?php

namespace Nabre\Models;

use App\Models\UserContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Nabre\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Auth\User as JUser;
use Jenssegers\Mongodb\Relations\HasMany;
use Jenssegers\Mongodb\Relations\HasOne;
use Nabre\Casts\PasswordCast;
use Nabre\Database\Eloquent\RelationshipsTrait;
use Nabre\Database\Eloquent\RecursiveSaveTrait;

class User extends JUser implements AuthenticatableContract, AuthorizableContract, MustVerifyEmail
{
    use HasRoles;
    use HasFactory, Authorizable, Notifiable;
    use RelationshipsTrait;
    use RecursiveSaveTrait;
    use HasEvents;

    protected $fillable = [
        'name',
        'email',
        'password',
        'disabled',
        'email_verified_at',
        'lang',
        'api_token', //bin2hex(openssl_random_pseudo_bytes(30)) //str_random(60);
    ];

    protected $attributes = [
        'disabled' => 0,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'name' => 'string',
        'disabled' => 'boolean',
        'password' => PasswordCast::class,
    ];

    protected $dates = ['email_verified_at'];

    function contact(): HasOne
    {
        return $this->hasOne(UserContact::class);
    }

    function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    function getLvlRoleAttribute()
    {
        return $this->roles()->get()->min("priority") ?? null;
    }

    function getActiveAttribute()
    {
        return !is_null($this->password) && !is_null($this->email_verified_at) && $this->enabled;
    }

    function getEtiAttribute(){
        return $this->email;
    }

    function getShowStringAttribute()
    {
        return $this->email;
    }

    #impersonate
    public function setImpersonating($id)
    {
        \Session::put('impersonate', $id);

        return $this;
    }

    public function stopImpersonating()
    {
        \Session::forget('impersonate');

        return $this;
    }

    public function isImpersonating()
    {
        return \Session::has('impersonate');
    }
}
