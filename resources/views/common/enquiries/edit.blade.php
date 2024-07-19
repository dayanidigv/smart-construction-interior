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

hr {
    border: 1px solid #2A3570;
}
</style>
@endpush

<style>
.select2 {
    width: 100%;
}
</style>

<div class="row align-items-center">
    <div class="col-sm-12 col-md-12 col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Edit Enquiry</h5>
                <form action="{{ route('enquiries.update', ['role' => $role, 'encodedId' => base64_encode($pageData->enquiry->id)]) }}" method="post">
                    @csrf
                    <div class="row">

                        <!-- Customer Details -->
                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
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

                        <!-- Customer Category -->
                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                            <label for="customer_category" class="control-label">Customer Category *</label>
                            <input type="text" class="form-control @error('customer_category') is-invalid @enderror" id="customer_category" name="customer_category" value="{{old('customer_category',$pageData->enquiry->customerCategory->name)}}" required />
                            @error('customer_category')
                            <div class="invalid-feedback d-block">
                                <p class="error">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Description about Enquiry -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="control-label">Description about Enquiry</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{old('description',$pageData->enquiry->description)}}"/>
                            @error('description')
                            <div class="invalid-feedback d-block">
                                <p class="error">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Site Status -->
                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                            <label for="site_status" class="control-label">Site Status *</label>
                            <input type="text" class="form-control @error('site_status') is-invalid @enderror" id="site_status" name="site_status" value="{{old('site_status',$pageData->enquiry->site_status)}}" required />
                            @error('site_status')
                            <div class="invalid-feedback d-block">
                                <p class="error">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Type of Work -->
                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                            <label for="type_of_work" class="control-label">Type of Work *</label>
                        
                            <select class="form-select mr-sm-2" class="form-control @error('type_of_work') is-invalid @enderror" id="type_of_work" name="type_of_work" required>
                                <option value="" selected disabled>Select...</option>
                                <option value="Interior"  @if (old('type_of_work',$pageData->enquiry->type_of_work) == "Interior") selected @endif>Interior</option>
                                <option value="Exterior"  @if (old('type_of_work',$pageData->enquiry->type_of_work) == "Exterior") selected @endif>Exterior</option>
                                <option value="Both"  @if (old('type_of_work',$pageData->enquiry->type_of_work) == "Both") selected @endif>Both</option>
                            </select>
                            @error('type_of_work')
                            <div class="invalid-feedback d-block">
                                <p class="error">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                            <label for="status" class="control-label">Status *</label>
                            <select type="text" class="form-control @error('status') is-invalid @enderror" id="status" name="status" required >
                                <option value="" selected disabled>-- Select</option>
                                <option value="follow-up" @if (old('status',$pageData->enquiry->status) == "follow-up") selected @endif>Followup</option>
                                <option value="cancelled" @if (old('status',$pageData->enquiry->status) == "cancelled") selected @endif> Cancelled</option>
                                <option value="confirmed" @if (old('status',$pageData->enquiry->status) == "confirmed") selected @endif>Confirmed</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback d-block">
                                <p class="error">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Follow Up -->
                        <div class="col-md-12 mb-3">
                            <h4 class="d-flex align-items-center">Follow Up 
                                <button type="button" class="btn btn-success mx-3 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Add"
                                        onclick="addFollowUpContainerField()">
                                    <span class="th-plus fs-5 fw-semibold"></span>
                                </button>
                            </h4>
                        </div>

                        <div id="follow-up-container">
                            
                        @if (count($pageData->followup) != 0)
                        
                        <div class="row">
                            @for ($i = 0 ; $i < count($pageData->followup) ; $i++)
                            <hr class="mt-4 mt-md-0">
                                <input type="hidden" name="alt_followup_id[]"
                                    value="{{$pageData->followup[$i]->id}}" autocomplete="off">
                                <?php 
                                        $fullDescription = $pageData->followup[$i]->description;
                                        $additionalNoteIndex = strpos($fullDescription, "Additional note:");
                                        if ($additionalNoteIndex !== false) {
                                            $additionalNote = substr($fullDescription, $additionalNoteIndex + strlen("Additional note:"));
                                            $additionalNote = trim($additionalNote);
                                        } else {
                                            $additionalNote = ""; 
                                        }
                                        $encodedId = str_replace('=', '', base64_encode($pageData->followup[$i]->id));

                                    ?>
                                    <div class="col-12 col-md-4 col-lg-4 mb-4 ">
                                        <label for="note">Note *</label>
                                        <input type="text" name="alt_note[]" class="form-control "
                                            value="{{$additionalNote}}" required />
                                    </div>

                                    <div class="col-12 col-md-3 col-lg-3 mb-4 ">
                                        <label class="control-label">Follow Date *</label>
                                        <input type="date" name="alt_follow_date[]" id="follow-date{{$encodedId}}"
                                            value="{{ \Carbon\Carbon::parse($pageData->followup[$i]->reminder_time)->format('Y-m-d') }}"
                                            class="form-control" required />
                                        @php
                                        $followUpDate =
                                        \Carbon\Carbon::parse($pageData->followup[$i]->reminder_time);
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
                                            <option value="3" @if ($pageData->followup[$i]->priority == "3") selected @endif>Green</option>
                                            <option value="2" @if ($pageData->followup[$i]->priority == "2") selected @endif>Yellow</option>
                                            <option value="1" @if ($pageData->followup[$i]->priority == "1") selected @endif>Red</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-2 mx-auto  d-flex justify-content-center">
                                        <div class="form-check form-check-inline mt-4">
                                            <input class="form-check-input danger check-outline outline-danger"
                                                type="checkbox" name="is_followup_delete[]"
                                                value="{{$pageData->followup[$i]->id}}">
                                            <label class="form-check-label" for="danger2-outline-check">Delete</label>
                                        </div>
                                    </div>

                                    <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        addDatePickerForElement(`#follow-date{{$encodedId}}`);
                                    });
                                     </script>
                                    
                                @endfor
                        </div>
                        @endif
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-info font-medium rounded-pill px-4">
                            <div class="d-flex align-items-center">
                               Update
                            </div>
                        </button>
                    </div>

                </form>
            </div>
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

@endsection

@push('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>

<script src="/js/bootstrap3-typeahead.min.js"></script>

<!-- Add new Customer -->
<script>
$(document).ready(function() {

    


    $(`#customer_category`).typeahead({
        source: function (query, process) {
            return $.get('/api/search/{{ base64_encode($userId) }}/customerCategories/' + query, function (data) {
                return process(data);
            });
        }
    });

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

    $('.select2-container').css('width', '100%');


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
            "<p class='text-muted mb-0' style='font-size: 0.8rem;'><strong>Phone:</strong> " + customer
            .phone + "</p>" +
            "<p class='text-muted mb-0' style='font-size: 0.8rem;'><strong>Address:</strong> " + customer
            .address + "</p>" +
            "</div>" +
            "</div>" +
            "</div>"
        );
        return $container;
    }

    function formatCustomerSelection(customer) {
        return customer.name || customer.text;
    }


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
});


var rowID = 1;
function addFollowUpContainerField(){
    rowID++;
    var objTo = document.getElementById("follow-up-container");
    var rowDiv = document.createElement("div");
    rowDiv.setAttribute("class", `row remove-follow-class${rowID}`);
    rowDiv.innerHTML = `
    <hr>

            <div class="col-12 col-md-4 col-lg-5 mb-4 my-auto">
                <label for="note">Note *</label>
                <input type="text" name="note[]" id="note${rowID}" class="form-control " placeholder="" required/>
            </div>

            <div class="col-12 col-md-3 col-lg-3 mb-4 my-auto">
                <label class="control-label">Follow Date *</label>
                <input type="date" name="follow_date[]" id="follow-date${rowID}"class="form-control" placeholder='Select Date' required/>
            </div>

            <div class="col-12 col-md-3 col-lg-3 mb-4 my-auto">
                <label class="control-label">Priority *</label>
                <select class="form-control @error('priority') is-invalid @enderror" name="follow_priority[]" id="follow-priority${rowID}" required>
                <option value="">-- Select Priority --</option>
                <option value="3">Green</option>
                <option value="2">Yellow</option>
                <option value="1">Red</option>
                </select>
            </div>

            <div class="col-sm-1 m-auto">
                <div class="form-group">
                    <button type="button" class="btn btn-danger mx-3 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Remove"
                            onclick="remove_follow_container(${rowID})">
                            <i class="ti ti-minus fs-5 fw-semibold"></i>
                    </button>
                </div>
            </div>

        `;
    objTo.appendChild(rowDiv);
    addDatePickerForElement(`#follow-date${rowID}`);
}


function addDatePickerForElement(select) {
    $(select).flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true,
        minDate: 'today',
    });
}


$(document).ready(function() {

if (<?= old('customer', $pageData->enquiry->customer_id) ?>) {
    $.ajax({
        url: `/api/get/{{ base64_encode($userId) }}/customer-by-id/{{base64_encode(old('customer',$pageData->enquiry->customer_id))  }}`,
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

// Remove Follow ups Elements
function remove_follow_container(rid) {
    document.querySelector(`.remove-follow-class${rid}`).remove();
}


</script>

@endpush