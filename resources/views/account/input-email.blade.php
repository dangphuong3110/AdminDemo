@extends('account.layout')

@section('content')
    <div class="wrapper">
        <div class="container main">
            <div class="row" style="height: 200px; width:350px">
                <div class="col-12 right">
                    <div class="input-box">
                        <form action="{{ route('without-token') }}" method="post">
                            @csrf
                            <div class="input-field">
                                <input type="email" class="input mb-0" name="email" id="email" value="{{ old('email') }}" required/>
                                <label for="email">Enter your email</label>
                                @if($errors->any())
                                    <span role="alert" style="color: red; font-size: 12px;">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="input-field mt-3">
                                <div class="d-flex">
                                    <div class="ms-auto">
                                        <a href="{{ route('login') }}" class="btn btn-danger">Cancel</a>
                                        <input type="submit" class="btn btn-success" value="Send">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
