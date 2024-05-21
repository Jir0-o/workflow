@extends('layouts.master')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <h4 class="py-2 m-4"><span class="text-muted fw-light">Assign Task</span></h4>

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
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#Request"
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
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
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
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Status</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
                                        @foreach($pendingTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($task->description)) !!}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->created_at->format('d F Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('asign_tasks.edit', ['asign_task' => $task->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('asign_tasks.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>                                                           
                                        </tr>
                                        @endforeach
                                        {{-- --<script>
                                            function confirmDeleteTask(DeleteTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to delete this task?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes, Delete it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`delete-task-form-${DeleteTaskId}`).submit();
                                                    }
                                                });
                                            } 
                                        </script>  --}}
                                        {{-- Sweet alart for completed task change  --}}
                                        {{-- <script>
                                            function confirmCompleteTask(StatusTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to make this task completed?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`complete-task-form-${StatusTaskId}`).submit();
                                                    }
                                                });
                                            } 
                                        </script>  --}}
                                        {{-- Sweet alart for requested task change  --}}
                                        {{-- <script>
                                            function confirmRequestedTask(requestTaskId) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: "Do you want to make this task requested?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`requested-task-form-${requestTaskId}`).submit();
                                                    }
                                                });
                                            } 
                                        </script>  --}}

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
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
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
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Status</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($incompleteTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($task->description)) !!}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->created_at->format('d F Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('asign_tasks.edit', ['asign_task' => $task->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('asign_tasks.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Make Pending
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('asign_tasks.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
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
                                        <h5>Completed Task</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
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
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Status</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($completeTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($task->description)) !!}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->created_at->format('d F Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('asign_tasks.edit', ['asign_task' => $task->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <form id="delete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('asign_tasks.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Make Pending
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="requested-task-form-{{ $task->id }}" action="{{ route('asign_tasks.requested', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmRequestedTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Move to Requested
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                   <div class="tab-pane" id="Request" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Requested Task</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
                                                <i class="bx bx-edit-alt me-1"></i> Assign Task
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
                                            <th>User Name</th>
                                            <th>Project Title</th>
                                            <th>Task</th>
                                            <th>Suggestion</th>
                                            <th>Status</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($inprogressTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->user->name }}</td>
                                            <td>{{ $task->title->project_title ?? 'No project title selected' }}</td>
                                            <td>{!! nl2br(e($task->description)) !!}</td>
                                            <td>{!! nl2br(e($task->message)) !!}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>{{ \Carbon\Carbon::parse($task->submit_date)->format('d F Y') }}</td>
                                            <td>{{ $task->created_at->format('d F Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('asign_tasks.edit', ['asign_task' => $task->id]) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Accept
                                                        </a>
                                                        <form id="incomplete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.incomplete', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="dropdown-item" onclick="confirmIncompleteTask({{ $task->id}})">
                                                                <i class="bx bx-trash me-1"></i> Reject
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <!-- Right-aligned dropdown for Change Status -->
                                                        <div class="dropdown-submenu">
                                                            <a class="dropdown-item test" href="#" id="dropdownStatusLink">
                                                                <i class="bx bx-refresh me-1"></i> Change Status
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownStatusLink">
                                                                <li>
                                                                    <form id="pending-task-form-{{ $task->id }}" action="{{ route('asign_tasks.pendingdate', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmPendingTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Make Pending
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form id="complete-task-form-{{ $task->id }}" action="{{ route('asign_tasks.complete', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="button" class="dropdown-item" onclick="confirmCompleteTask({{ $task->id}})">
                                                                            <i class="bx bx-trash me-1"></i> Make Completed
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <script>
                    function confirmDeleteTask(DeleteTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to delete this task?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`delete-task-form-${DeleteTaskId}`).submit();
                            }
                        });
                    } 
                </script> 
                {{-- Sweet alart for completed task change  --}}
                <script>
                    function confirmCompleteTask(StatusTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task completed?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`complete-task-form-${StatusTaskId}`).submit();
                            }
                        });
                    } 
                </script> 
                {{-- Sweet alart for requested task change  --}}
                <script>
                    function confirmRequestedTask(requestTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task requested?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`requested-task-form-${requestTaskId}`).submit();
                            }
                        });
                    } 
                </script> 
                {{-- Sweet alart for pending task change  --}}
                <script>
                    function confirmPendingTask(pendingTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task pending? Task last date of submit will be today.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`pending-task-form-${pendingTaskId}`).submit();
                            }
                        });
                    } 
                </script>
                {{-- Sweet alart for incomplete task change  --}}
                <script>
                    function confirmIncompleteTask(incompleteTaskId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to make this task incomplete or pending? If task last submit date is not expired then pending other wise incomplete.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`incomplete-task-form-${incompleteTaskId}`).submit();
                            }
                        });
                    } 
                </script>
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
