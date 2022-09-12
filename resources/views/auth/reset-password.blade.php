@extends('Nabre::paginate.area.app')
@section('CONTENT')

<div class="card w-50 m-auto">
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="mb-3 row">
                    <label for="inputMail" class="col-sm-2 col-form-label">{{__('Email')}}</label>
                    <div class="col-sm-10">
                        <input class="form-control" required autofocus id="inputMail" value="{{old('email',$request->email)}}" type="email" name="email">
                    </div>
                </div>
                <!-- Password -->
                <div class="mb-3 row">
                    <label for="inputPsw" class="col-sm-2 col-form-label">{{__('Password')}}</label>
                    <div class="col-sm-10">
                        <input class="form-control" required id="inputPsw"  type="password" name="password">
                    </div>
                </div>
                <!-- Confirm Password -->
                <div class="mb-3 row">
                    <label for="inputPswC" class="col-sm-2 col-form-label">{{__('Confirm Password')}}</label>
                    <div class="col-sm-10">
                        <input class="form-control" required id="inputPswC" type="password" name="password_confirmation">
                    </div>
                </div>
              <div class="mb-3 row">
                <div class="col-sm-2 col-form-label"></div>
                <div class="col-sm-10">
                    <button class="btn btn-primary" type="submit">
                    {{ __('Reset Password') }}
                    </button>
                </div>
              </div>
            </form>

        </div>
    </div>
@endsection
