@extends('layouts.layout')

@section('title', 'Manufacturer')

@section('route-title')
    {{ route('manufacturers.index') }}
@endsection

@section('content')

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($message = Session::get('failure'))

        <div class="alert alert-danger">
            {{ $message }}
        </div>

    @endif
    <div class="card">
        <div class="card-header title">Edit Manufacturer</div>
        <div class="card-body">
            <form method="post" action="{{ route('manufacturers.update', $manufacturer->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">General information</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Name Manufacturer</label>
                                    <div class="col-12">
                                        <input type="text" name="name-manufacturer" class="form-control" value="{{ $manufacturer->name }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="ms-auto p-2 me-5">
                        <a href="{{ route('manufacturers.index') }}" class="btn btn-danger me-2">Cancel</a>
                        <input type="submit" class="btn btn-warning" value="Save"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection('content')
