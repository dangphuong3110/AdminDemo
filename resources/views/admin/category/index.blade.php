@extends('layouts.layout')

@section('title', 'Category')

@section('route-title')
    {{ route('categories.index') }}
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
                <div class="col col-md-6">Category Data</div>
                <div class="col col-md-6">
                    <a href="{{ route('categories.create') }}" class="btn btn-success btn-sm float-end">Add</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Name Category</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
                </thead>
                @if(count($categories) >= 0)
                    {!! $listCategories !!}
                @else
                    <tr>
                        <td colspan="5" class="text-center">No Data Found</td>
                    </tr>
                @endif
            </table>
{{--            {!! $categories->render('pagination::bootstrap-5') !!}--}}
        </div>
    </div>

@endsection
