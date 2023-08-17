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

    <div class="card">
        <div class="card-header title">
            <div class="row">
                <div class="col col-md-6">Product Data</div>
                <div class="col col-md-6">
                    <a href="{{ route('products.create') }}" class="btn btn-success btn-sm float-end">Add</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
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
                        <td colspan="6" class="text-center">No Data Found</td>
                    </tr>
                @endif
            </table>
            {!! $products->render('pagination::bootstrap-5') !!}
        </div>
    </div>

@endsection
