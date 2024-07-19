@extends('layout.admin-app')
@section('adminContent')


@push('style')
<link rel="stylesheet" href="/libs/sweetalert2/dist/sweetalert2.min.css">
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
                                <input type="text" name="name" id="name" value="{{old('name')}}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter name here" required />
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
                                <input type="text" name="username" id="username" value="{{old('username')}}"
                                    class="form-control @error('username') is-invalid @enderror"  placeholder="Enter user name here" required />
                                <label for="username"> Username</label>
                                <small>Note: Unique username or phone number</small>
                                @error('username')
                                <div class="invalid-feedback">
                                    <p class="error">{{ $message }}</p>
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-floating">
                                <select class="form-control @error('role') is-invalid @enderror" name="role" id="role"
                                    required>
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
<script src="/libs/sweetalert2/dist/sweetalert2.min.js"></script>
<script>
@if(session('username'))
document.addEventListener('DOMContentLoaded', (event) => {
    setTimeout(() => {
        Swal.fire({
            title: '<span class="text-success">User Login Details</span>',
            html: `
                        <p>Username : <span id="login-username">{{session('username')}}</span></p>
                        <p>Password : <span id="login-password">{{session('password')}}</span></p>
                    `,
            showCancelButton: true,
            confirmButtonColor: '#5D87FF',
            confirmButtonText: '<svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg> Copy Details',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                copyAllDetails()
            }
        }).catch(error => {
            console.error('Error displaying the reminder:', error);
        });
    }, 10);
});

@endif

function copyAllDetails() {
    const loginUsername = document.getElementById('login-username').innerText;
    const loginPassword = document.getElementById('login-password').innerText;

    const allDetails = `Link URL: {{url('')}}\nLogin Username: ${loginUsername}\nLogin Password: ${loginPassword}`;

    const textarea = document.createElement('textarea');
    textarea.value = allDetails;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);

    Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: 'All details have been copied to the clipboard.',
        timer: 2000,
        showConfirmButton: false
    });
}

function completeReminder(reminderId) {
    // Your complete reminder logic here
    console.log('Reminder ID:', reminderId);
}
</script>
@endpush