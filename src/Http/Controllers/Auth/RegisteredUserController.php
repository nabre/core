<?php

namespace Nabre\Http\Controllers\Auth;

use Nabre\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Nabre\Forms\Auth\UserRegisterForm;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Build $build)
    {
        $build = $build->structure(UserRegisterForm::class);
        $form = $build->html('register');
        return view('Nabre::auth.register', compact('form'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Validator $validate)
    {
        $user = $validate->structure(UserRegisterForm::class)->saveIn();
        Auth::login($user);
        event(new Registered($user));

        return redirect(RouteServiceProvider::HOME);
    }
}
