@extends('layouts.layout')

@section('title', 'Option')

@section('route-title')
    {{ route('homepage.index') }}
@endsection

@section('content')
    @if($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif
    @if($message = Session::get('failure'))
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @endif

    <div class="wrapper">
        <div class="img-test mt-2">
            <form action="{{ route('option.processImage') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="file" name="img" id="imageInput" class="form-control fs-6">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success">
                                Submit
                            </button>
                        </div>
                        <div class="text col-md-6 border border-danger">
                            <h2 class="text-danger fw-bold text-decoration-underline">Result:</h2>
                            @if(empty($result))
                                <p>No text found in the image.</p>
                            @else
                                @foreach($result as $r)
                                    <p>{{ $r }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
