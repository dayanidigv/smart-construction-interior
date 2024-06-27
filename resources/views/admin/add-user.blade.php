@extends('layout.admin-app')
@section('adminContent')


@push('style')

@endpush


<div class="row align-items-center">
  <div class="col-sm-8 col-md-6 col-lg-5 mx-auto">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">New User</h5>
        <form action="{{route('admin.add-user.store')}}" method="post">
            @csrf
          <div class="row">
            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <input type="text" name="name" id="name" value="{{old('name')}}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter user name here" required/>
                <label for="fname"> Name</label>
                @error('name')
                  <div class="invalid-feedback">
                    <p class="error">{{ $message }}</p>
                  </div>
                @enderror
              </div>
            </div>

            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" required/>
                <label for="email"> Email address</label>
                @error('email')
                  <div class="invalid-feedback">
                    <p class="error">{{ $message }}</p>
                  </div>
                @enderror
              </div>
            </div>

            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <select class="form-control @error('role') is-invalid @enderror" name="role" id="role" required>
                  <option value="">-- Select User Role --</option>
                  <option value="admin">Admin</option>
                  <option value="manager">Manager</option>
                </select>
                <label for="role"> Role</label>
                @error('role')
                  <div class="invalid-feedback">
                    <p class="error">{{ $message }}</p>
                  </div>
                @enderror
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



@endsection

@push('script')

@endpush