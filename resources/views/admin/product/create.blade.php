@extends('layouts.layout')

@section('title', 'Product')

@section('route-title')
    {{ route('products.index') }}
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
        <div class="card-header title">Add Product</div>
        <div class="card-body">
            <form method="post" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">General information</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Name Product</label>
                                    <div class="col-12">
                                        <input type="text" name="name-product" class="form-control" value="{{ old('name-product') }}" required/>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Short Description</label>
                                    <div class="col-12">
                                        <textarea id="tinyMCEInput" name="short-description" class="form-control">{{ old('short-description') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Detail Description</label>
                                    <div class="col-12">
                                        <textarea id="tinyMCEInput" name="detail-description" class="form-control">{{ old('detail-description') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Price</label>
                                    <div class="col-12">
                                        <input type="text" name="price" class="form-control" value="{{ old('price') }}"/>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-12 mb-1 col-label-form">Stock Quantity</label>
                                    <div class="col-12">
                                        <input type="text" name="stock-quantity" class="form-control" value="{{ old('stock-quantity') }}"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-12 mb-1 col-label-form">Video</label>
                                    <div class="col-12">
                                        <input type="text" name="video" class="form-control" value="{{ old('video') }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-3 mb-3">
                            <div class="card-header">Product images</div>
                            <div class="card-body">
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-8 mb-2 d-flex align-items-center">
                                        <input type="file" name="image" id="imageInput" class="form-control fs-6"/>
                                    </div>
                                    <div class="col-md-3">
                                        <img id="preview" src="{{ asset('assets/image/no-photo.jpg') }}" alt="Preview Image" class="image-preview"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Display status</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked name="display-status">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-header">Category</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        {!! $listCategories !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-header">Manufacturer</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <select class="form-select" name="manufacturer">
                                            <option selected disabled>Select a Manufacturer</option>
                                            @foreach($manufacturers as $manufacturer)
                                                <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="ms-auto p-2 me-5">
                        <a href="{{ route('products.index') }}" class="btn btn-danger me-2">Cancel</a>
                        <input type="submit" class="btn btn-success" value="Add"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection('content')
