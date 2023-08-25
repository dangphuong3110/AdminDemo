@extends('layouts.layout')

@section('title', 'Product')

@section('route-title')
    {{ route('products.index') }}
@endsection

@section('content')

    @if($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header">List</div>
        <div class="card-body">
            <form action="{{ route('filter-product') }}" method="GET">
                @csrf
                <div class="row">
                    <div class="col-xxl-6 mb-3">
                        <label class="col-12 mb-1 col-label-form">Name Product</label>
                        <div class="col-12">
                            <input type="text" name="name-product-filter" class="form-control" value="{{ $filter_name_product ?: '' }}"/>
                        </div>
                    </div>
                    <div class="col-xxl-3 mb-3">
                        <label class="col-12 mb-1 col-label-form">Category</label>
                        <div class="col-12">
                            <select name="category-filter" class="form-select">
                                <option value="0">          </option>
                                {!! $listCategories !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-xxl-2 mb-3">
                        <label class="col-12 mb-1 col-label-form">Status</label>
                        <div class="col-12">
                            <select name="status-filter" class="form-select">
                                <option selected value="2">           </option>
                                <option value="1" {{ $filter_status == 1 ? 'selected' : '' }}>On</option>
                                <option value="0" {{ $filter_status == 0 ? 'selected' : '' }}>Off</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="page" value="{{ $products->currentPage() }}">
                    <div class="col-xxl-1 mt-4">
                        <div class="col-12 mt-1">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2">
        <div class="row d-flex">
            <div class="col-md-2 mb-2">
                <form action="{{ route('copy-product') }}" method="post">
                    @csrf
                    <input type="hidden" name="selectedItemsCopy" value="" id="hiddenInputCopy">
                    <button type="submit" id="btn-copy" class="btn btn-warning hidden btn-option"><i class="fa-regular fa-copy"></i> Copy product</button>
                </form>
            </div>
            <div class="col-md-2 mb-2">
                <form method="post" action="{{ route('delete-product') }}">
                    @csrf
                    <input type="hidden" name="selectedItemsDelete" value="" id="hiddenInputDelete">
                    <button type="submit" id="btn-delete" class="btn btn-danger hidden btn-option"><i class="fa-solid fa-trash"></i> Delete product</button>
                </form>
            </div>
            <div class="col-4 ms-auto mb-3">
                <select class="form-select form-select-sm" aria-label="Small select example">
                    <option selected>Customize table display</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header title">
            <div class="row">
                <div class="col col-md-6">Product Data</div>
                <div class="col col-md-6">
                    <a href="{{ route('products.create') }}" class="btn btn-success btn-sm float-end">Add</a>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                <tr>
                    <th class="text-center">
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" value="" id="main-checkbox">
                        </div>
                    </th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Name Product</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Stock Quantity</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
                </thead>
                @if(count($products) > 0)
                        @foreach($products as $product)
                            <tbody>
                            <tr>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input checkbox-for-copy-or-delete" type="checkbox" value="{{ $product->id }}" name="selectedItem">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input form-check-input-product" type="checkbox" role="switch" id="flexSwitchCheckChecked-{{ $product->id }}" {{ $product->status ? 'checked' : '' }} name="display-status" data-product-id="{{ $product->id }}">
                                    </div>
                                </td>
                                <td>{{ $product->name }}</td>
                                <td class="text-center">{{ $product->price }}</td>
                                <td class="text-center">{{ $product->quantity }}</td>
                                <td class="d-flex justify-content-center">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                </td>
                                <td>
                                    <form method="post" action="{{ route('products.destroy', $product->id) }}" class="d-flex justify-content-center">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDelete-{{ $product->id }}">
                                            Delete
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="confirmDelete-{{ $product->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered text-center">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="confirmDeleteLabel">Delete Product</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Do you really want to delete this product?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <input type="submit" class="btn btn-danger" value="Delete"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            </tbody>
                        @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">No Data Found</td>
                    </tr>
                @endif
            </table>
            {!! $products->appends(Request::all())->render('pagination::bootstrap-5') !!}
        </div>
    </div>

@endsection
