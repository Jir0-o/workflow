@extends('layouts.master')
@section('content')
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
                                    Complete Task
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
                                        <h5>Tasks</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
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
                                            <th>User Name</th>
                                            <th>Task</th>
                                            <th>Status</th>
                                            <th>Last Date of Submit</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($pendingTasks as $key => $task)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $task->user->name }}</td>
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
                                                        <form action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">
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
                    </div>
                    
                    <div class="tab-pane" id="Incomplete" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Tasks</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
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
                                            <th>User Name</th>
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
                                                        <form action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">
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
                    </div>
                    
                    <div class="tab-pane" id="Complete" role="tabpanel" aria-labelledby="messages-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Tasks</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
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
                                            <th>User Name</th>
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
                                                        <form action="{{ route('asign_tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">
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
                    </div>
                    
                   <div class="tab-pane" id="Request" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Tasks</h5>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <!-- Button trigger modal -->
                                            <a href="{{ route('asign_tasks.create') }}" class="btn btn-primary">
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
                                            <th>User Name</th>
                                            <th>Task</th>
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
                                                            <i class="bx bx-edit-alt me-1"></i> Accept
                                                        </a>
                                                        <form action="{{ route('asign_tasks.incomplete', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bx bx-trash me-1"></i> Reject
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
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
