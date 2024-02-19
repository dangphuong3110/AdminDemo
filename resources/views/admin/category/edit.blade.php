@extends('layouts.layout')

@section('title', 'Category')

@section('route-title')
    {{ route('categories.index') }}
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
        <div class="card-header title">Edit Category</div>
        <div class="card-body">
            <form method="post" action="{{ route('categories.update', $category->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Main content</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Parent Category</label>
                                    <div class="col-12">
                                        <select class="form-select" name="parent-id">
                                            <option selected>-----Select-----</option>
                                            {!! $listCategories !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Name Category</label>
                                    <div class="col-12">
                                        <input type="text" name="name-category" class="form-control" value="{{ $category->name }}"/>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Description</label>
                                    <div class="col-12">
                                        <textarea id="tinyMCEInput" name="description" class="form-control">{{ $category->desc }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">General information</div>
                            <div class="card-body">
                                <div class="row">
                                    <label class="col-12 mb-1 col-label-form">Display status</label>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" {{ $category->status ? 'checked' : '' }} name="display-status">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="ms-auto p-2 me-5">
                        <a href="{{ route('categories.index') }}" class="btn btn-danger me-2">Cancel</a>
                        <input type="submit" class="btn btn-warning" value="Save"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection('content')
