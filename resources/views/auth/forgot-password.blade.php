@extends('Nabre::paginate.area.app')
@section('CONTENT')



<div class="card w-50 m-auto">
        <div class="card-body">

        <div class="mb-4 text-sm text-gray-600">
            {{ __('forgotpage.description') }}
        </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                      <div class="mb-3 row">
                        <label for="inputMail" class="col-sm-2 col-form-label">{{__('Email')}}</label>
                        <div class="col-sm-10">
                          <input class="form-control" required id="inputMail" value="{{old('email')}}" type="email" name="email" required autofocus>
                        </div>
                      </div>
                <div class="mb-3 row">
                <div class="col-sm-2 col-form-label"></div>
                <div class="col-sm-10">
                    <button class="btn btn-primary" type="submit">
                    {{ __("Send Password Reset Link") }}
                    </button>
                </div>
              </div>
            </form>
        </div>
    </div>
@endsection
