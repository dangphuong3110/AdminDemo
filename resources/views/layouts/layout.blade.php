<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>

<div class="container mt-5">
    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom border-body fixed-top p-0">
        <div class="container-fluid pe-0">
            <div class="me-2">
                <button class="btn btn-white border border-3 border-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions"><i class="fas fa-indent fa-lg"></i></button>
            </div>
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <form action="{{ route('logout') }}" method="post" id="logout-form">
                        @csrf
                        <a class="nav-link pt-3 pb-3" href="#" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal">Logout<i class="fa-solid fa-right-from-bracket ms-1"></i></a>
                    </form>
                </div>
            </div>
        </div>
    </nav>
   <div class="table-contents pt-4">
       <div class="d-inline"><b class="fs-1 text-danger">@yield('title')</b></div>
       <div class="ms-3 d-inline"><a href="{{ route('homepage.index') }}">Homepage</a> <span>/</span> <a href=@yield('route-title')>@yield('title')</a></div>
   </div>

    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h6 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Administrator: {{ $nameOfAdmin }}</h6>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="homepage">
                <a href="{{ route('homepage.index') }}" class="btn btn-secondary width-100">Homepage</a>
            </div>
            <div class="dropdown-center mt-3">
                <button class="btn btn-secondary dropdown-toggle width-100" type="button" data-bs-toggle="dropdown">
                    Product
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="{{ route('products.index') }}">Product</a></li>
                    <li><a class="dropdown-item" href="{{ route('categories.index') }}">Category</a></li>
                    <li><a class="dropdown-item" href="{{ route('manufacturers.index') }}">Manufacturer</a></li>
                </ul>
            </div>
            <div class="dropdown-center mt-3">
                <button class="btn btn-secondary dropdown-toggle width-100" type="button" data-bs-toggle="dropdown">
                    Settings
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="{{ route('general-setting') }}">General Settings</a></li>
                </ul>
            </div>
            <div class="dropdown-center mt-3">
                <button class="btn btn-secondary dropdown-toggle width-100" type="button" data-bs-toggle="dropdown">
                    Options
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="{{ route('option.index') }}">Scan text from Image</a></li>
                </ul>
            </div>
        </div>
    </div>

    @yield('content')

</div>
<!-- MODAL-LOGOUT -->
<div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-labelledby="confirmLogoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-center">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmLogoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body fs-5">
                Are you sure you want to log out?
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger me-2" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.tiny.cloud/1/ea10g924cjxiko0v5pbqmfozvr89vzzxnefhzkggqqnblhnw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
    //LIBRARY TINYMCE
    tinymce.init({
        selector: '#tinyMCEInput',
        plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant"))
    });

    //LOGOUT
    function confirmLogout() {
        document.getElementById('logout-form').submit();
    }

</script>
<script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
