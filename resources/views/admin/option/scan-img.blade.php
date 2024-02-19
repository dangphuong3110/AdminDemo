@extends('layouts.layout')

@section('title', 'Option')

@section('route-title')
    {{ route('option.index') }}
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
            <form action="{{ route('option.processImage') }}" method="post" enctype="multipart/form-data" id="form-img-input">
                @csrf
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="file" name="img" id="imageInput" class="form-control fs-6">
                            <select class="form-select mt-2" name="language" required>
                                <option selected disabled>Select language (Default: Vietnamese)</option>
                                <option value="1">English</option>
                                <option value="2">Vietnamese</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-success">
                                Submit
                            </button>
                        </div>
                        <div class="text col-md-6">
                            <div class="d-flex justify-content-between">
                                <h2 class="text-danger fw-bold text-decoration-underline">Result:</h2>
                                <a href="#" class="copy" id="btn-copy-text">
                                    <span class="before-copy" id="before-copy"><i class="fa-regular fa-copy mt-4"></i> Copy</span>
                                    <span class="after-copy hidden" id="after-copy"><i class="fa-solid fa-check mt-4"></i> Copied</span>
                                </a>
                            </div>
                            <div class="border border-danger rounded-3">
                                <div class="ms-3 me-3">
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
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('paste', e => {
            if (e.clipboardData.files.length > 0) {
                const form = document.getElementById('form-img-input');
                const imageInput = document.getElementById('imageInput');

                imageInput.files = e.clipboardData.files;
                form.submit();
            }
        });

        const btnCopyText = document.getElementById('btn-copy-text');
        btnCopyText.addEventListener('click', function () {
            const textToCopy = @json($result);

            const textArea = document.createElement('textarea');
            textArea.value = textToCopy.join("\n");

            document.body.appendChild(textArea);

            textArea.select();
            navigator.clipboard.writeText(textArea.value);

            document.body.removeChild(textArea);

            const beforeCopy = document.getElementById('before-copy');
            const afterCopy = document.getElementById('after-copy');

            beforeCopy.classList.add('hidden');
            afterCopy.classList.remove('hidden');
        });
    </script>
@endsection
