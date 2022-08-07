@extends('layouts.app', ['class' => 'login-page', 'page' => _('Reset password'), 'contentClass' => 'login-page'])

@section('content')
<div class="header py-7 py-lg-8">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-6">
                        <h1 class="text-white">{{ __('OTP Sent') }}</h1>
                        <p class="text-lead text-light">
                            {{ __('Please Enter OTP You Just Receive On Your Email') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-md-7 ml-auto mr-auto">
        <form class="form" method="post" action="{{ route('password.otp') }}">
            @csrf

            <input type="hidden" name="email" value="{{$email}}">
            <div class="card card-login card-white">
                <div class="card-header">
                    <img src="{{ asset('white') }}/img/card-primary.png" alt="">
                    <h1 class="card-title">{{ _('Enter OTP') }}</h1>
                </div>
                <div class="card-body">
                    @include('alerts.success')

                    <div class="input-group{{ $errors->has('otp') ? ' has-danger' : '' }}">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tim-icons icon-key-25"></i>
                            </div>
                        </div>
                        <input type="number" name="otp" class="form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}" placeholder="{{ _('6 digits OTP') }}" value="{{old('otp')}}">
                        @include('alerts.feedback', ['field' => 'otp'])
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-default btn-lg btn-block mb-3">{{ _('Submit OTP') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
