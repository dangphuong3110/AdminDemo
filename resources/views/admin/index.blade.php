@extends('layouts.layout')

@section('title', 'Dashboard')

@section('route-title')
    {{ route('homepage.index') }}
@endsection

@section('content')
    @if($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif
@endsection
