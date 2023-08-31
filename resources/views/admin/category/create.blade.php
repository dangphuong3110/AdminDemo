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
        <div class="card-header title">Add Category</div>
        <div class="card-body">
            <form method="post" action="{{ route('categories.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Main content</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Parent Category</label>
                                    <div class="col-12">
                                        <select class="form-select" name="parent-id">
                                            <option selected value="0">-----Select-----</option>
                                            {!! $listCategories !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="mb-1 col-label-form">Name Category</label>
                                        <div class="mb-1">
                                            <input class="form-check-input" type="checkbox" id="checkboxMultiCategories" name="checkboxMultiCategories">
                                            <label class="form-check-label col-label-form" for="checkboxMultiCategories">Add multiple categories</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <input id="name-one-category" type="text" name="name-one-category" class="form-control" value="{{ old('name-category') }}"/>
                                        <textarea placeholder="Enter each name of category on a separate line." id="name-multiple-categories" name="name-multiple-categories" class="form-control hidden">{{ old('name-category') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Description</label>
                                    <div class="col-12">
                                        <textarea id="tinyMCEInput" name="description" class="form-control">{{ old('description') }}</textarea>
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
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked name="display-status">
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
                        <input type="submit" class="btn btn-success" value="Add"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        //ADD MULTIPLE CATEGORIES
        const checkboxMultipleCategories = document.getElementById('checkboxMultiCategories');
        const nameOneCategory = document.getElementById('name-one-category');
        const nameMultipleCategories = document.getElementById('name-multiple-categories');

        checkboxMultipleCategories.checked = false;
        checkboxMultipleCategories.addEventListener('change', function () {
            if (checkboxMultipleCategories.checked) {
                if (nameOneCategory.value !== '') {
                    nameMultipleCategories.value += nameOneCategory.value + "\n";
                }
                nameOneCategory.classList.add('hidden');
                nameMultipleCategories.classList.remove('hidden');
            } else {
                nameOneCategory.value = '';
                nameOneCategory.classList.remove('hidden');
                nameMultipleCategories.classList.add('hidden');
            }
        });
    </script>

@endsection('content')
