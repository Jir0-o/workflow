@extends('layouts.master')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <h4 class="py-2 m-4"><span class="text-muted fw-light">My Task</span></h4>
    {{-- @include('sweetalert::alert') --}}
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
                                    Pending Task
                                    <span class="badge bg-primary"> {{ $pendingCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Incomplete"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Incomplete Task
                                    <span class="badge bg-primary"> {{ $incompleteCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Complete"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Completed Task
                                    <span class="badge bg-primary"> {{ $completeCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Requested"
                                    type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    Requested Task
                                    <span class="badge bg-primary"> {{ $inprogressCount }}</span>
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
                                        <h5>Pending Task</h5>
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Create Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable1" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($pendingTasks as $key => $pendingTask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $pendingTask->title->project_title ?? 'No project title selected'  }}</td>
                                            <td>{!! nl2br(e($pendingTask->description)) !!}</td>
                                            <td>{{ \Carbon\Carbon::parse($pendingTask->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $pendingTask->created_at->format('d F Y') }}</td>
                                            <td>{{ $pendingTask->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('tasks.edit', ['task' => $pendingTask->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Suggest Edit
                                                        </a>
                                                        <form id="complete-task-form-{{ $pendingTask->id }}" action="{{ route('tasks.complete', $pendingTask->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $pendingTask->id }})">
                                                                <i class="bx bx-check me-1"></i> Complete Task
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>                                                
                                            </td>
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmCompleteTask(taskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to complete this task?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes, complete it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`complete-task-form-${taskId}`).submit();
                                                    }
                                                });
                                            } 
                                        </script> 

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
                                        <h5>Incomplete Task</h5>
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Create Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable2" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($incompletedTasks as $key => $incompletedtask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $incompletedtask->title->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($incompletedtask->description)) !!}</td>
                                            <td>{{ \Carbon\Carbon::parse($incompletedtask->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $incompletedtask->created_at->format('d F Y') }}</td>
                                            <td>{{ $incompletedtask->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        {{-- <a class="dropdown-item" href="{{ route('tasks.edit', ['task' => $incompletedtask->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form action="{{ route('tasks.destroy', $incompletedtask->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form> --}}
                                                        <form id="incomplete-task-form-{{ $incompletedtask->id }}" action="{{ route('tasks.extend', $incompletedtask->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmInCompleteTask({{ $incompletedtask->id }})">
                                                                <i class="bx bx-check me-1"></i> Extend Task
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmInCompleteTask(intaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to extend this task? Request will be send to admin.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`incomplete-task-form-${intaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 

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
                                        <h5>Completed Task</h5>
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Create Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable3" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($completedTasks as $key => $completedtask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $completedtask->title->project_title  ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($completedtask->description)) !!}</td>
                                            <td>{{ \Carbon\Carbon::parse($completedtask->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $completedtask->created_at->format('d F Y') }}</td>
                                            <td>{{ $completedtask->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <form id="complete-task-form-{{ $completedtask->id }}" action="{{ route('tasks.redo', $completedtask->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmCompletedTask({{ $completedtask->id }})">
                                                                <i class="bx bx-check me-1"></i> Re-Do Task
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmCompletedTask(completetaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Re-do this task? Last submit date of this task will be today.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`complete-task-form-${completetaskId}`).submit();
                                                    }
                                                });
                                            }
                                        </script> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="Requested" role="tabpanel" aria-labelledby="messages-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Requested Task</h5>
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Create Task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive text-nowrap p-3">
                                <table id="datatable4" class="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($requestedTasks as $key => $requestedTask)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $requestedTask->title->project_title  ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($requestedTask->description)) !!}</td>
                                            <td>{{ \Carbon\Carbon::parse($requestedTask->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $requestedTask->created_at->format('d F Y') }}</td>
                                            <td>{{ $requestedTask->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <form id="request-task-form-{{ $requestedTask->id }}" action="{{ route('tasks.cancel', $requestedTask->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $requestedTask->id }})">
                                                                <i class="bx bx-check me-1"></i> Request Cancel
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <script>
                                            function confirmRequestedTask(requestTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to Re-do this task? Last submit date of this task will be today.",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`request-task-form-${requestTaskId}`).submit();
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
