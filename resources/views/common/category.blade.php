@extends($pageData->role == 'admin' ? 'layout.admin-app' : 'layout.manager-app')
@section($pageData->role == 'admin' ? 'adminContent' : 'managerContent')



@push('style')
<style>
.dd-content {
    padding: 6px 16px 8px 30px;
    height: auto;
    font-weight: 400;
    border-radius: 0;
    display: block;
    height: 30px;
    margin: 5px 5px;
    color: #979898;
    text-decoration: none;
    font-weight: 700;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    display: block;
    height: 30px;
    margin: 5px 0;
}

.action {
    display: none;
}

.dd-content:hover .action {
    display: inline-block;
}
</style>
@endpush

<div class="row align-items-center">
    <div class="col-lg-8 col-xl-6 col-md-12 mx-auto">
        <div class="card">
            <div
                class="border-bottom title-part-padding d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h4 class="card-title mb-3 mb-md-0">Categories List</h4>
                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 gap-sm-3">
                    <button class="btn btn-primary mb-2 mb-sm-0" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNewCategory" aria-controls="offcanvasNewCategory">Add
                        Category</button>
                    <button class="btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNewSubCategory"
                        aria-controls="offcanvasNewSubCategory">Add Sub Category</button>
                </div>
            </div>
            <div class="card-body">
                <div class="myadmin-dd dd" id="nestable">
                    <ol class="dd-list">
                        @for ($i = 0 ; $i < $pageData->category->count();$i++)
                            <li class="dd-item" data-id="{{$i+1}}">
                                <div class="dd-content fs-4" style="color: {{ $pageData->category[$i]->deleted_at ? 'red' : 'inherit' }}">{{$pageData->category[$i]->name}}
                                    @if ($pageData->category[$i]->deleted_at != null)
                                    <form
                                        action="{{route('category.restore', ['encodedId' => base64_encode($pageData->category[$i]->id)])}}"
                                        method="post" class="delete-form mx-1" style="display:inline;"
                                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Restore">
                                        @csrf
                                        <button type="submit" class="delete-btn" onclick="return confirmRestore()"
                                            style="background:none; border:none; padding:0; margin:0; color:green; cursor:pointer;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-restore">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3.06 13a9 9 0 1 0 .49 -4.087" />
                                                <path d="M3 4.001v5h5" />
                                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                            </svg>
                                        </button>
                                    </form>
                                    @else
                                    <span class="action">
                                        <span class="text-primary mx-1"
                                            onclick="edit('{{ route('category.getbyid', ['encodedId' => base64_encode($pageData->category[$i]->id)]) }}','{{ route('category.update', ['encodedId' => base64_encode($pageData->category[$i]->id),'returnType'=>'json']) }}', 'category')"
                                            style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Update">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icon-tabler-outline icon-tabler-edit">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path
                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </span>
                                        <form
                                            action="{{route('category.destroy', ['encodedId' => base64_encode($pageData->category[$i]->id)])}} "
                                            method="post" class="delete-form" style="display:inline;"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Soft Delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn" onclick="return confirmDelete()"
                                                style="background:none; border:none; padding:0; margin:0; color:red; cursor:pointer;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    </span>
                                    @endif
                                </div>

                                <ol class="dd-list">
                                    @for ($j = 0 ; $j < $pageData->category[$i]->subCategorieswithTrashed->count()
                                        ;$j++)
                                        <li class="dd-item" data-id="3">
                                            <div class="dd-content fs-4" style="color: {{ $pageData->category[$i]->subCategorieswithTrashed[$j]->deleted_at ? 'red' : 'inherit' }}">
                                                {{$pageData->category[$i]->subCategorieswithTrashed[$j]->name}}

                                                @if ($pageData->category[$i]->subCategorieswithTrashed[$j]->deleted_at
                                                != null)
                                                <form
                                                    action="{{route('category.restore', ['encodedId' => base64_encode($pageData->category[$i]->subCategorieswithTrashed[$j]->id)])}}"
                                                    method="post" class="delete-form mx-1" style="display:inline;"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Restore">
                                                    @csrf
                                                    <button type="submit" class="delete-btn"
                                                        onclick="return confirmRestore()"
                                                        style="background:none; border:none; padding:0; margin:0; color:green; cursor:pointer;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-restore">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3.06 13a9 9 0 1 0 .49 -4.087" />
                                                            <path d="M3 4.001v5h5" />
                                                            <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                        </svg>
                                                    </button>
                                                </form>

                                                @else

                                                <span class="action">
                                                    <span class="text-primary mx-1"
                                                        onclick="edit('{{ route('category.getbyid', ['encodedId' => base64_encode($pageData->category[$i]->subCategorieswithTrashed[$j]->id)]) }}','{{ route('category.update', ['encodedId' => base64_encode($pageData->category[$i]->subCategorieswithTrashed[$j]->id),'returnType'=>'json']) }}', 'subcategory')"
                                                        style="cursor: pointer;" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" data-bs-title="Update">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                            <path
                                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                            <path d="M16 5l3 3" />
                                                        </svg>
                                                    </span>
                                                    <form
                                                        action="{{route('category.destroy', ['encodedId' => base64_encode($pageData->category[$i]->subCategorieswithTrashed[$j]->id)])}} "
                                                        method="post" class="delete-form" style="display:inline;"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-title="Soft Delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="delete-btn"
                                                            onclick="return confirmDelete()"
                                                            style="background:none; border:none; padding:0; margin:0; color:red; cursor:pointer;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M4 7l16 0" />
                                                                <path d="M10 11l0 6" />
                                                                <path d="M14 11l0 6" />
                                                                <path
                                                                    d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </span>

                                                @endif
                                            </div>
                                        </li>
                                        @endfor
                                </ol>
                            </li>
                            @endfor
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Add New Category Details -->
<div class="offcanvas offcanvas-end customizer" tabindex="-1" id="offcanvasNewCategory"
    aria-labelledby="offcanvasNewCategoryLabel" data-simplebar="init" aria-modal="true" role="dialog">
    <div class="simplebar-wrapper" style="margin: 0px;">
        <div class="simplebar-mask">
            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                    style="height: 100%; overflow: hidden scroll;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h4 class="offcanvas-title fw-semibold" id="offcanvasNewCategoryLabel">New Category</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body p-4">
                            <div class="row">
                                <form action="{{route('category.store',['returnType'=>'json'])}}" id="newCategoryForm"
                                    method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="text" name="category" id="category" value=""
                                                    class="form-control " placeholder="Enter Category Name" required />
                                                <label for="category">Category Name *</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <select class="form-select mr-sm-2" id="type" name="type" required>
                                                    <option value="" disabled selected>Select...</option>
                                                    <option value="Interior">Interior</option>
                                                    <option value="Exterior">Exterior</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <label for="type">Type *</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-info font-medium rounded-pill px-4">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-send me-2 fs-4"></i>
                                                Submit
                                            </div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="simplebar-placeholder" style="width: auto; height: 1171px;"></div>
    </div>

</div>

<!-- Add New Sub Category Details -->
<div class="offcanvas offcanvas-end customizer" tabindex="-1" id="offcanvasNewSubCategory"
    aria-labelledby="offcanvasNewSubCategoryLabel" data-simplebar="init" aria-modal="true" role="dialog">
    <div class="simplebar-wrapper" style="margin: 0px;">
        <div class="simplebar-mask">
            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                    style="height: 100%; overflow: hidden scroll;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h4 class="offcanvas-title fw-semibold" id="offcanvasNewSubCategoryLabel">New Sub Category
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body p-4">
                            <div class="row">
                                <form action="{{route('sub-category.store',['returnType'=>'json'])}}"
                                    id="NewSubCategoryForm" method="post">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <select class="form-control" name="category_id" id="category_id"
                                                    required>
                                                    <option value="" disabled selected>--Select Category</option>
                                                    @for ($i = 0 ; $i < $pageData->category->count();$i++)
                                                        <option value="{{$pageData->category[$i]->id}}">
                                                            {{$pageData->category[$i]->name}}</option>
                                                        @endfor
                                                </select>
                                                <label for="type">Parent Category *</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="text" name="subcategory" id="subcategory" value=""
                                                    class="form-control " placeholder="Enter Sub Category Name"
                                                    required />
                                                <label for="subcategory">Sub Category Name *</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <select class="form-select mr-sm-2" id="type" name="type" required>
                                                    <option value="" disabled selected>Select...</option>
                                                    <option value="Interior">Interior</option>
                                                    <option value="Exterior">Exterior</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <label for="type">Type *</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-info font-medium rounded-pill px-4">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-send me-2 fs-4"></i>
                                                Submit
                                            </div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="simplebar-placeholder" style="width: auto; height: 1171px;"></div>
    </div>

</div>

<!-- update Category Details -->
<div class="offcanvas offcanvas-end customizer" tabindex="-1" id="offcanvasUpdateCategory"
    aria-labelledby="offcanvasUpdateCategoryLabel" data-simplebar="init" aria-modal="true" role="dialog">
    <div class="simplebar-wrapper" style="margin: 0px;">
        <div class="simplebar-mask">
            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                    style="height: 100%; overflow: hidden scroll;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h4 class="offcanvas-title fw-semibold" id="offcanvasUpdateCategoryLabel">Update Category
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body p-4">
                            <div class="row">
                                <form action="" id="UpdateCategoryForm" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="text" name="category" id="category" value=""
                                                    class="form-control " placeholder="Enter Category Name" required />
                                                <label for="category">Category Name *</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <select class="form-select mr-sm-2" id="type" name="type" required>
                                                    <option value="" disabled>Select...</option>
                                                    <option value="Interior">Interior</option>
                                                    <option value="Exterior">Exterior</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <label for="type">Type *</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-info font-medium rounded-pill px-4">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-send me-2 fs-4"></i>
                                                Submit
                                            </div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="simplebar-placeholder" style="width: auto; height: 1171px;"></div>
    </div>
</div>

<!-- update Sub Category Details -->
<div class="offcanvas offcanvas-end customizer" tabindex="-1" id="offcanvasUpdateSubCategory"
    aria-labelledby="offcanvasUpdateSubCategoryLabel" data-simplebar="init" aria-modal="true" role="dialog">
    <div class="simplebar-wrapper" style="margin: 0px;">
        <div class="simplebar-mask">
            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                    style="height: 100%; overflow: hidden scroll;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h4 class="offcanvas-title fw-semibold" id="offcanvasUpdateSubCategoryLabel">Update Sub
                                Category
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body p-4">
                            <div class="row">
                                <form action="" id="UpdateSubCategoryForm" method="post">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <select class="form-control" name="parent_id" id="category_id" required>
                                                    @for ($i = 0 ; $i < $pageData->category->count();$i++)
                                                        <option value="{{$pageData->category[$i]->id}}">
                                                            {{$pageData->category[$i]->name}}</option>
                                                        @endfor
                                                </select>
                                                <label for="type">Parent Category *</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="text" name="category" id="subcategory" value=""
                                                    class="form-control " placeholder="Enter Sub Category Name"
                                                    required />
                                                <label for="subcategory">Sub Category Name *</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <select class="form-select mr-sm-2" id="type" name="type" required>
                                                    <option value="" disabled>Select...</option>
                                                    <option value="Interior">Interior</option>
                                                    <option value="Exterior">Exterior</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <label for="type">Type *</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-info font-medium rounded-pill px-4">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-send me-2 fs-4"></i>
                                                Submit
                                            </div>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="simplebar-placeholder" style="width: auto; height: 1171px;"></div>
    </div>

</div>


@endsection

@push('script')
<script src="{{asset('/libs/nestable/jquery.nestable.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>

<script>
$(function() {
    // Nestable
    var updateOutput = function(e) {
        var list = e.length ? e : $(e.target),
            output = list.data("output");
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable("serialize"))); //, null, 2));
        } else {
            output.val("JSON browser support required for this demo.");
        }
    };

    $("#nestable")
        .nestable({
            group: 1,
        })
        .on("change", updateOutput);

    $("#nestable-menu").on("click", function(e) {
        var target = $(e.target),
            action = target.data("action");
        if (action === "expand-all") {
            $(".dd").nestable("expandAll");
        }
        if (action === "collapse-all") {
            $(".dd").nestable("collapseAll");
        }
    });

    $(".dd").nestable("collapseAll");
    $("#nestable-menu").nestable();

    const handleFormSubmit = (formSelector, successMessage, errorMessage) => {
        $(formSelector).on('submit', function(e) {
            e.preventDefault();
            $(formSelector).find('.text-danger').remove();

            let formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Form submitted successfully:', response);
                    if (response.status === 'success') {
                        $(formSelector)[0].reset();
                        $('.offcanvas').offcanvas('hide');
                        new Notify({
                            status: response.status,
                            title: response.message,
                            autoclose: true,
                            autotimeout: 5000,
                            effect: "slide",
                            speed: 300,
                            position: "right bottom"
                        });

                        setTimeout(function() {
                            location.reload();
                        }, 1000); // 1000 milliseconds = 1 second
                    } else {
                        new Notify({
                            status: 'error',
                            title: errorMessage,
                            text: 'Please try again.',
                            autoclose: true,
                            autotimeout: 5000,
                            effect: "slide",
                            speed: 300,
                            position: "right bottom"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error submitting form:', status, error);
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(formSelector).find(`[name="${key}"]`).after(
                                `<span class="text-danger text-tiny fs-2">${value}</span>`
                            );
                        });
                    } else {
                        new Notify({
                            status: 'error',
                            title: 'Error submitting form.',
                            text: 'Please try again later.',
                            autoclose: true,
                            autotimeout: 5000,
                            effect: "slide",
                            speed: 300,
                            position: "right bottom"
                        });
                    }
                }
            });
        });
    };

    handleFormSubmit('#newCategoryForm', 'Category created successfully.', 'Failed to create category.');
    handleFormSubmit('#NewSubCategoryForm', 'Sub-category created successfully.',
        'Failed to create sub-category.');
    handleFormSubmit('#UpdateCategoryForm', 'Category update successfully.', 'Failed to update category.');
    handleFormSubmit('#UpdateSubCategoryForm', 'Sub-category update successfully.',
        'Failed to update sub-category.');


});

function edit(URL, UpdateUrl, type) {
    let category = new bootstrap.Offcanvas(document.getElementById('offcanvasUpdateCategory'));
    let subcategory = new bootstrap.Offcanvas(document.getElementById('offcanvasUpdateSubCategory'));

    $.ajax({
        url: URL,
        type: "GET",
        success: function(data) {
            if (data.status == 'success') {
                console.log(data.category);
                if (type === 'category') {
                    $('#UpdateCategoryForm').attr('action', UpdateUrl);
                    $('#UpdateCategoryForm').find('#category_id').val(data.category.id);
                    $('#UpdateCategoryForm').find('#category').val(data.category.name);
                    $('#UpdateCategoryForm').find('#type').val(data.category.type);
                    category.show();
                } else if (type === 'subcategory') {
                    $('#UpdateSubCategoryForm').attr('action', UpdateUrl);
                    $('#UpdateSubCategoryForm').find('#category_id').val(data.category.parent_id);
                    $('#UpdateSubCategoryForm').find('#subcategory').val(data.category.name);
                    $('#UpdateSubCategoryForm').find('#type').val(data.category.type);
                    subcategory.show();
                }
            }
        }
    });
};
</script>


@endpush