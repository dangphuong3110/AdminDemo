@extends('layouts.layout')

@section('title', 'Management Account')

@section('route-title')
    {{ route('account.index') }}
@endsection

@section('content')
    @if($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @endif
    <div class="card">
        <div class="card-header title">
            <div class="row">
                <div class="col col-md-6">List Account</div>
                <div class="col col-md-6">
                    <a href="{{ route('account.create') }}" class="btn btn-success btn-sm float-end">Add</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                <tr>
                    <th class="text-center">Username</th>
                    <th class="text-center">Created at</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
                </thead>
                @if(count($accounts) > 0)

                    @foreach($accounts as $account)
                        <tbody>
                        <tr>
                            <td class="text-center">{{ $account->username }}</td>
                            <td class="text-center">{{ explode(" ", $account->created_at)[0] }}</td>
                            <td class="d-flex justify-content-center">
                                <a href="{{ route('account.edit', $account->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                            <td>
                                <form method="post" action="{{ route('account.destroy', $account->id) }}" class="d-flex justify-content-center">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDelete-{{ $account->id }}">
                                        Delete
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="confirmDelete-{{ $account->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-center">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="confirmDeleteLabel">Delete Account</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Do you really want to delete this account?
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
                        <td colspan="4" class="text-center">No Data Found</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
@endsection

