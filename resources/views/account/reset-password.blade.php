@extends('account.layout')

@section('content')
    <div class="wrapper">
        <div class="container main">
            <div class="row" style="height: 200px; width:350px">
                <div class="col-12 right">
                    <div class="input-box">
                        <form action="{{ route('with-token', $token) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="input-field">
                                <input type="password" class="input mb-0" name="password" id="password" required>
                                <label for="password">Enter your new password</label>
                                @if ($errors->has('password'))
                                     <span role="alert" style="color: red; font-size: 12px;">
                                        <strong>{{ $errors->first('password') }}</strong>
                                     </span>
                                @endif
                            </div>
                            <div class="input-field mt-3">
                                <input type="password" class="input mb-0" name="confirm-password" id="confirm-password" required>
                                <label for="confirm-password">Confirm password</label>
                                @if ($errors->has('confirm-password'))
                                    <span role="alert" style="color: red; font-size: 12px;">
                                        <strong>{{ $errors->first('confirm-password') }}</strong>
                                     </span>
                                @endif
                            </div>
                            <div class="input-field mt-3">
                                <div class="d-flex">
                                    <div class="ms-auto">
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
