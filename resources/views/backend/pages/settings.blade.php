@extends('layouts.master')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <h4 class="py-2 m2-4"><span class="text-muted fw-light">Users,Roles & Permissions</span></h4>

    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <!-- Roles -->
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Roles</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="float-end">

                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">
                                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                        <i class="bx bx-edit-alt me-1"></i> Create Role
                                    </a>
                                </button>

                                <!-- Modal -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap p-3">
                    <table id="datatable2" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($roles as $role)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>{{ $role->name }}</td>
                                    <th class="py-4 px-6" style="width: 500px;">
                                        <div id="permission{{ $role->id }}" class="hidden flex gap-4 flex-wrap">
                                            @foreach ($role->permissions as $item)
                                                <div class="bg-green-500 text-dark p-1 rounded font-bold">
                                                    {{ $item->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </th>
                                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('roles.edit', $role->id) }}"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <form id="delete-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmDelete({{ $role->id}})">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>       

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <!--/ Roles -->
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            {{-- Permissions --}}
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Permissions</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="float-end">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="">
                                <a href="{{ route('permission.create') }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Create Permission
                                </a>
                            </button>
                                <!-- Modal -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap p-3">
                    <table id="datatable" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Permission Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>{{ $permission->name }}</td>
                                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('permission.edit', $permission->id) }}"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit</a>
                                                        <form id="delete-{{ $permission->id }}" action="{{ route('permission.destroy', $permission->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDelete({{ $permission->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>                                                    
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <!--/ Permissions -->
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12 col-md-12 col-lg-12">
            {{-- Users --}}
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Users</h5>
                        </div>
                        @can('Create User')
                        <div class="col-12 col-md-6">
                            <div class="float-end">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="">
                                <a href="{{ route('user.create') }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Create User
                                </a>
                            </button>

                                <!-- Modal -->
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive text-nowrap p-3">
                    <table id="datatable1" class="table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            {{$role->name }}
                                        @endforeach
                                    </td>
                                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('edit User')
                                                <a class="dropdown-item" href="{{ route('user.edit', $user->id) }}"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit</a>
                                                        @endcan
                                                        @can('delete User')
                                                        <form id="delete-{{ $user->id }}" action="{{ route('user.destroy', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDelete({{ $user->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                function confirmDelete(DeleteId) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to delete this element? You will get back any deleted Data",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-${DeleteId}`).submit();
                        }
                    });
                } 
            </script> 
            <!--/ Permissions -->
        </div>
    </div>
@endsection
