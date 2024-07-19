@extends('layout.admin-app')
@section('adminContent')

@push('style')
<link rel="stylesheet" href="/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
@endpush




<div class="card w-100 position-relative overflow-hidden">
    <div class="px-4 py-3 border-bottom">
        <h5 class="card-title fw-semibold mb-0 lh-sm">List User</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive rounded-2 py-5 mb-4">
            <table
                class="table border text-nowrap customize-table mb-0 align-middle @if ($pageData->users && count($pageData->users) != 0) datatable-select-inputs @endif">
                <thead class="text-dark fs-4">
                    <tr>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">#</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Name</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Role</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Username</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">is Active</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Action</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($pageData->users && count($pageData->users) != 0)
                    @for ($i = 0; $i < count($pageData->users); $i++)
                        <tr>
                            <td>
                            <input class="id" type="hidden" name="id[]" value="{{$pageData->users[$i]->id}}">
                                <p class="mb-0 fw-normal fs-4" style="color: {{$pageData->users[$i]->deleted_at ? 'red' : 'inherit' }}">{{ $i + 1 }}</p>
                            </td>
                            <td>
                                <p class="mb-0 fw-normal fs-4" style="color: {{$pageData->users[$i]->deleted_at ? 'red' : 'inherit' }}">{{ $pageData->users[$i]->name }}</p>
                            </td>
                            <td>
                                <p class="mb-0 fw-normal fs-4" style="color: {{$pageData->users[$i]->deleted_at ? 'red' : 'inherit' }}">{{ $pageData->users[$i]->role }}</p>
                            </td>
                            <td>
                                <p class="mb-0 fw-normal fs-4" style="color: {{$pageData->users[$i]->deleted_at ? 'red' : 'inherit' }}">{{ $pageData->users[$i]->username }}</p>
                            </td>
                            <td>
                                <p class="mb-0 fw-normal fs-4" style="color: {{$pageData->users[$i]->deleted_at ? 'red' : 'inherit' }}">
                                    {{$pageData->users[$i]->deleted_at != null ? "No" : "Yes"}}</p>
                            </td>
                            <td class="">
                                @if ($pageData->users[$i]->id === 1)
                                Super Admin
                                @elseif($pageData->users[$i]->deleted_at == null )
                                <form
                                    action="{{route('admin.user.destroy', ['encodedId' => base64_encode($pageData->users[$i]->id)]) }}"
                                    method="post" class="delete-form" style="display:inline;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Soft Delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" onclick="return confirmDelete()"
                                        style="background:none; border:none; padding:0; margin:0; color:red; cursor:pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
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
                                @else
                                <form
                                    action="{{route('admin.user.restore', ['encodedId' => base64_encode($pageData->users[$i]->id)])}}"
                                    method="post" class="delete-form" style="display:inline;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Restore">
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
                                @endif
                            </td>
                        </tr>
                        @endfor
                        @else
                        <tr>
                            <td>
                                <p class="mb-0 fw-normal fs-4">No users Found</p>
                            </td>
                        </tr>
                        @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

@push('script')
<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this user?');
}

function confirmRestore() {
    return confirm('Are you sure you want to restore this user?');
}
</script>

<script src="/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/js/datatable/datatable-api.init.js"></script>


@endpush