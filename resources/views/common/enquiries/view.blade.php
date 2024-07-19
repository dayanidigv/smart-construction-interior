@extends($role == 'admin' ? 'layout.admin-app' : 'layout.manager-app')
@section($role == 'admin' ? 'adminContent' : 'managerContent')



@push('style')

@endpush



<div class="row align-items-center">
  <div class="col-sm-12 col-md-10  col-lg-8 mx-auto">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">
            <a href="{{url()->previous()}}"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg></a>
        Enquirie Details</h5>
        <form action="" method="post">
            @csrf
          <div class="row">
            <div class="col-md-12 col-lg-6 mb-3">
              <div class="form-floating">
                <input type="text" value="{{$pageData->enquiry->customer->name}}" class="form-control" disabled/>
                <label>Customer Name</label>
              </div>
            </div>

            <div class="col-md-12 col-lg-6 mb-3">
                <div class="form-floating">
                <input type="text" value="{{$pageData->enquiry->customerCategory->name}}" class="form-control" disabled/>
                    <label>Customer Category</label>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <div class="form-floating">
                    <textarea class="form-control h-100" rows="2"  readonly disabled>{{$pageData->enquiry->description}}</textarea>
                    <label for="address">Description</label>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 mb-3">
                <div class="form-floating">
                <input type="text" value="{{$pageData->enquiry->site_status}}" class="form-control" disabled/>
                    <label>Site Status</label>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 mb-3">
                <div class="form-floating">
                <input type="text" value="{{$pageData->enquiry->type_of_work}}" class="form-control" disabled/>
                    <label>Type of Work</label>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 mb-3">
                <div class="form-floating">
                <input type="text" value="{{$pageData->enquiry->status}}" class="form-control" disabled/>
                    <label>Status</label>
                </div>
            </div>

            @if (count($pageData->followup) != 0)
                <div class="col-md-12 my-3">
                    <h4>Follow Up</h4>
                </div>
            @endif
            @foreach ( $pageData->followup as $followup)
                <div class="row">
                    <hr>
                    <div class="col-12 col-md-6 col-lg-6 mb-4 my-auto">
                        <label for="note">Note</label>
                        @php
                            $keyword = "Additional note:";
                            $start_pos = strpos($followup->description, $keyword);
                            if ($start_pos !== false) {
                                $start_pos += strlen($keyword);
                                $enqury_note = trim(substr($followup->description, $start_pos));
                            } else {
                                $enqury_note = "";
                            }
                        @endphp
                        <input type="text" class="form-control h-100 " placeholder=""  value="{{ $enqury_note}}" disabled/>
                    </div>

                    <div class="col-12 col-md-3 col-lg-3 mb-4 my-auto">
                        <label class="control-label">Follow Date</label>
                        <input type="text" class="form-control " placeholder="" value="{{\Carbon\Carbon::parse($followup->reminder_time)->format('jS F Y') }}" disabled/>
                    </div>

                    <div class="col-12 col-md-3 col-lg-3 mb-4 my-auto">
                        <label class="control-label">Priority</label>
                        <select class="form-control"  disabled>
                        <option value="">-- Select Priority --</option>
                        <option value="3" @if ($followup->priority == "3") selected @endif>Green</option>
                        <option value="2" @if ($followup->priority == "2") selected @endif>Yellow</option>
                        <option value="1" @if ($followup->priority == "1") selected @endif>Red</option>
                        </select>
                    </div>
                </div>
            @endforeach
            

            
            <div class="d-flex justify-content-end mt-3">
            <a href="{{route('enquiries.edit',['encodedId' => base64_encode($pageData->enquiry->id),'role' => $role])}}" type="submit" class="btn btn-info font-medium rounded-pill px-4">
              <div class="d-flex align-items-center">
                <i class="ti ti-pencil me-2 fs-4"></i>
                Edit
              </div>
            </a>
          </div>

          </div>

        </form>
      </div>
    </div>
  </div>
</div>


@endsection

@push('script')

@endpush