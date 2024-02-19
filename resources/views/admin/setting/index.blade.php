@extends('layouts.layout')

@section('title', 'General Setting')

@section('route-title')
    {{ route('general-setting') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 d-flex justify-content-center align-items-center border border-dark-subtle pt-4 pb-4">
                    <div class="setting-option ms-4 mt-3 mb-3">
                        <a href="{{ route('account.index') }}"><i class="fa-solid fa-users-gear fa-2xl"></i> Management administrator accounts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

