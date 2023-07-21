@extends(backpack_view('layouts.plain'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h3 class="text-center mb-4">{{ 'Register Your Organization' }}</h3>
            <p class="text-center mb-1">{{ 'To register as a donor, please download the UnlimitedBlood app.' }}
            <p>
            <div class="card">
                <div class="card-body">
                    <form class="col-md-15 p-t-10" role="form" method="POST"
                          action="{{ route('backpack.auth.register') }}">
                        {!! csrf_field() !!}

                        <div class="form-group">
                            <label class="control-label" for="name">{{ 'Organization Name' }}</label>

                            <div>
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                       name="name" id="name" value="{{ old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"
                                   for="{{ backpack_authentication_column() }}">{{ "Organization Phone Number" }}</label>

                            <div>
                                <input
                                    type="{{ backpack_authentication_column()==backpack_email_column()?'email':'numeric'}}"
                                    class="form-control{{ $errors->has(backpack_authentication_column()) ? ' is-invalid' : '' }}"
                                    name="{{ backpack_authentication_column() }}"
                                    id="{{ backpack_authentication_column() }}"
                                    value="{{ old(backpack_authentication_column()) }}">

                                @if ($errors->has(backpack_authentication_column()))
                                    <span class="invalid-feedback">
                                            <strong>{{ $errors->first(backpack_authentication_column()) }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"
                                   for="email">{{ 'Personal Phone Number (Login Number)' }}</label>
                            <div>
                                <input type="numeric"
                                       class="form-control{{ $errors->has('personal_num') ? ' is-invalid' : '' }}"
                                       name="personal_num" id="personal_num" value="{{ old('personal_num') }}">
                                @if ($errors->has('personal_num'))
                                    <span class="invalid-feedback">
                                         <strong>{{ $errors->first('personal_num') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"
                                   for="email">{{ 'Your Full Name' }}</label>
                            <div>
                                <input type="text"
                                       class="form-control{{ $errors->has('personal_name') ? ' is-invalid' : '' }}"
                                       name="personal_name" id="personal_num" value="{{ old('personal_name') }}">
                                @if ($errors->has('personal_name'))
                                    <span class="invalid-feedback">
                                         <strong>{{ $errors->first('personal_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label" for="email">{{ 'Organization Email' }}</label>
                            <div>
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       name="email" id="email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                         <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">

                            <label class="control-label" for="website">{{ 'Website' }}</label>
                            <div>
                                <input type="url" class="form-control{{ $errors->has('website') ? ' is-invalid' : '' }}"
                                       name="website" id="website" value="{{ old('website') }}">
                                @if ($errors->has('website'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('website') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label" for="address">{{ 'Address' }}</label>

                            <div>
                                <input type="text"
                                       class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}"
                                       name="address" id="address" value="{{ old('address') }}">

                                @if ($errors->has('address'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label" for="password">{{ trans('backpack::base.password') }}</label>

                            <div>
                                <input type="password"
                                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       name="password" id="password">

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"
                                   for="password_confirmation">{{ trans('backpack::base.confirm_password') }}</label>

                            <div>
                                <input type="password"
                                       class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                       name="password_confirmation" id="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="logo">{{ 'Logo' }}</label>

                            <div>
                                <input type="file" class="form-control{{ $errors->has('logo') ? ' is-invalid' : '' }}"
                                       name="logo" id="logo">

                                @if ($errors->has('logo'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('logo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-block btn-primary">
                                    {{ trans('backpack::base.register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if (backpack_users_have_email() && backpack_email_column() == 'email' && config('backpack.base.setup_password_recovery_routes', true))
                <div class="text-center"><a
                        href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a>
                </div>
            @endif
            <div class="text-center"><a
                    href="{{ route('backpack.auth.login') }}">{{ trans('backpack::base.login') }}</a></div>
        </div>
    </div>
@endsection

