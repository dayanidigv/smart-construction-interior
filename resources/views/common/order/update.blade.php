@extends($role == 'admin' ? 'layout.admin-app' : 'layout.manager-app')
@section($role == 'admin' ? 'adminContent' : 'managerContent')

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

.select2-container {
    width: 100%;
}

hr{
    border: 1px solid #2A3570;
}
</style>
@endpush


<div class="card w-100 position-relative overflow-hidden">
    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-semibold mb-0 lh-sm">{{$title}}</h5>
        <h5 class="card-title fw-semibold mb-0 lh-sm">#ODR-{{str_pad($pageData->order->id,5,'0',STR_PAD_LEFT)}}</h5>
    </div>

    <div class="card-body p-4">
        <div class="row">

            <form action="{{route('order.update',["encodedId" => base64_encode($pageData->order->id)])}}" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-md-6 mb-4">
                        <label for="customer">Customer Details *</label>
                        <select class="customer-details form-control mb-4 d-block" id="customer" name="customer" required></select>
                        <small class="form-control-feedback mt-2 d-block">
                            If customer is not found,
                            <button type="button" class="btn btn-link p-0 m-0" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                                click here to add customer
                            </button>
                        </small>

                        @error('customer')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="location">Site location *</label>
                        <input type="text" name="location" id="location"
                            value="{{ old('location',$pageData->order->location) }}"
                            class="form-control @error('location') is-invalid @enderror" placeholder="" />
                        @error('location')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="type">Order Type *</label>
                        <select class="form-select mr-sm-2" id="type" name="type" required>
                            <option value="Interior" @if(old('type',$pageData->order->type) == 'Interior') selected
                                @endif>Interior</option>
                            <option value="Exterior" @if(old('type',$pageData->order->type) == 'Exterior') selected
                                @endif>Exterior</option>
                            <option value="Both" @if(old('type',$pageData->order->type) == 'Both') selected @endif>Both
                            </option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="control-label">Order Starting Date *</label>
                        <input type="date" class="form-control" name="order_starting_date" id="order_starting_date"
                            value="{{old('order_starting_date', \Carbon\Carbon::parse($pageData->order->start_date)->format('Y-m-d'))}}"
                            required />
                    </div>

                    <div class="col-md-4 col-12 mb-4">
                        <label class="control-label">Order Ending Date </label>
                        <input type="date" class="form-control" name="order_ending_date" id="order_ending_date"
                            value="{{old('order_ending_date', $pageData->order->end_date != null ? \Carbon\Carbon::parse($pageData->order->end_date)->format('Y-m-d') : null)}}" />
                    </div>


                    <div class="col-md-4 col-12 mb-4">
                        <label for="estimated_cost">Order Status *</label>
                        <select class="form-select mr-sm-2" id="status" name="status" required>
                            <option value="" selected disabled>Select--</option>
                            <option value="ongoing" @if(old('status',$pageData->order->status) == 'ongoing') selected
                                @endif >Ongoing</option>
                            <option value="cancelled" @if(old('status',$pageData->order->status) == 'cancelled')
                                selected @endif>Cancelled</option>
                            <option value="completed" @if(old('status',$pageData->order->status) == 'completed')
                                selected @endif>Completed</option>
                            <option value="follow-up" @if(old('status',$pageData->order->status) == 'follow-up')
                                selected @endif>Follow Up</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    @if ($role == 'admin')
                        <div class="col-md-4 col-12 mb-4">
                            <label for="manage_access">Order Manage Access *</label>
                            <select class="form-select mr-sm-2 @error('manage_access') is-invalid @enderror"
                                id="manage_access" name="manage_access" required>
                                <option value="only-for-me" @if(old('manage_access',$pageData->order->user_id) ==
                                    "only-for-me") selected @endif>Only For me</option>
                                @if ($pageData->managers->count() != 0)
                                @foreach ($pageData->managers as $manager)
                                <option value="{{$manager->id}}" @if(old('manage_access',$pageData->order->user_id) ==
                                    $manager->id) selected @endif>{{$manager->name}}</option>
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
                                    <h5 class="card-title fw-semibold mb-0 lh-sm ">Order Items</h5>
                                    <small class="form-text text-muted pr-2">
                                        Can't find the design you're looking for? <br>
                                        <a href="{{ route('admin.new.design') }}" target="_blank">Click here to add a
                                            new design</a>.
                                    </small>
                                </div>
                                <button onclick="order_item_container();"
                                    class="btn btn-success d-flex justify-content-center align-items-center rounded-circle p-0"
                                    type="button" style="width: 40px; height: 40px;">
                                    <i class="ti ti-circle-plus fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="order-item-container">
                        <hr>
                        @if (count($pageData->order->orderItems()->get()) != 0)
                        <div class="row">
                            @foreach ($pageData->order->orderItems()->get() as $index => $orderItem)
                            <input type="hidden" name="alt_order_item_id[]" value="{{ $orderItem->id }}"
                                autocomplete="off">
                            <input type="hidden" name="alt_category_id[]"
                                value="{{ $orderItem->catagories()->first()->parentCategory()->first()->id }}"
                                autocomplete="off">
                            <input type="hidden" name="alt_sub_category_id[]"
                                value="{{ $orderItem->catagories()->first()->id }}" autocomplete="off">

                            @php
                            $encodedId = str_replace('=', '', base64_encode($orderItem->id));
                            @endphp
                            <div class="col-12 col-md-11 ">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <label for="category{{ $encodedId }}">Category *</label>
                                        <select class="form-control" id="category{{ $encodedId }}" name="alt_category[]" required></select>
                                        <small class="form-control-feedback">
                                            <button type="button" class="btn btn-link p-0 m-0 " data-bs-toggle="offcanvas" data-bs-target="#offcanvasNewCategory" aria-controls="offcanvasNewCategory" >
                                                <span class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Click here to create a new category">Create New Category</span>
                                            </button>
                                        </small>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <label for="sub-category{{ $encodedId }}">Sub Category *</label>
                                        <select class="form-control" name="alt_sub_category[]" id="sub-category{{ $encodedId }}" required></select>
                                        <small class="form-control-feedback">
                                            <button type="button" class="btn btn-link p-0 m-0 " data-bs-toggle="offcanvas" data-bs-target="#offcanvasNewSubCategory" aria-controls="offcanvasNewSubCategory" >
                                                <span class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Click here to create a new sub category">Create New Sub Category</span>
                                            </button>
                                        </small>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <label for="design{{ $encodedId }}" class="d-block">Design</label>
                                        <select class="Order-product form-control" id="design{{ $encodedId }}"
                                            name="alt_design[]"></select>
                                    </div>

                                    <div class="col-12 col-md-4 col-lg-2 mb-3">
                                        <label for="length{{ $encodedId }}">Length</label>
                                        <input type="number"  id="length{{ $encodedId }}"
                                            name="alt_length[]" value="{{$orderItem->length}}"
                                            class="form-control" placeholder="Enter Length value" />
                                    </div>

                                    <div class="col-12 col-md-4 col-lg-2 mb-3">
                                        <label for="breath{{ $encodedId }}">Breath</label>
                                        <input type="number" id="breath{{ $encodedId }}"
                                            name="alt_breath[]" value="{{$orderItem->breath}}"
                                            class="form-control" placeholder="Enter Breath value" />
                                    </div>

                                    <div class="col-12 col-md-4 col-lg-2 mb-3">
                                        <label for="order_item_quantity{{ $encodedId }}">Quantity *</label>
                                        <input type="number" id="order_item_quantity{{ $encodedId }}"
                                            name="alt_order_item_quantity[]" value="{{$orderItem->quantity}}"
                                            class="form-control" placeholder="Enter item quantity value" required />
                                    </div>

                                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                                        <label for="alt_rate_per{{ $encodedId }}">Rate Per *</label>
                                        <input type="number" step="0.01" id="rate_per{{ $encodedId }}"
                                            name="alt_rate_per[]" value="{{$orderItem->rate_per}}" class="form-control"
                                            placeholder="Enter rate per value" required />
                                    </div>

                                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                                        <label for="sub_total{{ $encodedId }}">Total *</label>
                                        <input type="number" step="0.01" id="sub_total{{ $encodedId }}"
                                            name="alt_sub_total[]" class="form-control"
                                            value="{{$orderItem->sub_total}}" placeholder="Enter Total value"
                                            required />
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-md-1 d-flex align-items-center justify-content-center">
                                <div class="form-check form-check-inline mx-auto ">
                                    <input class="form-check-input danger check-outline outline-danger" type="checkbox"
                                        name="is_order_item_delete[]" value="{{$orderItem->id}}">
                                    <label class="form-check-label" for="danger2-outline-check">Delete</label>
                                </div>
                            </div>

                           
                            <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                refreshSearch('{{ $encodedId }}');
                                
                                
                                var initialDataForCategory = {
                                    id: <?=$orderItem->catagories()->first()->parentCategory()->first()->id ?>,
                                    name: '{{$orderItem->catagories()->first()->parentCategory()->first()->name}}'
                                };
                                
                                var initialDataForSubCategory = {
                                    id: <?=$orderItem->catagories()->first()->id ?>,
                                    name: '{{$orderItem->catagories()->first()->name}}'
                                };
                                @if ($orderItem->design_id)
                                    var initialDataForDesign = {
                                        id: <?= $orderItem->design_id ?>,
                                        name: '{{$orderItem->design->name}}'
                                    };
                                    initializeSelect2WithInitialValue("#design{{ $encodedId }}", initialDataForDesign);
                                @endif
                                initializeSelect2WithInitialValue("#category{{ $encodedId }}", initialDataForCategory);
                                initializeSelect2WithInitialValue("#sub-category{{ $encodedId }}", initialDataForSubCategory);
                            });
                            </script>
                            
                            

                            <hr class="mt-4 mt-md-0">
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Follow Up -->
                    <div class="row my-1">
                        <div class="col-md-12">
                            <div class=" py-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title fw-semibold mb-0 lh-sm">Follow Up</h5>
                                <button onclick="follow_container();"
                                    class="btn btn-success d-flex justify-content-center align-items-center rounded-circle p-0"
                                    type="button" style="width: 40px; height: 40px;">
                                    <i class="ti ti-circle-plus fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="follow-container">
                        <hr>
                        @if ($pageData->order->followup()->first() != null)
                        <div class="row">
                            @for ($i = 0 ; $i < count($pageData->order->followup()->get()) ; $i++)
                                <input type="hidden" name="alt_followup_id[]"
                                    value="{{$pageData->order->followup()->get()[$i]->id}}" autocomplete="off">
                                <?php 
                                        $fullDescription = $pageData->order->followup()->get()[$i]->description;
                                        $additionalNoteIndex = strpos($fullDescription, "Additional note:");
                                        if ($additionalNoteIndex !== false) {
                                            $additionalNote = substr($fullDescription, $additionalNoteIndex + strlen("Additional note:"));
                                            $additionalNote = trim($additionalNote);
                                        } else {
                                            $additionalNote = ""; 
                                        }
                                        $encodedId = str_replace('=', '', base64_encode($pageData->order->followup()->get()[$i]->id));

                                    ?>
                                    <div class="col-12 col-md-4 col-lg-4 mb-4 ">
                                        <label for="note">Note *</label>
                                        <input type="text" name="alt_note[]" class="form-control "
                                            value="{{$additionalNote}}" required />
                                    </div>

                                    <div class="col-12 col-md-3 col-lg-3 mb-4 ">
                                        <label class="control-label">Follow Date *</label>
                                        <input type="date" name="alt_follow_date[]" id="follow-date{{$encodedId}}"
                                            value="{{ \Carbon\Carbon::parse($pageData->order->followup()->get()[$i]->start)->format('Y-m-d') }}"
                                            class="form-control" required />
                                        @php
                                        $followUpDate =
                                        \Carbon\Carbon::parse($pageData->order->followup()->get()[$i]->start);
                                        @endphp
                                        @if ($followUpDate->isPast() && !$followUpDate->isToday())
                                        <small class="form-text text-danger">
                                            Follow-up date has ended.
                                        </small>
                                        @elseif ($followUpDate->isToday())
                                        <small class="form-text text-warning">
                                            Follow-up is scheduled for today.
                                        </small>
                                        @endif
                                    </div>


                                    <div class="col-12 col-md-3 col-lg-3 mb-4 ">
                                        <label class="control-label">Follow Priority *</label>
                                        <select class="form-control @error('priority') is-invalid @enderror" name="alt_follow_priority[]" id="follow-priority{{$encodedId}}" required>
                                            <option value="" selected disabled>-- Select Priority --</option>
                                            <option value="3" @if ($pageData->order->reminder()->get()[$i]->priority == 3) selected @endif>Green</option>
                                            <option value="2" @if ($pageData->order->reminder()->get()[$i]->priority == 2) selected @endif>Yellow</option>
                                            <option value="1" @if ($pageData->order->reminder()->get()[$i]->priority == 1) selected @endif>Red</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-2 mx-auto  d-flex justify-content-center">
                                        <div class="form-check form-check-inline mt-4">
                                            <input class="form-check-input danger check-outline outline-danger"
                                                type="checkbox" name="is_followup_delete[]"
                                                value="{{$pageData->order->followup()->get()[$i]->id}}">
                                            <label class="form-check-label" for="danger2-outline-check">Delete</label>
                                        </div>
                                    </div>

                                    <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        addDatePickerForElement(`#follow-date{{$encodedId}}`);
                                    });
                                     </script>
                                    <hr class="mt-4 mt-md-0">
                                @endfor
                        </div>
                        @endif
                    </div>

                    <!-- Invoice -->
                    <div class="row my-1">
                        <div class="col-md-12">
                            <div class=" py-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title fw-semibold mb-0 lh-sm">Invoice</h5>
                                <h5 class="card-title fw-semibold mb-0 lh-sm">
                                    {{$pageData->order->invoice()->first()->invoice_number}}</h5>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <input type="hidden" name="invoice_id" value="{{ $pageData->order->invoice()->first()->id }}"
                        autocomplete="off">


                    <div class="col-md-6 mb-4">
                        <label class="control-label">Creating Date *</label>
                        <input type="date" class="form-control" name="created_date" id="invoice_created_date"
                            value="{{old('created_date', \Carbon\Carbon::parse($pageData->order->invoice()->first()->created_date)->format('Y-m-d'))}}"
                            required />
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="control-label">Due Date *</label>
                        <input type="date" class="form-control" name="due_date" id="invoice_due_date" placeholder="Selete Due Date"
                            value="{{old('due_date', $pageData->order->invoice()->first()->due_date != null ? \Carbon\Carbon::parse($pageData->order->invoice()->first()->due_date)->format('Y-m-d') : null)}}"
                            required />
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

                    <div class="col-md-4 mb-4">
                        <label for="payment_status">Payment Status *</label>
                        <select class="form-select mr-sm-2 @error('payment_status') is-invalid @enderror"
                            id="payment_status" name="payment_status" required>
                            <option value="not confirmed" selected>Not Confirmed</option>
                            <option value="pending" @if(old('payment_status',$pageData->
                                order->invoice()->first()->payment_status) == 'pending') selected @endif>Pending
                            </option>
                            <option value="paid" @if(old('payment_status',$pageData->
                                order->invoice()->first()->payment_status) == 'paid') selected @endif>Paid</option>
                            <option value="partially_paid" @if(old('payment_status',$pageData->
                                order->invoice()->first()->payment_status) == 'partially_paid') selected
                                @endif>Partially Paid</option>
                            <option value="late" @if(old('payment_status',$pageData->
                                order->invoice()->first()->payment_status) == 'late') selected @endif>Late</option>
                            <option value="overdue" @if(old('payment_status',$pageData->
                                order->invoice()->first()->payment_status) == 'overdue') selected @endif>Overdue
                            </option>
                        </select>
                        @error('payment_status')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="discount_percentage">Discount Percentage</label>
                        <input type="number" step="0.01" name="discount_percentage" id="discount_percentage"
                            value="{{ old('discount_percentage',$pageData->order->invoice()->first()->discount_percentage) }}"
                            class="form-control @error('discount_percentage') is-invalid @enderror" placeholder="" />
                        @error('discount_percentage')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="advance_pay_amount">Advance Payment</label>
                        <input type="number" step="0.01" name="advance_pay_amount" id="advance_pay_amount"
                            value="{{ old('advance_pay_amount',$pageData->order->invoice()->first()->advance_pay_amount) }}"
                            class="form-control @error('advance_pay_amount') is-invalid @enderror" placeholder="" />
                        @error('advance_pay_amount')
                        <div class="invalid-feedback">
                            <p class="error">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-lg-7 mb-4">
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
                            <div id="payment-history">

                                @if ($pageData->order->paymentHistory()->exists())
                                <div class="row">
                                    @foreach ($pageData->order->paymentHistory as $payment)
                                    <input type="hidden" name="alt_payment_history_id[]" value="{{ $payment->id }}"
                                        autocomplete="off">

                                    <div class="col-md-3 col-12 mb-3">
                                        <label for="alt_payment_amount_{{ $payment->id }}">Paid Amount *</label>
                                        <input type="number" step="0.01" name="alt_payment_amount[]"
                                            id="paid_amount_{{ $payment->id }}" value="{{ $payment->amount }}"
                                            class="form-control @error('alt_payment_amount') is-invalid @enderror"
                                            placeholder="" required />
                                    </div>

                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="payment_date_{{ $payment->id }}">Payment Date *</label>
                                        <input type="date" name="alt_payment_date[]"
                                            id="payment_date_{{ $payment->id }}"
                                            value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}"
                                            class="form-control @error('payment_date') is-invalid @enderror"
                                            placeholder="" required />
                                    </div>

                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="payment_method_{{ $payment->id }}">Payment Method *</label>
                                        <select class="form-select @error('payment_method') is-invalid @enderror"
                                            id="payment_method_{{ $payment->id }}" name="alt_payment_method[]" required>
                                            <option value="" disabled selected>Select--</option>
                                            <option value="cash" @if(old('payment_method', $payment->payment_method) ==
                                                'cash') selected @endif>Cash</option>
                                            <option value="credit_card" @if(old('payment_method', $payment->
                                                payment_method) == 'credit_card') selected @endif>Credit Card</option>
                                            <option value="bank_transfer" @if(old('payment_method', $payment->
                                                payment_method) == 'bank_transfer') selected @endif>Bank Transfer
                                            </option>
                                            <option value="paypal" @if(old('payment_method', $payment->payment_method)
                                                == 'paypal') selected @endif>Paypal</option>
                                            <option value="UPI" @if(old('payment_method', $payment->payment_method) ==
                                                'UPI') selected @endif>UPI</option>
                                            <option value="other" @if(old('payment_method', $payment->payment_method) ==
                                                'other') selected @endif>Other</option>
                                        </select>
                                    </div>

                                    <div class="col-md-1 col-12 d-flex align-items-center mb-3">
                                        <div class="form-check form-check-inline mt-md-0">
                                            <input class="form-check-input danger check-outline outline-danger"
                                                type="checkbox" name="is_payment_history_delete[]"
                                                value="{{ $payment->id }}">
                                            <label class="form-check-label"
                                                for="delete_{{ $payment->id }}">Delete</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    @endforeach
                                </div>
                                @endif


                            </div>
                        </div>

                        <div class="col-lg-5 mb-4">
                            <label for="terms_and_conditions">Terms and Conditions</label>
                            <textarea name="terms_and_conditions" id="terms_and_conditions" rows="4"
                                class="form-control @error('terms_and_conditions') is-invalid @enderror"
                                placeholder="">{{ old('terms_and_conditions', $pageData->order->invoice()->first()->terms_and_conditions) }}</textarea>
                            @error('terms_and_conditions')
                            <div class="invalid-feedback">
                                <p class="error">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                    </div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-info rounded-pill px-4">
                                <div class="d-flex align-items-center">
                                    Update
                                </div>
                            </button>
                        </div>
                    </div>
                </div> -->

                <button
                    class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn"
                    type="submit">
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Update">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit-circle">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                            <path d="M16 5l3 3" />
                            <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
                        </svg>
                    </span>
                </button>

            </form>
        </div>
    </div>
</div>


<!-- Add New Customer Details -->
<div class="offcanvas offcanvas-end customizer" tabindex="-1" id="offcanvasExample"
    aria-labelledby="offcanvasExampleLabel" data-simplebar="init" aria-modal="true" role="dialog">
    <div class="simplebar-wrapper" style="margin: 0px;">
        <div class="simplebar-mask">
            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                    style="height: 100%; overflow: hidden scroll;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h4 class="offcanvas-title fw-semibold" id="offcanvasExampleLabel">New Customer</h4>
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


<script src="/js/select2.min.js"></script>

<script>
var roomID = 1;

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
                <input type="date" name="follow_date[]" id="follow-date${roomID}"class="form-control" placeholder="Select Follow up Date" required/>
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

            <div class="col-sm-1  my-sm-3 my-md-auto mx-auto d-flex justify-content-center align-items-center">
                <div class="form-group">
                    <button class="btn btn-danger d-flex justify-content-center align-items-center rounded-circle p-0 remove-field" type="button" data-room="${roomID}" onclick="remove_follow_container(${roomID})" style="width: 40px; height: 40px;">
                        <i class="ti ti-minus fs-5"></i>
                    </button>
                </div>
            </div>

            <hr class="">
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

function remove_follow_container(rid) {
    document.querySelector(`.remove-follow-class${rid}`).remove();
}


var room = 1;

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
                        <label for="design">Design</label>
                        <select class="Order-product form-control" id="design${room}" name="design[]" ></select>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2 mb-3">
                        <label for="length">length</label>
                        <input type="number" min='0' id="length${room}" name="length[]" class="form-control" value='0' placeholder="" />
                    </div>
                    <div class="col-12 col-md-3 col-lg-2 mb-3">
                        <label for="Breath">Breath</label>
                        <input type="number" min='0' id="breath${room}" name="breath[]" class="form-control" value='0' placeholder="" />
                    </div>
                    <div class="col-12 col-md-4 col-lg-2 mb-3">
                        <label for="order_item_quantity">Quantity *</label>
                        <input type="number" step="0.01" id="order_item_quantity${room}" name="order_item_quantity[]" class="form-control" placeholder="Enter item quantity value" required />
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                        <label for="rate_per">Rate Per *</label>
                        <input type="number" step="0.01" id="rate_per${room}" name="rate_per[]" class="form-control" placeholder="Enter rate per value" required />
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                        <label for="order_item">Total *</label>
                        <input type="number" step="0.01" id="sub_total${room}" name="sub_total[]" class="form-control" value="0" placeholder="Enter Total value" required />
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

var quantityData = <?= json_encode($pageData->QuantityUnits->map(function ($QuantityUnit) {
        return [
        'id' => $QuantityUnit->id,
        'name' => $QuantityUnit->name,
        'description' => $QuantityUnit->description,
        ];
    })) ?>;


function refreshSearch(rid = 2) {

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

    // Function to calculate quantity and subtotal
    function calculateQuantityAndSubtotal() {
        var length = parseInt($(`#length${rid}`).val()) || 0;
        var breath = parseInt($(`#breath${rid}`).val()) || 0;
        var ratePer = parseInt($(`#rate_per${rid}`).val()) || 0;

        var quantity = parseInt($(`#order_item_quantity${rid}`).val()) || 0;
        
        if (length * breath != 0){
            var quantity =  length * breath;
        }

        $(`#order_item_quantity${rid}`).val(quantity);

        var subtotal = quantity * ratePer;
        $(`#sub_total${rid}`).val(subtotal);
    }

    // Handle Length input
    $(`#length${rid}`).on('input', (e) => {
        let length = e.target.value;
        if (length <= 0) {
            e.target.value = '';
            return;
        }
        calculateQuantityAndSubtotal();
    });

    // Handle Breath input
    $(`#breath${rid}`).on('input', (e) => {
        let breath = e.target.value;
        if (breath <= 0) {
            e.target.value = '';
            return;
        }
        calculateQuantityAndSubtotal();
    });

    // Handle Rate Per input
    $(`#rate_per${rid}`).on('input', (e) => {
        var ratePer = parseFloat(e.target.value);
        if (ratePer <= 0) {
            e.target.value = '';
            return;
        }
        calculateQuantityAndSubtotal();
    });

    // Handle Quantity input directly (optional, for validation)
    $(`#order_item_quantity${rid}`).on('input', (e) => {
        var quantity = parseInt(e.target.value);
        if (quantity <= 0) {
            e.target.value = '';
            return;
        }
        var ratePer = parseInt($(`#rate_per${rid}`).val()) || 0;
        var subtotal = quantity * ratePer;
        $(`#sub_total${rid}`).val(subtotal.toFixed(2));
    });

    // Handle Sub Total input directly (optional, for validation)
    $(`#sub_total${rid}`).on('input', (e) => {
        var subtotal = parseInt(e.target.value);
        if (subtotal <= 0) {
            e.target.value = '';
            return;
        }
    });

    $('.select2-container').css('width', '100%');
}


function initializeSelect2WithInitialValue(select, initialData) {
    var option = new Option(initialData.name, initialData.id, true, true);
    $(select).append(option).trigger('change');
}




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

function formatDesignSelection(design) {
    return design.name || design.text;
}


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

$(document).ready(function() {

    if (<?= old('customer', $pageData->order->customer_id) ?>) {
        $.ajax({
            url: `/api/get/{{ base64_encode($userId) }}/customer-by-id/{{base64_encode(old('customer',$pageData->order->customer_id))  }}`,
            dataType: 'json',
            success: function(data) {
                if (data && data.id) {
                    var option = new Option(data.name, data.id, true, true);
                    $('.customer-details').append(option).trigger('change');
                    $('.customer-details').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', status, error);
            }
        });
    } else {
        console.log('not done');
    }


});

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
        "<p class='text-muted mb-0' style='font-size: 0.8rem;'><strong>Phone:</strong> " + customer.phone + "</p>" +
        "<p class='text-muted mb-0' style='font-size: 0.8rem;'><strong>Address:</strong> " + customer.address + "</p>" +
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
                    <option value="cash" @if(old('payment_method',$pageData->order->payment_method) == 'cash') selected @endif>Cash</option>
                    <option value="credit_card" @if(old('payment_method',$pageData->order->payment_method) == 'credit_card') selected @endif>Credit Card</option>
                    <option value="bank_transfer" @if(old('payment_method',$pageData->order->payment_method) == 'bank_transfer') selected @endif>Bank Transfer</option>
                    <option value="paypal" @if(old('payment_method',$pageData->order->payment_method) == 'paypal') selected @endif>Paypal</option>
                    <option value="UPI" @if(old('payment_method',$pageData->order->payment_method) == 'UPI') selected @endif>UPI</option>
                    <option value="other" @if(old('payment_method',$pageData->order->payment_method) == 'other') selected @endif>Other</option>
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


function remove_payment_container(rid) {
    document.querySelector(`.remove-payment-history-class${rid}`).remove();
}
</script>

<script src="/js/bootstrap3-typeahead.min.js"></script>
<script>
$(document).ready(() => {
    $('.select2-container').css('width', '100%');
});
</script>


<!-- Add new Customer -->
<script>

// For Handle offcanvas
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

</script>


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