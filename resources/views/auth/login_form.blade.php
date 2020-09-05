<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group row">
        <label for="email" class="text-md-right">{{ __('Email Address') }}</label>

        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
               value="{{ old('email') }}" required autofocus>

        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
        @endif
    </div>

    <div class="form-group row">
        <label for="password" class="text-md-right">{{ __('Password') }}</label>

        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
               name="password" required>

        @if ($errors->has('password'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
        @endif
    </div>

    <div class="form-group row">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember"
                   id="remember" {{ old('remember') ? 'checked' : '' }}>

            <label class="form-check-label" for="remember">
                {{ __('Remember Me') }}
            </label>
        </div>
    </div>

    <div class="btn-group row flex-center">
        <button type="submit" class="btn btn-outline-dark w-100">
            {{ __('Login') }}
        </button>
    </div>
</form>
<hr>
<a class="btn btn-outline-dark w-100" href="{{ route('signin') }}">Login with King's Account</a>
@if ($errors->has('non_kcl_account'))
    <small>{{$errors->first('non_kcl_account')}}</small>
@endif
