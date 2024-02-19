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

    <div class="card mb-3">
        <div class="card-header">List</div>
        <div class="card-body">
            <form action="{{ route('filter-category') }}" method="GET">
                @csrf
                <div class="row">
                    <div class="col-xxl-7 mb-3">
                        <label class="col-12 mb-1 col-label-form">Name Category</label>
                        <div class="col-12">
                            <input type="text" name="name-category-filter" class="form-control" value="{{ $filter_name_category ?: '' }}"/>
                        </div>
                    </div>
                    <div class="col-xxl-4 mb-3">
                        <label class="col-12 mb-1 col-label-form">Status</label>
                        <div class="col-12">
                            <select name="status-filter" class="form-select">
                                <option selected value="2">           </option>
                                <option value="1" {{ $filter_status == 1 ? 'selected' : '' }}>On</option>
                                <option value="0" {{ $filter_status == 0 ? 'selected' : '' }}>Off</option>
                            </select>
                        </div>
                    </div>
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
        <div class="row">
            <div class="col-md-2 mb-2">
                <form method="post" action="{{ route('delete-category') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="selectedItemsDelete" value="" id="hiddenInputDelete">
                    <button type="button" id="btn-delete" class="btn btn-danger hidden btn-option" data-bs-toggle="modal" data-bs-target="#confirmDelete-all"><i class="fa-solid fa-trash"></i> Delete category</button>
                    <!-- Modal -->
                    <div class="modal fade" id="confirmDelete-all" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
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
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header title">
            <div class="row">
                <div class="col col-md-6">Category Data</div>
                <div class="col col-md-6">
                    <a href="{{ route('categories.create') }}" class="btn btn-success btn-sm float-end">Add</a>
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
                        <td colspan="6" class="text-center">No Data Found</td>
                    </tr>
                @endif
            </table>
{{--            {!! $categories->render('pagination::bootstrap-5') !!}--}}
        </div>
    </div>

    <script>
        //UPDATE STATUS CATEGORY
        const checkboxCategories = document.querySelectorAll(".form-check-input-category");
        checkboxCategories.forEach(checkbox => {
            checkbox.addEventListener("click", function () {
                const isChecked = checkbox.checked;
                const categoryId = checkbox.getAttribute('data-category-id');
                updateStatusCategory(isChecked, categoryId);
            });
        });

        function updateStatusCategory(isChecked, categoryId) {
            fetch(`/admin/categories/update-status-category/${categoryId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ isChecked }),
            });
        }

        //SELECT ALL CHECKBOX OF PRODUCT
        const mainCheckbox = document.getElementById('main-checkbox');
        const buttonOptions = document.querySelectorAll('.btn-option');
        const allCheckboxProducts = document.querySelectorAll('.checkbox-for-delete');
        mainCheckbox.addEventListener('click', function () {
            buttonOptions.forEach(buttonOption => {
                if (mainCheckbox.checked) {
                    buttonOption.classList.remove('hidden');
                } else {
                    buttonOption.classList.add('hidden');
                }
            });

            allCheckboxProducts.forEach(checkboxProduct => {
                checkboxProduct.checked = !!mainCheckbox.checked;
            });
        });

        //HIDDEN OR DISPLAY BUTTON DELETE
        allCheckboxProducts.forEach(checkboxProduct => {
            checkboxProduct.addEventListener('change', function () {
                buttonOptions.forEach(buttonOption => {
                    if (areAnyCheckboxesChecked()) {
                        buttonOption.classList.remove('hidden');
                    } else {
                        buttonOption.classList.add('hidden');
                    }
                });

            });
        });

        function areAnyCheckboxesChecked() {
            return Array.from(allCheckboxProducts).some(checkboxProduct => checkboxProduct.checked);
        }

        //DELETE CATEGORY
        const buttonDeleteCategory = document.getElementById('btn-delete');

        buttonDeleteCategory.addEventListener('click', function () {
            const arraySelectedItems = [];
            const checkboxesChecked = document.querySelectorAll('.checkbox-for-delete:checked');
            const inputs = document.getElementById('hiddenInputDelete');
            checkboxesChecked.forEach(checkbox => {
                arraySelectedItems.push(checkbox.value);
            });

            inputs.value = arraySelectedItems;
        });
    </script>
@endsection
