<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email Address -->
          <div class="mb-3 row">
            <label for="inputMail" class="col-md-3 col-form-label">{{__("Email Address")}}</label>
            <div class="col">
              <input class="form-control" required id="inputMail" value="{{old('email')}}" type="email" name="email">
            </div>
          </div>

    <!-- Password -->
          <div class="mb-3 row">
            <label for="inputPsw" class="col-md-3 col-form-label">{{__('Password')}}</label>
            <div class="col">
              <input class="form-control" autocomplete="current-password" required id="inputPsw" type="password" name="password">
            </div>
          </div>

    <!-- Remember Me -->
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="" id="remember_me" name="remember">
      <label class="form-check-label" for="remember_me">
        {{ __("Remember Me") }}
      </label>
    </div>

    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">
            {{ __("Forgot Your Password?") }}
        </a>
    @endif

  <div class="mb-3 row">
    <div class="col-sm-2 col-form-label"></div>
    <div class="col-sm-10">
        <button class="btn btn-primary" type="submit">
             {{ __("Login") }}
        </button>
    </div>
  </div>
</form>
