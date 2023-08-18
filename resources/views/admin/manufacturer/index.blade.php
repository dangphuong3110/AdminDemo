@extends('layouts.layout')

@section('title', 'Manufacturer')

@section('route-title')
    {{ route('manufacturers.index') }}
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
                <div class="col col-md-6">Manufacturer Data</div>
                <div class="col col-md-6">
                    <a href="{{ route('manufacturers.create') }}" class="btn btn-success btn-sm float-end">Add</a>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th class="text-center">Name</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
                </thead>
                @if(count($manufacturers) > 0)

                    @foreach($manufacturers as $manufacturer)
                        <tbody>
                        <tr>
                            <td>{{ $manufacturer->name }}</td>
                            <td class="d-flex justify-content-center">
                                <a href="{{ route('manufacturers.edit', $manufacturer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                            <td>
                                <form method="post" action="{{ route('manufacturers.destroy', $manufacturer->id) }}" class="d-flex justify-content-center">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDelete-{{ $manufacturer->id }}">
                                        Delete
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="confirmDelete-{{ $manufacturer->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-center">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="confirmDeleteLabel">Delete Manufacturer</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Do you really want to delete this manufacturer?
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
                        <td colspan="3" class="text-center">No Data Found</td>
                    </tr>
                @endif
            </table>
            {!! $manufacturers->render('pagination::bootstrap-5') !!}
        </div>
    </div>

@endsection
