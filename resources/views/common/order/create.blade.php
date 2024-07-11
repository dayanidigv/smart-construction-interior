@extends($role == 'admin' ? 'layout.admin-app' : 'layout.manager-app')
@section($role == 'admin' ? 'adminContent' : 'managerContent')
@use('Carbon\Carbon')
@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

<style>
.result {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    background-color: #f9f9f9;
    padding: 10px;
    margin-bottom: 1px;
}

.product-details {
    padding-left: 15px;
}

.product-name {
    font-weight: bold;
}
hr{
    border: 1px solid #2A3570;
}
</style>
@endpush

<style>
.select2 {
    width: 100%;
}
</style>

<div class="card w-100 position-relative overflow-hidden">
    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-semibold mb-0 lh-sm">{{$title}}</h5>
    </div>



    <div class="card-body p-4">
        <div class="row">

            <form action="{{ route('order.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <!-- Customer Details -->
                    <div class="col-md-6 mb-4">
                        <label for="customer" class="control-label">Customer Details *</label>
                        <select class="customer-details form-control" id="customer" name="customer" required></select>
                        <small class="form-control-feedback mt-2 d-block">
                            If customer is not found,
                            <button type="button" class="btn btn-link p-0 m-0" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasNewCustomer" aria-controls="offcanvasNewCustomer">
                                click here to add customer
                            </button>
                        </small>
                        @error('customer')
                        <div class="invalid-feedback d-block">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>


                    <!-- Site Location -->
                    <div class="col-md-6 mb-4">
                        <label for="location">Site Location *</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                            class="form-control @error('location') is-invalid @enderror" placeholder="" />
                        @error('location')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <!-- Order Type -->
                    <div class="col-md-4 col-lg-3 col-12 mb-4">
                        <label for="type">Order Type *</label>
                        <select class="form-select mr-sm-2" id="type" name="type" required>
                            <option value="" disabled selected>Choose...</option>
                            <option value="Interior" @if(old('type')=='Interior' ) Selected @endif>Interior</option>
                            <option value="Exterior" @if(old('type')=='Exterior' ) Selected @endif>Exterior</option>
                            <option value="Both" @if(old('type')=='Both' ) Selected @endif>Both</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <!-- Starting Date -->
                    <div class="col-md-4 col-lg-3 col-12 mb-4">
                        <label class="control-label">Order Starting Date *</label>
                        <input type="date" class="form-control date-picker" name="order_starting_date"
                            id="order_starting_date" value="{{old('order_starting_date')}}" placeholder="Select Date"
                            required />
                    </div>

                    <!-- Ending Date -->
                    <div class="col-md-4 col-lg-3 col-12 mb-4">
                        <label class="control-label">Order Ending Date </label>
                        <input type="date" class="form-control date-picker" placeholder="Select Date"
                            name="order_ending_date" id="order_ending_date" value="{{old('order_ending_date')}}" />
                    </div>

                    @if ($role == 'admin' )
                        <!-- Manage Access -->
                        <div class="col-md-4 col-lg-3 col-12 mb-4">
                            <label for="manage_access">Order Manage Access *</label>
                            <select class="form-select mr-sm-2 @error('manage_access') is-invalid @enderror"
                                id="manage_access" name="manage_access" required>
                                <option value="only-for-me" @if(old('manage_access')=="only-for-me" ) selected @endif>Only
                                    For me</option>
                                @if ($pageData->managers->count() != 0)
                                @foreach ($pageData->managers as $manager)
                                <option value="{{$manager->id}}" @if(old('manage_access')==$manager->id) selected
                                    @endif>{{$manager->name}}</option>
                                @endforeach
                                @endif
                            </select>
                            @error('manage_access')
                            <div class="invalid-feedback">
                                <p class="error">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>
                    @endif
                    

                    <!-- Order Items -->
                    <div class="row my-1">
                        <div class="col-md-12">
                            <div class=" py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title fw-semibold mb-0 lh-sm">Order items </h5>
                                    <small class="form-control-feedback"><a href="{{ route('admin.new.design') }}"
                                            target="_blank">Click here to add Design</a></small>

                                </div>
                                <button onclick="order_item_container();"
                                    class="btn btn-success font-weight-medium waves-effect waves-light rounded-pill pt-2 px-2"
                                    type="button">
                                    <i class="ti ti-circle-plus fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamically add Order items -->
                    <div id="order-item-container">
                        <hr>
                    </div>

                    <!-- Follow up -->
                    <div class="row my-1">
                        <div class="col-md-12">
                            <div class=" py-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title fw-semibold mb-0 lh-sm">Follow Up</h5>
                                <button onclick="follow_container();"
                                    class="btn btn-success font-weight-medium waves-effect waves-light rounded-pill py-auto px-2"
                                    type="button">
                                    <i class="ti ti-circle-plus fs-5 my-auto"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamically add Follow ups -->
                    <div id="follow-container">
                        <hr>
                    </div>

                </div>

                <!-- Invoice -->
                <div class="row my-1">
                    <div class="col-md-12">
                        <div class=" py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-semibold mb-0 lh-sm">Invoice</h5>
                        </div>
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <!-- Creating Date -->
                    <div class="col-md-6 mb-4">
                        <label class="control-label">Creating Date *</label>
                        <input type="date" class="form-control" name="created_date" id="invoice_created_date"
                            value="{{old('created_date',Carbon::now()->format('Y-m-d'))}}" required />
                    </div>

                    <!-- Due Date -->
                    <div class="col-md-6 mb-4">
                        <label class="control-label">Due Date </label>
                        <input type="date" class="form-control" name="due_date" id="invoice_due_date"
                            value="{{old('due_date')}}" />
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="row my-1">
                    <div class="col-md-12">
                        <div class=" py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-semibold mb-0 lh-sm">Payments Details</h5>
                        </div>
                        <hr>
                    </div>
                </div>

                <div class="row">

                    <!-- Payment Status -->
                    <div class="col-md-4 mb-4">
                        <label for="payment_status">Payment Status</label>
                        <select class="form-select mr-sm-2 @error('payment_status') is-invalid @enderror"
                            id="payment_status" name="payment_status" required>
                            <option value="not confirmed" selected>Not Confirmed</option>
                            <option value="pending" @if(old('payment_status')=='pending' ) selected @endif>Pending
                            </option>
                            <option value="paid" @if(old('payment_status')=='paid' ) selected @endif>Paid</option>
                            <option value="partially_paid" @if(old('payment_status')=='partially_paid' ) selected
                                @endif>Partially Paid</option>
                            <option value="late" @if(old('payment_status')=='late' ) selected @endif>Late</option>
                            <option value="overdue" @if(old('payment_status')=='overdue' ) selected @endif>Overdue
                            </option>
                        </select>
                        @error('payment_status')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <!--  Discount Percentage -->
                    <div class="col-md-4 mb-4">
                        <label for="discount_percentage">Discount Percentage</label>
                        <input type="number" step="0.01" name="discount_percentage" id="discount_percentage"
                            value="{{ old('discount_percentage',0) }}"
                            class="form-control @error('discount_percentage') is-invalid @enderror" placeholder="" />
                        @error('discount_percentage')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <!-- Advance Payment -->
                    <div class="col-md-4 mb-4">
                        <label for="advance_pay_amount">Advance Payment</label>
                        <input type="number" step="0.01" name="advance_pay_amount" id="advance_pay_amount"
                            value="{{ old('advance_pay_amount',0) }}"
                            class="form-control @error('advance_pay_amount') is-invalid @enderror" placeholder="" />
                        @error('advance_pay_amount')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="row my-1">
                            <div class="col-md-12">
                                <div class=" py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="card-title fw-semibold mb-0 lh-sm">Payment History</h5>
                                    <button onclick="payment_history_container();"
                                        class="btn btn-success d-flex justify-content-center align-items-center rounded-circle p-0"
                                        type="button" style="width: 40px; height: 40px;">
                                        <i class="ti ti-circle-plus fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <!-- Dynamically add Payments History -->
                        <div id="payment-history"></div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="col-lg-6 mb-4">
                        <label for="terms_and_conditions">Terms and Conditions</label>
                        <textarea name="terms_and_conditions" id="terms_and_conditions"
                            class="form-control @error('terms_and_conditions') is-invalid @enderror"
                            placeholder="">{{ old('terms_and_conditions', "1. In case of changes in design rate will be changed\r\n2.Extra works causes extra charges.") }}</textarea>
                        @error('terms_and_conditions')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- Errors -->
                @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Save Button -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-info rounded-pill px-4" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="Save">
                                <div class="d-flex align-items-center">
                                    Save
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Floating Save Button -->
                <!-- <button
                    class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn"
                    type="submit">
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add"
                        class="th-plus fs-5 fw-semibold">

                    </span>
                </button> -->
            </form>
        </div>
    </div>
</div>

<!-- Add New Customer Details -->
<div class="offcanvas offcanvas-end customizer" tabindex="-1" id="offcanvasNewCustomer"
    aria-labelledby="offcanvasNewCustomerLabel" data-simplebar="init" aria-modal="true" role="dialog">
    <div class="simplebar-wrapper" style="margin: 0px;">
        <div class="simplebar-mask">
            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                    style="height: 100%; overflow: hidden scroll;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h4 class="offcanvas-title fw-semibold" id="offcanvasNewCustomerLabel">New Customer</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body p-4">
                            <div class="row">
                                <form action="{{route('customer.store',['returnType'=>'json'])}}" id="newCustomerForm"
                                    method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="text" name="name" id="name" value="{{old('name')}}"
                                                    class="form-control " placeholder="Enter user name here" required />
                                                <label for="fname"> Name *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="email" name="email" id="email" value="{{old('email')}}"
                                                    class="form-control " placeholder="name@example.com" />
                                                <label for="email"> Email address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <input type="number" name="phone" id="phone" value="{{old('phone')}}"
                                                    class="form-control" placeholder="Enter customer Phone no."
                                                    required />
                                                <label for="phone"> Phone no. *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-floating">
                                                <textarea type="text" name="address" id="address"
                                                    value="{{old('address')}}" class="form-control "
                                                    placeholder="Enter customer Address" required></textarea>
                                                <label for="address"> Address *</label>
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
                                                    required></select>
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>

<script>
var roomID = 1;

// Add Follow Ups Elements Dynamically
function follow_container() {
    roomID++;
    var objTo = document.getElementById("follow-container");
    var rowDiv = document.createElement("div");
    rowDiv.setAttribute("class", `row remove-follow-class${roomID}`);
    rowDiv.innerHTML = `
            <div class="col-12 col-md-4 col-lg-5 mb-4 my-auto">
                <label for="note">Note *</label>
                <input type="text" name="note[]" id="note${roomID}" class="form-control " placeholder="" required/>
            </div>

            <div class="col-12 col-md-3 col-lg-3 mb-4 my-auto">
                <label class="control-label">Follow Date *</label>
                <input type="date" name="follow_date[]" id="follow-date${roomID}"class="form-control" placeholder='Select Date' required/>
            </div>

            <div class="col-12 col-md-3 col-lg-3 mb-4 my-auto">
                <label class="control-label">Priority *</label>
                <select class="form-control @error('priority') is-invalid @enderror" name="follow_priority[]" id="follow-priority${roomID}" required>
                <option value="">-- Select Priority --</option>
                <option value="3">Green</option>
                <option value="2">Yellow</option>
                <option value="1">Red</option>
                </select>
            </div>

            <div class="col-sm-1 my-auto">
                <div class="form-group">
                    <button class="btn btn-danger remove-field rounded-pill py-2 px-2" type="button" data-room="${roomID}" onclick="remove_follow_container(${roomID})">
                        <i class="ti ti-minus"></i>
                    </button>
                </div>
            </div>

            <hr>
        `;
    objTo.appendChild(rowDiv);
    addDatePickerForElement(`#follow-date${roomID}`);
}


function addDatePickerForElement(select) {
    $(select).flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true,
        minDate:'today',
    });
}

// Remove Follow ups Elements
function remove_follow_container(rid) {
    document.querySelector(`.remove-follow-class${rid}`).remove();
}


var room = 1;
// Add Order items Elements Dynamically
function order_item_container() {
    room++;
    var objTo = document.getElementById("order-item-container");
    var rowDiv = document.createElement("div");
    rowDiv.setAttribute("class", `row removeClass${room}`);
    rowDiv.innerHTML = `
            <div class="col-12 col-md-11 ">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <label for="category">Category *</label>
                        <!-- <input type="text" name="category[]" id="category${room}" class="form-control" placeholder="Enter category here" required /> -->
                        <select class="form-control" id="category${room}" name="category[]" required></select>
                        <small class="form-control-feedback">
                            <button type="button" class="btn btn-link p-0 m-0 " data-bs-toggle="offcanvas" data-bs-target="#offcanvasNewCategory" aria-controls="offcanvasNewCategory" >
                                <span class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Click here to create a new category">Create New Category</span>
                            </button>
                        </small>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label for="sub-category">Sub Category *</label>
                        <!-- <input type="text" name="sub_category[]" id="sub-category${room}" class="form-control typeahead" placeholder="Enter sub-category here" required /> -->
                        <select class="form-control" id="sub-category${room}" name="sub_category[]" required></select>
                        <small class="form-control-feedback">
                            <button type="button" class="btn btn-link p-0 m-0 " data-bs-toggle="offcanvas" data-bs-target="#offcanvasNewSubCategory" aria-controls="offcanvasNewSubCategory" >
                                <span class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Click here to create a new Sub category">Create New Sub Category</span>
                            </button>
                        </small>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label for="design">Design *</label>
                        <select class="Order-product form-control" id="design${room}" name="design[]" required></select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
                        <label for="dimension${room}">Dimension </label>
                        <input type="text"  id="dimension${room}" name="dimension[]" value="" class="form-control" placeholder="Enter dimension value" />
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                        <label for="order_item_quantity">Quantity *</label>
                        <input type="number" min='0' id="order_item_quantity${room}" name="order_item_quantity[]" class="form-control" placeholder="Enter item quantity value" required />
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                        <label for="rate_per">Rate Per *</label>
                        <input type="number" min='0' step="0.01" id="rate_per${room}" name="rate_per[]" class="form-control" placeholder="Enter rate per value" required />
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                        <label for="order_item">Total *</label>
                        <input type="number" min='0' step="0.01" id="sub_total${room}" name="sub_total[]" class="form-control" value="0" placeholder="Enter Total value" required />
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-1 d-flex align-items-center justify-content-center">
                <button class="btn btn-danger remove-field rounded-pill py-2 px-2" type="button" data-room="${room}" onclick="remove_order_item_container(${room})">
                    <i class="ti ti-minus"></i>
                </button>
            </div>

        <hr class="mt-4 mt-md-0">
        `;
    objTo.appendChild(rowDiv);
    refreshSearch(room);
}



function remove_order_item_container(rid) {
    document.querySelector(`.removeClass${rid}`).remove();
}


// List of Quantity Units
var quantityData = <?= json_encode($pageData->QuantityUnits->map(function ($QuantityUnit) {
        return [
        'id' => $QuantityUnit->id,
        'name' => $QuantityUnit->name,
        'description' => $QuantityUnit->description,
        ];
    })) ?>;


// control function For Dynamically created new order items category, sub-category, design, 
function refreshSearch(rid = 2) {

    // Category Search 
    // $(`#category${rid}`).typeahead({
    //     source: function(query, process) {
    //         return $.get('/api/search/{{ base64_encode($userId) }}/categories/' + query, function(data) {
    //             return process(data);
    //         });
    //     }
    // });


    // Category Search with Select2
    $(`#category${rid}`).select2({
        placeholder: "Select Category",
        allowClear: true,
        ajax: {
            url: function(params) {
                let searchTerm = params.term || '-';
                return '/api/search/{{ base64_encode($userId) }}/categories/' + encodeURIComponent(
                    searchTerm);
            },
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });


    //Sub Category Search with Select2
    $(`#sub-category${rid}`).select2({
        placeholder: "Select Sub Category",
        allowClear: true,
        ajax: {
            url: function(params) {
                let searchTerm = params.term || '-';
                return '/api/search/{{ base64_encode($userId) }}/subcategories/' + searchTerm +
                    '?categoryID=' +
                    $(`#category${rid}`).val();
            },
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Sub Category Search
    // $(`#sub-category${rid}`).typeahead({
    //     source: function(query, process) {
    //         return $.get('/api/search/{{ base64_encode($userId) }}/subcategories/' + query + '?category=' +
    //             $(`#category${rid}`).val(),
    //             function(data) {
    //                 return process(data);
    //             });
    //     }
    // });

    // Search Designs
    $(`#design${rid}`).select2({
        ajax: {
            url: function(params) {
                return '/api/search/{{ base64_encode($userId) }}/designs/all?categoryID=' + $(
                        `#category${rid}`).val() + '&subcategoryID=' + $(`#sub-category${rid}`).val() +
                    '&searchKey=' + params.term;
            },
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true,
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', status, error);
            }
        },
        placeholder: 'Search for a Design',
        templateResult: formatDesign,
        templateSelection: formatDesignSelection
    });

    // Handle Rate Per function
    $(`#rate_per${rid}`).on('input', (e) => {
        var rate = parseFloat(e.target.value);
        if (rate <= 0) {
            e.target.value = '';
            return;
        }
        var quantity = $(`#order_item_quantity${rid}`).val();
        var rate = parseFloat(e.target.value);
        if (!isNaN(quantity)) {
            var subtotal = quantity * rate;
            $(`#sub_total${rid}`).val(subtotal.toFixed(2));
        } else {
            alert('Invalid quantity input');
        }
    });

    // Handle Quantity 
    $(`#order_item_quantity${rid}`).on('input', (e) => {
        var quantity = parseFloat(e.target.value);
        if (quantity <= 0) {
            e.target.value = '';
            return;
        }
        var rate_per = $(`#rate_per${rid}`).val();
        var quantity = parseFloat(e.target.value);
        if (!isNaN(rate_per)) {
            var subtotal = quantity * rate_per;
            $(`#sub_total${rid}`).val(subtotal.toFixed(2));
        } else {
            alert('Invalid input');
        }
    });

    // Handle Sub Total 
    $(`#sub_total${rid}`).on('input', (e) => {
        var quantity = parseFloat(e.target.value);
        if (quantity <= 0) {
            e.target.value = '';
            return;
        }
    });


    $('.select2-container').css('width', '100%');
}

//showing Format Design for Design
function formatDesign(design) {
    if (!design || design.length === 0) {
        return 'No products found.';
    }

    if (design.loading) {
        return design.text;
    }


    var $container = $(
        "<div class='container mb-1'>" +
        "<div class='row result'>" +
        "<div class='col-lg-5 col-md-5 col-5 d-flex justify-content-center align-items-center'>" +
        "<img src='" + design.image_url +
        "' alt='design' class='img-fluid img-thumbnail' style='min-width: 60px; width:auto; height: auto;' />" +
        "</div>" +
        "<div class='col-lg-7 col-md-7 col-7 product-details'>" +
        "<p class='product-name mb-1 text-dark'>" + design.name + "</p>" +
        "<p class='mb-1 text-dark'>" + (design.description != null ? design.description : '') + "</p>" +
        "<p class='mb-1 text-dark text-tiny'> Unit: " + (quantityData.find(e => e.id === design.unit_id)?.name ||
            '') + " (" + (quantityData.find(e => e.id === design.unit_id)?.description || '') + ")</p>" +
        "</div>" +
        "</div>" +
        "</div>"
    );



    return $container;
}

// Selection handle for Design 
function formatDesignSelection(design) {
    return design.name || design.text;
}

// Customer Search
$(".customer-details").select2({
    ajax: {
        url: function(params) {
            return '/api/search/{{base64_encode($userId)}}/customers/' + params.term;
        },
        dataType: 'json',
        delay: 250,
        processResults: function(data, params) {
            var results = [];

            for (var i = 0; i < data.length; i++) {
                var customer = data[i];
                results.push(customer);
                if (customer.id == '<?= old('customer',0) ?>') {
                    console.log(`${customer.id} selected`)
                    $(`.customer-details`).select2('data', customer);
                }
            }
            return {
                results: results
            };
        },
        cache: true,
        error: function(xhr, status, error) {
            console.error('AJAX request failed:', status, error);
        }
    },
    placeholder: 'Search for a Customer',
    minimumInputLength: 1,
    templateResult: formatCustomer,
    templateSelection: formatCustomerSelection,

});



// Get Old Customer Details 
function fetchOldCustomerData(userId, customerId) {
    $.ajax({
        url: `/api/get/${userId}/customer-by-id/${customerId}`,
        dataType: 'json',
        success: handleCustomerDataSuccess,
        error: handleAjaxError
    });
}

function handleCustomerDataSuccess(data) {
    if (data && data.id) {
        const option = new Option(data.name, data.id, true, true);
        const $customerDetails = $('.customer-details');
        $customerDetails.append(option).trigger('change');
        $customerDetails.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    }
}

function handleAjaxError(xhr, status, error) {
    console.error('AJAX request failed:', status, error);
}

// showing Customer details selection format 
function formatCustomer(customer) {
    if (!customer || customer.length === 0) {
        return 'No customer found.';
    }

    if (customer.loading) {
        return customer.text;
    }

    var $container = $(
        "<div class='container'>" +
        "<div class='row result'>" +
        "<div class='col-12 customer-details'>" +
        "<h6 class='customer-name mb-1' >" + customer.name + "</h6>" +
        "<p class='text-muted mb-1' style='font-size: 0.8rem;'><strong>Email:</strong> " + customer.email + "</p>" +
        "<p class='text-muted mb-0' style='font-size: 0.8rem;'><strong>Phone:</strong> " + customer.phone + "</p>" +
        "</div>" +
        "</div>" +
        "</div>"
    );
    return $container;
}

function formatCustomerSelection(customer) {
    return customer.name || customer.text;
}


var phID = 1;
// Dynamically add Payment history Elements
function payment_history_container() {
    phID++;
    var objTo = document.getElementById("payment-history");
    var rowDiv = document.createElement("div");
    rowDiv.setAttribute("class", `row remove-payment-history-class${phID}`);
    rowDiv.innerHTML = `
            <div class="col-md-3 col-12">
                <label for="payment_history">Paid Amount *</label>
                <input type="number" step="0.01" name="paid_amount[]" id="paid_amount" value="" 
                    class="form-control @error('paid_amount') is-invalid @enderror" placeholder="" required/>
            </div>

            <div class="col-md-4 col-12">
                <label for="payment_date">Payment Date *</label>
                <input type="date" name="payment_date[]" id="payment_date" value="" 
                    class="form-control @error('payment_date') is-invalid @enderror" placeholder="" required/>
            </div>

            <div class="col-md-4 col-12 mb-4">
                <label for="payment_method">Payment Method *</label>
                <select class="form-select mr-sm-2 @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method[]" required>
                    <option value="" disabled selected>Select-- </option>
                    <option value="cash" @if(old('payment_method') == 'cash') selected @endif>Cash</option>
                    <option value="credit_card" @if(old('payment_method') == 'credit_card') selected @endif>Credit Card</option>
                    <option value="bank_transfer" @if(old('payment_method') == 'bank_transfer') selected @endif>Bank Transfer</option>
                    <option value="paypal" @if(old('payment_method') == 'paypal') selected @endif>Paypal</option>
                    <option value="UPI" @if(old('payment_method') == 'UPI') selected @endif>UPI</option>
                    <option value="other" @if(old('payment_method') == 'other') selected @endif>Other</option>
                </select>
            </div>

            <div class="col-md-1 my-auto d-flex justify-content-center align-items-center">
                <div class="form-group">
                    <button class="btn btn-danger d-flex justify-content-center align-items-center rounded-circle p-0 remove-field" type="button" data-room="${phID}"  onclick="remove_payment_container(${phID})" style="width: 40px; height: 40px;">
                        <i class="ti ti-minus fs-5"></i>
                    </button>
                </div>
            </div>

            <hr>
        `;
    objTo.appendChild(rowDiv);
}

// Remove Payment History elements
function remove_payment_container(rid) {
    document.querySelector(`.remove-payment-history-class${rid}`).remove();
}
</script>

<!-- get old Customer id -->
@php
$oldCustomer = old('customer') ?? '0';
echo "<script>
$(document).ready(function() {
    const userId = '" . base64_encode($userId) . "';
    const oldCustomerId = '" . base64_encode($oldCustomer)  . "';
    if (oldCustomerId !== 'MA==') {
        fetchOldCustomerData(userId, oldCustomerId);
    }
});
</script>";
@endphp


<!-- Add new Customer -->
<script>
$(document).ready(function() {
    $('#newCustomerForm').on('submit', function(e) {
        e.preventDefault();
        $('#newCustomerForm').find('.text-danger').remove();

        let formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Form submitted successfully:', response);
                if (response.status === 'success') {
                    $('#newCustomerForm')[0].reset();
                    $('.offcanvas').offcanvas('hide');
                    new Notify({
                        status: response.status,
                        title: response.message,
                        text: response.customer ?
                            `${response.customer.name} has been added as a new customer.` :
                            '',
                        autoclose: true,
                        autotimeout: 5000,
                        effect: "slide",
                        speed: 300,
                        position: "right bottom"
                    });
                } else {
                    new Notify({
                        status: 'error',
                        title: 'Failed to create customer.',
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
                        $('#newCustomerForm').find(`[name="${key}"]`).after(
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


    $('#newCategoryForm').on('submit', function(e) {
        e.preventDefault();
        $('#newCategoryForm').find('.text-danger').remove();

        let formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Form submitted successfully:', response);
                if (response.status === 'success') {
                    $('#newCategoryForm')[0].reset();
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
                } else {
                    new Notify({
                        status: 'error',
                        title: 'Failed to create category.',
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
                        $('#newCategoryForm').find(`[name="${key}"]`).after(
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

    $('#NewSubCategoryForm').on('submit', function(e) {
        e.preventDefault();
        $('#NewSubCategoryForm').find('.text-danger').remove();

        let formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Form submitted successfully:', response);
                if (response.status === 'success') {
                    $('#category_id').val(null).trigger('change');
                    $('#NewSubCategoryForm')[0].reset();
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
                } else {
                    new Notify({
                        status: 'error',
                        title: 'Failed to create category.',
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
                        $('#NewSubCategoryForm').find(`[name="${key}"]`).after(
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

    $('#offcanvasNewSubCategory').on('hide.bs.offcanvas', function(e) {
        $('#category_id').val(null).trigger('change');
    });

    // for create ne sub category, Category Search with Select2
    $(`#category_id`).select2({
        placeholder: "Select Category",
        allowClear: true,
        ajax: {
            url: function(params) {
                let searchTerm = params.term || '-';
                return '/api/search/{{ base64_encode($userId) }}/categories/' + encodeURIComponent(
                    searchTerm);
            },
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });


});

$(document).ready(() => {
    $('.select2-container').css('width', '100%');
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // For Order Date picker
    let orderStartDatePicker = $("#order_starting_date").flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true,
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                let minDate = selectedDates[0];
                OrderEndDatePicker.set('minDate',
                    minDate);
            }
        }
    });
    let OrderEndDatePicker = $("#order_ending_date").flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true
    });

    // For Invoice Date picker
    let invoiceStartDatePicker = $("#invoice_created_date").flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true,
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                let minDate = selectedDates[0];
                invoiceEndDatePicker.set('minDate',
                    minDate);
            }
        }
    });
    let invoiceEndDatePicker = $("#invoice_due_date").flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true
    });
    // Set minDate for the end date picker based on the initial value of the start date picker
    let initialStartDate = $("#invoice_created_date").val();
    if (initialStartDate) {
        invoiceEndDatePicker.set('minDate', initialStartDate);
    }
});
</script>

@endpush