@extends('layouts.master')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <h4 class="py-2 m-4"><span class="text-muted fw-light">Project Details</span></h4>

    <div class="row mt-5">
        <div class="col-12 col-md-12 col-lg-12">

            <div class="card p-lg-4 p-2">
                <!-- Nav tabs -->
                <div class="container">
                    <div class="row justify-content-center">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Pending"
                                    type="button" role="tab" aria-controls="home" aria-selected="true">
                                    Running Project 
                                    <span class="badge bg-primary"> {{$runningCount}}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Completed Project
                                    <span class="badge bg-primary">{{$completedCount}}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Complete"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Dropped Project
                                    <span class="badge bg-primary">{{$droppedCount}}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="Pending" role="tabpanel" aria-labelledby="home-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Running Project</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('project_title.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Create New Project
                                            </a>
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
                                            <th>Start Date</th>
                                            <th>Expected End Date</th>
                                            <th>Completed Date</th>
                                            <th>Project Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>assigned User</th>
                                            @can('View Project Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($runningProject as $key => $project)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d F Y') }}</td>
                                            <td>{{ $project->end_by_date ? \Carbon\Carbon::parse($project->end_by_date)->format('d F Y, h:i A') : 'Project Running' }}</td>
                                            <td>{{ $project->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($project->description)) !!}</td>
                                            <td>{{ $project->status }}</td>
                                            <td>
                                                @foreach(explode(',', $project->user_id) as $userId)
                                                @php
                                                    $user = App\Models\User::find($userId);
                                                @endphp
                                                 {{ $user->name ?? 'No user assigned' }}<br>
                                                @endforeach
                                            </td>
                                            @can('View Project Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('project_title.edit', ['project_title' => $project->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="Delete-task-form-{{ $project->id }}" action="{{ route('project_title.destroy', ['project_title' => $project->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $project->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @can('Project Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="Complete-task-form-{{ $project->id }}" action="{{ route('project.complete', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Complete Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="Drop-task-form-{{ $project->id }}" action="{{ route('project.drop', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmDropTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Drop Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>                                                           
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="Incomplete" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Completed Project</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('project_title.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Create New Project
                                            </a>
                                        </div>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable2" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Start Date</th>
                                            <th>Expected End Date</th>
                                            <th>Completed Date</th>
                                            <th>Project Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>assigned User</th>
                                            @can('View Project Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($completedProject as $key => $project)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d F Y') }}</td>
                                            <td>{{ $project->end_by_date ? \Carbon\Carbon::parse($project->end_by_date)->format('d F Y, h:i A') : 'Project completed' }}</td>
                                            <td>{{ $project->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($project->description)) !!}</td>
                                            <td>{{ $project->status }}</td>
                                            <td> @foreach(explode(',', $project->user_id) as $userId)
                                                @php
                                                    $user = App\Models\User::find($userId);
                                                @endphp
                                                 {{ $user->name ?? 'No user assigned' }}<br>
                                                @endforeach
                                            </td>
                                            @can('View Project Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('project_title.edit', ['project_title' => $project->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="Delete-task-form-{{ $project->id }}" action="{{ route('project_title.destroy', ['project_title' => $project->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $project->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @can('Project Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="Running-task-form-{{ $project->id }}" action="{{ route('project.running', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRunningTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Running Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="Drop-task-form-{{ $project->id }}" action="{{ route('project.drop', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmDropTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Drop Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>                                                           
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="Complete" role="tabpanel" aria-labelledby="messages-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Dropped Project</h5>
                                    </div>
                                    @can('Create Project')
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('project_title.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Create New Project
                                            </a>
                                        </div>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable3" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Start Date</th>
                                            <th>Expected End Date</th>
                                            <th>Project Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>assigned User</th>
                                            @can('View Project Action')
                                            <th>Actions</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($droppedProject as $key => $project)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d F Y') }}</td>
                                            <td>{{ $project->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($project->description)) !!}</td>
                                            <td>{{ $project->status }}</td>
                                            <td> @foreach(explode(',', $project->user_id) as $userId)
                                                @php
                                                    $user = App\Models\User::find($userId);
                                                @endphp
                                                 {{ $user->name ?? 'No user assigned' }}<br>
                                                @endforeach
                                            </td>
                                            @can('View Project Action')
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('project_title.edit', ['project_title' => $project->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="Delete-task-form-{{ $project->id }}" action="{{ route('project_title.destroy', ['project_title' => $project->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $project->id }})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        @can('Project Change Status')
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <form id="Running-task-form-{{ $project->id }}" action="{{ route('project.running', ['project_title' => $project->id]) }}" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="button" class="dropdown-item" onclick="confirmRunningTask({{ $project->id }})">
                                                                        <i class="bx bx-check me-1"></i> Running Project
                                                                    </button>
                                                                </form>
                                                                <li>
                                                                    <form id="Complete-task-form-{{ $project->id }}" action="{{ route('project.complete', ['project_title' => $project->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $project->id }})">
                                                                            <i class="bx bx-check me-1"></i> Complete Project
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </div>                                                           
                                            </td>
                                            @endcan
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmRunningTask(runningTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to make this project running? Your project submitted date will be reset",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Running-task-form-${runningTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                        <script>
                                            function confirmCompleteTask(CompleteTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Complete this project? Your project completed will be today.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Complete-task-form-${CompleteTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                        <script>
                                            function confirmDeleteTask(DeleteTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Delete this project?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Delete-task-form-${DeleteTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                        <script>
                                            function confirmDropTask(DropTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Drop this project?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`Drop-task-form-${DropTaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    $('.dropdown-submenu a.test').on("click", function(e){
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
});
</script>
@if (session('success'))
<script>
    Swal.fire({
        toast: true,
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000

        }
    );
</script>
@endif 
@endsection
